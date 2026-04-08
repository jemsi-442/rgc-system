<?php

namespace App\Http\Requests;

use App\Models\BranchMessage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreBranchMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'message' => ['nullable', 'string', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:branch_messages,id'],
            'attachment' => [
                'nullable',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
            ],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => [
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $message = trim((string) $this->input('message', ''));
            $hasLegacyAttachment = $this->hasFile('attachment');
            $hasAttachments = $this->hasFile('attachments');
            $uploadedFiles = collect();

            if ($hasLegacyAttachment) {
                $uploadedFiles->push($this->file('attachment'));
            }

            if ($hasAttachments) {
                $uploadedFiles = $uploadedFiles->merge(collect($this->file('attachments'))->filter());
            }

            if ($message === '' && ! $hasLegacyAttachment && ! $hasAttachments) {
                $validator->errors()->add('message', __('Write a message or attach a file.'));
            }

            if ($uploadedFiles->count() > 5) {
                $validator->errors()->add('attachments', __('You can upload up to 5 attachments per message.'));
            }

            if (! $this->filled('parent_id') || ! $this->user()) {
                return;
            }

            $parent = BranchMessage::query()
                ->select('id', 'church_id')
                ->find($this->integer('parent_id'));

            if (! $parent || $parent->church_id !== $this->user()->effectiveBranchId()) {
                $validator->errors()->add('parent_id', __('You can only reply to messages from your branch.'));
            }
        });
    }
}
