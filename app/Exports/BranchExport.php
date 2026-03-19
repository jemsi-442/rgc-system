<?php

namespace App\Exports;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BranchExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(
        private readonly ?int $regionId = null,
        private readonly ?int $districtId = null,
    ) {
    }

    public function collection()
    {
        return Branch::query()
            ->with(['region', 'district'])
            ->when($this->regionId, fn (Builder $query) => $query->where('region_id', $this->regionId))
            ->when($this->districtId, fn (Builder $query) => $query->where('district_id', $this->districtId))
            ->orderBy('region_id')
            ->orderBy('district_id')
            ->orderBy('name')
            ->get();
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

    public function map($branch): array
    {
        return [
            $branch->region?->name,
            $branch->district?->name,
            $branch->name,
            $branch->branch_type,
            $branch->address,
            $branch->phone,
            $branch->email,
            $branch->status,
        ];
    }
}
