@extends('layouts.app')

@section('title', 'Taarifa za Sadaka - Mfumo wa Kanisa')
@section('page-title', 'Taarifa za Sadaka')
@section('page-subtitle', 'Angalia maelezo kamili ya sadaka')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Taarifa za Sadaka</h1>
            <p class="text-gray-600 mt-2">Angalia maelezo kamili ya sadaka</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('offerings.edit', $income->id) }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700">
                <i class="fas fa-edit"></i>
                <span class="font-medium">Hariri</span>
            </a>
            <a href="{{ route('offerings.index') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Rudi Orodhani</span>
            </a>
        </div>
    </div>

    <!-- Offering Profile Header -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-green-800 p-8">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Profile Icon -->
                <div class="h-24 w-24 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-white text-5xl"></i>
                </div>

                <!-- Offering Info -->
                <div class="flex-1 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-3xl font-bold">{{ number_format($income->amount, 0) }} TSh</h2>
                            <div class="flex flex-wrap items-center gap-4 mt-2">
                                <span class="inline-flex items-center px-3 py-1 bg-white bg-opacity-20 text-white rounded-full text-sm font-semibold">
                                    <i class="fas fa-tag mr-1.5"></i>{{ $income->category->name }}
                                </span>
                                @if($income->receipt_number)
                                    <span class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-check-circle mr-1.5"></i>Risiti: {{ $income->receipt_number }}
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
        <!-- Left Column: Offering Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Offering Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-info-circle text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Sadaka</h3>
                        <p class="text-sm text-gray-600">Maelezo ya msingi ya sadaka</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Collection Date -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tarehe ya Kukusanya</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ \Carbon\Carbon::parse($income->collection_date)->format('d/m/Y') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($income->collection_date)->format('l') }}
                        </p>
                    </div>

                    <!-- Amount -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Kiasi</p>
                        <p class="text-lg font-semibold text-green-600">
                            {{ number_format($income->amount, 0) }} TSh
                        </p>
                    </div>

                    <!-- Category -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Aina ya Sadaka</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $income->category->name }}
                        </p>
                        @if($income->category->code)
                            <p class="text-xs text-gray-500 mt-1">Code: {{ $income->category->code }}</p>
                        @endif
                    </div>

                    <!-- Receipt Number -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Namba ya Risiti</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ $income->receipt_number ?? '-' }}
                        </p>
                    </div>
                </div>

                @if($income->notes)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Maelezo</p>
                    <p class="text-gray-900">{{ $income->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Member Information Card -->
            @if($income->member)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Mwanachama</h3>
                        <p class="text-sm text-gray-600">Maelezo ya mwanachama aliyetoa sadaka</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Jina</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $income->member->full_name }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Namba ya Mwanachama</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $income->member->member_number }}</p>
                    </div>
                    @if($income->member->phone)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Simu</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $income->member->phone }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: System Information -->
        <div class="space-y-6">
            <!-- System Info Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-info-circle text-gray-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Mfumo</h3>
                        <p class="text-sm text-gray-600">Maelezo ya mfumo</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Imeundwa</p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $income->created_at->format('d/m/Y H:i') }}
                        </p>
                        @if($income->creator)
                            <p class="text-xs text-gray-500 mt-1">na {{ $income->creator->name }}</p>
                        @endif
                    </div>

                    @if($income->updated_at != $income->created_at)
                    <div class="pt-4 border-t border-gray-200">
                        <p class="text-sm text-gray-600 mb-1">Imesasishwa</p>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $income->updated_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Vitendo</h3>
                <div class="space-y-3">
                    <a href="{{ route('offerings.edit', $income->id) }}" class="w-full flex items-center justify-center px-4 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Hariri Sadaka
                    </a>
                    <form action="{{ route('offerings.destroy', $income->id) }}" method="POST" onsubmit="return confirm('Je, una uhakika unataka kufuta sadaka hii?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-200">
                            <i class="fas fa-trash mr-2"></i>
                            Futa Sadaka
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



