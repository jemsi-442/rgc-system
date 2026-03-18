<?php

namespace App\Exports\Traits;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;

trait ExcelStyles
{
    /**
     * Apply header style to Excel cells
     */
    protected function applyHeaderStyle($sheet, $cellRange, $backgroundColor = '360958')
    {
        $sheet->getStyle($cellRange)->applyFromArray([
            'font' => [
                'bold' => true, 
                'color' => ['rgb' => 'FFFFFF'], 
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, 
                'startColor' => ['rgb' => $backgroundColor]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, 
                'vertical' => Alignment::VERTICAL_CENTER, 
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, 
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
    }

    /**
     * Apply data row style to Excel cells
     */
    protected function applyDataRowStyle($sheet, $cellRange)
    {
        $sheet->getStyle($cellRange)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT, 
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, 
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
    }

    /**
     * Apply total row style to Excel cells
     */
    protected function applyTotalRowStyle($sheet, $cellRange)
    {
        $sheet->getStyle($cellRange)->applyFromArray([
            'font' => [
                'bold' => true, 
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, 
                'startColor' => ['rgb' => 'F0F0F0']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT, 
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, 
                    'color' => ['rgb' => '000000']
                ],
                'top' => [
                    'borderStyle' => Border::BORDER_DOUBLE, 
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
    }

    /**
     * Apply church header style
     */
    protected function applyChurchHeaderStyle($sheet, $cellRange)
    {
        $sheet->getStyle($cellRange)->applyFromArray([
            'font' => [
                'bold' => true, 
                'size' => 16, 
                'color' => ['rgb' => '000000']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, 
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);
    }

    /**
     * Apply title style
     */
    protected function applyTitleStyle($sheet, $cellRange)
    {
        $sheet->getStyle($cellRange)->applyFromArray([
            'font' => [
                'bold' => true, 
                'size' => 14, 
                'color' => ['rgb' => '000000']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, 
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THICK, 
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);
    }

    /**
     * Set row height
     */
    protected function setRowHeight($sheet, $row, $height)
    {
        $sheet->getRowDimension($row)->setRowHeight($height);
    }

    /**
     * Merge cells
     */
    protected function mergeCells($sheet, $range)
    {
        $sheet->mergeCells($range);
    }

    /**
     * Set cell value
     */
    protected function setCellValue($sheet, $cell, $value)
    {
        $sheet->setCellValue($cell, $value);
    }
}
