@extends('layouts.app')

@section('title', __('Edit Announcement') . ' - RGC')

@section('content')
<section class="page-banner">
    <div class="page-banner-content">
        <span class="section-kicker !border-white/10 !bg-white/10 !text-rgc-yellow">{{ __('Announcements') }}</span>
        <h1 class="mt-5">{{ __('Edit Announcement') }}</h1>
        <p class="mt-4 max-w-3xl text-sm leading-7 text-white/82">{{ __('Update the message, refresh the image if needed, or adjust who should receive this announcement.') }}</p>
    </div>
</section>

<section class="card-rgc mt-8 max-w-4xl">
    <form method="POST" action="{{ route('announcements.update', $announcement) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('panel.announcements._form', ['submitLabel' => __('Save')])
    </form>
</section>
@endsection
