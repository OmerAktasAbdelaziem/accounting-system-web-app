<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class AutoTranslate extends Command
{
    protected $signature = 'translation:auto-translate
        {source=en : Source locale code}
        {--locales=ar,tr : Comma-separated target locale codes}
        {--force : Re-translate existing translations}
        {--only-missing : Translate only missing or empty entries}
        {--provider=google : Translation provider (google|libretranslate)}
        {--api-key= : Optional API key for the selected provider}
        {--ignore-ssl : Skip SSL verification for translation provider requests}
        {--dry-run : Show the changes without writing files}';

    protected $description = 'Auto-translate JSON translation files with normalized source keys and cleaned output.';

    protected array $supportedProviders = ['google', 'libretranslate'];
    protected bool $ignoreSsl = false;

    public function handle()
    {
        $sourceLocale = Str::lower(trim($this->argument('source') ?? 'en'));
        $targetLocales = $this->parseLocales($this->option('locales'));
        $provider = Str::lower($this->option('provider') ?? 'google');
        $apiKey = $this->option('api-key') ?: env('TRANSLATION_API_KEY');
        $force = (bool) $this->option('force');
        $onlyMissing = (bool) $this->option('only-missing');
        $dryRun = (bool) $this->option('dry-run');
        $this->ignoreSsl = (bool) $this->option('ignore-ssl');

        if (! in_array($provider, $this->supportedProviders, true)) {
            $this->error("Unsupported provider: {$provider}");
            $this->line('Supported providers: ' . implode(', ', $this->supportedProviders));
            return 1;
        }

        $langRoot = $this->detectLangRoot();
        if (! $langRoot) {
            $this->error('Could not determine translation root. Please create resources/lang or lang directory.');
            return 1;
        }

        $this->info("Using translation root: {$langRoot}");

        $sourceKeys = $this->gatherSourceKeys($langRoot, $sourceLocale, $dryRun);
        if (empty($sourceKeys)) {
            $this->error("Source locale '{$sourceLocale}' contains no translatable strings.");
            return 1;
        }

        $summary = [];
        foreach ($targetLocales as $targetLocale) {
            if ($targetLocale === $sourceLocale) {
                continue;
            }

            $this->info("Processing target locale: {$targetLocale}");
            $targetPath = $this->getLocaleJsonPath($langRoot, $targetLocale);
            $targetMessages = file_exists($targetPath) ? $this->loadJsonFile($targetPath) : [];
            $targetMessages = $this->normalizeTranslationKeys($targetMessages);

            $summary[$targetLocale] = $this->translateLocaleJson(
                $sourceKeys,
                $targetMessages,
                $sourceLocale,
                $targetLocale,
                $provider,
                $apiKey,
                $force,
                $onlyMissing,
                $dryRun,
                $targetPath
            );
        }

        $this->line($dryRun ? 'Dry run complete. No files were written.' : 'Translation run complete.');
        $this->displaySummary($summary);

        return 0;
    }

    protected function gatherSourceKeys(string $langRoot, string $sourceLocale, bool $dryRun): array
    {
        $sourceJsonPath = $this->getLocaleJsonPath($langRoot, $sourceLocale);
        $sourcePhpPath = $this->getLocalePhpMessagesPath($langRoot, $sourceLocale);
        $keys = [];

        if (file_exists($sourceJsonPath)) {
            $sourceMessages = $this->loadJsonFile($sourceJsonPath);
            $cleanSourceMessages = $this->normalizeTranslationKeys($sourceMessages);
            $keys = array_keys($cleanSourceMessages);

            if (! $dryRun && $cleanSourceMessages !== $sourceMessages) {
                $this->writeJsonFile($sourceJsonPath, $cleanSourceMessages);
                $this->line("Cleaned source JSON file: {$sourceJsonPath}");
            }
        } elseif (file_exists($sourcePhpPath)) {
            $sourceMessages = $this->loadPhpArrayFile($sourcePhpPath);
            $keys = $this->normalizeSourceStrings($this->extractSourceStrings($sourceMessages));
        }

        $scanKeys = $this->scanSourceKeysFromFiles($dryRun);
        if (! empty($scanKeys)) {
            $keys = array_merge($keys, $scanKeys);
        }

        return array_values(array_unique(array_filter(array_map(fn ($key) => $this->normalizeKey((string) $key), $keys), fn ($key) => $this->isValidTranslationKey($key))));
    }

    protected function scanSourceKeysFromFiles(bool $dryRun): array
    {
        $keys = [];
        $directories = [base_path('resources/views'), base_path('app')];

        foreach ($directories as $directory) {
            if (! is_dir($directory)) {
                continue;
            }

            foreach (File::allFiles($directory) as $file) {
                $fileName = $file->getFilename();
                if (! str_ends_with($fileName, '.blade.php') && $file->getExtension() !== 'php') {
                    continue;
                }

                $keys = array_merge($keys, $this->extractTranslationKeysFromFile($file->getPathname(), $dryRun));
            }
        }

        return array_values(array_unique($keys));
    }

    protected function extractTranslationKeysFromFile(string $path, bool $dryRun): array
    {
        $contents = File::get($path);
        $keys = [];

        preg_match_all("/(?<![\\w\\])(?:__|trans)\(\s*(['\"])(.*?)(?<!\\\\)\1/s", $contents, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $key = $this->normalizeKey($match[2]);
            if ($this->isValidTranslationKey($key)) {
                $keys[] = $key;
            }
        }

        preg_match_all("/@lang\(\s*(['\"])(.*?)(?<!\\\\)\1/s", $contents, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $key = $this->normalizeKey($match[2]);
            if ($this->isValidTranslationKey($key)) {
                $keys[] = $key;
            }
        }

        return array_values(array_unique($keys));
    }

    protected function normalizeTranslationKeys(array $translations): array
    {
        $normalized = [];

        foreach ($translations as $key => $value) {
            $cleanKey = $this->normalizeKey((string) $key);
            if ($cleanKey === '' || ! $this->isValidTranslationKey($cleanKey)) {
                continue;
            }

            if (is_array($value)) {
                $nested = $this->normalizeTranslationKeys($value);
                if ($nested === []) {
                    continue;
                }

                $normalized[$cleanKey] = $nested;
                continue;
            }

            $normalized[$cleanKey] = $value;
        }

        ksort($normalized, SORT_NATURAL | SORT_FLAG_CASE);

        return $normalized;
    }

    protected function normalizeSourceStrings(array $sourceStrings): array
    {
        return array_values(array_unique(array_filter(array_map(fn ($string) => $this->normalizeKey((string) $string), $sourceStrings), fn ($key) => $this->isValidTranslationKey($key))));
    }

    protected function extractSourceStrings(array $messages): array
    {
        $flattened = [];
        array_walk_recursive($messages, function ($value) use (&$flattened) {
            if (is_string($value) && trim($value) !== '') {
                $flattened[] = $value;
            }
        });

        return $flattened;
    }

    protected function translateLocaleJson(
        array $sourceKeys,
        array $targetMessages,
        string $sourceLocale,
        string $targetLocale,
        string $provider,
        ?string $apiKey,
        bool $force,
        bool $onlyMissing,
        bool $dryRun,
        string $targetPath
    ): array {
        $currentMessages = $targetMessages;
        $updatedMessages = [];
        $stats = ['added' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0];

        foreach ($sourceKeys as $sourceKey) {
            if ($sourceKey === '') {
                continue;
            }

            $targetValue = $currentMessages[$sourceKey] ?? null;
            $needsTranslation = $force
                || $targetValue === null
                || $targetValue === ''
                || (! $onlyMissing && $targetValue === $sourceKey);

            if ($onlyMissing && $targetValue !== null && $targetValue !== '') {
                $updatedMessages[$sourceKey] = $targetValue;
                $stats['skipped']++;
                continue;
            }

            if (! $needsTranslation && $targetValue !== null && $targetValue !== '' && ! $force) {
                $updatedMessages[$sourceKey] = $targetValue;
                $stats['skipped']++;
                continue;
            }

            try {
                $translated = $this->translateString($sourceKey, $sourceLocale, $targetLocale, $provider, $apiKey);
                $translated = $this->normalizeKey($translated);
                $updatedMessages[$sourceKey] = $translated;

                if ($targetValue !== null && $targetValue !== '') {
                    $stats['updated']++;
                } else {
                    $stats['added']++;
                }
            } catch (Throwable $exception) {
                $stats['errors']++;
                $updatedMessages[$sourceKey] = $targetValue ?? $sourceKey;
                $this->error("[{$targetLocale}] {$sourceKey}: {$exception->getMessage()}");
            }
        }

        if (! $dryRun) {
            $updatedMessages = $this->normalizeTranslationKeys($updatedMessages);
            $this->writeJsonFile($targetPath, $updatedMessages);
            $this->line("Written JSON translation file: {$targetPath}");
        }

        return $stats;
    }

    protected function writeJsonFile(string $path, array $translations): void
    {
        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL);
    }

    protected function translateString(string $text, string $sourceLocale, string $targetLocale, string $provider, ?string $apiKey): string
    {
        if ($text === '') {
            return $text;
        }

        [$masked, $placeholders] = $this->maskPlaceholders($text);
        $translated = match ($provider) {
            'libretranslate' => $this->translateWithLibreTranslate($masked, $sourceLocale, $targetLocale, $apiKey),
            default => $this->translateWithGoogle($masked, $sourceLocale, $targetLocale, $apiKey),
        };

        return $this->restorePlaceholders($translated, $placeholders);
    }

    protected function maskPlaceholders(string $text): array
    {
        preg_match_all('/(:[A-Za-z_][A-Za-z0-9_]*)/', $text, $matches);
        $placeholders = [];

        foreach (array_unique($matches[0]) as $index => $token) {
            $placeholder = '__LARAVEL_PLACEHOLDER_' . $index . '__';
            $placeholders[$placeholder] = $token;
            $text = str_replace($token, $placeholder, $text);
        }

        return [$text, $placeholders];
    }

    protected function restorePlaceholders(string $text, array $placeholders): string
    {
        return strtr($text, $placeholders);
    }

    protected function normalizeKey(string $key): string
    {
        return preg_replace('/\s+/u', ' ', trim($key));
    }

    protected function isValidTranslationKey(string $key): bool
    {
        if ($key === '') {
            return false;
        }

        if (preg_match('/[\r\n]/', $key)) {
            return false;
        }

        $trimmedKey = trim($key);
        if ($trimmedKey === '' || str_starts_with($trimmedKey, '@')) {
            return false;
        }

        if ($trimmedKey === '"' || $trimmedKey === "'") {
            return false;
        }

        if (preg_match('/\{\{|\}\}|__\(|@(?:csrf|method|error|enderror|else|endforelse|endfeature|yield|include|auth|empty|endif|isset|foreach|for|while|switch|case|end)/i', $trimmedKey)) {
            return false;
        }

        return true;
    }

    protected function escapePhpString(string $value, string $quote): string
    {
        $value = str_replace('\\', '\\\\', $value);

        if ($quote === "'") {
            $value = str_replace("'", "\\'", $value);
        } else {
            $value = str_replace('"', '\"', $value);
        }

        return $value;
    }

    protected function translateWithGoogle(string $text, string $sourceLocale, string $targetLocale, ?string $apiKey): string
    {
        $response = $this->getHttpClient()
            ->get('https://translate.googleapis.com/translate_a/single', [
                'client' => 'gtx',
                'sl' => $sourceLocale,
                'tl' => $targetLocale,
                'dt' => 't',
                'q' => $text,
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Google translation failed: ' . $response->body());
        }

        $data = $response->json();
        return (string) ($data[0][0][0] ?? $text);
    }

    protected function translateWithLibreTranslate(string $text, string $sourceLocale, string $targetLocale, ?string $apiKey): string
    {
        $payload = [
            'q' => $text,
            'source' => $sourceLocale,
            'target' => $targetLocale,
            'format' => 'text',
        ];

        if ($apiKey) {
            $payload['api_key'] = $apiKey;
        }

        $response = $this->getHttpClient()
            ->post('https://libretranslate.de/translate', $payload);

        if (! $response->successful()) {
            throw new \RuntimeException('LibreTranslate failed: ' . $response->body());
        }

        $data = $response->json();
        if (! isset($data['translatedText'])) {
            throw new \RuntimeException('Unexpected LibreTranslate response.');
        }

        return (string) $data['translatedText'];
    }

    protected function getHttpClient()
    {
        return Http::withOptions(['verify' => ! $this->ignoreSsl])->timeout(60);
    }

    protected function detectLangRoot(): ?string
    {
        $resourcesLang = base_path('resources/lang');
        $langRoot = base_path('lang');

        if (is_dir($resourcesLang)) {
            return $resourcesLang;
        }

        if (is_dir($langRoot)) {
            return $langRoot;
        }

        return null;
    }

    protected function getLocaleJsonPath(string $root, string $locale): string
    {
        return $root . '/' . $locale . '.json';
    }

    protected function getLocalePhpMessagesPath(string $root, string $locale): string
    {
        return $root . '/' . $locale . '/messages.php';
    }

    protected function loadJsonFile(string $path): array
    {
        $content = @file_get_contents($path);
        if ($content === false) {
            return [];
        }

        $decoded = json_decode($content, true);
        if (! is_array($decoded)) {
            throw new \RuntimeException("Invalid JSON in {$path}: " . json_last_error_msg());
        }

        return $decoded;
    }

    protected function loadPhpArrayFile(string $path): array
    {
        $messages = include $path;
        return is_array($messages) ? $messages : [];
    }

    protected function displaySummary(array $summary): void
    {
        if (empty($summary)) {
            return;
        }

        $table = [];
        foreach ($summary as $locale => $stats) {
            $table[] = [
                'Locale' => $locale,
                'Added' => $stats['added'],
                'Updated' => $stats['updated'],
                'Skipped' => $stats['skipped'],
                'Errors' => $stats['errors'],
            ];
        }

        $this->table(['Locale', 'Added', 'Updated', 'Skipped', 'Errors'], $table);
    }

    protected function parseLocales(string $value): array
    {
        return collect(explode(',', $value))
            ->map(fn ($locale) => Str::lower(trim($locale)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
