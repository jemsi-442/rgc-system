<div class="form-stack">
    <section class="form-section">
        <div class="form-section-heading">
            <h2>{{ __('Account details') }}</h2>
            <p>{{ __('Set the user identity, access status, and the role they will use across the RGC platform.') }}</p>
        </div>

        <div class="form-grid-responsive">
            <div class="md:col-span-2">
                <label class="field-label" for="name">{{ __('Full name') }}</label>
                <input class="input-rgc" id="name" name="name" value="{{ old('name', $managedUser?->name ?? '') }}" required>
            </div>

            <div class="md:col-span-2">
                <label class="field-label" for="email">{{ __('Email address') }}</label>
                <input class="input-rgc" id="email" type="email" name="email" value="{{ old('email', $managedUser?->email ?? '') }}" required>
            </div>

            <div>
                <label class="field-label" for="phone">{{ __('Phone') }}</label>
                <input class="input-rgc" id="phone" name="phone" value="{{ old('phone', $managedUser?->phone ?? '') }}">
            </div>

            <div>
                <label class="field-label" for="role">{{ __('System role') }}</label>
                <select class="select-rgc" id="role" name="role" required>
                    @foreach($roleOptions as $roleOption)
                        <option value="{{ $roleOption }}" @selected(old('role', $managedUser?->normalizedRoleName() ?? 'member') === $roleOption)>{{ __(Illuminate\Support\Str::headline($roleOption)) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="field-label" for="status">{{ __('Account status') }}</label>
                <select class="select-rgc" id="status" name="status">
                    @foreach($statusOptions as $statusOption)
                        <option value="{{ $statusOption }}" @selected(old('status', $managedUser?->status ?? 'active') === $statusOption)>{{ __(Illuminate\Support\Str::headline($statusOption)) }}</option>
                    @endforeach
                </select>
                <p class="form-hint mt-2">{{ __('Inactive accounts can stay in the system for audit purposes but cannot sign in to web or API access.') }}</p>
            </div>
        </div>
    </section>

    <section class="form-section">
        <div class="form-section-heading">
            <h2>{{ __('Governance scope') }}</h2>
            <p>{{ __('Choose the exact region, district, and branch so mobile users can review the hierarchy without confusion.') }}</p>
        </div>

        <div class="form-grid-responsive">
            <div>
                <label class="field-label" for="region_id">{{ __('Region') }}</label>
                <select class="select-rgc" id="region_id" name="region_id" data-region-select required>
                    <option value="">{{ __('Select region') }}</option>
                    @foreach($regions as $region)
                        <option value="{{ $region->id }}" @selected(old('region_id', $managedUser?->region_id) == $region->id)>{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="field-label" for="district_id">{{ __('District') }}</label>
                <select class="select-rgc" id="district_id" name="district_id" data-district-select data-empty-option-label="{{ __('Select district') }}" data-selected-value="{{ old('district_id', $managedUser?->district_id ?? '') }}" required>
                    @if(! empty($managedUser?->district_id))
                        <option value="{{ $managedUser->district_id }}">{{ $managedUser->district?->name }}</option>
                    @else
                        <option value="">{{ __('Select district') }}</option>
                    @endif
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="field-label" for="branch_id">{{ __('Branch') }}</label>
                <select class="select-rgc" id="branch_id" name="branch_id" data-branch-select data-empty-option-label="{{ __('Select branch') }}" data-selected-value="{{ old('branch_id', $managedUser?->effectiveBranchId() ?? '') }}" required>
                    @if(! empty($managedUser?->effectiveBranchId()))
                        <option value="{{ $managedUser->effectiveBranchId() }}">{{ $managedUser->branch?->name }}</option>
                    @else
                        <option value="">{{ __('Select branch') }}</option>
                    @endif
                </select>
            </div>
        </div>
    </section>

    <section class="form-section">
        <div class="form-section-heading">
            <h2>{{ __('Password access') }}</h2>
            <p>
                {{ ($passwordRequired ?? false)
                    ? __('Create a temporary password that the user can use immediately.')
                    : __('Leave these fields empty if you do not want to reset the current password.') }}
            </p>
        </div>

        <div class="form-grid-responsive">
            <div>
                <label class="field-label" for="password">{{ $passwordLabel ?? __('Password') }}</label>
                <input class="input-rgc" id="password" type="password" name="password" {{ ($passwordRequired ?? false) ? 'required' : '' }}>
                @if(! ($passwordRequired ?? false))
                    <p class="form-hint mt-2">{{ __('Leave blank to keep the current password. Enter a new one here to reset the user password.') }}</p>
                @endif
            </div>

            <div>
                <label class="field-label" for="password_confirmation">{{ __('Confirm password') }}</label>
                <input class="input-rgc" id="password_confirmation" type="password" name="password_confirmation" {{ ($passwordRequired ?? false) ? 'required' : '' }}>
            </div>
        </div>
    </section>
</div>
