<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDistrictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRoleOrLegacy(['super_admin']) ?? false;
    }

    public function rules(): array
    {
        return [
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:30',
        ];
    }
}
