<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRoleOrLegacy(['super_admin']) ?? false;
    }

    public function rules(): array
    {
        $regionId = $this->route('region')?->id ?? $this->route('region');

        return [
            'name' => 'required|string|max:255|unique:regions,name,' . $regionId,
            'code' => 'nullable|string|max:30|unique:regions,code,' . $regionId,
        ];
    }
}
