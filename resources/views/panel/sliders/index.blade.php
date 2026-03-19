@extends('layouts.app')

@section('content')
<div class="slider-admin-shell">
    <div class="card-rgc slider-admin-hero">
        <div class="slider-admin-header">
            <div>
                <span class="section-kicker">{{ __('Homepage Slides') }}</span>
                <h1 class="mt-4 text-2xl font-semibold">{{ __('Manage Homepage Slides') }}</h1>
                <p class="mt-2 text-sm text-black/65">{{ __('Control which images appear on the public homepage, arrange their order, and keep the hero slider fresh for visitors.') }}</p>
            </div>
            <a class="btn-rgc w-full sm:w-auto" href="{{ route('sliders.create') }}">{{ __('Add Slide') }}</a>
        </div>

        <div class="slider-admin-stats mt-5">
            <article class="branch-preview-stat">
                <span>{{ __('Total slides') }}</span>
                <strong>{{ $sliders->total() }}</strong>
                <p>{{ __('All slider records currently stored in the system.') }}</p>
            </article>
            <article class="branch-preview-stat">
                <span>{{ __('Active slides') }}</span>
                <strong>{{ $activeCount }}</strong>
                <p>{{ __('Slides currently visible on the public homepage.') }}</p>
            </article>
            <article class="branch-preview-stat">
                <span>{{ __('Inactive slides') }}</span>
                <strong>{{ $inactiveCount }}</strong>
                <p>{{ __('Slides saved in the system but hidden from visitors.') }}</p>
            </article>
        </div>
    </div>

    <div class="slider-admin-list mt-5">
        @forelse($sliders as $slider)
            <article class="card-rgc slider-admin-card">
                <img class="slider-admin-preview slider-admin-preview-lg" src="{{ route('slides.show', $slider) }}" alt="{{ $slider->title }}">
                <div class="slider-admin-copy">
                    <div class="branch-preview-breakdown">
                        <span>{{ __('Order: :order', ['order' => $slider->sort_order]) }}</span>
                        <span>{{ $slider->is_active ? __('Active') : __('Inactive') }}</span>
                    </div>
                    <h2 class="mt-4 text-xl font-semibold">{{ $slider->title }}</h2>
                    <p class="mt-2 text-sm text-black/65">{{ $slider->subtitle ?: __('No subtitle provided for this slide yet.') }}</p>
                    <p class="mt-3 text-xs uppercase tracking-[0.2em] text-black/45">{{ __('Updated :date', ['date' => $slider->updated_at?->format('d M Y H:i')]) }}</p>
                </div>
                <div class="slider-admin-actions">
                    <form method="POST" action="{{ route('sliders.status', $slider) }}" class="slider-quick-form">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="is_active" value="{{ $slider->is_active ? 0 : 1 }}">
                        <button class="btn-rgc-alt w-full sm:w-auto" type="submit">{{ $slider->is_active ? __('Hide Slide') : __('Activate Slide') }}</button>
                    </form>
                    <form method="POST" action="{{ route('sliders.sort-order', $slider) }}" class="slider-quick-form slider-quick-sort">
                        @csrf
                        @method('PATCH')
                        <label class="field-label" for="sort_order_{{ $slider->id }}">{{ __('Sort order') }}</label>
                        <div class="slider-quick-sort-row">
                            <input class="input-rgc" id="sort_order_{{ $slider->id }}" type="number" min="0" name="sort_order" value="{{ $slider->sort_order }}">
                            <button class="btn-rgc-alt" type="submit">{{ __('Save Order') }}</button>
                        </div>
                    </form>
                    <a class="btn-rgc-alt w-full sm:w-auto" href="{{ route('sliders.edit', $slider) }}">{{ __('Edit') }}</a>
                    <form method="POST" action="{{ route('sliders.destroy', $slider) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn-rgc w-full sm:w-auto" type="submit" onclick="return confirm('{{ __('Delete this slide?') }}')">{{ __('Delete') }}</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="card-rgc">
                <h2 class="text-lg font-semibold">{{ __('No slides yet') }}</h2>
                <p class="mt-2 text-sm text-black/65">{{ __('Create the first homepage slide to start populating the public landing page hero carousel.') }}</p>
                <div class="mt-4">
                    <a class="btn-rgc w-full sm:w-auto" href="{{ route('sliders.create') }}">{{ __('Create First Slide') }}</a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-5">{{ $sliders->links() }}</div>
</div>
@endsection
