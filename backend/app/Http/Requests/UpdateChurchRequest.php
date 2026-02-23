<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChurchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRoleOrLegacy(['super_admin', 'regional_admin', 'district_admin', 'admin']) ?? false;
    }

    public function rules(): array
    {
        return [
            'region_id' => 'required|exists:regions,id',
            'district_id' => [
                'required',
                Rule::exists('districts', 'id')->where(fn ($q) => $q->where('region_id', $this->input('region_id'))),
            ],
            'name' => 'required|string|max:255',
            'type' => 'required|in:headquarters,regional,district,local',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'pastor_id' => 'nullable|exists:pastors,id',
            'assigned_branch_admin_id' => [
                'nullable',
                'exists:users,id',
                Rule::prohibitedIf(!$this->user()?->hasAnyRoleOrLegacy(['super_admin'])),
            ],
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
