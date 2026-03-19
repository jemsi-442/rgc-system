<?php

namespace App\Http\Requests;

use App\Models\Branch;
use App\Models\District;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'max:60'],
            'status' => ['nullable', 'in:active,inactive'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'branch_id' => ['required', 'integer', 'exists:churches,id'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $district = District::query()->find($this->integer('district_id'));
            $branch = Branch::query()->find($this->integer('branch_id'));

            if (! $district || (int) $district->region_id !== $this->integer('region_id')) {
                $validator->errors()->add('district_id', __('District must belong to selected region.'));
            }

            if (! $branch || (int) $branch->district_id !== $this->integer('district_id')) {
                $validator->errors()->add('branch_id', __('Branch must belong to selected district.'));
            }
        });
    }
}
