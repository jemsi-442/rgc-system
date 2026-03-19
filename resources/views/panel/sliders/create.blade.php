@extends('layouts.app')

@section('content')
<div class="slider-admin-shell">
    <div class="card-rgc slider-admin-hero">
        <div>
            <span class="section-kicker">{{ __('Homepage Slides') }}</span>
            <h1 class="mt-4 text-2xl font-semibold">{{ __('Create Slide') }}</h1>
            <p class="mt-2 text-sm text-black/65">{{ __('Add a new homepage slide with the right title, image, and order so the public landing page stays polished.') }}</p>
        </div>
    </div>

    <form class="mt-5" method="POST" action="{{ route('sliders.store') }}" enctype="multipart/form-data">
        @csrf
        @include('panel.sliders._form', ['submitLabel' => __('Create Slide')])
    </form>
</div>
@endsection
