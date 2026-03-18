<?php

namespace App\Exports;

use App\Models\Income;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class MapatoExport implements WithEvents, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $year;
    protected $month;

    public function __construct($startDate = null, $endDate = null, $year = null, $month = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->year = $year;
        $this->month = $month;
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
                $titleText = 'RIPOTI YA MAPATO';
                if ($this->year) {
                    $titleText .= ' - MWAKA ' . $this->year;
                }
                if ($this->month) {
                    $titleText .= ' - ' . strtoupper($monthNames[(int)$this->month]);
                }
                if ($this->startDate && $this->endDate) {
                    $titleText .= ' (' . Carbon::parse($this->startDate)->format('d/m/Y') . ' - ' . Carbon::parse($this->endDate)->format('d/m/Y') . ')';
                }
                $sheet->setCellValue('A3', $titleText);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '360958']],
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
                    'B' => 'TAREHE',
                    'C' => 'AINA YA MAPATO',
                    'D' => 'MWANACHAMA',
                    'E' => 'KIASI (TSH)',
                    'F' => 'MAELEZO',
                ];
                foreach ($headers as $col => $value) {
                    $sheet->setCellValue($col . '6', $value);
                }
                $sheet->getStyle('A6:F6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '360958']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $sheet->getRowDimension(6)->setRowHeight(30);

                // Get data
                $query = Income::with(['category', 'member'])->orderBy('collection_date', 'asc');

                if ($this->startDate) {
                    $query->where('collection_date', '>=', $this->startDate);
                }
                if ($this->endDate) {
                    $query->where('collection_date', '<=', $this->endDate);
                }
                if ($this->year) {
                    $query->whereYear('collection_date', $this->year);
                }
                if ($this->month) {
                    $query->whereMonth('collection_date', $this->month);
                }

                $incomes = $query->get();
                $currentRow = 7;
                $grandTotal = 0;
                $rowNumber = 1;

                foreach ($incomes as $income) {
                    $sheet->setCellValue('A' . $currentRow, $rowNumber);
                    $sheet->setCellValue('B' . $currentRow, Carbon::parse($income->collection_date)->format('d/m/Y'));
                    $sheet->setCellValue('C' . $currentRow, $income->category ? $income->category->name : '-');
                    $sheet->setCellValue('D' . $currentRow, $income->member ? $income->member->first_name . ' ' . $income->member->last_name : '-');
                    $sheet->setCellValue('E' . $currentRow, floatval($income->amount));
                    $sheet->setCellValue('F' . $currentRow, $income->description ?? '-');

                    // Apply borders to data row
                    $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                    ]);

                    $grandTotal += floatval($income->amount);
                    $rowNumber++;
                    $currentRow++;
                }

                // If no data, show placeholder
                if ($incomes->isEmpty()) {
                    $sheet->setCellValue('A7', '1');
                    $sheet->mergeCells('B7:E7');
                    $sheet->setCellValue('B7', 'Hakuna mapato yaliyopatikana kwa kipindi hiki');
                    $sheet->setCellValue('F7', '-');
                    $sheet->getStyle('A7:F7')->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                    ]);
                    $currentRow = 8;
                }

                // Grand total row
                $sheet->setCellValue('A' . $currentRow, '');
                $sheet->mergeCells('B' . $currentRow . ':D' . $currentRow);
                $sheet->setCellValue('B' . $currentRow, 'JUMLA KUU YA MAPATO');
                $sheet->setCellValue('E' . $currentRow, $grandTotal);
                $sheet->setCellValue('F' . $currentRow, '');

                $sheet->getStyle('A' . $currentRow . ':F' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '360958']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($currentRow)->setRowHeight(30);

                // Format amount column
                $sheet->getStyle('E7:E' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('E7:E' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('A7:A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(30);

                // Freeze header row
                $sheet->freezePane('A7');
            },
        ];
    }

    public function title(): string
    {
        return 'MAPATO';
    }
}
