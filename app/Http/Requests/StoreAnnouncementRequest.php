<?php

namespace App\Http\Requests;

use App\Models\Branch;
use App\Models\District;
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
            'delivery_scope' => ['nullable', 'string', 'in:global,selected_branches,region,district,branch'],
            'district_id' => ['nullable', 'integer', 'exists:districts,id'],
            'branch_id' => ['nullable', 'integer', 'exists:churches,id'],
            'selected_branch_ids' => ['nullable', 'array'],
            'selected_branch_ids.*' => ['integer', 'exists:churches,id'],
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

            $user = $this->user();

            if (! $user) {
                return;
            }

            $deliveryScope = (string) $this->input('delivery_scope', '');

            if ($user->hasSystemRole('super_admin')) {
                $scope = $deliveryScope !== '' ? $deliveryScope : 'global';

                if (! in_array($scope, ['global', 'selected_branches'], true)) {
                    $validator->errors()->add('delivery_scope', __('Super Admin can publish to everyone or only to selected branches.'));
                    return;
                }

                if ($scope === 'selected_branches') {
                    $selectedBranches = collect($this->input('selected_branch_ids', []))
                        ->filter(fn ($value) => filled($value))
                        ->map(fn ($value) => (int) $value)
                        ->unique()
                        ->values();

                    if ($selectedBranches->isEmpty()) {
                        $validator->errors()->add('selected_branch_ids', __('Select at least one branch for this announcement.'));
                        return;
                    }

                    $validBranchCount = Branch::query()->whereIn('id', $selectedBranches)->count();

                    if ($validBranchCount !== $selectedBranches->count()) {
                        $validator->errors()->add('selected_branch_ids', __('One or more selected branches are invalid.'));
                    }
                }

                return;
            }

            if ($user->hasSystemRole('regional_admin')) {
                $scope = $deliveryScope !== '' ? $deliveryScope : 'region';

                if (! in_array($scope, ['region', 'district', 'branch'], true)) {
                    $validator->errors()->add('delivery_scope', __('Regional admins can target the whole region, one district, or one branch inside their region.'));
                    return;
                }

                if (in_array($scope, ['district', 'branch'], true)) {
                    if (! $this->filled('district_id')) {
                        $validator->errors()->add('district_id', __('Select the district that should receive this announcement.'));
                        return;
                    }

                    $district = District::query()->find($this->integer('district_id'));

                    if (! $district || (int) $district->region_id !== (int) $user->region_id) {
                        $validator->errors()->add('district_id', __('Selected district is outside your region scope.'));
                        return;
                    }
                }

                if ($scope === 'branch') {
                    if (! $this->filled('branch_id')) {
                        $validator->errors()->add('branch_id', __('Select the branch that should receive this announcement.'));
                        return;
                    }

                    $branch = Branch::query()->find($this->integer('branch_id'));

                    if (! $branch || (int) $branch->region_id !== (int) $user->region_id) {
                        $validator->errors()->add('branch_id', __('Selected branch is outside your region scope.'));
                        return;
                    }

                    if ((int) $branch->district_id !== (int) $this->integer('district_id')) {
                        $validator->errors()->add('branch_id', __('Selected branch does not belong to the selected district.'));
                    }
                }

                return;
            }

            if ($user->hasSystemRole('district_admin')) {
                if ($deliveryScope !== '' && $deliveryScope !== 'district') {
                    $validator->errors()->add('delivery_scope', __('District admin announcements stay inside your district.'));
                }

                return;
            }

            if ($deliveryScope !== '' && $deliveryScope !== 'branch') {
                $validator->errors()->add('delivery_scope', __('Branch announcements stay inside your branch.'));
            }
        });
    }
}
