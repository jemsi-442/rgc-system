<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRoleOrLegacy(['super_admin']) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:regions,name',
            'code' => 'nullable|string|max:30|unique:regions,code',
        ];
    }
}
