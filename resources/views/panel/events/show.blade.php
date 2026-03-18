@extends('layouts.app')

@section('title', 'Taarifa za Tukio - Mfumo wa Kanisa')
@section('page-title', 'Taarifa za Tukio')
@section('page-subtitle', 'Angalia maelezo kamili ya tukio')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Taarifa za Tukio</h1>
            <p class="text-gray-600 mt-2">Angalia maelezo kamili ya tukio la kanisa</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('events.edit', $event->id) }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700">
                <i class="fas fa-edit"></i>
                <span class="font-medium">Hariri</span>
            </a>
            <a href="{{ route('events.index') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Rudi Orodhani</span>
            </a>
        </div>
    </div>

    <!-- Event Profile Header -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 p-8">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Profile Icon -->
                <div class="h-24 w-24 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-white text-5xl"></i>
                </div>

                <!-- Event Info -->
                <div class="flex-1 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-3xl font-bold">{{ $event->title }}</h2>
                            <div class="flex flex-wrap items-center gap-4 mt-2">
                                <span class="inline-flex items-center px-3 py-1 bg-white bg-opacity-20 text-white rounded-full text-sm font-semibold">
                                    <i class="fas fa-tag mr-1.5"></i>{{ $event->event_type }}
                                </span>
                                @if($event->is_active)
                                    <span class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-check-circle mr-1.5"></i>Hai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 bg-red-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-times-circle mr-1.5"></i>Si Hai
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Event Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Event Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-info-circle text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Tukio</h3>
                        <p class="text-sm text-gray-600">Maelezo ya msingi ya tukio</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Event Date -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tarehe</p>
                        <div class="flex items-center">
                            <i class="fas fa-calendar text-primary-500 mr-2"></i>
                            <div>
                                <p class="text-base font-medium text-gray-900">{{ $event->event_date->format('d/m/Y') }}</p>
                                <p class="text-sm text-gray-500">{{ $event->event_date->translatedFormat('l') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Event Time -->
                    @if($event->start_time)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Muda</p>
                        <div class="flex items-center">
                            <i class="fas fa-clock text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">
                                {{ date('H:i', strtotime($event->start_time)) }} - {{ date('H:i', strtotime($event->end_time)) }}
                            </p>
                        </div>
                    </div>
                    @endif

                    <!-- Venue -->
                    @if($event->venue)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Mahali</p>
                        <div class="flex items-center">
                            <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $event->venue }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Event Type -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Aina ya Tukio</p>
                        <div class="flex items-center">
                            <i class="fas fa-tag text-purple-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $event->event_type }}</p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Hali</p>
                        <div class="flex items-center">
                            @if($event->is_active)
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <p class="text-base font-medium text-green-600">Hai</p>
                            @else
                                <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                <p class="text-base font-medium text-red-600">Si Hai</p>
                            @endif
                        </div>
                    </div>

                    <!-- Creator -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Iliyoandikwa na</p>
                        <div class="flex items-center">
                            <i class="fas fa-user text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $event->creator->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Card -->
            @if($event->description)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-align-left text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Maelezo ya Tukio</h3>
                        <p class="text-sm text-gray-600">Maelezo kamili kuhusu tukio hili</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-quote-left text-primary-500 mr-2 mt-1"></i>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $event->description }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Additional Notes Card -->
            @if($event->notes)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-sticky-note text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Maelezo Mengine</h3>
                        <p class="text-sm text-gray-600">Taarifa zingine muhimu</p>
                    </div>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mr-2 mt-1"></i>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $event->notes }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Stats & Budget -->
        <div class="space-y-6">
            <!-- Budget Card -->
            @if($event->budget)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-money-bill-wave text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Bajeti</h3>
                        <p class="text-sm text-gray-600">Bajeti ya tukio</p>
                    </div>
                </div>

                <div class="bg-green-50 border-2 border-green-200 p-4 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <i class="fas fa-coins text-green-600 mr-2"></i>
                            <span class="font-medium text-gray-900">Jumla ya Bajeti</span>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-green-600 mt-2">TZS {{ number_format($event->budget, 2) }}</p>
                </div>
            </div>
            @endif

            <!-- Attendance Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-users text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Mahudhurio</h3>
                        <p class="text-sm text-gray-600">Takwimu za mahudhurio</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @if($event->expected_attendance)
                    <!-- Expected Attendance -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-user-friends text-blue-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Watarajiwa</span>
                        </div>
                        <span class="text-base font-bold text-gray-900">{{ $event->expected_attendance }}</span>
                    </div>
                    @endif

                    @if($event->actual_attendance)
                    <!-- Actual Attendance -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-user-check text-green-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Waliohudhuria</span>
                        </div>
                        <span class="text-base font-bold text-primary-600">{{ $event->actual_attendance }}</span>
                    </div>

                    @if($event->expected_attendance)
                    <!-- Attendance Percentage -->
                    <div class="bg-primary-50 border-2 border-primary-200 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-medium text-gray-900">Asilimia ya Mahudhurio</span>
                            <span class="text-xl font-bold text-primary-600">{{ round(($event->actual_attendance / $event->expected_attendance) * 100, 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ min(($event->actual_attendance / $event->expected_attendance) * 100, 100) }}%"></div>
                        </div>
                    </div>
                    @endif
                    @endif

                    @if(!$event->expected_attendance && !$event->actual_attendance)
                    <div class="text-center py-4">
                        <div class="mx-auto w-12 h-12 mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-users-slash text-gray-400 text-xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">Hakuna taarifa za mahudhurio</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-chart-line text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Takwimu za Haraka</h3>
                        <p class="text-sm text-gray-600">Muhtasari wa tukio</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Event Date -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-calendar text-purple-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Tarehe</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">{{ $event->event_date->format('d/m/Y') }}</span>
                    </div>

                    <!-- Days Until/Since Event -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-hourglass-half text-orange-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Muda</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">{{ $event->event_date->diffForHumans() }}</span>
                    </div>

                    <!-- Created Date -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-plus-circle text-green-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Iliandikwa</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">{{ $event->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
