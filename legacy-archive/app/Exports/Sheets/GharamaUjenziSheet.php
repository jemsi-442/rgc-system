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

class GharamaUjenziSheet implements WithEvents, WithTitle
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
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', strtoupper($churchName));
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '16A34A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // ROW 2: Diocese and Parish
                $sheet->mergeCells('A2:F2');
                $sheet->setCellValue('A2', $diocese . ' - ' . $parish);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                // ROW 3: Report Title
                $sheet->mergeCells('A3:F3');
                $titleText = 'GHARAMA ZA UJENZI';
                if ($this->startYear == $this->endYear) {
                    $titleText .= ' - MWAKA ' . $this->year;
                } else {
                    $titleText .= ' - ' . $monthNames[$this->startMonth] . ' ' . $this->startYear . ' HADI ' . $monthNames[$this->endMonth] . ' ' . $this->endYear;
                }
                $sheet->setCellValue('A3', $titleText);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B45309']], // Orange/Brown for construction
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(35);

                // ROW 4: Date generated
                $sheet->mergeCells('A4:F4');
                $sheet->setCellValue('A4', 'Imetengenezwa: ' . date('d/m/Y H:i'));
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(20);

                // ROW 5: Spacing
                $sheet->getRowDimension(5)->setRowHeight(10);

                // ROW 6: Headers
                $headers = [
                    'A' => 'Na.',
                    'B' => 'KIPINDI (TAREHE)',
                    'C' => 'AINA YA GHARAMA',
                    'D' => 'MAELEZO',
                    'E' => 'KIASI (TSH)',
                    'F' => 'JUMLA YA MWEZI',
                ];
                foreach ($headers as $col => $value) {
                    $sheet->setCellValue($col . '6', $value);
                }
                $sheet->getStyle('A6:F6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B45309']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $sheet->getRowDimension(6)->setRowHeight(30);

                // Get construction categories
                $categories = ExpenseCategory::where('is_active', true)
                    ->where(function($q) {
                        $q->where('name', 'like', '%ujenzi%')
                          ->orWhere('name', 'like', '%jengo%')
                          ->orWhere('name', 'like', '%construction%');
                    })
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get();

                $currentRow = 7;
                $grandTotal = 0;
                $rowNumber = 1;

                // Group expenses by month
                for ($month = $this->startMonth; $month <= $this->endMonth; $month++) {
                    $monthlyTotal = 0;
                    $monthStartRow = $currentRow;
                    $hasExpenses = false;

                    // Get expenses for this month
                    foreach ($categories as $category) {
                        $expenses = Expense::where('expense_category_id', $category->id)
                            ->where('year', $this->year)
                            ->where('month', $month)
                            ->get();

                        foreach ($expenses as $expense) {
                            $hasExpenses = true;

                            // Determine date range for the expense
                            $startDate = $this->year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
                            $endDate = date('Y-m-t', strtotime($startDate));
                            $dateRange = date('d/m', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate));

                            $sheet->setCellValue('A' . $currentRow, $rowNumber);
                            $sheet->setCellValue('B' . $currentRow, $dateRange);
                            $sheet->setCellValue('C' . $currentRow, $category->name);
                            $sheet->setCellValue('D' . $currentRow, $expense->notes ?? '-');
                            $sheet->setCellValue('E' . $currentRow, floatval($expense->amount));

                            $monthlyTotal += floatval($expense->amount);
                            $rowNumber++;
                            $currentRow++;
                        }
                    }

                    // If no expenses for this month, add a placeholder row
                    if (!$hasExpenses) {
                        $startDate = $this->year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
                        $endDate = date('Y-m-t', strtotime($startDate));
                        $dateRange = date('d/m', strtotime($startDate)) . ' - ' . date('d/m/Y', strtotime($endDate));

                        $sheet->setCellValue('A' . $currentRow, $rowNumber);
                        $sheet->setCellValue('B' . $currentRow, $dateRange);
                        $sheet->setCellValue('C' . $currentRow, '-');
                        $sheet->setCellValue('D' . $currentRow, 'Hakuna matumizi');
                        $sheet->setCellValue('E' . $currentRow, 0);
                        $rowNumber++;
                        $currentRow++;
                    }

                    // Monthly subtotal row
                    $sheet->setCellValue('A' . $currentRow, '');
                    $sheet->mergeCells('B' . $currentRow . ':D' . $currentRow);
                    $sheet->setCellValue('B' . $currentRow, 'JUMLA - ' . strtoupper($monthNames[$month]) . ' ' . $this->year);
                    $sheet->setCellValue('E' . $currentRow, $monthlyTotal);
                    $sheet->setCellValue('F' . $currentRow, $monthlyTotal);

                    $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FED7AA']], // Light orange
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    ]);

                    $grandTotal += $monthlyTotal;
                    $currentRow++;

                    // Add spacing between months
                    $sheet->getRowDimension($currentRow)->setRowHeight(5);
                    $currentRow++;
                }

                // Grand total row
                $sheet->setCellValue('A' . $currentRow, '');
                $sheet->mergeCells('B' . $currentRow . ':E' . $currentRow);
                $sheet->setCellValue('B' . $currentRow, 'JUMLA KUU YA GHARAMA ZA UJENZI');
                $sheet->setCellValue('F' . $currentRow, $grandTotal);

                $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'B45309']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($currentRow)->setRowHeight(30);

                // Apply formatting
                $sheet->getStyle('A7:F' . ($currentRow - 1))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                ]);
                $sheet->getStyle('E7:F' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('E7:F' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('A7:A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(40);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(18);

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
        return 'GHARAMA ZA UJENZI';
    }
}
