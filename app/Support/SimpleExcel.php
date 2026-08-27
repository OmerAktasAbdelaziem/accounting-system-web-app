<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class SimpleExcel
{
    /**
     * Create an Excel file from table data with professional formatting
     * 
     * @param string $title - The title of the report/sheet
     * @param array $headers - Column headers
     * @param array $rows - Data rows (each row is an array)
     * @param array $metadata - Optional metadata like date range, user, etc.
     * @return string - The Excel file content as string
     */
    public static function createFromTable(
        string $title,
        array $headers,
        array $rows,
        array $metadata = []
    ): string {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(substr($title, 0, 31)); // Excel sheet name max 31 chars

        $currentRow = 1;

        // Add title
        $sheet->setCellValue('A' . $currentRow, $title);
        $sheet->mergeCells('A' . $currentRow . ':' . chr(64 + count($headers)) . $currentRow);
        
        $titleStyle = $sheet->getStyle('A' . $currentRow);
        $titleStyle->getFont()->setBold(true)->setSize(14)->setColor(new Color('FFFFFF'));
        $titleStyle->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('366092'));
        $titleStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($currentRow)->setRowHeight(25);
        
        $currentRow += 2;

        // Add metadata if provided
        if (!empty($metadata)) {
            foreach ($metadata as $key => $value) {
                if ($value !== null && $value !== '') {
                    $sheet->setCellValue('A' . $currentRow, $key . ':');
                    $sheet->setCellValue('B' . $currentRow, $value);
                    
                    $style = $sheet->getStyle('A' . $currentRow . ':B' . $currentRow);
                    $style->getFont()->setSize(10)->setColor(new Color('444444'));
                    
                    $currentRow++;
                }
            }
            $currentRow++; // Add blank row after metadata
        }

        // Add headers
        $headerRow = $currentRow;
        foreach ($headers as $colIndex => $header) {
            $colLetter = chr(65 + $colIndex); // A, B, C, etc.
            $sheet->setCellValue($colLetter . $headerRow, $header);
            
            $headerStyle = $sheet->getStyle($colLetter . $headerRow);
            $headerStyle->getFont()->setBold(true)->setColor(new Color('FFFFFF'));
            $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('4472C4'));
            $headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
        }
        
        $sheet->getRowDimension($headerRow)->setRowHeight(20);

        // Add data rows
        $dataStartRow = $headerRow + 1;
        foreach ($rows as $rowIndex => $row) {
            $excelRow = $dataStartRow + $rowIndex;
            
            foreach ($row as $colIndex => $value) {
                $colLetter = chr(65 + $colIndex);
                $sheet->setCellValue($colLetter . $excelRow, $value);
                
                $cellStyle = $sheet->getStyle($colLetter . $excelRow);
                $cellStyle->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
                
                // Alternate row colors for better readability
                if ($rowIndex % 2 === 0) {
                    $cellStyle->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('F2F2F2'));
                }
            }
        }

        // Auto-adjust column widths
        foreach (range(0, count($headers) - 1) as $colIndex) {
            $colLetter = chr(65 + $colIndex);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Set min column width to ensure readability
        foreach (range(0, count($headers) - 1) as $colIndex) {
            $colLetter = chr(65 + $colIndex);
            if ($sheet->getColumnDimension($colLetter)->getWidth() < 12) {
                $sheet->getColumnDimension($colLetter)->setWidth(12);
            }
        }

        // Freeze header rows
        $sheet->freezePane('A' . ($headerRow + 1));

        // Write to string
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return $content;
    }

    /**
     * Create an Excel file from text lines (similar to SimplePdf)
     * 
     * @param string $title - The title of the report
     * @param array $lines - Array of text lines
     * @param string|null $sheetName - Custom sheet name
     * @return string - The Excel file content as string
     */
    public static function createFromLines(
        string $title,
        array $lines,
        ?string $sheetName = null
    ): string {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheetName ?? substr($title, 0, 31));

        $currentRow = 1;

        // Add title
        $sheet->setCellValue('A' . $currentRow, $title);
        $titleStyle = $sheet->getStyle('A' . $currentRow);
        $titleStyle->getFont()->setBold(true)->setSize(14)->setColor(new Color('FFFFFF'));
        $titleStyle->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('366092'));
        $sheet->getRowDimension($currentRow)->setRowHeight(25);
        
        $currentRow += 2;

        // Add lines
        foreach ($lines as $line) {
            if ($line === '---' || $line === '') {
                // Skip separator lines or leave blank
                if ($line === '---') {
                    // Add a visual separator with border
                    $sheet->getRowDimension($currentRow)->setRowHeight(2);
                }
                $currentRow++;
            } else {
                $sheet->setCellValue('A' . $currentRow, $line);
                $sheet->getStyle('A' . $currentRow)->getAlignment()->setWrapText(true);
                $currentRow++;
            }
        }

        // Auto-adjust column A width
        $sheet->getColumnDimension('A')->setWidth(120);

        // Write to string
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return $content;
    }

    /**
     * Create multi-sheet Excel file
     * 
     * @param array $sheets - Array of sheet definitions
     *   Each sheet: ['title' => 'Sheet Title', 'headers' => [], 'rows' => [], 'metadata' => []]
     * @return string - The Excel file content as string
     */
    public static function createMultiSheet(array $sheets): string
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0); // Remove default sheet

        foreach ($sheets as $sheetIndex => $sheetData) {
            $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet(
                $spreadsheet,
                substr($sheetData['title'] ?? 'Sheet ' . ($sheetIndex + 1), 0, 31)
            );
            $spreadsheet->addSheet($sheet, $sheetIndex);

            $currentRow = 1;

            // Add title
            if (isset($sheetData['title'])) {
                $sheet->setCellValue('A' . $currentRow, $sheetData['title']);
                $sheet->mergeCells('A' . $currentRow . ':' . chr(64 + count($sheetData['headers'] ?? [])) . $currentRow);
                
                $titleStyle = $sheet->getStyle('A' . $currentRow);
                $titleStyle->getFont()->setBold(true)->setSize(14)->setColor(new Color('FFFFFF'));
                $titleStyle->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('366092'));
                $titleStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension($currentRow)->setRowHeight(25);
                
                $currentRow += 2;
            }

            // Add metadata if provided
            if (!empty($sheetData['metadata'])) {
                foreach ($sheetData['metadata'] as $key => $value) {
                    if ($value !== null && $value !== '') {
                        $sheet->setCellValue('A' . $currentRow, $key . ':');
                        $sheet->setCellValue('B' . $currentRow, $value);
                        $currentRow++;
                    }
                }
                $currentRow++;
            }

            // Add headers
            if (!empty($sheetData['headers'])) {
                $headerRow = $currentRow;
                foreach ($sheetData['headers'] as $colIndex => $header) {
                    $colLetter = chr(65 + $colIndex);
                    $sheet->setCellValue($colLetter . $headerRow, $header);
                    
                    $headerStyle = $sheet->getStyle($colLetter . $headerRow);
                    $headerStyle->getFont()->setBold(true)->setColor(new Color('FFFFFF'));
                    $headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('4472C4'));
                    $headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                }
                $sheet->getRowDimension($headerRow)->setRowHeight(20);
                $currentRow++;
            }

            // Add data rows
            if (!empty($sheetData['rows'])) {
                foreach ($sheetData['rows'] as $rowIndex => $row) {
                    $excelRow = $currentRow + $rowIndex;
                    
                    foreach ($row as $colIndex => $value) {
                        $colLetter = chr(65 + $colIndex);
                        $sheet->setCellValue($colLetter . $excelRow, $value);
                        
                        $cellStyle = $sheet->getStyle($colLetter . $excelRow);
                        $cellStyle->getAlignment()->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);
                        
                        if ($rowIndex % 2 === 0) {
                            $cellStyle->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('F2F2F2'));
                        }
                    }
                }
            }

            // Auto-adjust column widths
            $maxCols = max(count($sheetData['headers'] ?? []), count($sheetData['rows'][0] ?? []));
            foreach (range(0, $maxCols - 1) as $colIndex) {
                $colLetter = chr(65 + $colIndex);
                $sheet->getColumnDimension($colLetter)->setAutoSize(true);
                if ($sheet->getColumnDimension($colLetter)->getWidth() < 12) {
                    $sheet->getColumnDimension($colLetter)->setWidth(12);
                }
            }
        }

        // Write to string
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return $content;
    }
}
