<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSlideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRoleOrLegacy(['super_admin', 'admin']) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }
}
