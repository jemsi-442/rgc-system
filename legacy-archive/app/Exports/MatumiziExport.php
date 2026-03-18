<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MatumiziExport implements WithMultipleSheets
{
    protected $year;
    protected $startMonth;
    protected $endMonth;
    protected $startYear;
    protected $endYear;

    public function __construct($year = null, $startMonth = null, $endMonth = null, $startYear = null, $endYear = null)
    {
        $this->year = $year ?? date('Y');
        $this->startMonth = $startMonth ?? 1;
        $this->endMonth = $endMonth ?? 12;
        $this->startYear = $startYear ?? $this->year;
        $this->endYear = $endYear ?? $this->year;
    }

    public function sheets(): array
    {
        return [
            new Sheets\MatumiziKawaidaSheet($this->year, $this->startMonth, $this->endMonth, $this->startYear, $this->endYear),
            new Sheets\GharamaUjenziSheet($this->year, $this->startMonth, $this->endMonth, $this->startYear, $this->endYear),
            new Sheets\SummaryMatumiziSheet($this->year, $this->startMonth, $this->endMonth, $this->startYear, $this->endYear),
        ];
    }
}
