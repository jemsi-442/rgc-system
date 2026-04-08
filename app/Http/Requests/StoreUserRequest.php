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
            'phone' => ['nullable', 'string', 'regex:/^255\d{9}$/'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'max:60'],
            'status' => ['nullable', 'in:active,inactive'],
            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'branch_id' => ['required', 'integer', 'exists:churches,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => $this->normalizeTanzaniaPhone($this->input('phone')),
        ]);
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
                return;
            }

            if (($branch->status ?? 'active') !== 'active') {
                $validator->errors()->add('branch_id', __('Selected branch is inactive. Choose an active branch before creating users there.'));
            }
        });
    }

    private function normalizeTanzaniaPhone(mixed $value): ?string
    {
        $phone = preg_replace('/\D+/', '', (string) $value) ?? '';

        if ($phone === '') {
            return null;
        }

        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '255' . substr($phone, 1);
        }

        if (strlen($phone) === 9 && in_array($phone[0], ['6', '7'], true)) {
            return '255' . $phone;
        }

        return $phone;
    }
}
