<?php

namespace App\Exports;

use App\Models\Member;
use App\Models\Income;
use App\Models\IncomeCategory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\DB;

class KiwanjaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected $pledgeCategoryId;
    protected $status;

    public function __construct($status = 'all')
    {
        // Get the "SADAKA YA AHADI" category ID
        $this->pledgeCategoryId = IncomeCategory::where('code', 'M0003')
            ->orWhere('name', 'LIKE', '%AHADI%')
            ->first()?->id;

        $this->status = $status;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get all active members with their pledge information
        $members = Member::active()
            ->with(['incomes' => function($query) {
                if ($this->pledgeCategoryId) {
                    $query->where('income_category_id', $this->pledgeCategoryId);
                }
            }])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        // Filter by status if specified
        if ($this->status === 'paid') {
            $members = $members->filter(function($member) {
                $totalPledges = $member->incomes->sum('amount');
                $totalPaid = $totalPledges * 0.7; // TODO: Replace with actual payment tracking
                $balance = $totalPledges - $totalPaid;
                return $balance <= 0;
            });
        } elseif ($this->status === 'pending') {
            $members = $members->filter(function($member) {
                $totalPledges = $member->incomes->sum('amount');
                $totalPaid = $totalPledges * 0.7; // TODO: Replace with actual payment tracking
                $balance = $totalPledges - $totalPaid;
                return $balance > 0;
            });
        }

        return $members;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'NAMBA YA ENVELOPE',
            'NAMBA YA AHADI',
            'JINA KAMILI',
            'SIMU',
            'JUMLA YA AHADI (TSh)',
            'IMETOZWA (TSh)',
            'SALIO (TSh)',
            'HALI',
        ];
    }

    /**
     * @param Member $member
     */
    public function map($member): array
    {
        // Calculate total pledges for this member
        $totalPledges = $member->incomes->sum('amount');

        // For demo purposes, assume 70% is paid (in real scenario, track payments separately)
        $totalPaid = $totalPledges * 0.7;
        $balance = $totalPledges - $totalPaid;

        // Determine status
        $status = 'Bado';
        if ($balance <= 0) {
            $status = 'Kamili';
        } elseif ($totalPaid > 0) {
            $status = 'Inalipa';
        }

        return [
            $member->envelope_number ?? '-',
            $member->pledge_number ?? '-',
            $member->full_name,
            $member->phone ?? '-',
            $totalPledges,
            $totalPaid,
            $balance,
            $status,
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Header row styling - same purple as other exports (RGB 360958)
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '360958'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Data rows borders
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('A2:H' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ]);

        // Format amount columns (E, F, G) with thousand separator
        $sheet->getStyle('E2:G' . $lastRow)
            ->getNumberFormat()
            ->setFormatCode('#,##0');

        // Align amount columns to right
        $sheet->getStyle('E2:G' . $lastRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Center align status column
        $sheet->getStyle('H2:H' . $lastRow)
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Conditional formatting for status
        for ($row = 2; $row <= $lastRow; $row++) {
            $statusCell = 'H' . $row;
            $status = $sheet->getCell($statusCell)->getValue();

            if ($status === 'Kamili') {
                $sheet->getStyle($statusCell)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '008000']],
                ]);
            } elseif ($status === 'Inalipa') {
                $sheet->getStyle($statusCell)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FF8C00']],
                ]);
            } else {
                $sheet->getStyle($statusCell)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'DC143C']],
                ]);
            }
        }

        return [];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Ahadi za Kiwanja';
    }
}
