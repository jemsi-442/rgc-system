@extends('layouts.app')

@section('title', __('New Announcement') . ' - RGC')

@section('content')
<section class="page-banner">
    <div class="page-banner-content">
        <span class="section-kicker !border-white/10 !bg-white/10 !text-rgc-yellow">{{ __('Announcements') }}</span>
        <h1 class="mt-5">{{ __('New Announcement') }}</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-white/82">{{ __('Send a clear church update with text, image support, and the right audience.') }}</p>
    </div>
</section>

<section class="card-rgc mt-8 max-w-4xl announcement-studio">
    <form method="POST" action="{{ route('announcements.store') }}" enctype="multipart/form-data">
        @csrf
        @include('panel.announcements._form', ['submitLabel' => __('Publish')])
    </form>
</section>
@endsection
