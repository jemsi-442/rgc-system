<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BranchRowsImport implements ToArray, WithHeadingRow, SkipsEmptyRows
{
    public function array(array $array): void
    {
        // We use Excel::toArray() in the controller; no in-import side effects are required.
    }
}
