<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BranchImportTemplateExport implements FromArray, WithHeadings
{
    public function __construct(private readonly bool $withSamples = false)
    {
    }

    public function headings(): array
    {
        return [
            'region',
            'district',
            'branch_name',
            'branch_type',
            'address',
            'phone',
            'email',
            'status',
        ];
    }

    public function array(): array
    {
        if (! $this->withSamples) {
            return [];
        }

        return [
            ['Dar es Salaam', 'Temeke', 'Example Local Branch', 'local', 'Toangoma, Temeke', '+255700000001', 'branch-one@rgc.or.tz', 'active'],
            ['Dar es Salaam', 'Ilala', 'Example District Branch', 'district', 'Ilala, Dar es Salaam', '+255700000002', 'branch-two@rgc.or.tz', 'inactive'],
            ['Mwanza', 'Ilemela', 'Example Regional Branch', 'regional', 'Ilemela, Mwanza', '+255700000003', 'branch-three@rgc.or.tz', 'active'],
        ];
    }
}
