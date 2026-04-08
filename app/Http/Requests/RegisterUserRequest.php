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
            'phone' => ['required', 'string', 'regex:/^255\d{9}$/'],
            'password' => ['required', 'confirmed', 'min:8'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'branch_id' => ['required', 'integer', Rule::exists((new Branch())->getTable(), 'id')],
        ];
    }

    protected function prepareForValidation(): void
    {
        $phone = preg_replace('/\D+/', '', (string) $this->input('phone', '')) ?? '';

        if ($phone !== '') {
            if (str_starts_with($phone, '0') && strlen($phone) === 10) {
                $phone = '255' . substr($phone, 1);
            } elseif (strlen($phone) === 9 && in_array($phone[0], ['6', '7'], true)) {
                $phone = '255' . $phone;
            }
        }

        $this->merge([
            'phone' => $phone,
        ]);
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
                return;
            }

            if (($branch->status ?? 'active') !== 'active') {
                $validator->errors()->add('branch_id', __('Selected branch is inactive. Please choose an active branch.'));
            }
        });
    }
}
