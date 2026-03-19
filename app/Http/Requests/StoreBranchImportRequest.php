<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasSystemRole('super_admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'branch_file' => ['required', 'file', 'mimes:csv,txt,xls,xlsx', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'branch_file.required' => __('Upload a CSV or Excel file before starting the branch import.'),
            'branch_file.mimes' => __('Use a valid CSV, XLS, or XLSX file for branch import.'),
            'branch_file.max' => __('The branch import file must not be larger than 10 MB.'),
        ];
    }
}
