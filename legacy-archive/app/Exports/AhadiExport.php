<?php

namespace App\Exports;

use App\Models\Setting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class AhadiExport implements WithEvents, WithTitle
{
    protected $ahadiData;
    protected $year;
    protected $month;

    public function __construct($ahadiData, $year, $month = null)
    {
        $this->ahadiData = $ahadiData;
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
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', strtoupper($churchName));
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '16A34A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // ROW 2: Diocese and Parish
                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', $diocese . ' - ' . $parish);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '374151']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                // ROW 3: Report Title
                $sheet->mergeCells('A3:H3');
                $titleText = 'RIPOTI YA AHADI - MWAKA ' . $this->year;
                if ($this->month) {
                    $titleText .= ' - ' . strtoupper($monthNames[(int)$this->month]);
                }
                $sheet->setCellValue('A3', $titleText);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '360958']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(35);

                // ROW 4: Date generated
                $sheet->mergeCells('A4:H4');
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
                    'B' => 'TAREHE AHADI',
                    'C' => 'MWANACHAMA',
                    'D' => 'SIMU',
                    'E' => 'KIASI AHADI (TSH)',
                    'F' => 'KIMELIPWA (TSH)',
                    'G' => 'BAKI (TSH)',
                    'H' => 'HALI',
                ];
                foreach ($headers as $col => $value) {
                    $sheet->setCellValue($col . '6', $value);
                }
                $sheet->getStyle('A6:H6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '360958']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                ]);
                $sheet->getRowDimension(6)->setRowHeight(30);

                // Data rows
                $currentRow = 7;
                $totalPledged = 0;
                $totalPaid = 0;
                $rowNumber = 1;

                foreach ($this->ahadiData as $ahadi) {
                    $paidAmount = $ahadi->payments ? $ahadi->payments->sum('amount') : 0;
                    $balance = floatval($ahadi->amount) - $paidAmount;
                    $status = $balance <= 0 ? 'Imelipwa' : 'Bado';

                    $sheet->setCellValue('A' . $currentRow, $rowNumber);
                    $sheet->setCellValue('B' . $currentRow, Carbon::parse($ahadi->pledge_date)->format('d/m/Y'));
                    $sheet->setCellValue('C' . $currentRow, $ahadi->member ? $ahadi->member->first_name . ' ' . $ahadi->member->last_name : '-');
                    $sheet->setCellValue('D' . $currentRow, $ahadi->member ? $ahadi->member->phone : '-');
                    $sheet->setCellValue('E' . $currentRow, floatval($ahadi->amount));
                    $sheet->setCellValue('F' . $currentRow, $paidAmount);
                    $sheet->setCellValue('G' . $currentRow, max(0, $balance));
                    $sheet->setCellValue('H' . $currentRow, $status);

                    // Apply borders to data row
                    $sheet->getStyle('A' . $currentRow . ':H' . $currentRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                    ]);

                    // Color status cell
                    if ($status === 'Imelipwa') {
                        $sheet->getStyle('H' . $currentRow)->applyFromArray([
                            'font' => ['color' => ['rgb' => '16A34A'], 'bold' => true],
                        ]);
                    } else {
                        $sheet->getStyle('H' . $currentRow)->applyFromArray([
                            'font' => ['color' => ['rgb' => 'DC2626'], 'bold' => true],
                        ]);
                    }

                    $totalPledged += floatval($ahadi->amount);
                    $totalPaid += $paidAmount;
                    $rowNumber++;
                    $currentRow++;
                }

                // If no data, show placeholder
                if ($this->ahadiData->isEmpty()) {
                    $sheet->setCellValue('A7', '1');
                    $sheet->mergeCells('B7:G7');
                    $sheet->setCellValue('B7', 'Hakuna ahadi zilizopatikana kwa kipindi hiki');
                    $sheet->setCellValue('H7', '-');
                    $sheet->getStyle('A7:H7')->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]],
                    ]);
                    $currentRow = 8;
                }

                // Grand total row
                $sheet->setCellValue('A' . $currentRow, '');
                $sheet->mergeCells('B' . $currentRow . ':D' . $currentRow);
                $sheet->setCellValue('B' . $currentRow, 'JUMLA KUU');
                $sheet->setCellValue('E' . $currentRow, $totalPledged);
                $sheet->setCellValue('F' . $currentRow, $totalPaid);
                $sheet->setCellValue('G' . $currentRow, max(0, $totalPledged - $totalPaid));
                $sheet->setCellValue('H' . $currentRow, '');

                $sheet->getStyle('A' . $currentRow . ':H' . $currentRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '360958']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '000000']]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($currentRow)->setRowHeight(30);

                // Format number columns
                $sheet->getStyle('E7:G' . $currentRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('E7:G' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('A7:A' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H7:H' . $currentRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(12);

                // Freeze header row
                $sheet->freezePane('A7');
            },
        ];
    }

    public function title(): string
    {
        return 'AHADI';
    }
}
