@php
    $selectedRoles = old('roles', $topic->roles ?? []);
    $selectedRegionId = old('region_id', $topic->region_id ?? '');
    $isRegionalManager = $manager->hasSystemRole('regional_admin') && ! $manager->hasSystemRole('super_admin');
    $managedRegion = $regionOptions->firstWhere('id', $manager->region_id);
@endphp

<div class="form-stack">
    <section class="form-section">
        <div class="form-section-heading">
            <h2>{{ __('Topic details') }}</h2>
            <p>{{ __('Define the title, slug, language, and publish state for this assistant topic.') }}</p>
        </div>

        <div class="form-grid-responsive">
            <div class="md:col-span-2">
                <label class="field-label" for="title">{{ __('Title') }}</label>
                <input class="input-rgc" id="title" name="title" value="{{ old('title', $topic->title ?? '') }}" required>
            </div>

            <div>
                <label class="field-label" for="slug">{{ __('Slug') }}</label>
                <input class="input-rgc" id="slug" name="slug" value="{{ old('slug', $topic->slug ?? '') }}" placeholder="system-topic-slug">
                <p class="form-hint mt-2">{{ __('If you leave this empty, the system will generate it from the title.') }}</p>
            </div>

            <div>
                <label class="field-label" for="locale">{{ __('Language') }}</label>
                <select class="select-rgc" id="locale" name="locale" required>
                    @foreach($localeOptions as $localeOption)
                        <option value="{{ $localeOption }}" @selected(old('locale', $topic->locale ?: app()->getLocale()) === $localeOption)>{{ strtoupper($localeOption) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="field-label" for="sort_order">{{ __('Sort order') }}</label>
                <input class="input-rgc" id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $topic->sort_order ?? 0) }}">
            </div>

            <div class="flex items-center gap-3 pt-7">
                <input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $topic->exists ? $topic->is_active : true))>
                <label class="field-label mb-0" for="is_active">{{ __('Active topic') }}</label>
            </div>
        </div>
    </section>

    <section class="form-section">
        <div class="form-section-heading">
            <h2>{{ __('Church coverage') }}</h2>
            <p>{{ __('Decide whether this topic should help the whole church or only one region. Regional admins are automatically locked to their own region.') }}</p>
        </div>

        @if($isRegionalManager)
            <input type="hidden" name="region_id" value="{{ $manager->region_id }}">
            <div class="announcement-empty-state">
                <strong>{{ __('Regional coverage locked') }}</strong>
                <p>{{ __('This topic will only serve users in :region.', ['region' => $managedRegion?->name ?? __('your region')]) }}</p>
            </div>
        @else
            <div class="form-grid-responsive">
                <div class="md:col-span-2">
                    <label class="field-label" for="region_id">{{ __('Topic coverage') }}</label>
                    <select class="select-rgc" id="region_id" name="region_id">
                        <option value="">{{ __('Whole church') }}</option>
                        @foreach($regionOptions as $regionOption)
                            <option value="{{ $regionOption->id }}" @selected((string) $selectedRegionId === (string) $regionOption->id)>{{ $regionOption->name }}</option>
                        @endforeach
                    </select>
                    <p class="form-hint mt-2">{{ __('Choose a region if this answer should only guide users from that region. Leave it on whole church if it should serve all branches and regions.') }}</p>
                </div>
            </div>
        @endif
    </section>

    <section class="form-section">
        <div class="form-section-heading">
            <h2>{{ __('Matching rules') }}</h2>
            <p>{{ __('Add keywords or short phrases that should trigger this answer. Separate them by comma or new line.') }}</p>
        </div>

        <div class="form-grid-responsive">
            <div class="md:col-span-2">
                <label class="field-label" for="keywords_text">{{ __('Keywords and phrases') }}</label>
                <textarea class="input-rgc min-h-[9rem]" id="keywords_text" name="keywords_text" required>{{ old('keywords_text', implode(PHP_EOL, $topic->keywords ?? [])) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="field-label" for="roles">{{ __('Visible roles') }}</label>
                <select class="select-rgc min-h-[10rem]" id="roles" name="roles[]" multiple>
                    @foreach($roleOptions as $roleOption)
                        <option value="{{ $roleOption }}" @selected(in_array($roleOption, $selectedRoles, true))>{{ __(Illuminate\Support\Str::headline($roleOption)) }}</option>
                    @endforeach
                </select>
                <p class="form-hint mt-2">{{ __('Leave this empty if the topic should work for all users and guests.') }}</p>
            </div>
        </div>
    </section>

    <section class="form-section">
        <div class="form-section-heading">
            <h2>{{ __('Answer content') }}</h2>
            <p>{{ __('Write the answer in a friendly, church-appropriate tone, then add up to a few helpful follow-up suggestions.') }}</p>
        </div>

        <div class="form-grid-responsive">
            <div class="md:col-span-2">
                <label class="field-label" for="answer">{{ __('Answer') }}</label>
                <textarea class="input-rgc min-h-[14rem]" id="answer" name="answer" required>{{ old('answer', $topic->answer ?? '') }}</textarea>
            </div>

            <div class="md:col-span-2">
                <label class="field-label" for="suggestions_text">{{ __('Suggested follow-up questions') }}</label>
                <textarea class="input-rgc min-h-[8rem]" id="suggestions_text" name="suggestions_text">{{ old('suggestions_text', implode(PHP_EOL, $topic->suggestions ?? [])) }}</textarea>
                <p class="form-hint mt-2">{{ __('Enter one suggestion per line.') }}</p>
            </div>
        </div>
    </section>
</div>
