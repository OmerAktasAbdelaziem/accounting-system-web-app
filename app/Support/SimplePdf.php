<?php

namespace App\Support;

class SimplePdf
{
    public static function textDocument(string $title, array $lines): string
    {
        $escapedTitle = self::escapeUnicode($title);
        $wrappedLines = [];

        foreach ($lines as $line) {
            foreach (self::wrapLine((string) $line, 72) as $wrappedLine) {
                $wrappedLines[] = self::escapeUnicode($wrappedLine);
            }
        }

        $contentLines = [];
        $contentLines[] = 'BT';
        $contentLines[] = '/F1 18 Tf';
        $contentLines[] = '50 770 Td';
        $contentLines[] = $escapedTitle . ' Tj';
        $contentLines[] = '/F1 11 Tf';
        $contentLines[] = '0 -28 Td';

        foreach ($wrappedLines as $index => $line) {
            if ($index > 0) {
                $contentLines[] = '0 -16 Td';
            }
            $contentLines[] = $line . ' Tj';
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

    /**
     * Convert UTF-8 string to PDF hex string format (UTF-16BE)
     * This properly handles Turkish and other Unicode characters
     */
    private static function escapeUnicode(string $text): string
    {
        // Convert UTF-8 string to UTF-16BE (Big Endian)
        $utf16 = iconv('UTF-8', 'UTF-16BE', $text);
        
        // Add UTF-16BE BOM and convert to hex string
        $hexString = 'FEFF' . bin2hex($utf16);
        
        return '<' . $hexString . '>';
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
