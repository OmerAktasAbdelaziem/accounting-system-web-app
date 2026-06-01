<?php

namespace App\Support;

class SimplePdf
{
    private const PAGE_HEIGHT = 842;
    private const PAGE_WIDTH = 595;
    private const MARGIN_TOP = 50;
    private const MARGIN_BOTTOM = 50;
    private const MARGIN_LEFT = 50;
    private const LINE_HEIGHT = 16;
    private const FONT_SIZE_TITLE = 18;
    private const FONT_SIZE_BODY = 11;
    private const LINES_PER_PAGE = 40;

    public static function textDocument(string $title, array $lines): string
    {
        // Wrap all lines
        $wrappedLines = [];
        foreach ($lines as $line) {
            foreach (self::wrapLine((string) $line, 72) as $wrappedLine) {
                $wrappedLines[] = self::escapeUnicode($wrappedLine);
            }
        }

        // Calculate pages needed
        $titleLines = 2; // Title + spacing
        $availableLinesPerPage = self::LINES_PER_PAGE;
        $bodyLines = count($wrappedLines);
        
        // Calculate page breaks
        $pages = [];
        $currentPage = [];
        $currentLineCount = $titleLines;

        foreach ($wrappedLines as $line) {
            if ($currentLineCount >= $availableLinesPerPage) {
                // Start new page
                $pages[] = $currentPage;
                $currentPage = [];
                $currentLineCount = 0;
            }
            $currentPage[] = $line;
            $currentLineCount++;
        }

        if (!empty($currentPage)) {
            $pages[] = $currentPage;
        }

        // Generate PDF with multiple pages
        $objects = [];
        $pageObjects = [];

        // Generate page content
        foreach ($pages as $pageIndex => $pageLines) {
            $contentLines = [];
            $contentLines[] = 'BT';
            $contentLines[] = '/F1 ' . self::FONT_SIZE_TITLE . ' Tf';
            $contentLines[] = self::MARGIN_LEFT . ' ' . (self::PAGE_HEIGHT - self::MARGIN_TOP) . ' Td';
            $contentLines[] = self::escapeUnicode($title) . ' Tj';
            
            $contentLines[] = '/F1 ' . self::FONT_SIZE_BODY . ' Tf';
            $contentLines[] = '0 -' . (self::FONT_SIZE_TITLE + self::LINE_HEIGHT) . ' Td';

            foreach ($pageLines as $index => $line) {
                if ($index > 0) {
                    $contentLines[] = '0 -' . self::LINE_HEIGHT . ' Td';
                }
                $contentLines[] = $line . ' Tj';
            }

            $contentLines[] = 'ET';
            $contentStream = implode("\n", $contentLines);
            $contentLength = strlen($contentStream);

            // Page object
            $pageObjNum = count($objects) + 3 + $pageIndex;
            $contentObjNum = count($objects) + 3 + count($pages) + $pageIndex;

            $pageObjects[] = $pageObjNum;
            $objects[] = "$contentObjNum 0 obj << /Length $contentLength >> stream\n$contentStream\nendstream endobj";
            $objects[] = "$pageObjNum 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 " . self::PAGE_WIDTH . " " . self::PAGE_HEIGHT . "] /Resources << /Font << /F1 4 0 R >> >> /Contents $contentObjNum 0 R >> endobj";
        }

        // PDF structure
        $pdfObjects = [
            '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj',
            '2 0 obj << /Type /Pages /Kids [' . implode(' ', array_map(fn($n) => "$n 0 R", $pageObjects)) . '] /Count ' . count($pageObjects) . ' >> endobj',
            '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Courier >> endobj',
        ];

        // Combine all objects
        $allObjects = array_merge($pdfObjects, $objects);

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($allObjects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object . "\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= 'xref' . "\n";
        $pdf .= '0 ' . (count($allObjects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i < count($offsets); $i++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$i]) . "\n";
        }

        $pdf .= 'trailer << /Size ' . (count($allObjects) + 1) . ' /Root 1 0 R >>' . "\n";
        $pdf .= 'startxref' . "\n";
        $pdf .= $xrefPosition . "\n";
        $pdf .= '%%EOF';

        return $pdf;
    }

    /**
     * Convert UTF-8 string to PDF-safe format with Turkish character transliteration
     */
    private static function escapeUnicode(string $text): string
    {
        // Turkish character to ASCII mapping
        $turkishChars = [
            'ç' => 'c', 'Ç' => 'C',
            'ğ' => 'g', 'Ğ' => 'G',
            'ı' => 'i', 'I' => 'I',
            'ş' => 's', 'Ş' => 'S',
            'ö' => 'o', 'Ö' => 'O',
            'ü' => 'u', 'Ü' => 'U',
        ];
        
        $text = strtr($text, $turkishChars);
        $text = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        $text = preg_replace('/[^\x20-\x7E\x0A\x0D]/', '?', $text) ?? $text;
        
        return '(' . $text . ')';
    }

    /**
     * Wrap a line into fixed-width chunks
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
