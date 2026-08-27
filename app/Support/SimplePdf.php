<?php

namespace App\Support;

class SimplePdf
{
    private const PAGE_HEIGHT = 842;
    private const PAGE_WIDTH = 595;
    private const MARGIN_TOP = 50;
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
        $titleLines = 2;
        $availableLinesPerPage = self::LINES_PER_PAGE;
        
        // Split lines into pages
        $pages = [];
        $currentPage = [];
        $currentLineCount = $titleLines;

        foreach ($wrappedLines as $line) {
            if ($currentLineCount >= $availableLinesPerPage) {
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

        // Generate content streams for each page
        $contentStreams = [];
        foreach ($pages as $pageLines) {
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
            $contentStreams[] = implode("\n", $contentLines);
        }

        // Build PDF with proper object numbering
        $objects = [];
        $pageCount = count($pages);

        // Object 1: Catalog
        $objects[1] = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';

        // Object 2: Pages (Parent)
        $pageKids = [];
        for ($i = 0; $i < $pageCount; $i++) {
            $pageKids[] = (3 + $i) . ' 0 R';
        }
        $objects[2] = '2 0 obj << /Type /Pages /Kids [' . implode(' ', $pageKids) . '] /Count ' . $pageCount . ' >> endobj';

        // Objects 3 to 3+pageCount-1: Page objects
        // Objects 3+pageCount to 3+2*pageCount-1: Content streams
        for ($i = 0; $i < $pageCount; $i++) {
            $pageObjNum = 3 + $i;
            $contentObjNum = 3 + $pageCount + $i;
            $contentLength = strlen($contentStreams[$i]);

            $objects[$contentObjNum] = $contentObjNum . ' 0 obj << /Length ' . $contentLength . ' >> stream' . "\n" . $contentStreams[$i] . "\nendstream endobj";
            $objects[$pageObjNum] = $pageObjNum . ' 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . self::PAGE_WIDTH . ' ' . self::PAGE_HEIGHT . '] /Resources << /Font << /F1 ' . (3 + 2 * $pageCount) . ' 0 R >> >> /Contents ' . $contentObjNum . ' 0 R >> endobj';
        }

        // Last object: Font
        $fontObjNum = 3 + 2 * $pageCount;
        $objects[$fontObjNum] = $fontObjNum . ' 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Courier >> endobj';

        // Build PDF file
        ksort($objects);
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

        foreach ($offsets as $offset) {
            if ($offset > 0) {
                $pdf .= sprintf('%010d 00000 n ', $offset) . "\n";
            }
        }

        $pdf .= 'trailer << /Size ' . (count($objects) + 1) . ' /Root 1 0 R >>' . "\n";
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
