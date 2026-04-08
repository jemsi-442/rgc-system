@php
    $isEdit = isset($announcement);
    $user = auth()->user();
    $availableDistricts = $availableDistricts ?? collect();
    $availableBranches = $availableBranches ?? collect();

    $defaultDeliveryScope = 'branch';

    if ($user->hasSystemRole('super_admin')) {
        $defaultDeliveryScope = 'global';
    } elseif ($user->hasSystemRole('regional_admin')) {
        $defaultDeliveryScope = 'region';
    } elseif ($user->hasSystemRole('district_admin')) {
        $defaultDeliveryScope = 'district';
    }

    if ($isEdit) {
        if ($announcement->is_global) {
            $defaultDeliveryScope = 'global';
        } elseif ($announcement->hasExplicitBranchTargets() && ! $announcement->church_id) {
            $defaultDeliveryScope = 'selected_branches';
        } elseif ($announcement->church_id) {
            $defaultDeliveryScope = 'branch';
        } elseif ($announcement->district_id) {
            $defaultDeliveryScope = 'district';
        } elseif ($announcement->region_id) {
            $defaultDeliveryScope = 'region';
        }
    }

    $selectedDeliveryScope = old('delivery_scope', $defaultDeliveryScope);
    $selectedDistrictId = old('district_id', $announcement->district_id ?? '');
    $selectedBranchId = old('branch_id', $announcement->church_id ?? '');
    $selectedBranchIds = collect(old('selected_branch_ids', $isEdit && isset($announcement) ? $announcement->targetBranches->pluck('id')->all() : []))
        ->filter(fn ($value) => filled($value))
        ->map(fn ($value) => (string) $value)
        ->values()
        ->all();

    $showDistrictTarget = $user->hasSystemRole('regional_admin') && in_array($selectedDeliveryScope, ['district', 'branch'], true);
    $showBranchTarget = $user->hasSystemRole('regional_admin') && $selectedDeliveryScope === 'branch';
    $showSelectedBranchesTarget = $user->hasSystemRole('super_admin') && $selectedDeliveryScope === 'selected_branches';

    $scopePill = __('Announcement audience');
    $scopeLabel = __('Choose who should receive this announcement.');
    $previewText = __('This announcement will follow the audience available to your account.');

    if ($user->hasSystemRole('super_admin')) {
        if ($selectedDeliveryScope === 'selected_branches') {
            $previewText = count($selectedBranchIds) === 1
                ? __('This announcement will go to the selected branch only.')
                : __('This announcement will go to :count selected branches.', ['count' => max(count($selectedBranchIds), 0)]);
        } else {
            $previewText = __('This announcement will go to all users and all branches.');
        }

        $scopePill = __('Whole church / selected branches');
        $scopeLabel = __('Choose whether this update should reach the whole church or only selected branches.');
    } elseif ($user->hasSystemRole('regional_admin')) {
        $scopePill = __('Region / district / branch');
        $scopeLabel = __('Choose whether this announcement should reach your whole region, one district, or one branch inside it.');

        if ($selectedDeliveryScope === 'branch') {
            $previewText = __('This announcement will go to the selected branch only.');
        } elseif ($selectedDeliveryScope === 'district') {
            $previewText = __('This announcement will go to the selected district only.');
        } else {
            $previewText = __('This announcement will go to your whole region.');
        }
    } elseif ($user->hasSystemRole('district_admin')) {
        $scopePill = __('District audience');
        $scopeLabel = __('This announcement will be visible across your district.');
        $previewText = __('This announcement will go to your whole district.');
    } else {
        $scopePill = __('Branch audience');
        $scopeLabel = __('This announcement will stay inside your branch scope.');
        $previewText = __('This announcement will stay inside your branch only.');
    }
@endphp

