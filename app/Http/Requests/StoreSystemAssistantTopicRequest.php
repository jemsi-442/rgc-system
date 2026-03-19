<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSystemAssistantTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnySystemRole(['super_admin', 'regional_admin']) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:120', 'regex:/^[a-z0-9\-]+$/', Rule::unique('system_assistant_topics', 'slug')->where(fn ($query) => $query->where('locale', $this->input('locale')))],
            'locale' => ['required', 'string', Rule::in(config('app.supported_locales', ['en', 'sw']))],
            'region_id' => ['nullable', 'integer', 'exists:regions,id'],
            'answer' => ['required', 'string', 'max:6000'],
            'keywords_text' => ['required', 'string', 'max:4000'],
            'suggestions_text' => ['nullable', 'string', 'max:4000'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', Rule::in(['super_admin', 'regional_admin', 'district_admin', 'branch_admin', 'bishop', 'pastor', 'accountant', 'member'])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
