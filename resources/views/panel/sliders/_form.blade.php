<div class="slider-form-grid">
    <section class="card-rgc slider-form-section">
        <span class="section-kicker">{{ __('Slide Content') }}</span>
        <div class="mt-4 grid gap-4">
            <div>
                <label class="field-label" for="title">{{ __('Title') }}</label>
                <input class="input-rgc" id="title" name="title" value="{{ old('title', $slider->title) }}" placeholder="{{ __('Title') }}" required>
            </div>
            <div>
                <label class="field-label" for="subtitle">{{ __('Subtitle') }}</label>
                <input class="input-rgc" id="subtitle" name="subtitle" value="{{ old('subtitle', $slider->subtitle) }}" placeholder="{{ __('Subtitle') }}">
            </div>
            <div>
                <label class="field-label" for="sort_order">{{ __('Sort order') }}</label>
                <input class="input-rgc" id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $slider->sort_order ?? 0) }}" placeholder="0">
                <p class="mt-2 text-sm text-black/60">{{ __('Lower numbers appear earlier on the homepage slider.') }}</p>
            </div>
            <div>
                <input type="hidden" name="is_active" value="0">
                <label class="inline-flex items-center gap-3 text-sm font-medium text-black/80" for="is_active">
                    <input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $slider->is_active ?? true))>
                    <span>{{ __('Show this slide on the public homepage') }}</span>
                </label>
            </div>
        </div>
    </section>

    <section class="card-rgc slider-form-section">
        <span class="section-kicker">{{ __('Slide Image') }}</span>
        <div class="mt-4 grid gap-4">
            @if($slider->exists && $slider->image_path)
                <div>
                    <p class="field-label">{{ __('Current image') }}</p>
                    <img class="slider-form-preview" src="{{ route('slides.show', $slider) }}" alt="{{ $slider->title }}">
                </div>
            @endif
            <div class="slider-dropzone" data-slider-dropzone>
                <div class="slider-dropzone-copy">
                    <strong>{{ $slider->exists ? __('Replace image') : __('Slider image') }}</strong>
                    <p>{{ __('Drop an image here or click to browse from your device.') }}</p>
                </div>
                <input class="slider-file-input" data-slider-image-input id="image" type="file" name="image" accept="image/*" @required(! $slider->exists)>
            </div>
            <div class="slider-live-preview hidden" data-slider-preview data-empty-label="{{ __('Selected image') }}">
                <p class="field-label">{{ __('Selected image') }}</p>
                <img class="slider-form-preview" data-slider-preview-image alt="{{ __('Slider preview') }}">
                <p class="mt-2 text-sm text-black/60" data-slider-preview-name>{{ __('Selected image') }}</p>
            </div>
            <p class="text-sm text-black/60">{{ __('Use a wide, high-quality image so the homepage slider stays sharp on mobile and desktop.') }}</p>
        </div>
    </section>
</div>

<div class="form-actions mt-5">
    <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('sliders.index') }}">{{ __('Back to slides') }}</a>
    <button class="btn-rgc w-full sm:w-auto" type="submit">{{ $submitLabel }}</button>
</div>
