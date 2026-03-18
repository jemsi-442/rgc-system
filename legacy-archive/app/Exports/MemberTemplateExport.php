<?php

namespace App\Exports;

use App\Models\Jumuiya;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class MemberTemplateExport implements WithEvents, WithTitle
{
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = 'X'; // Column X is the 24th column

                // Get church settings
                $churchName = Setting::get('church_name', 'KANISA LA KIINJILI LA KILUTHERI TANZANIA');
                $diocese = Setting::get('diocese', 'DAYOSISI YA MASHARIKI NA PWANI');
                $parish = Setting::get('parish', 'USHARIKA WA MAKABE');

                // ========================================
                // ROW 1-3: Header with Logo and Church Name
                // ========================================
                $sheet->mergeCells('A1:X1');
                $sheet->setCellValue('A1', strtoupper($churchName));
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '16A34A'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Row 2: Diocese and Parish
                $sheet->mergeCells('A2:X2');
                $sheet->setCellValue('A2', $diocese . ' - ' . $parish);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => '374151'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // Row 3: Template Title
                $sheet->mergeCells('A3:X3');
                $sheet->setCellValue('A3', 'TEMPLATE YA KUINGIZA WAUMINI KWA WINGI (BULK IMPORT)');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '16A34A'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(30);

                // Row 4: Instructions
                $sheet->mergeCells('A4:X4');
                $sheet->setCellValue('A4', 'MAELEKEZO: Jaza taarifa za waumini kuanzia safu ya 7. Safu zenye * ni lazima. Nenosiri = Jina la Ukoo (Last Name). Namba ya Muumini na Bahasha zitatengenezwa moja kwa moja.');
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 10,
                        'color' => ['rgb' => '92400E'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FEF3C7'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(35);

                // Row 5: Empty row for spacing
                $sheet->getRowDimension(5)->setRowHeight(10);

                // ========================================
                // ROW 6: Column Headers with Green Background
                // ========================================
                $headers = [
                    'A' => 'Jina la Kwanza *',
                    'B' => 'Jina la Kati',
                    'C' => 'Jina la Ukoo *',
                    'D' => 'Tarehe Kuzaliwa * (YYYY-MM-DD)',
                    'E' => 'Jinsia * (Mme/Mke)',
                    'F' => 'Namba ya NIDA',
                    'G' => 'Namba ya Simu *',
                    'H' => 'Barua Pepe',
                    'I' => 'Anwani',
                    'J' => 'Namba ya Nyumba',
                    'K' => 'Namba ya Block',
                    'L' => 'Jiji',
                    'M' => 'Mkoa',
                    'N' => 'Jumuiya *',
                    'O' => 'Tarehe Ubatizo',
                    'P' => 'Tarehe Kipaimara',
                    'Q' => 'Hali ya Ndoa *',
                    'R' => 'Kundi Maalum',
                    'S' => 'Kazi/Ajira',
                    'T' => 'Mzee wa Kanisa',
                    'U' => 'Jina la Mwenzi',
                    'V' => 'Simu ya Mwenzi',
                    'W' => 'Jina la Jirani',
                    'X' => 'Simu ya Jirani',
                ];

                foreach ($headers as $col => $value) {
                    $sheet->setCellValue($col . '6', $value);
                }

                $sheet->getStyle('A6:X6')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 10,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '16A34A'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension(6)->setRowHeight(40);

                // ========================================
                // ROW 7: Sample Data (Light Green Background)
                // ========================================
                $jumuiya = Jumuiya::first();
                $jumuiyaName = $jumuiya ? $jumuiya->name : 'Jumuiya ya Kwanza';

                $sampleData = [
                    'A' => 'Yohana',
                    'B' => 'Petro',
                    'C' => 'Mwakasege',
                    'D' => '1985-06-15',
                    'E' => 'Mme',
                    'F' => '19850615123456789012',
                    'G' => '0712345678',
                    'H' => 'yohana@gmail.com',
                    'I' => 'Kijitonyama, Mtaa wa 5',
                    'J' => 'A-123',
                    'K' => 'Block 7',
                    'L' => 'Dar es Salaam',
                    'M' => 'Dar es Salaam',
                    'N' => $jumuiyaName,
                    'O' => '2000-12-25',
                    'P' => '2005-04-10',
                    'Q' => 'Ameoa/Ameolewa',
                    'R' => 'Kwaya Kuu',
                    'S' => 'Mwalimu',
                    'T' => 'Mzee John Makundi',
                    'U' => 'Maria Mwakasege',
                    'V' => '0723456789',
                    'W' => 'James Paulo',
                    'X' => '0734567890',
                ];

                foreach ($sampleData as $col => $value) {
                    $sheet->setCellValue($col . '7', $value);
                }

                $sheet->getStyle('A7:X7')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'DCFCE7'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'font' => [
                        'italic' => true,
                        'color' => ['rgb' => '6B7280'],
                    ],
                ]);
                $sheet->getRowDimension(7)->setRowHeight(25);

                // Add note below sample data
                $sheet->mergeCells('A8:X8');
                $sheet->setCellValue('A8', '↑ Mfano wa data (unaweza kuifuta safu hii). Anza kuingiza waumini kuanzia safu ya 7 au 8.');
                $sheet->getStyle('A8')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 9,
                        'color' => ['rgb' => '6B7280'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // ========================================
                // Set Column Widths
                // ========================================
                $columnWidths = [
                    'A' => 18, 'B' => 15, 'C' => 18, 'D' => 22, 'E' => 16,
                    'F' => 22, 'G' => 16, 'H' => 22, 'I' => 28, 'J' => 15,
                    'K' => 14, 'L' => 15, 'M' => 15, 'N' => 20, 'O' => 16,
                    'P' => 16, 'Q' => 18, 'R' => 16, 'S' => 16, 'T' => 22,
                    'U' => 20, 'V' => 16, 'W' => 20, 'X' => 16,
                ];

                foreach ($columnWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Freeze header rows
                $sheet->freezePane('A7');

                // Add logo if exists
                $logoPath = public_path('images/RGC_logo.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setDescription('RGC Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(50);
                    $drawing->setCoordinates('A1');
                    $drawing->setOffsetX(5);
                    $drawing->setOffsetY(5);
                    $drawing->setWorksheet($sheet);
                }
            },
        ];
    }

    public function title(): string
    {
        return 'Waumini Template';
    }
}
