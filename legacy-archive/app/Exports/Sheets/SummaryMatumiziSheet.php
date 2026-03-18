<?php

namespace App\Exports\Sheets;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class SummaryMatumiziSheet implements WithEvents, WithTitle
{
    protected $year;
    protected $startMonth;
    protected $endMonth;
    protected $startYear;
    protected $endYear;

    public function __construct($year, $startMonth, $endMonth, $startYear, $endYear)
    {
        $this->year = $year;
        $this->startMonth = $startMonth;
        $this->endMonth = $endMonth;
        $this->startYear = $startYear;
        $this->endYear = $endYear;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $churchName = Setting::get('church_name', 'KANISA LA KIINJILI LA KILUTHERI TANZANIA');
                $diocese = Setting::get('diocese', 'DAYOSISI YA MASHARIKI NA PWANI');
                $parish = Setting::get('parish', 'USHARIKA WA MAKABE');

                $monthNames = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Machi', 4 => 'Aprili',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Julai', 8 => 'Agosti',
                    9 => 'Septemba', 10 => 'Oktoba', 11 => 'Novemba', 12 => 'Desemba'
                ];

                // ROW 1: Church Name
                $sheet->mergeCells('A1:E1');
                $sheet->setCellValue('A1', strtoupper($churchName));
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '16A34A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // ROW 2: Diocese and Parish
                $sheet->mergeCells('A2:E2');
                $sheet->setCellValue('A2', $diocese . ' - ' . $parish);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                // ROW 3: Report Title
                $sheet->mergeCells('A3:E3');
                $titleText = 'MUHTASARI WA MATUMIZI YOTE';
                if ($this->startYear == $this->endYear) {
                    $titleText .= ' - MWAKA ' . $this->year;
                } else {
                    $titleText .= ' - ' . $monthNames[$this->startMonth] . ' ' . $this->startYear . ' HADI ' . $monthNames[$this->endMonth] . ' ' . $this->endYear;
                }
                $sheet->setCellValue('A3', $titleText);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']], // Green
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(35);

                // ROW 4: Date generated
                $sheet->mergeCells('A4:E4');
                $sheet->setCellValue('A4', 'Imetengenezwa: ' . date('d/m/Y H:i'));
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(20);

                // ROW 5: Spacing
                $sheet->getRowDimension(5)->setRowHeight(15);

                // ========================================
                // SECTION 1: SUMMARY BY MONTH
                // ========================================
                $currentRow = 6;
                $sheet->mergeCells('A' . $currentRow . ':E' . $currentRow);
                $sheet->setCellValue('A' . $currentRow, 'MUHTASARI WA MATUMIZI KWA MWEZI');
                $sheet->getStyle('A' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1F2937']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $currentRow++;

                // Headers for monthly summary
                $sheet->setCellValue('A' . $currentRow, 'Na.');
                $sheet->setCellValue('B' . $currentRow, 'MWEZI');
                $sheet->setCellValue('C' . $currentRow, 'MATUMIZI KAWAIDA');
                $sheet->setCellValue('D' . $currentRow, 'GHARAMA UJENZI');
                $sheet->setCellValue('E' . $currentRow, 'JUMLA');

                $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $currentRow++;

                // Get regular categories
                $regularCategoryIds = ExpenseCategory::where('is_active', true)
                    ->where('name', 'not like', '%ujenzi%')
                    ->where('name', 'not like', '%jengo%')
                    ->where('name', 'not like', '%construction%')
                    ->pluck('id');

                // Get construction categories
                $constructionCategoryIds = ExpenseCategory::where('is_active', true)
                    ->where(function($q) {
                        $q->where('name', 'like', '%ujenzi%')
                          ->orWhere('name', 'like', '%jengo%')
                          ->orWhere('name', 'like', '%construction%');
                    })
                    ->pluck('id');

                $totalRegular = 0;
                $totalConstruction = 0;
                $rowNum = 1;

                for ($month = 1; $month <= 12; $month++) {
                    $regularAmount = Expense::whereIn('expense_category_id', $regularCategoryIds)
                        ->where('year', $this->year)
                        ->where('month', $month)
                        ->sum('amount');

                    $constructionAmount = Expense::whereIn('expense_category_id', $constructionCategoryIds)
                        ->where('year', $this->year)
                        ->where('month', $month)
                        ->sum('amount');

                    $monthTotal = $regularAmount + $constructionAmount;

                    $sheet->setCellValue('A' . $currentRow, $rowNum);
                    $sheet->setCellValue('B' . $currentRow, $monthNames[$month]);
                    $sheet->setCellValue('C' . $currentRow, floatval($regularAmount));
                    $sheet->setCellValue('D' . $currentRow, floatval($constructionAmount));
                    $sheet->setCellValue('E' . $currentRow, floatval($monthTotal));

                    if ($rowNum % 2 == 0) {
                        $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
                        ]);
                    }

