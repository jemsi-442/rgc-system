<?php

namespace App\Http\Requests;

use App\Models\Branch;
use App\Models\District;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'branch_id' => ['required', 'integer', Rule::exists((new Branch())->getTable(), 'id')],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $district = District::query()->find($this->integer('district_id'));
            $branch = Branch::query()->find($this->integer('branch_id'));

            if (! $district || (int) $district->region_id !== $this->integer('region_id')) {
                $validator->errors()->add('district_id', __('Selected district does not belong to the selected region.'));
            }

            if (! $branch || (int) $branch->district_id !== $this->integer('district_id')) {
                $validator->errors()->add('branch_id', __('Selected branch does not belong to the selected district.'));
            }
        });
    }
}
