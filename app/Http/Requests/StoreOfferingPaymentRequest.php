<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'description' => ['nullable', 'string', 'max:255'],
            'payer_name' => ['required', 'string', 'max:255'],
            'payer_phone' => ['nullable', 'string', 'max:30'],
            'payer_email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
