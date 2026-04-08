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
                <input class="input-rgc" id="phone" name="phone" type="tel" inputmode="tel" autocomplete="tel" value="{{ old('phone', $managedUser?->phone ?? '') }}" placeholder="{{ __('Start with 06, 07, or 255') }}">
                <p class="form-hint mt-2">{{ __('Use a Tanzania number. Start with 06, 07, or 255.') }}</p>
            </div>

            <div>
                <label class="field-label" for="role">{{ __('System role') }}</label>
                <select class="select-rgc" id="role" name="role" data-role-select required>
                    @foreach($roleOptions as $roleOption)
                        <option value="{{ $roleOption }}" @selected(old('role', $managedUser?->normalizedRoleName() ?? 'member') === $roleOption)>{{ __(Illuminate\Support\Str::headline($roleOption)) }}</option>
                    @endforeach
                </select>
                <p class="form-hint mt-2">{{ __('Choose member for normal branch users, or assign regional, district, or branch admin here when the person should lead that governance area.') }}</p>
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

        <div class="form-callout mt-5" data-role-guidance-panel>
            <strong data-role-guidance-title>{{ __('Member access') }}</strong>
            <p class="mt-2 text-sm text-black/70" data-role-guidance-copy>
                {{ __('Use member for regular church users who should receive branch updates, giving access, and normal sign-in without leadership controls.') }}
            </p>
            <p class="mt-3 text-sm text-black/60" data-role-scope-copy>
                {{ __('The region, district, and branch below still matter because every account keeps a home branch, even when the role is later promoted into district or regional leadership.') }}
            </p>
        </div>
    </section>

    <section class="form-section">
        <div class="form-section-heading">
            <h2>{{ __('Governance scope') }}</h2>
            <p>{{ __('Set the exact region, district, and branch that the account will govern. This is what lets Super Admin promote someone into the right leadership scope and later return them to ordinary member access without confusion.') }}</p>
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
