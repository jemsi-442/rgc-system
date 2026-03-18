@php($isEdit = isset($announcement))
@php($user = auth()->user())
@php($scopeLabel = $user->hasSystemRole('super_admin') ? __('This announcement will be delivered to every user and every branch in the system.') : ($user->hasSystemRole('regional_admin') ? __('This announcement will be visible across your region.') : ($user->hasSystemRole('district_admin') ? __('This announcement will be visible across your district.') : __('This announcement will stay inside your branch scope.'))))

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
                <span class="announcement-scope-pill">{{ $isEdit ? ($announcement->audienceLabel()) : ($user->hasSystemRole('super_admin') ? __('National') : ($user->hasSystemRole('regional_admin') ? __('Region') : ($user->hasSystemRole('district_admin') ? __('District') : __('Branch')))) }}</span>
                <p>{{ $scopeLabel }}</p>
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