<div class="form-stack mt-6">
    <div class="form-section">
        <div class="form-section-heading">
            <h2>{{ __('Announcement Content') }}</h2>
            <p>{{ __('Write a clear update for the people who should receive it.') }}</p>
        </div>

        <div class="form-grid-responsive mt-5">
            <div>
                <label class="field-label" for="title">{{ __('Title') }}</label>
                <input class="input-rgc" id="title" name="title" value="{{ old('title', $announcement->title ?? '') }}" placeholder="{{ __('Title') }}" required>
                @error('title')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="announcement-scope-note">
                <span class="announcement-scope-pill">{{ $scopePill }}</span>
                <p>{{ $scopeLabel }}</p>

                @if($user->hasSystemRole('super_admin'))
                    <div class="announcement-expiry-field">
                        <label class="field-label" for="delivery_scope">{{ __('Audience') }}</label>
                        <select class="input-rgc" id="delivery_scope" name="delivery_scope" data-announcement-scope-select>
                            <option value="global" @selected($selectedDeliveryScope === 'global')>{{ __('Whole church') }}</option>
                            <option value="selected_branches" @selected($selectedDeliveryScope === 'selected_branches')>{{ __('Selected branches only') }}</option>
                        </select>
                        <p class="field-hint">{{ __('Super Admin can reach everyone in the platform or only the branches selected below.') }}</p>
                        @error('delivery_scope')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="announcement-expiry-field {{ $showSelectedBranchesTarget ? '' : 'hidden' }}" data-announcement-selected-branches-shell>
                        <label class="field-label" for="selected_branch_ids">{{ __('Choose branches') }}</label>
                        <select
                            class="input-rgc announcement-branch-multiselect"
                            id="selected_branch_ids"
                            name="selected_branch_ids[]"
                            multiple
                            size="8"
                            data-announcement-selected-branches-select
                        >
                            @foreach($availableBranches as $branchOption)
                                <option value="{{ $branchOption->id }}" @selected(in_array((string) $branchOption->id, $selectedBranchIds, true))>
                                    {{ $branchOption->name }} - {{ $branchOption->district?->name }}, {{ $branchOption->region?->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="field-hint">{{ __('Hold Ctrl or Cmd to choose several branches. Only those branches will receive this announcement.') }}</p>
                        @error('selected_branch_ids')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                        @error('selected_branch_ids.*')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                @elseif($user->hasSystemRole('regional_admin'))
                    <div class="announcement-expiry-field">
                        <label class="field-label" for="delivery_scope">{{ __('Audience') }}</label>
                        <select class="input-rgc" id="delivery_scope" name="delivery_scope" data-announcement-scope-select>
                            <option value="region" @selected($selectedDeliveryScope === 'region')>{{ __('Whole region') }}</option>
                            <option value="district" @selected($selectedDeliveryScope === 'district')>{{ __('One district') }}</option>
                            <option value="branch" @selected($selectedDeliveryScope === 'branch')>{{ __('One branch') }}</option>
                        </select>
                        <p class="field-hint">{{ __('Regional admins can target the whole region, one district, or one branch inside their region.') }}</p>
                        @error('delivery_scope')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="announcement-expiry-field {{ $showDistrictTarget ? '' : 'hidden' }}" data-announcement-district-shell>
                        <label class="field-label" for="district_id">{{ __('Choose district') }}</label>
                        <select class="input-rgc" id="district_id" name="district_id" data-announcement-district-select @disabled(! $showDistrictTarget)>
                            <option value="">{{ __('Select district for scoped delivery') }}</option>
                            @foreach($availableDistricts as $district)
                                <option value="{{ $district->id }}" @selected((string) $selectedDistrictId === (string) $district->id)>{{ $district->name }}</option>
                            @endforeach
                        </select>
                        <p class="field-hint">{{ __('Choose a district when the announcement should stay within one district or one branch in your region.') }}</p>
                        @error('district_id')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="announcement-expiry-field {{ $showBranchTarget ? '' : 'hidden' }}" data-announcement-branch-shell>
                        <label class="field-label" for="branch_id">{{ __('Choose branch') }}</label>
                        <select
                            class="input-rgc"
                            id="branch_id"
                            name="branch_id"
                            data-announcement-branch-select
                            data-selected-value="{{ $selectedBranchId }}"
                            data-empty-option-label="{{ __('Select branch for branch delivery') }}"
                            @disabled(! $showBranchTarget)
                        >
                            <option value="">{{ __('Select branch for branch delivery') }}</option>
                        </select>
                        <p class="field-hint">{{ __('Choose a branch only when the announcement should stay within one branch in the selected district.') }}</p>
                        @error('branch_id')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>
                @elseif($user->hasSystemRole('district_admin'))
                    <input type="hidden" name="delivery_scope" value="district">
                @else
                    <input type="hidden" name="delivery_scope" value="branch">
                @endif

                <div
                    class="announcement-delivery-preview"
                    data-announcement-delivery-preview
                    data-label-global="{{ __('This announcement will go to all users and all branches.') }}"
                    data-label-selected-count="{{ __('This announcement will go to :count selected branches.') }}"
                    data-label-selected-one="{{ __('This announcement will go to the selected branch only.') }}"
                    data-label-region="{{ __('This announcement will go to your whole region.') }}"
                    data-label-district="{{ __('This announcement will go to the selected district only.') }}"
                    data-label-district-fixed="{{ __('This announcement will go to your whole district.') }}"
                    data-label-branch="{{ __('This announcement will go to the selected branch only.') }}"
                    data-label-branch-fixed="{{ __('This announcement will stay inside your branch only.') }}"
                >
                    <strong>{{ __('Audience preview') }}</strong>
                    <p>{{ $previewText }}</p>
                </div>

                <label class="announcement-pin-toggle">
                    <input type="checkbox" name="is_pinned" value="1" @checked(old('is_pinned', $announcement->is_pinned ?? false))>
                    <span>
                        <strong>{{ __('Pin this announcement') }}</strong>
                        <small>{{ __('Pinned announcements stay at the top for everyone in scope.') }}</small>
                    </span>
                </label>

                <div class="announcement-expiry-field">
                    <label class="field-label" for="expires_at">{{ __('Expiry Date') }}</label>
                    <input
                        class="input-rgc"
                        id="expires_at"
                        name="expires_at"
                        type="date"
                        value="{{ old('expires_at', optional($announcement->expires_at ?? null)?->format('Y-m-d')) }}"
                        min="{{ now()->format('Y-m-d') }}"
                    >
                    <p class="field-hint">{{ __('Set an expiry date if this announcement should drop lower after a certain day.') }}</p>
                    @error('expires_at')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mt-5">
            <label class="field-label" for="body">{{ __('Body') }}</label>
            <textarea class="textarea-rgc min-h-48" id="body" name="body" rows="7" placeholder="{{ __('Body') }}">{{ old('body', $announcement->body ?? '') }}</textarea>
            @error('body')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="form-section">
        <div class="form-section-heading">
            <h2>{{ __('Announcement Image') }}</h2>
            <p>{{ __('Attach an image when the announcement needs a visual poster, flyer, or ministry update banner.') }}</p>
        </div>

        @if($isEdit && $announcement->hasImage())
            <div class="announcement-form-image mt-5">
                <img src="{{ route('announcements.image', $announcement) }}" alt="{{ $announcement->title }}">
                <label class="announcement-remove-toggle">
                    <input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))>
                    <span>{{ __('Remove current image') }}</span>
                </label>
            </div>
        @endif

        <div class="mt-5">
            <label class="field-label" for="image">{{ __('Image') }}</label>
            <input class="input-rgc" id="image" name="image" type="file" accept="image/*" data-announcement-image-input>
            <p class="field-hint">{{ __('Supported formats: JPG, PNG, WEBP, GIF. Max size 6 MB.') }}</p>
            @error('image')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="announcement-preview-shell mt-5 hidden" data-announcement-preview data-empty-label="{{ __('Selected image') }}">
            <div class="announcement-preview-header">
                <strong>{{ __('Image Preview') }}</strong>
                <span data-announcement-preview-name>{{ __('Selected image') }}</span>
            </div>
            <div class="announcement-preview-frame">
                <img src="" alt="{{ __('Announcement preview image') }}" data-announcement-preview-image>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <button class="btn-rgc w-full sm:w-auto" type="submit">{{ $submitLabel }}</button>
        <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('announcements.index') }}">{{ __('Cancel') }}</a>
    </div>
</div>
