<?php

namespace App\Support;

class SimplePdf
{
    public static function textDocument(string $title, array $lines): string
    {
        $escapedTitle = self::escape($title);
        $wrappedLines = [];

        foreach ($lines as $line) {
            foreach (self::wrapLine((string) $line, 72) as $wrappedLine) {
                $wrappedLines[] = self::escape($wrappedLine);
            }
        }

        $contentLines = [];
        $contentLines[] = 'BT';
        $contentLines[] = '/F1 18 Tf';
        $contentLines[] = '50 770 Td';
        $contentLines[] = '(' . $escapedTitle . ') Tj';
        $contentLines[] = '/F1 11 Tf';
        $contentLines[] = '0 -28 Td';

        foreach ($wrappedLines as $index => $line) {
            if ($index > 0) {
                $contentLines[] = '0 -16 Td';
            }
            $contentLines[] = '(' . $line . ') Tj';
        }

        $contentLines[] = 'ET';
        $contentStream = implode("\n", $contentLines);
        $contentLength = strlen($contentStream);

        $objects = [];
        $objects[] = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
        $objects[] = '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj';
        $objects[] = '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj';
        $objects[] = '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Courier >> endobj';
        $objects[] = '5 0 obj << /Length ' . $contentLength . ' >> stream' . "\n" . $contentStream . "\n" . 'endstream endobj';

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object . "\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= 'xref' . "\n";
        $pdf .= '0 ' . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i < count($offsets); $i++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$i]) . "\n";
        }

        $pdf .= 'trailer << /Size ' . (count($objects) + 1) . ' /Root 1 0 R >>' . "\n";
        $pdf .= 'startxref' . "\n";
        $pdf .= $xrefPosition . "\n";
        $pdf .= '%%EOF';

        return $pdf;
    }

    private static function escape(string $text): string
    {
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        return preg_replace('/[^\x20-\x7E\x0A\x0D]/', '?', $text) ?? $text;
    }

    /**
     * Wrap a line into fixed-width chunks for simple PDF rendering.
     */
    private static function wrapLine(string $text, int $width): array
    {
        $text = trim($text);

        if ($text === '') {
            return [''];
        }

        $wrapped = wordwrap($text, $width, "\n", true);

        return explode("\n", $wrapped);
    }
}
