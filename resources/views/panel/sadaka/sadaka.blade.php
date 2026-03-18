@extends('layouts.app')

@section('title', 'Sadaka na Ahadi - Mfumo wa Kanisa')
@section('page-title', 'Sadaka na Ahadi')
@section('page-subtitle', 'Rekodi na usimamizi wa sadaka na ahadi za kanisa')

@section('content')
<div class="space-y-6">
    <!-- Success/Error/Warning Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg mb-6 shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg mb-6 shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg mb-6 shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                <span class="block sm:inline">{{ session('warning') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg mb-6 shadow-sm" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <div>
                    <span class="block font-medium">Tafadhali sahihisha makosa yafuatayo:</span>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Sadaka na Ahadi</h1>
            <p class="text-gray-600 mt-2">Usimamizi kamili wa rekodi za sadaka na ahadi za kanisa</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="exportSadakaExcel()" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700">
                <i class="fas fa-file-excel"></i>
                <span class="font-medium">Export Sadaka</span>
            </button>
            <button onclick="exportAhadiExcel()" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700">
                <i class="fas fa-file-excel"></i>
                <span class="font-medium">Export Ahadi</span>
            </button>
            <a href="{{ route('offerings.create') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800">
                <i class="fas fa-plus"></i>
                <span class="font-medium">Ongeza Rekodi</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jumla ya Sadaka {{ date('Y') }}</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalSadaka, 0) }} TZS</p>
                </div>
                <div class="h-12 w-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-hand-holding-heart text-xl text-primary-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jumla ya Ahadi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalAhadi, 0) }} TZS</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-hands-praying text-xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Imelipwa</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($totalMalipo, 0) }} TZS</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Salio la Ahadi</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($totalAhadi - $totalMalipo, 0) }} TZS</p>
                </div>
                <div class="h-12 w-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-xl text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-bolt text-primary-500 mr-2"></i> Hatua za Haraka
            </h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4 cursor-pointer hover:border-primary-300 hover:shadow-md transition-all duration-200" onclick="openModal('sadakaModal')">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-hand-holding-heart text-primary-500 text-lg"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Ingiza Sadaka</div>
                        <div class="text-sm text-gray-500">Jumapili au Sikukuu</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-4 cursor-pointer hover:border-purple-300 hover:shadow-md transition-all duration-200" onclick="openModal('ahadiModal')">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-hands-praying text-purple-600 text-lg"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Ahadi Mpya</div>
                        <div class="text-sm text-gray-500">Kiwanja, RGC, Mavuno</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-4 cursor-pointer hover:border-green-300 hover:shadow-md transition-all duration-200" onclick="openModal('malipoModal')">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-coins text-green-600 text-lg"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Rekodi Malipo</div>
                        <div class="text-sm text-gray-500">Malipo ya ahadi</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-4 cursor-pointer hover:border-blue-300 hover:shadow-md transition-all duration-200" onclick="openCalculatorModal()">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-calculator text-blue-600 text-lg"></i>
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">Kikokotoo</div>
                        <div class="text-sm text-gray-500">Hesabu noti na sarafu</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Year/Month Filter -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter text-primary-500 mr-2"></i> Chuja Rekodi
            </h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Chagua Mwaka</label>
                <select id="yearFilter" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @for($y = date('Y'); $y >= 2020; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Chagua Mwezi</label>
                <select id="monthFilter" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Zote</option>
                    @foreach(['Januari', 'Februari', 'Machi', 'Aprili', 'Mei', 'Juni', 'Julai', 'Agosti', 'Septemba', 'Oktoba', 'Novemba', 'Desemba'] as $index => $month)
                        <option value="{{ $index + 1 }}" {{ $month == $selectedMonth ? 'selected' : '' }}>{{ $month }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button onclick="applyFilters()" class="w-full px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-filter"></i>
                    <span>Tafuta</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button id="sadakaTab" class="px-4 py-3 text-sm font-medium border-b-2 border-primary-500 text-primary-600" role="tab" aria-selected="true" aria-controls="sadakaContainer">
                    <i class="fas fa-hand-holding-heart mr-2"></i> Sadaka za Wiki
                </button>
                <button id="ahadiTab" class="px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" role="tab" aria-selected="false" aria-controls="ahadiContainer">
                    <i class="fas fa-hands-praying mr-2"></i> Ahadi na Malipo
                </button>
            </nav>
        </div>

        <!-- Sadaka Tab Content -->
        <div id="sadakaContainer" class="tab-content p-6">
            <!-- Search and Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-700 flex items-center">
                        <i class="fas fa-hand-holding-heart text-primary-500 mr-2"></i> Rekodi za Sadaka
                        <span class="ml-2 text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                            <span id="sadakaCount">{{ $sadaka->total() }}</span> rekodi
                        </span>
                    </h3>
                </div>
                <div class="flex gap-3">
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="searchSadaka" class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Tafuta kwa tarehe au aina...">
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-primary-600 text-white text-sm">
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar mr-2"></i>
                                        Tarehe
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-tag mr-2"></i>
                                        Aina ya Sadaka
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-user mr-2"></i>
                                        Muumini
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-money-bill-wave mr-2"></i>
                                        Kiasi (TZS)
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Maelezo
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider sticky right-0 bg-primary-600">
                                    <div class="flex items-center">
                                        <i class="fas fa-cogs mr-2"></i>
                                        Hatua
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="sadakaTableBody">
                            @foreach($sadaka as $record)
                            <tr class="bg-white hover:bg-gray-50 transition-all duration-200">
                                <!-- Date -->
                                <td class="py-4 px-6">
                                    <div class="text-sm text-gray-900">
                                        {{ $record->collection_date ? $record->collection_date->format('d/m/Y') : 'N/A' }}
                                    </div>
                                </td>

                                <!-- Type -->
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                        {{ $record->category->name ?? 'N/A' }}
                                    </span>
                                </td>

                                <!-- Member -->
                                <td class="py-4 px-6">
                                    <div class="text-sm text-gray-900">
                                        {{ $record->member->full_name ?? 'Jumla' }}
                                    </div>
                                </td>

                                <!-- Amount -->
                                <td class="py-4 px-6">
                                    <div class="text-lg font-bold text-green-600">
                                        {{ number_format($record->amount ?? 0, 0) }}
                                    </div>
                                </td>

                                <!-- Description -->
                                <td class="py-4 px-6">
                                    <div class="text-sm text-gray-600 max-w-xs truncate">
                                        {{ $record->notes ?? '-' }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-6 text-sm sticky right-0 bg-white">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="viewSadakaDetails('{{ $record->id }}')" class="h-8 w-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-200 transition-all duration-200" title="Angalia Maelezo">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        <button onclick="deleteSadaka('{{ $record->id }}')" class="h-8 w-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200 transition-all duration-200" title="Futa Rekodi">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                @if($sadaka->count() == 0)
                    <div class="text-center py-12 px-6">
                        <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-hand-holding-heart text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna rekodi za sadaka</h3>
                        <p class="text-gray-500 mb-6">Hakuna rekodi zilizopatikana kwenye kipindi kilichochaguliwa.</p>
                        <button class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 inline-flex" onclick="openModal('sadakaModal')">
                            <i class="fas fa-hand-holding-heart"></i> Ingiza Sadaka
                        </button>
                    </div>
                @endif

                <!-- Pagination -->
                @if($sadaka->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $sadaka->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Ahadi Tab Content -->
        <div id="ahadiContainer" class="tab-content p-6 hidden">
            <!-- Search and Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-700 flex items-center">
                        <i class="fas fa-hands-praying text-purple-600 mr-2"></i> Ahadi na Malipo
                        <span class="ml-2 text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                            <span id="ahadiCount">{{ $ahadi->total() }}</span> rekodi
                        </span>
                    </h3>
                </div>
                <div class="flex gap-3">
                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="searchAhadi" class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Tafuta kwa jina au aina...">
                    </div>
                </div>
            </div>

            <!-- Summary Cards for Ahadi -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-hands-praying text-purple-600 text-lg"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Jumla ya Ahadi</div>
                            <div class="text-lg font-bold text-purple-600">{{ number_format($totalAhadi, 0) }} TZS</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-check-circle text-green-600 text-lg"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Imelipwa</div>
                            <div class="text-lg font-bold text-green-600">{{ number_format($totalMalipo, 0) }} TZS</div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="flex items-center">
                        <div class="h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-clock text-yellow-600 text-lg"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Bado Hajalipa</div>
                            <div class="text-lg font-bold text-yellow-600">{{ number_format($totalAhadi - $totalMalipo, 0) }} TZS</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-primary-600 text-white text-sm">
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider sticky left-0 bg-primary-600">
                                    <div class="flex items-center">
                                        <i class="fas fa-user mr-2"></i>
                                        Muumini
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-tag mr-2"></i>
                                        Aina ya Ahadi
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-hand-holding-usd mr-2"></i>
                                        Ahadi (TZS)
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Imelipwa (TZS)
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-clock mr-2"></i>
                                        Salio (TZS)
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-circle mr-2"></i>
                                        Hali
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider sticky right-0 bg-primary-600">
                                    <div class="flex items-center">
                                        <i class="fas fa-cogs mr-2"></i>
                                        Hatua
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="ahadiTableBody">
                            @foreach($ahadi as $record)
                            @php
                                $salio = $record->amount - $record->amount_paid;
                                $statusColor = $salio == 0 ? 'bg-green-100 text-green-800' : ($salio > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800');
                                $statusText = $salio == 0 ? 'Imekamilika' : ($salio > 0 ? 'Sehemu' : 'Bado');
                                $memberName = $record->member->full_name ?? 'N/A';
                            @endphp
                            <tr class="bg-white hover:bg-gray-50 transition-all duration-200">
                                <!-- Member -->
                                <td class="py-4 px-6 sticky left-0 bg-white">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                            <span class="font-medium text-primary-800 text-sm">{{ substr($memberName, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $memberName }}</div>
                                            <div class="text-xs text-gray-500">{{ $record->member->phone ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Type -->
                                <td class="py-4 px-6">
                                    <div class="text-sm text-gray-900">{{ $record->pledge_type ?? 'N/A' }}</div>
                                </td>

                                <!-- Pledge Amount -->
                                <td class="py-4 px-6">
                                    <div class="text-lg font-bold text-purple-600">{{ number_format($record->amount, 0) }}</div>
                                </td>

                                <!-- Paid Amount -->
                                <td class="py-4 px-6">
                                    <div class="text-lg font-bold text-green-600">{{ number_format($record->amount_paid, 0) }}</div>
                                </td>

                                <!-- Balance -->
                                <td class="py-4 px-6">
                                    <div class="text-lg font-bold {{ $salio > 0 ? 'text-yellow-600' : 'text-red-600' }}">
                                        {{ number_format($salio, 0) }}
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                        {{ $statusText }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-6 text-sm sticky right-0 bg-white">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="viewAhadiDetails('{{ $record->id }}')" class="h-8 w-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-200 transition-all duration-200" title="Angalia Maelezo">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        <button onclick="addMalipo('{{ $record->id }}')" class="h-8 w-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-200 transition-all duration-200" title="Ongeza Malipo">
                                            <i class="fas fa-plus text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Empty State -->
                @if($ahadi->count() == 0)
                    <div class="text-center py-12 px-6">
                        <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-hands-praying text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna rekodi za ahadi</h3>
                        <p class="text-gray-500 mb-6">Hakuna rekodi zilizopatikana kwenye kipindi kilichochaguliwa.</p>
                        <button class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 inline-flex" onclick="openModal('ahadiModal')">
                            <i class="fas fa-hands-praying"></i> Ongeza Ahadi
                        </button>
                    </div>
                @endif

                <!-- Pagination -->
                @if($ahadi->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $ahadi->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Sadaka Modal -->
<div id="sadakaModal" class="modal-overlay hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95">
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-xl border-b border-gray-200 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-primary-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-hand-holding-heart text-primary-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Ingiza Sadaka Mpya</h3>
                        <p class="text-sm text-gray-600">Rekodi sadaka ya leo</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('sadakaModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="sadakaForm" action="{{ route('sadaka.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label for="tarehe" class="block text-gray-700 text-sm font-medium mb-1">Tarehe</label>
                    <input type="text" id="tarehe" name="collection_date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" value="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label for="aina_sadaka" class="block text-gray-700 text-sm font-medium mb-1">Aina ya Sadaka</label>
                    <select id="aina_sadaka" name="income_category_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                        <option value="">Chagua aina ya sadaka</option>
                        @foreach(\App\Models\IncomeCategory::all() as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="kiasi" class="block text-gray-700 text-sm font-medium mb-1">Kiasi (TZS)</label>
                    <input type="number" id="kiasi" name="amount" placeholder="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-xl font-bold text-primary-600 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" required>
                </div>
                <div>
                    <label for="notes" class="block text-gray-700 text-sm font-medium mb-1">Maelezo (Hiari)</label>
                    <textarea id="notes" name="notes" rows="2" placeholder="Maelezo..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                </div>
            </div>
            <div class="sticky bottom-0 flex justify-end gap-3 px-6 py-5 bg-gray-50 rounded-b-xl border-t border-gray-200">
                <button type="button" onclick="closeModal('sadakaModal')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">Ghairi</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Hifadhi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Ahadi Modal -->
<div id="ahadiModal" class="modal-overlay hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95">
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-xl border-b border-gray-200 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-hands-praying text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Ahadi Mpya</h3>
                        <p class="text-sm text-gray-600">Rekodi ahadi ya muumini</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('ahadiModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="ahadiForm" action="{{ route('ahadi.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label for="member_id" class="block text-gray-700 text-sm font-medium mb-1">Muumini</label>
                    <select id="member_id" name="member_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Chagua muumini (hiari)</option>
                        @foreach(\App\Models\Member::orderBy('first_name')->get() as $member)
                            <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="pledge_type" class="block text-gray-700 text-sm font-medium mb-1">Aina ya Ahadi</label>
                    <select id="pledge_type" name="pledge_type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                        <option value="">Chagua aina</option>
                        <option value="Kiwanja">Kiwanja</option>
                        <option value="Usiku wa RGC">Usiku wa RGC</option>
                        <option value="Mavuno">Mavuno</option>
                        <option value="Ujenzi">Ujenzi</option>
                        <option value="Nyingine">Nyingine</option>
                    </select>
                </div>
                <div>
                    <label for="kiasi_ahadi" class="block text-gray-700 text-sm font-medium mb-1">Kiasi (TZS)</label>
                    <input type="number" id="kiasi_ahadi" name="amount" placeholder="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-xl font-bold text-purple-600 focus:ring-2 focus:ring-purple-500 focus:border-purple-500" required>
                </div>
                <div>
                    <label for="tarehe_ahadi" class="block text-gray-700 text-sm font-medium mb-1">Tarehe</label>
                    <input type="text" id="tarehe_ahadi" name="pledge_date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            <div class="sticky bottom-0 flex justify-end gap-3 px-6 py-5 bg-gray-50 rounded-b-xl border-t border-gray-200">
                <button type="button" onclick="closeModal('ahadiModal')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">Ghairi</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Hifadhi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Malipo Modal -->
<div id="malipoModal" class="modal-overlay hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95">
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-xl border-b border-gray-200 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-coins text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Rekodi Malipo</h3>
                        <p class="text-sm text-gray-600">Lipa ahadi ya muumini</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('malipoModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="malipoForm" action="{{ route('malipo.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label for="pledge_id" class="block text-gray-700 text-sm font-medium mb-1">Chagua Ahadi</label>
                    <select id="pledge_id" name="pledge_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                        <option value="">Chagua ahadi</option>
                        @foreach(\App\Models\Pledge::with('member')->whereRaw('amount > amount_paid')->get() as $pledge)
                            <option value="{{ $pledge->id }}">
                                {{ $pledge->member->full_name ?? 'N/A' }} - {{ $pledge->pledge_type }} (Salio: TZS {{ number_format($pledge->amount - $pledge->amount_paid, 0) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tarehe_malipo" class="block text-gray-700 text-sm font-medium mb-1">Tarehe</label>
                    <input type="text" id="tarehe_malipo" name="payment_date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" value="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label for="kiasi_malipo" class="block text-gray-700 text-sm font-medium mb-1">Kiasi (TZS)</label>
                    <input type="number" id="kiasi_malipo" name="amount" placeholder="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-xl font-bold text-green-600 focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                </div>
                <div>
                    <label for="payment_method" class="block text-gray-700 text-sm font-medium mb-1">Njia ya Malipo</label>
                    <select id="payment_method" name="payment_method" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                        <option value="">Chagua njia</option>
                        <option value="Taslimu">Pesa Taslimu</option>
                        <option value="M-Pesa">M-Pesa</option>
                        <option value="Tigo Pesa">Tigo Pesa</option>
                        <option value="Benki">Benki</option>
                    </select>
                </div>
            </div>
            <div class="sticky bottom-0 flex justify-end gap-3 px-6 py-5 bg-gray-50 rounded-b-xl border-t border-gray-200">
                <button type="button" onclick="closeModal('malipoModal')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">Ghairi</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Hifadhi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Calculator Modal -->
<div id="calculatorModal" class="modal-overlay hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg transform transition-all duration-300 scale-95">
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-xl border-b border-gray-200 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-calculator text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Kikokotoo cha Noti na Sarafu</h3>
                        <p class="text-sm text-gray-600">Hesabu jumla ya sadaka</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('calculatorModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-1">TZS 10,000</label>
                    <input type="number" id="note_10000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="0" onchange="calculateTotal()">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-1">TZS 5,000</label>
                    <input type="number" id="note_5000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="0" onchange="calculateTotal()">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-1">TZS 2,000</label>
                    <input type="number" id="note_2000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="0" onchange="calculateTotal()">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-1">TZS 1,000</label>
                    <input type="number" id="note_1000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="0" onchange="calculateTotal()">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-1">TZS 500</label>
                    <input type="number" id="coin_500" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="0" onchange="calculateTotal()">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-1">TZS 200</label>
                    <input type="number" id="coin_200" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="0" onchange="calculateTotal()">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-1">TZS 100</label>
                    <input type="number" id="coin_100" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="0" onchange="calculateTotal()">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-medium mb-1">TZS 50</label>
                    <input type="number" id="coin_50" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" value="0" onchange="calculateTotal()">
                </div>
            </div>
            <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 text-center">
                <p class="text-sm text-gray-600 mb-1">Jumla</p>
                <p class="text-2xl font-bold text-blue-600" id="calculatorTotal">TZS 0</p>
            </div>
        </div>
        <div class="sticky bottom-0 flex justify-end gap-3 px-6 py-5 bg-gray-50 rounded-b-xl border-t border-gray-200">
            <button type="button" onclick="resetCalculator()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-redo"></i>
                <span>Safisha</span>
            </button>
            <button type="button" onclick="copyToSadaka()" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-copy"></i>
                <span>Nakili kwa Sadaka</span>
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">

<script>
    // ============================================
    // Helper Functions for Notifications & Modals
    // ============================================

    // Show success notification
    function showSuccess(message, title = 'Imefanikiwa') {
        showNotification(message, title, 'success');
    }

    // Show error notification
    function showError(message, title = 'Hitilafu') {
        showNotification(message, title, 'error');
    }

    // Show warning notification
    function showWarning(message, title = 'Onyo') {
        showNotification(message, title, 'warning');
    }

    // Generic notification function
    function showNotification(message, title, type) {
        const colors = {
            success: { bg: 'bg-green-50', border: 'border-green-400', text: 'text-green-800', icon: 'fa-check-circle text-green-500' },
            error: { bg: 'bg-red-50', border: 'border-red-400', text: 'text-red-800', icon: 'fa-exclamation-circle text-red-500' },
            warning: { bg: 'bg-yellow-50', border: 'border-yellow-400', text: 'text-yellow-800', icon: 'fa-exclamation-triangle text-yellow-500' }
        };
        const color = colors[type] || colors.success;

        const notification = document.createElement('div');
        notification.className = `${color.bg} border-l-4 ${color.border} ${color.text} p-4 rounded-lg shadow-lg fixed top-4 right-4 z-[9999] max-w-md transform transition-all duration-300 translate-x-full`;
        notification.innerHTML = `
            <div class="flex items-start">
                <i class="fas ${color.icon} mr-3 mt-0.5"></i>
                <div class="flex-1">
                    <p class="font-medium">${title}</p>
                    <p class="text-sm mt-1">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-3 hover:opacity-70">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => notification.classList.remove('translate-x-full'), 10);

        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Show confirm dialog (returns Promise)
    function showConfirm(message, title = 'Thibitisha') {
        return new Promise((resolve) => {
            // Create modal backdrop
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[9999]';
            modal.innerHTML = `
                <div class="bg-white rounded-xl w-full max-w-md transform transition-all duration-300 scale-95 opacity-0">
                    <div class="p-5 bg-gradient-to-r from-red-600 to-red-700 rounded-t-xl">
                        <div class="flex items-center">
                            <div class="h-10 w-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-white">${title}</h3>
                        </div>
                    </div>
                    <div class="p-5">
                        <p class="text-gray-700">${message}</p>
                    </div>
                    <div class="flex justify-end gap-3 px-5 py-4 bg-gray-50 rounded-b-xl border-t">
                        <button id="confirmCancel" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all duration-200 font-medium">
                            Ghairi
                        </button>
                        <button id="confirmOk" class="px-5 py-2 bg-red-600 text-white rounded-lg font-bold hover:bg-red-700 transition-all duration-200 flex items-center">
                            <i class="fas fa-check mr-2"></i> Thibitisha
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            // Animate in
            const content = modal.querySelector('.bg-white');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);

            // Handle buttons
            modal.querySelector('#confirmCancel').onclick = () => {
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => modal.remove(), 200);
                resolve(false);
            };

            modal.querySelector('#confirmOk').onclick = () => {
                content.classList.add('scale-95', 'opacity-0');
                setTimeout(() => modal.remove(), 200);
                resolve(true);
            };

            // Close on backdrop click
            modal.onclick = (e) => {
                if (e.target === modal) {
                    content.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => modal.remove(), 200);
                    resolve(false);
                }
            };
        });
    }

    // ============================================
    // Main Application Code
    // ============================================

    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - initializing church finance system');

        // Initialize tabs
        const tabs = {
            sadaka: { tab: document.getElementById('sadakaTab'), container: document.getElementById('sadakaContainer') },
            ahadi: { tab: document.getElementById('ahadiTab'), container: document.getElementById('ahadiContainer') }
        };

        // Initialize tabs from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'sadaka';
        console.log('Active tab:', activeTab);

        // Activate the correct tab on load
        setTimeout(() => {
            switchTab(activeTab);
        }, 100);

        // Tab click events - FIXED: Use proper event delegation
        Object.keys(tabs).forEach(tabKey => {
            const tabElement = tabs[tabKey].tab;
            if (tabElement) {
                tabElement.addEventListener('click', (e) => {
                    e.preventDefault();
                    console.log('Tab clicked:', tabKey);
                    switchTab(tabKey);
                });
            }
        });

        // Search functionality
        const searchElements = [
            { inputId: 'searchSadaka', tableId: 'sadakaTableBody' },
            { inputId: 'searchAhadi', tableId: 'ahadiTableBody' }
        ];

        searchElements.forEach(item => {
            const input = document.getElementById(item.inputId);
            if (input) {
                input.addEventListener('input', function() {
                    filterTable(item.tableId, this.value.toLowerCase());
                });
            }
        });

        // Initialize date pickers
        initializeDatePickers();

        // Initialize calculator totals if calculator exists
        if (document.getElementById('note_10000')) {
            updateNoteTotals();
        }
    });

    // Tab switch function - FIXED
    function switchTab(tabName) {
        console.log('Switching to tab:', tabName);

        // Hide all containers
        const allContainers = document.querySelectorAll('.tab-content');
        allContainers.forEach(c => {
            c.classList.add('hidden');
        });

        // Reset all tabs
        const allTabs = document.querySelectorAll('[role="tab"]');
        allTabs.forEach(t => {
            t.classList.remove('border-primary-500', 'text-primary-600');
            t.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            t.setAttribute('aria-selected', 'false');
        });

        // Activate selected tab and container
        const activeContainer = document.getElementById(tabName + 'Container');
        const activeTab = document.getElementById(tabName + 'Tab');

        console.log('Active Container:', activeContainer);
        console.log('Active Tab:', activeTab);

        if (activeContainer && activeTab) {
            activeContainer.classList.remove('hidden');
            activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            activeTab.classList.add('border-primary-500', 'text-primary-600');
            activeTab.setAttribute('aria-selected', 'true');
            console.log('Tab activated successfully:', tabName);
        } else {
            console.error('Tab or container not found:', tabName);
        }

        // Update URL without page reload
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url);
    }

    // Table filter function
    function filterTable(tableId, searchTerm) {
        const rows = document.querySelectorAll(`#${tableId} tr`);
        let visibleCount = 0;
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const countElement = document.getElementById(tableId.replace('TableBody', 'Count'));
        if (countElement) {
            countElement.textContent = visibleCount;
        }
    }

    // Modal functions - WITH ANIMATION (respects sidebar/header)
    function openModal(modalId) {
        console.log('Opening modal:', modalId);
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            // Animate in - remove scale-95 after a small delay
            setTimeout(() => {
                const content = modal.querySelector('div');
                if (content) {
                    content.classList.remove('scale-95');
                    content.classList.add('scale-100');
                }
            }, 10);
            console.log('Modal opened successfully');
        } else {
            console.error('Modal not found:', modalId);
        }
    }

    function closeModal(modalId) {
        console.log('Closing modal:', modalId);
        const modal = document.getElementById(modalId);
        if (modal) {
            // Animate out - add scale-95 first
            const content = modal.querySelector('div');
            if (content) {
                content.classList.remove('scale-100');
                content.classList.add('scale-95');
            }
            // Then hide after animation completes
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 200);
        }
    }

    // Close modal when clicking outside (on backdrop)
    document.querySelectorAll('[role="dialog"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[role="dialog"]:not(.hidden)').forEach(modal => {
                closeModal(modal.id);
            });
        }
    });

    // Date pickers initialization
    function initializeDatePickers() {
        const dateInputs = [
            { id: 'tarehe', options: { dateFormat: "Y-m-d", locale: "sw" } },
            { id: 'tarehe_ahadi', options: { dateFormat: "Y-m-d", locale: "sw" } },
            { id: 'tarehe_mwisho', options: { dateFormat: "Y-m-d", locale: "sw" } },
            { id: 'tarehe_malipo', options: { dateFormat: "Y-m-d", locale: "sw" } },
            { id: 'calc_collection_date', options: { dateFormat: "Y-m-d", locale: "sw" } }
        ];

        dateInputs.forEach(item => {
            const el = document.getElementById(item.id);
            if (el) {
                flatpickr(el, item.options);
            }
        });
    }

    // Apply filters
    function applyFilters() {
        const year = document.getElementById('yearFilter').value;
        const month = document.getElementById('monthFilter').value;

        const url = new URL(window.location);
        url.searchParams.set('year', year);
        if (month) {
            url.searchParams.set('month', month);
        } else {
            url.searchParams.delete('month');
        }

        // Preserve current tab
        const currentTab = new URLSearchParams(window.location.search).get('tab') || 'sadaka';
        url.searchParams.set('tab', currentTab);

        window.location.href = url.toString();
    }

    // Update salio in malipo modal
    function updateSalio() {
        const select = document.getElementById('ahadi_id');
        if (!select) return;

        const selectedOption = select.options[select.selectedIndex];
        const detailsDiv = document.getElementById('ahadiDetails');

        if (selectedOption && selectedOption.value) {
            const salio = selectedOption.getAttribute('data-salio') || 0;
            const jina = selectedOption.getAttribute('data-jina') || '';
            const ahadi = selectedOption.getAttribute('data-ahadi') || 0;
            const lipwa = selectedOption.getAttribute('data-lipwa') || 0;

            if (document.getElementById('ahadiJina')) {
                document.getElementById('ahadiJina').textContent = jina;
                document.getElementById('ahadiKiasi').textContent = parseInt(ahadi).toLocaleString('en-US') + ' TZS';
                document.getElementById('ahadiLipwa').textContent = parseInt(lipwa).toLocaleString('en-US') + ' TZS';
                document.getElementById('ahadiSalio').textContent = parseInt(salio).toLocaleString('en-US') + ' TZS';

                if (detailsDiv) detailsDiv.classList.remove('hidden');
            }
        } else {
            if (detailsDiv) detailsDiv.classList.add('hidden');
        }
    }

    // Export functions
    function exportSadakaExcel() {
        const year = document.getElementById('yearFilter').value;
        const month = document.getElementById('monthFilter').value;

        let url = '/panel/export-excel/sadaka?year=' + year;
        if (month) {
            url += '&month=' + month;
        }

        window.location.href = url;
    }

    function exportAhadiExcel() {
        const year = document.getElementById('yearFilter').value;
        const month = document.getElementById('monthFilter').value;

        let url = '/panel/export-excel/ahadi?year=' + year;
        if (month) {
            url += '&month=' + month;
        }

        window.location.href = url;
    }

    // Calculator Modal Functions
    function openCalculatorModal() {
        console.log('Opening calculator modal');
        // Reset form
        const calcForm = document.getElementById('calculatorForm');
        if (calcForm) {
            calcForm.reset();
        }
        resetCalculator();

        // Show modal
        const calculatorModal = document.getElementById('calculatorModal');
        if (calculatorModal) {
            calculatorModal.classList.remove('hidden');
            calculatorModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeCalculatorModal() {
        const calculatorModal = document.getElementById('calculatorModal');
        if (calculatorModal) {
            calculatorModal.classList.remove('flex');
            calculatorModal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        resetCalculator();
    }

    function resetCalculator() {
        // Reset all inputs
        const inputIds = ['note_10000', 'note_5000', 'note_2000', 'note_1000', 'note_500', 'coin_500', 'coin_200', 'coin_100', 'coin_50'];
        inputIds.forEach(id => {
            const input = document.getElementById(id);
            if (input) input.value = 0;
        });

        // Reset displays
        const totalDisplay = document.getElementById('total_amount_display');
        if (totalDisplay) totalDisplay.textContent = '0.00';

        const totalAmount = document.getElementById('calc_total_amount');
        if (totalAmount) totalAmount.value = 0;

        const jimboDisplay = document.getElementById('jimbo_amount_display');
        if (jimboDisplay) jimboDisplay.textContent = '0.00';

        const jimboAmount = document.getElementById('jimbo_amount');
        if (jimboAmount) jimboAmount.value = 0;

        const jimboSection = document.getElementById('jimbo_section');
        if (jimboSection) jimboSection.classList.add('hidden');

        // Reset individual totals
        updateNoteTotals();
    }

    // Adjust note/coin quantity with buttons
    function adjustNote(inputId, change) {
        const input = document.getElementById(inputId);
        if (!input) return;

        let currentValue = parseInt(input.value) || 0;
        currentValue += change;
        if (currentValue < 0) currentValue = 0;
        input.value = currentValue;
        calculateTotal();
    }

    // Update individual note/coin totals
    function updateNoteTotals() {
        const notes = [
            { id: 'note_10000', value: 10000, totalId: 'total_10000' },
            { id: 'note_5000', value: 5000, totalId: 'total_5000' },
            { id: 'note_2000', value: 2000, totalId: 'total_2000' },
            { id: 'note_1000', value: 1000, totalId: 'total_1000' },
            { id: 'note_500', value: 500, totalId: 'total_500' },
            { id: 'coin_500', value: 500, totalId: 'total_coin_500' },
            { id: 'coin_200', value: 200, totalId: 'total_coin_200' },
            { id: 'coin_100', value: 100, totalId: 'total_coin_100' },
            { id: 'coin_50', value: 50, totalId: 'total_coin_50' }
        ];

        notes.forEach(note => {
            const input = document.getElementById(note.id);
            const totalElement = document.getElementById(note.totalId);
            if (input && totalElement) {
                const count = parseInt(input.value) || 0;
                const total = count * note.value;
                totalElement.textContent = total.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0}) + ' TZS';
            }
        });
    }

    function calculateTotal() {
        // Get all note values
        const note10000 = parseInt(document.getElementById('note_10000')?.value) || 0;
        const note5000 = parseInt(document.getElementById('note_5000')?.value) || 0;
        const note2000 = parseInt(document.getElementById('note_2000')?.value) || 0;
        const note1000 = parseInt(document.getElementById('note_1000')?.value) || 0;
        const note500 = parseInt(document.getElementById('note_500')?.value) || 0;
        const coin500 = parseInt(document.getElementById('coin_500')?.value) || 0;
        const coin200 = parseInt(document.getElementById('coin_200')?.value) || 0;
        const coin100 = parseInt(document.getElementById('coin_100')?.value) || 0;
        const coin50 = parseInt(document.getElementById('coin_50')?.value) || 0;

        const total = (note10000 * 10000) + (note5000 * 5000) + (note2000 * 2000) +
                      (note1000 * 1000) + (note500 * 500) + (coin500 * 500) +
                      (coin200 * 200) + (coin100 * 100) + (coin50 * 50);

        // Update display - support both calculator modal formats
        const totalDisplay = document.getElementById('total_amount_display');
        if (totalDisplay) {
            totalDisplay.textContent = total.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        }

        // Update simple calculator modal display
        const calculatorTotal = document.getElementById('calculatorTotal');
        if (calculatorTotal) {
            calculatorTotal.textContent = 'TZS ' + total.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        }

        const totalAmount = document.getElementById('calc_total_amount');
        if (totalAmount) totalAmount.value = total;

        // Store total for copyToSadaka function
        window.calculatorTotalValue = total;

        // Update individual note totals
        updateNoteTotals();

        // Check if Sadaka ya Shukrani ya Wiki and calculate 8%
        const selectedOption = document.getElementById('calc_sadaka_type');
        if (selectedOption) {
            const selectedText = selectedOption.options[selectedOption.selectedIndex]?.text || '';
            const jimboSection = document.getElementById('jimbo_section');

            if (selectedText.toLowerCase().includes('shukrani') || selectedText.toLowerCase().includes('wiki')) {
                const jimboAmount = Math.round(total * 0.08);
                const jimboDisplay = document.getElementById('jimbo_amount_display');
                const jimboInput = document.getElementById('jimbo_amount');

                if (jimboDisplay) jimboDisplay.textContent = jimboAmount.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
                if (jimboInput) jimboInput.value = jimboAmount;
                if (jimboSection) jimboSection.classList.remove('hidden');
            } else {
                if (jimboSection) jimboSection.classList.add('hidden');
            }
        }
    }

    function handleSadakaTypeChange() {
        // Recalculate if there's already a total
        const totalAmount = document.getElementById('calc_total_amount');
        if (totalAmount && parseFloat(totalAmount.value) > 0) {
            calculateTotal();
        }
    }

    function submitCalculatorData(event) {
        event.preventDefault();

        const spinner = document.getElementById('calcSpinner');
        if (spinner) spinner.classList.remove('hidden');

        const formData = new FormData(event.target);
        const data = Object.fromEntries(formData);

        // Submit main sadaka
        fetch('{{ route("sadaka.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // If there's jimbo amount, submit that too
                const jimboAmount = parseFloat(document.getElementById('jimbo_amount')?.value) || 0;
                if (jimboAmount > 0) {
                    return fetch('{{ route("sadaka.jimbo.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            amount: jimboAmount,
                            collection_date: data.collection_date,
                            notes: 'Asilimia 8% ya Sadaka ya Shukrani'
                        })
                    });
                }
                return Promise.resolve(result);
            }
            throw new Error(result.message || 'Hitilafu imetokea');
        })
        .then(() => {
            if (spinner) spinner.classList.add('hidden');
            closeCalculatorModal();

            // Show success message
            const successDiv = document.createElement('div');
            successDiv.className = 'bg-green-50 border-l-4 border-green-400 text-green-800 p-4 rounded-lg shadow-sm fixed top-4 right-4 z-50';
            successDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span>Sadaka imehifadhiwa kikamilifu!</span>
                </div>
            `;
            document.body.appendChild(successDiv);
            setTimeout(() => successDiv.remove(), 3000);

            // Reload page
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        })
        .catch(error => {
            if (spinner) spinner.classList.add('hidden');
            showError('Hitilafu: ' + error.message);
        });
    }

    // Close modals when clicking outside - FIXED
    document.addEventListener('click', function(event) {
        // Check if clicked element has class 'fixed' and is a modal backdrop
        if (event.target.classList.contains('fixed') &&
            (event.target.id === 'sadakaModal' ||
             event.target.id === 'ahadiModal' ||
             event.target.id === 'malipoModal' ||
             event.target.id === 'calculatorModal')) {
            closeModal(event.target.id);
        }
    });

    // Close modals with Escape key - FIXED
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modals = ['sadakaModal', 'ahadiModal', 'malipoModal', 'calculatorModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && !modal.classList.contains('hidden')) {
                    closeModal(modalId);
                }
            });
        }
    });

    // Additional view/delete functions
    function viewSadakaDetails(id) {
        window.location.href = '/panel/offerings/' + id;
    }

    async function deleteSadaka(id) {
        const confirmed = await showConfirm('Je, una uhakika unataka kufuta rekodi hii ya sadaka?', 'Thibitisha Kufuta');
        if (confirmed) {
            fetch('/panel/offerings/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Rekodi imefutwa kikamilifu!');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showError('Hitilafu: ' + data.message);
                }
            })
            .catch(error => {
                showError('Hitilafu: ' + error.message);
            });
        }
    }

    function viewAhadiDetails(id) {
        window.location.href = '/panel/ahadi/' + id;
    }

    function addMalipo(id) {
        openModal('malipoModal');
        // Set the selected ahadi
        const select = document.getElementById('ahadi_id');
        if (select) {
            select.value = id;
            updateSalio();
        }
    }

    // Copy calculator total to sadaka modal
    function copyToSadaka() {
        const total = window.calculatorTotalValue || 0;
        if (total <= 0) {
            if (typeof showWarning === 'function') {
                showWarning('Tafadhali ingiza noti au sarafu kwanza.');
            }
            return;
        }

        // Close calculator modal
        closeModal('calculatorModal');

        // Open sadaka modal and set the amount
        openModal('sadakaModal');

        // Set the amount in sadaka form
        const kiasi = document.getElementById('kiasi');
        if (kiasi) {
            kiasi.value = total;
        }

        // Show success message
        if (typeof showSuccess === 'function') {
            showSuccess('Kiasi cha TZS ' + total.toLocaleString() + ' kimenakiliwa kwenye fomu ya sadaka.');
        }
    }
</script>

<style>
    .flatpickr-calendar {
        z-index: 10000 !important;
        background: #ffffff !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }
    .fixed.inset-0 {
        overflow: visible !important;
    }
    #sadakaModal, #ahadiModal, #malipoModal, #calculatorModal {
        z-index: 9999 !important;
    }
    .sticky {
        position: -webkit-sticky;
        position: sticky;
    }
    .rounded-xl {
        border-radius: 1rem;
    }
    .rounded-2xl {
        border-radius: 1.5rem;
    }

    /* Calculator input number hide arrows */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Tab styles */
    [role="tab"] {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    [role="tab"]:hover {
        transform: translateY(-1px);
    }

    /* Modal backdrop - no blur per user request */
</style>
@endsection