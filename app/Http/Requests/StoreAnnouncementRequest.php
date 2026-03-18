<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'max:6144'],
            'remove_image' => ['nullable', 'boolean'],
            'is_pinned' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $body = trim((string) $this->input('body', ''));
            $hasImage = $this->hasFile('image');
            $removeImage = $this->boolean('remove_image');

            if ($body === '' && ! $hasImage && ! $removeImage && $this->route('announcement')?->image_path === null) {
                $validator->errors()->add('body', __('Add announcement details or attach an image.'));
            }
        });
    }
}
