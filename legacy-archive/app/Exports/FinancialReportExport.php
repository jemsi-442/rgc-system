<?php

namespace App\Exports;

use App\Models\Setting;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class FinancialReportExport implements WithEvents, WithTitle
{
    private array $incomeData;
    private array $expenseData;
    private string $reportTitle;
    private string $periodText;

    public function __construct(array $incomeData, array $expenseData, string $title, string $periodText)
    {
        $this->incomeData = $incomeData;
        $this->expenseData = $expenseData;
        $this->reportTitle = $title;
        $this->periodText = $periodText;
    }

    public function title(): string
    {
        return 'Ripoti';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Get church settings
                $churchName = Setting::get('church_name', 'KANISA LA KIINJILI LA KILUTHERI TANZANIA');
                $diocese = Setting::get('diocese', 'DAYOSISI YA MASHARIKI NA PWANI');
                $parish = Setting::get('parish', 'USHARIKA WA MAKABE');

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
                $sheet->setCellValue('A3', strtoupper($this->reportTitle));
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '360958']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(35);

                // ROW 4: Period and Date
                $sheet->mergeCells('A4:F4');
                $sheet->setCellValue('A4', 'Kipindi: ' . $this->periodText . ' | Imetengenezwa: ' . date('d/m/Y H:i'));
                $sheet->getStyle('A4')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(20);

                // ROW 5: Spacing
                $sheet->getRowDimension(5)->setRowHeight(10);

                // Calculate totals
                $totalIncome = collect($this->incomeData)->sum('amount');
                $totalExpense = collect($this->expenseData)->sum('amount');
                $balance = $totalIncome - $totalExpense;

                // ROW 6: Summary Section Header
                $sheet->mergeCells('A6:F6');
                $sheet->setCellValue('A6', 'MUHTASARI WA FEDHA');
                $sheet->getStyle('A6')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '360958']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(6)->setRowHeight(28);

                // Summary rows
                $summaryData = [
                    ['Jumla ya Mapato', $totalIncome, '16A34A'],
                    ['Jumla ya Matumizi', $totalExpense, 'DC2626'],
                    ['Salio', $balance, $balance >= 0 ? '16A34A' : 'DC2626'],
                ];

                $currentRow = 7;
                foreach ($summaryData as $item) {
                    $sheet->mergeCells('A' . $currentRow . ':D' . $currentRow);
                    $sheet->setCellValue('A' . $currentRow, $item[0]);
                    $sheet->mergeCells('E' . $currentRow . ':F' . $currentRow);
                    $sheet->setCellValue('E' . $currentRow, $item[1]);
                    $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getStyle('E' . $currentRow)->applyFromArray([
                        'font' => ['color' => ['rgb' => $item[2]]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ]);
                    $sheet->getRowDimension($currentRow)->setRowHeight(25);
                    $currentRow++;
                }

                // Spacing
                $currentRow++;
                $sheet->getRowDimension($currentRow - 1)->setRowHeight(15);

                // INCOME SECTION
                if (!empty($this->incomeData)) {
                    // Income Header
                    $sheet->mergeCells('A' . $currentRow . ':F' . $currentRow);
                    $sheet->setCellValue('A' . $currentRow, 'MAPATO');
                    $sheet->getStyle('A' . $currentRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16A34A']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension($currentRow)->setRowHeight(28);
                    $currentRow++;

                    // Income table headers
                    $incomeHeaders = ['Na.', 'TAREHE', 'KATEGORIA', 'MAELEZO', 'MCHANGIAJI', 'KIASI (TSH)'];
                    $cols = ['A', 'B', 'C', 'D', 'E', 'F'];
                    foreach ($incomeHeaders as $idx => $header) {
                        $sheet->setCellValue($cols[$idx] . $currentRow, $header);
                    }
                    $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '22C55E']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    ]);
                    $sheet->getRowDimension($currentRow)->setRowHeight(25);
                    $currentRow++;

                    // Income data rows
                    $rowNum = 1;
                    foreach ($this->incomeData as $item) {
                        $sheet->setCellValue('A' . $currentRow, $rowNum);
                        $sheet->setCellValue('B' . $currentRow, Carbon::parse($item['date'])->format('d/m/Y'));
                        $sheet->setCellValue('C' . $currentRow, $item['category'] ?? '-');
                        $sheet->setCellValue('D' . $currentRow, $item['description'] ?? '-');
                        $sheet->setCellValue('E' . $currentRow, $item['contributor'] ?? '-');
                        $sheet->setCellValue('F' . $currentRow, floatval($item['amount'] ?? 0));

                        $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                        ]);
                        $rowNum++;
                        $currentRow++;
                    }

                    // Income total row
                    $sheet->mergeCells('A' . $currentRow . ':E' . $currentRow);
                    $sheet->setCellValue('A' . $currentRow, 'JUMLA YA MAPATO');
                    $sheet->setCellValue('F' . $currentRow, $totalIncome);
                    $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16A34A']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension($currentRow)->setRowHeight(28);
                    $currentRow++;

                    // Spacing
                    $currentRow++;
                    $sheet->getRowDimension($currentRow - 1)->setRowHeight(15);
                }

                // EXPENSE SECTION
                if (!empty($this->expenseData)) {
                    // Expense Header
                    $sheet->mergeCells('A' . $currentRow . ':F' . $currentRow);
                    $sheet->setCellValue('A' . $currentRow, 'MATUMIZI');
                    $sheet->getStyle('A' . $currentRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension($currentRow)->setRowHeight(28);
                    $currentRow++;

                    // Expense table headers
                    $expenseHeaders = ['Na.', 'TAREHE', 'KATEGORIA', 'MAELEZO', 'MLIPWAJI', 'KIASI (TSH)'];
                    $cols = ['A', 'B', 'C', 'D', 'E', 'F'];
                    foreach ($expenseHeaders as $idx => $header) {
                        $sheet->setCellValue($cols[$idx] . $currentRow, $header);
                    }
                    $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EF4444']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                    ]);
                    $sheet->getRowDimension($currentRow)->setRowHeight(25);
                    $currentRow++;

                    // Expense data rows
                    $rowNum = 1;
                    foreach ($this->expenseData as $item) {
                        $sheet->setCellValue('A' . $currentRow, $rowNum);
                        $sheet->setCellValue('B' . $currentRow, Carbon::parse($item['date'])->format('d/m/Y'));
                        $sheet->setCellValue('C' . $currentRow, $item['category'] ?? '-');
                        $sheet->setCellValue('D' . $currentRow, $item['description'] ?? '-');
                        $sheet->setCellValue('E' . $currentRow, $item['payee'] ?? '-');
                        $sheet->setCellValue('F' . $currentRow, floatval($item['amount'] ?? 0));

                        $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                        ]);
                        $rowNum++;
                        $currentRow++;
                    }

                    // Expense total row
                    $sheet->mergeCells('A' . $currentRow . ':E' . $currentRow);
                    $sheet->setCellValue('A' . $currentRow, 'JUMLA YA MATUMIZI');
                    $sheet->setCellValue('F' . $currentRow, $totalExpense);
                    $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getRowDimension($currentRow)->setRowHeight(28);
                    $currentRow++;
                }

                // Format number column
                $sheet->getStyle('F7:F' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('F7:F' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('A7:A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(30);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(18);

                // Freeze header
                $sheet->freezePane('A7');
            },
        ];
    }
}
