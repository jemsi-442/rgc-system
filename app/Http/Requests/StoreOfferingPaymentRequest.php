<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOfferingPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'offering_date' => ['nullable', 'date'],
            'amount' => ['required', 'numeric', 'min:100'],
            'payment_type' => ['nullable', 'in:offering,sadaka,thanksgiving,special_contribution,project_support'],
            'mobile_network' => ['nullable', Rule::in(['mpesa', 'airtel_money', 'mixx_by_yas', 'halopesa'])],
            'description' => ['nullable', 'string', 'max:255'],
            'payer_name' => ['required', 'string', 'max:255'],
            'payer_phone' => ['required', 'string', 'regex:/^255\d{9}$/'],
            'payer_email' => ['nullable', 'email', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $phone = preg_replace('/\D+/', '', (string) $this->input('payer_phone', '')) ?? '';

        if ($phone !== '') {
            if (str_starts_with($phone, '0') && strlen($phone) === 10) {
                $phone = '255' . substr($phone, 1);
            } elseif (strlen($phone) === 9 && in_array($phone[0], ['6', '7'], true)) {
                $phone = '255' . $phone;
            }
        }

        $this->merge([
            'payer_phone' => $phone,
            'mobile_network' => $this->input('mobile_network') ?: null,
        ]);
    }
}
