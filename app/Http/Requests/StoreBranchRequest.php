<?php

namespace App\Http\Requests;

use App\Models\Branch;
use App\Models\District;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasSystemRole('super_admin') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'branch_type' => ['required', 'in:headquarters,regional,district,local'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status', 'active'),
            'name' => trim((string) $this->input('name')),
            'address' => $this->filled('address') ? trim((string) $this->input('address')) : null,
            'phone' => $this->filled('phone') ? trim((string) $this->input('phone')) : null,
            'email' => $this->filled('email') ? trim((string) $this->input('email')) : null,
        ]);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $district = District::query()->find($this->integer('district_id'));

            if (! $district || (int) $district->region_id !== $this->integer('region_id')) {
                $validator->errors()->add('district_id', __('District must belong to selected region.'));
            }

            $branch = $this->route('branch');
            $branchId = $branch?->id;
            $branchName = Str::lower(trim((string) $this->input('name')));

            if ($branchName !== '') {
                $duplicateBranch = Branch::query()
                    ->when($branchId, fn ($query) => $query->whereKeyNot($branchId))
                    ->where('district_id', $this->integer('district_id'))
                    ->get()
                    ->first(fn (Branch $branch) => Str::lower($branch->name) === $branchName);

                if ($duplicateBranch) {
                    $validator->errors()->add('name', __('A branch with that name already exists in the selected district.'));
                }
            }

            if ($this->input('branch_type') === 'headquarters') {
                $headquartersExists = Branch::query()
                    ->when($branchId, fn ($query) => $query->whereKeyNot($branchId))
                    ->where('type', 'headquarters')
                    ->exists();

                if ($headquartersExists) {
                    $validator->errors()->add('branch_type', __('The headquarters branch already exists. Use another branch type for this record.'));
                }
            }
        });
    }
}
