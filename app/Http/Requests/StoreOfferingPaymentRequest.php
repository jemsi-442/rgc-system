<?php

namespace App\Http\Requests;

use App\Support\TanzaniaMobileNetwork;
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
        $phone = TanzaniaMobileNetwork::normalizePhone($this->input('payer_phone')) ?? '';
        $selectedNetwork = $this->filled('mobile_network')
            ? $this->input('mobile_network')
            : TanzaniaMobileNetwork::inferNetwork($phone);

        $this->merge([
            'payer_phone' => $phone,
            'mobile_network' => $selectedNetwork ?: null,
        ]);
    }
}
