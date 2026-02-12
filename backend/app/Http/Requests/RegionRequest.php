<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegionRequest extends FormRequest
{
    public function authorize(): bool {
        return true; // later we add policies
    }

    public function rules(): array {
        return [
            'name' => 'required|string|max:255|unique:regions,name,' . $this->id,
            'code' => 'nullable|string|max:50',
        ];
    }
}