                    $totalRegular += $regularAmount;
                    $totalConstruction += $constructionAmount;
                    $rowNum++;
                    $currentRow++;
                }

                // Yearly total row
                $sheet->setCellValue('A' . $currentRow, '');
                $sheet->setCellValue('B' . $currentRow, 'JUMLA YA MWAKA');
                $sheet->setCellValue('C' . $currentRow, floatval($totalRegular));
                $sheet->setCellValue('D' . $currentRow, floatval($totalConstruction));
                $sheet->setCellValue('E' . $currentRow, floatval($totalRegular + $totalConstruction));

                $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFC120']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]],
                ]);
                $currentRow += 2;

                // ========================================
                // SECTION 2: SUMMARY BY CATEGORY
                // ========================================
                $sheet->mergeCells('A' . $currentRow . ':E' . $currentRow);
                $sheet->setCellValue('A' . $currentRow, 'MUHTASARI WA MATUMIZI KWA AINA');
                $sheet->getStyle('A' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1F2937']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $currentRow++;

                // Headers for category summary
                $sheet->setCellValue('A' . $currentRow, 'Na.');
                $sheet->setCellValue('B' . $currentRow, 'AINA YA MATUMIZI');
                $sheet->setCellValue('C' . $currentRow, 'JUMLA (TSH)');
                $sheet->setCellValue('D' . $currentRow, 'ASILIMIA (%)');
                $sheet->setCellValue('E' . $currentRow, 'AINA');

                $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $currentRow++;

                // Get all categories with their totals
                $allCategories = ExpenseCategory::where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get();

                $grandTotal = $totalRegular + $totalConstruction;
                $catNum = 1;

                foreach ($allCategories as $category) {
                    $categoryTotal = Expense::where('expense_category_id', $category->id)
                        ->where('year', $this->year)
                        ->sum('amount');

                    if ($categoryTotal > 0) {
                        $percentage = $grandTotal > 0 ? ($categoryTotal / $grandTotal) * 100 : 0;
                        $type = (stripos($category->name, 'ujenzi') !== false ||
                                 stripos($category->name, 'jengo') !== false ||
                                 stripos($category->name, 'construction') !== false) ? 'Ujenzi' : 'Kawaida';

                        $sheet->setCellValue('A' . $currentRow, $catNum);
                        $sheet->setCellValue('B' . $currentRow, $category->name);
                        $sheet->setCellValue('C' . $currentRow, floatval($categoryTotal));
                        $sheet->setCellValue('D' . $currentRow, number_format($percentage, 1) . '%');
                        $sheet->setCellValue('E' . $currentRow, $type);

                        if ($catNum % 2 == 0) {
                            $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9FAFB']],
                            ]);
                        }

                        $catNum++;
                        $currentRow++;
                    }
                }

                // Grand total row
                $sheet->setCellValue('A' . $currentRow, '');
                $sheet->setCellValue('B' . $currentRow, 'JUMLA KUU');
                $sheet->setCellValue('C' . $currentRow, floatval($grandTotal));
                $sheet->setCellValue('D' . $currentRow, '100%');
                $sheet->setCellValue('E' . $currentRow, '');

                $sheet->getStyle('A' . $currentRow . ':E' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]],
                ]);
                $currentRow += 2;

                // ========================================
                // SECTION 3: KEY STATISTICS
                // ========================================
                $sheet->mergeCells('A' . $currentRow . ':E' . $currentRow);
                $sheet->setCellValue('A' . $currentRow, 'TAKWIMU MUHIMU');
                $sheet->getStyle('A' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1F2937']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $currentRow++;

                $avgMonthly = $grandTotal / 12;
                $stats = [
                    ['Jumla ya Matumizi Yote', number_format($grandTotal, 0) . ' TSH'],
                    ['Matumizi ya Kawaida', number_format($totalRegular, 0) . ' TSH'],
                    ['Gharama za Ujenzi', number_format($totalConstruction, 0) . ' TSH'],
                    ['Wastani wa Kila Mwezi', number_format($avgMonthly, 0) . ' TSH'],
                    ['Asilimia ya Ujenzi', number_format(($grandTotal > 0 ? ($totalConstruction / $grandTotal) * 100 : 0), 1) . '%'],
                ];

                foreach ($stats as $stat) {
                    $sheet->setCellValue('B' . $currentRow, $stat[0]);
                    $sheet->mergeCells('C' . $currentRow . ':E' . $currentRow);
                    $sheet->setCellValue('C' . $currentRow, $stat[1]);
                    $sheet->getStyle('B' . $currentRow)->applyFromArray([
                        'font' => ['bold' => true],
                    ]);
                    $sheet->getStyle('C' . $currentRow)->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                        'font' => ['color' => ['rgb' => '059669']],
                    ]);
                    $currentRow++;
                }

                // Apply number formatting
                $sheet->getStyle('C8:E' . ($currentRow - 1))->getNumberFormat()->setFormatCode('#,##0');

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(35);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(15);

                $sheet->freezePane('A7');

                // Logo
                $logoPath = public_path('images/RGC_logo.png');
                if (file_exists($logoPath)) {
                    $drawing = new Drawing();
                    $drawing->setName('Logo');
                    $drawing->setPath($logoPath);
                    $drawing->setHeight(50);
                    $drawing->setCoordinates('A1');
                    $drawing->setWorksheet($sheet);
                }
            },
        ];
    }

    public function title(): string
    {
        return 'SUMMARY YA MATUMIZI';
    }
}
