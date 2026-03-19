@extends('layouts.app')

@section('content')
<div class="slider-admin-shell">
    <div class="card-rgc slider-admin-hero">
        <div>
            <span class="section-kicker">{{ __('Homepage Slides') }}</span>
            <h1 class="mt-4 text-2xl font-semibold">{{ __('Edit Slide') }}</h1>
            <p class="mt-2 text-sm text-black/65">{{ __('Update image visibility, sort order, and messaging for this homepage slide without affecting the others.') }}</p>
        </div>
    </div>

    <form class="mt-5" method="POST" action="{{ route('sliders.update', $slider) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('panel.sliders._form', ['submitLabel' => __('Save Changes')])
    </form>
</div>
@endsection
