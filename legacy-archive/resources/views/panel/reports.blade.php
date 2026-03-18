@extends('layouts.app')

@section('title', 'Ripoti - Mfumo wa Kanisa')
@section('page-title', 'Ripoti za Fedha')
@section('page-subtitle', 'Tengeneza na pakua ripoti za fedha za kitaalamu')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Ripoti za Fedha</h1>
            <p class="text-gray-600 mt-2">Tengeneza na pakua ripoti za fedha za {{ $settings->company_name ?? 'RGC Makabe RGC' }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="toggleGenerateForm()" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800">
                <i class="fas fa-plus"></i>
                <span class="font-medium">Tengeneza Ripoti</span>
            </button>
        </div>
    </div>

    <!-- Quick Report Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Weekly Report Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="h-14 w-14 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-week text-2xl text-blue-600"></i>
                </div>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">Wiki</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Ripoti ya Kila Wiki</h3>
            <p class="text-sm text-gray-600 mb-4">Muhtasari wa mapato na matumizi ya wiki iliyopita</p>
            <div class="flex gap-2">
                <button onclick="generateQuickReport('weekly', 'pdf')" class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button onclick="generateQuickReport('weekly', 'excel')" class="flex-1 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button onclick="printReport('weekly')" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-all duration-200">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>

        <!-- Monthly Report Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="h-14 w-14 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-2xl text-purple-600"></i>
                </div>
                <span class="px-3 py-1 bg-purple-100 text-purple-700 text-sm font-medium rounded-full">Mwezi</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Ripoti ya Kila Mwezi</h3>
            <p class="text-sm text-gray-600 mb-4">Muhtasari wa mapato na matumizi ya mwezi uliopita</p>
            <div class="flex gap-2">
                <button onclick="generateQuickReport('monthly', 'pdf')" class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button onclick="generateQuickReport('monthly', 'excel')" class="flex-1 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button onclick="printReport('monthly')" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-all duration-200">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>

        <!-- Yearly Report Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-4">
                <div class="h-14 w-14 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar text-2xl text-orange-600"></i>
                </div>
                <span class="px-3 py-1 bg-orange-100 text-orange-700 text-sm font-medium rounded-full">Mwaka</span>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Ripoti ya Kila Mwaka</h3>
            <p class="text-sm text-gray-600 mb-4">Muhtasari wa mapato na matumizi ya mwaka mzima</p>
            <div class="flex gap-2">
                <button onclick="generateQuickReport('yearly', 'pdf')" class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button onclick="generateQuickReport('yearly', 'excel')" class="flex-1 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button onclick="printReport('yearly')" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-all duration-200">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jumla ya Faili</p>
                    <p class="text-2xl font-bold text-gray-900">{{ count($recentExports) }}</p>
                </div>
                <div class="h-12 w-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-folder-open text-xl text-primary-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Ripoti za Mapato</p>
                    <p class="text-2xl font-bold text-green-600">{{ collect($recentExports)->where('type', 'mapato')->count() }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-hand-holding-usd text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Ripoti za Matumizi</p>
                    <p class="text-2xl font-bold text-red-600">{{ collect($recentExports)->where('type', 'matumizi')->count() }}</p>
                </div>
                <div class="h-12 w-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-receipt text-xl text-red-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Ripoti za PDF</p>
                    <p class="text-2xl font-bold text-red-600">{{ collect($recentExports)->where('format', 'pdf')->count() }}</p>
                </div>
                <div class="h-12 w-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-pdf text-xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Export Form (Hidden by default) -->
    <div id="generateFormContainer" class="hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center mb-6">
                <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-magic text-primary-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Tengeneza Ripoti Mpya</h3>
                    <p class="text-sm text-gray-600">Jaza maelezo ya ripoti unayotaka kutengeneza</p>
                </div>
                <button onclick="toggleGenerateForm()" class="ml-auto text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form id="generateExportForm" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Report Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Aina ya Ripoti</label>
                        <select name="export_type" id="export_type" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Chagua aina ya ripoti</option>
                            <option value="mapato">Mapato (Michango)</option>
                            <option value="matumizi">Matumizi (Expenses)</option>
                            <option value="mapato_matumizi">Mapato na Matumizi</option>
                            <option value="kiwanja">Kiwanja na Ahadi</option>
                            <option value="custom">Muhtasari wa Kila Kitu</option>
                        </select>
                    </div>

                    <!-- Period Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kipindi</label>
                        <select name="period_type" id="period_type" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Chagua kipindi</option>
                            <option value="weekly">Wiki (Siku 7 zilizopita)</option>
                            <option value="monthly">Mwezi (Siku 30 zilizopita)</option>
                            <option value="yearly">Mwaka (Miezi 12 iliyopita)</option>
                            <option value="custom">Kipindi Maalum</option>
                        </select>
                    </div>

                    <!-- Start Date (shown for custom period) -->
                    <div id="customDateRange" class="hidden md:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tarehe ya Kuanzia</label>
                                <input type="text" name="start_date" id="start_date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Toka...">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tarehe ya Mwisho</label>
                                <input type="text" name="end_date" id="end_date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Mpaka...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Format -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Muundo wa Faili</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-500 transition-all duration-200 format-option" data-format="pdf">
                            <input type="radio" name="export_format" value="pdf" class="hidden">
                            <div class="h-12 w-12 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-file-pdf text-2xl text-red-600"></i>
                            </div>
                            <div>
                                <span class="block font-semibold text-gray-900">PDF</span>
                                <span class="text-xs text-gray-500">Ripoti ya Kitaalamu</span>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition-all duration-200 format-option" data-format="excel">
                            <input type="radio" name="export_format" value="excel" checked class="hidden">
                            <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-file-excel text-2xl text-green-600"></i>
                            </div>
                            <div>
                                <span class="block font-semibold text-gray-900">Excel</span>
                                <span class="text-xs text-gray-500">Faili ya .xlsx</span>
                            </div>
                        </label>
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-all duration-200 format-option" data-format="csv">
                            <input type="radio" name="export_format" value="csv" class="hidden">
                            <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-file-csv text-2xl text-blue-600"></i>
                            </div>
                            <div>
                                <span class="block font-semibold text-gray-900">CSV</span>
                                <span class="text-xs text-gray-500">Faili ya .csv</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- PDF Options (shown when PDF selected) -->
                <div id="pdfOptions" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Chaguo za PDF</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg">
                            <input type="checkbox" name="include_logo" checked class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm text-gray-700">Jumuisha Logo</span>
                        </label>
                        <label class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg">
                            <input type="checkbox" name="include_header" checked class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm text-gray-700">Jumuisha Header ya Kanisa</span>
                        </label>
                        <label class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg">
                            <input type="checkbox" name="include_signature" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm text-gray-700">Nafasi ya Sahihi</span>
                        </label>
                        <label class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg">
                            <input type="checkbox" name="include_watermark" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm text-gray-700">Watermark</span>
                        </label>
                    </div>
                </div>

                <!-- Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Chaguo Zaidi</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg">
                            <input type="checkbox" name="include_charts" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm text-gray-700">Jumuisha Michoro</span>
                        </label>
                        <label class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg">
                            <input type="checkbox" name="include_summary" checked class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm text-gray-700">Jumuisha Muhtasari</span>
                        </label>
                        <label class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg">
                            <input type="checkbox" name="group_by_category" checked class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm text-gray-700">Gawa kwa Kategoria</span>
                        </label>
                        <label class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg">
                            <input type="checkbox" name="include_totals" checked class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                            <span class="text-sm text-gray-700">Jumuisha Jumla</span>
                        </label>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="toggleGenerateForm()" class="px-6 py-2.5 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i>
                        <span>Ghairi</span>
                    </button>
                    <button type="button" onclick="previewReport()" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-eye"></i>
                        <span>Hakiki</span>
                    </button>
                    <button type="button" onclick="generateExport()" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-bolt" id="generateIcon"></i>
                        <span id="generateText">Tengeneza Faili</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-search text-primary-500 mr-2"></i> Tafuta Ripoti
            </h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tafuta</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input id="searchExport" type="text" class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Tafuta kwa jina la faili, aina, au tarehe...">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Aina</label>
                <select id="filterType" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Zote</option>
                    <option value="mapato">Mapato</option>
                    <option value="matumizi">Matumizi</option>
                    <option value="kiwanja">Kiwanja</option>
                    <option value="custom">Nyingine</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <!-- Table Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b border-gray-200">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-file-excel text-primary-500 mr-2"></i> Faili za Ripoti
                    <span class="ml-3 text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                        {{ count($recentExports) }} faili
                    </span>
                </h3>
            </div>
            <div class="mt-3 sm:mt-0 flex items-center gap-2">
                <button onclick="refreshExports()" class="h-8 w-8 bg-gray-100 text-gray-600 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-all duration-200" title="Fresha Orodha">
                    <i class="fas fa-sync-alt text-sm" id="refreshIcon"></i>
                </button>
                <button onclick="openBulkDeleteModal()" id="deleteSelectedBtn" class="h-8 px-3 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200 transition-all duration-200 hidden" title="Futa Faili Zilizochaguliwa">
                    <i class="fas fa-trash text-sm mr-1"></i>
                    <span id="deleteCount">0</span>
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-primary-600 text-white text-sm">
                        <th class="py-4 px-4 text-center font-semibold w-12">
                            <input type="checkbox" id="selectAll" class="w-4 h-4 text-primary-600 bg-white border-gray-300 rounded focus:ring-primary-500">
                        </th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-file mr-2"></i>
                                Jina la Faili
                            </div>
                        </th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-tag mr-2"></i>
                                Aina
                            </div>
                        </th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Maelezo
                            </div>
                        </th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-hdd mr-2"></i>
                                Ukubwa
                            </div>
                        </th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Tarehe
                            </div>
                        </th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider sticky right-0 bg-primary-600">
                            <div class="flex items-center">
                                <i class="fas fa-cogs mr-2"></i>
                                Vitendo
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="exportsTable">
                    @forelse($recentExports as $export)
                    @php
                        $typeColors = [
                            'mapato' => 'bg-green-100 text-green-800',
                            'kiwanja' => 'bg-blue-100 text-blue-800',
                            'matumizi' => 'bg-red-100 text-red-800',
                            'custom' => 'bg-purple-100 text-purple-800'
                        ];
                        $typeColor = $typeColors[$export['type'] ?? 'custom'] ?? 'bg-gray-100 text-gray-800';

                        $typeIcons = [
                            'mapato' => 'fas fa-hand-holding-usd',
                            'kiwanja' => 'fas fa-hands-praying',
                            'matumizi' => 'fas fa-money-bill-wave',
                            'custom' => 'fas fa-chart-pie'
                        ];
                        $typeIcon = $typeIcons[$export['type'] ?? 'custom'] ?? 'fas fa-file-excel';
                    @endphp
                    <tr class="bg-white hover:bg-gray-50 transition-all duration-200 export-row"
                        data-filename="{{ strtolower($export['filename'] ?? '') }}"
                        data-type="{{ strtolower($export['type'] ?? '') }}"
                        data-description="{{ strtolower($export['description'] ?? '') }}">
                        <td class="py-4 px-4 text-center">
                            <input type="checkbox" name="export_ids[]" value="{{ $export['id'] }}" class="export-checkbox w-4 h-4 text-primary-600 bg-gray-100 border-gray-300 rounded focus:ring-primary-500">
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-excel text-green-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 text-sm">{{ $export['filename'] }}</div>
                                    <div class="text-xs text-gray-500 font-mono">{{ $export['id'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $typeColor }}">
                                <i class="{{ $typeIcon }} mr-1.5"></i>
                                {{ ucfirst($export['type'] ?? 'custom') }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="text-sm text-gray-700 max-w-xs truncate">{{ $export['description'] }}</div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="text-sm text-gray-600 font-medium">{{ $export['size'] }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-gray-400"></i>
                                    {{ \Carbon\Carbon::parse($export['date'])->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 sticky right-0 bg-white">
                            <div class="flex items-center space-x-2">
                                @if($export['download_url'] && $export['download_url'] !== '#')
                                <a href="{{ $export['download_url'] }}" class="h-8 w-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-200 transition-all duration-200" title="Pakua Faili">
                                    <i class="fas fa-download text-sm"></i>
                                </a>
                                @else
                                <span class="h-8 w-8 bg-gray-100 text-gray-400 rounded-lg flex items-center justify-center cursor-not-allowed" title="Faili Haipatikani">
                                    <i class="fas fa-download text-sm"></i>
                                </span>
                                @endif
                                <button onclick="deleteExport('{{ $export['id'] }}')" class="h-8 w-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200 transition-all duration-200" title="Futa Faili">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 px-6 text-center">
                            <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-file-excel text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna faili zilizotengenezwa bado</h3>
                            <p class="text-gray-500 mb-6">Anza kwa kutengeneza faili yako ya kwanza ya Excel.</p>
                            <button onclick="toggleGenerateForm()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                                <i class="fas fa-plus mr-2"></i> Tengeneza Ripoti Mpya
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bulk Delete Modal -->
<div id="bulkDeleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-50">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95">
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-xl border-b border-gray-200 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Thibitisha Ufutaji</h3>
                        <p class="text-sm text-gray-600">Hatua hii haiwezi kubatilika</p>
                    </div>
                </div>
                <button type="button" onclick="closeBulkDeleteModal()" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <div class="p-6">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mb-4">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                    <p class="text-sm text-yellow-700">
                        Una uhakika unataka kufuta <span id="bulkDeleteCount" class="font-semibold">0</span> faili zilizochaguliwa?
                    </p>
                </div>
            </div>
        </div>

        <div class="sticky bottom-0 bg-gray-50 px-6 py-5 rounded-b-xl border-t border-gray-200 flex justify-end space-x-3">
            <button onclick="closeBulkDeleteModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">
                Ghairi
            </button>
            <button onclick="confirmBulkDelete()" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-trash"></i>
                <span>Futa Faili</span>
            </button>
        </div>
    </div>
</div>

@include('partials.loading-modal')

<!-- Include Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr
    flatpickr('#start_date', {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        maxDate: new Date()
    });

    flatpickr('#end_date', {
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
        maxDate: new Date()
    });

    // Period type change handler
    const periodType = document.getElementById('period_type');
    const customDateRange = document.getElementById('customDateRange');

    periodType.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateRange.classList.remove('hidden');
        } else {
            customDateRange.classList.add('hidden');
        }
    });

    // Format selection handler
    const formatOptions = document.querySelectorAll('.format-option');
    const pdfOptions = document.getElementById('pdfOptions');

    formatOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active state from all
            formatOptions.forEach(opt => {
                opt.classList.remove('border-red-500', 'border-green-500', 'border-blue-500', 'bg-gray-50');
                opt.classList.add('border-gray-200');
            });

            // Add active state to clicked
            const format = this.dataset.format;
            const colorClass = format === 'pdf' ? 'border-red-500' : (format === 'excel' ? 'border-green-500' : 'border-blue-500');
            this.classList.remove('border-gray-200');
            this.classList.add(colorClass, 'bg-gray-50');

            // Check the radio
            this.querySelector('input[type="radio"]').checked = true;

            // Show/hide PDF options
            if (format === 'pdf') {
                pdfOptions.classList.remove('hidden');
            } else {
                pdfOptions.classList.add('hidden');
            }
        });
    });

    // Set initial active state for Excel (default)
    const excelOption = document.querySelector('.format-option[data-format="excel"]');
    if (excelOption) {
        excelOption.classList.remove('border-gray-200');
        excelOption.classList.add('border-green-500', 'bg-gray-50');
    }

    // Search functionality
    const searchInput = document.getElementById('searchExport');
    const filterType = document.getElementById('filterType');

    function filterRows() {
        const searchValue = searchInput.value.toLowerCase();
        const typeValue = filterType.value.toLowerCase();
        const rows = document.querySelectorAll('.export-row');

        rows.forEach(row => {
            const filename = row.dataset.filename || '';
            const type = row.dataset.type || '';
            const description = row.dataset.description || '';

            const matchesSearch = filename.includes(searchValue) ||
                                type.includes(searchValue) ||
                                description.includes(searchValue);
            const matchesType = typeValue === '' || type === typeValue;

            row.style.display = matchesSearch && matchesType ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterRows);
    filterType.addEventListener('change', filterRows);

    // Checkbox functionality
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.export-checkbox');
    const deleteBtn = document.getElementById('deleteSelectedBtn');
    const deleteCount = document.getElementById('deleteCount');

    function updateDeleteButton() {
        const checked = document.querySelectorAll('.export-checkbox:checked').length;
        if (checked > 0) {
            deleteBtn.classList.remove('hidden');
            deleteCount.textContent = checked;
        } else {
            deleteBtn.classList.add('hidden');
        }
    }

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateDeleteButton();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateDeleteButton);
    });
});

// Toggle generate form
function toggleGenerateForm() {
    const form = document.getElementById('generateFormContainer');
    form.classList.toggle('hidden');
}

// Refresh exports
function refreshExports() {
    const icon = document.getElementById('refreshIcon');
    icon.classList.add('fa-spin');
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

// Generate quick report (for quick cards)
function generateQuickReport(period, format) {
    const loadingModal = document.getElementById('loadingModal');
    const progressBar = document.getElementById('progressBar');
    const loadingMessage = document.getElementById('loadingMessage');

    loadingMessage.textContent = format === 'pdf' ? 'Inatengeneza ripoti ya PDF...' : 'Inatengeneza faili ya Excel...';
    loadingModal.classList.remove('hidden');

    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;
        progressBar.style.width = progress + '%';
    }, 200);

    fetch('/panel/reports/quick-export', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            type: 'mapato_matumizi',
            period: period,
            format: format,
            include_logo: true,
            include_header: true
        })
    })
    .then(response => response.json())
    .then(data => {
        clearInterval(interval);
        progressBar.style.width = '100%';

        setTimeout(() => {
            loadingModal.classList.add('hidden');
            progressBar.style.width = '0%';
            if (data.success) {
                if (data.download_url && data.download_url !== '#') {
                    window.location.href = data.download_url;
                }
                setTimeout(() => location.reload(), 500);
            } else {
                showError(data.message || 'Hitilafu imetokea!');
            }
        }, 500);
    })
    .catch(error => {
        clearInterval(interval);
        loadingModal.classList.add('hidden');
        progressBar.style.width = '0%';
        showError('Hitilafu ya mtandao!', 'Hitilafu ya Uunganisho');
    });
}

// Print report
function printReport(period) {
    const printWindow = window.open('/panel/reports/print?period=' + period, '_blank');
    if (printWindow) {
        printWindow.focus();
    }
}

// Preview report
function previewReport() {
    const exportType = document.getElementById('export_type').value;
    const periodType = document.getElementById('period_type').value;

    if (!exportType || !periodType) {
        showWarning('Tafadhali chagua aina ya ripoti na kipindi.', 'Taarifa Zinahitajika');
        return;
    }

    let url = '/panel/reports/preview?type=' + exportType + '&period=' + periodType;

    if (periodType === 'custom') {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        url += '&start_date=' + startDate + '&end_date=' + endDate;
    }

    window.open(url, '_blank');
}

// Generate export
function generateExport() {
    const exportType = document.getElementById('export_type').value;
    const periodType = document.getElementById('period_type').value;
    const exportFormat = document.querySelector('input[name="export_format"]:checked').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (!exportType || !periodType) {
        showWarning('Tafadhali jaza aina ya ripoti na kipindi.', 'Taarifa Zinahitajika');
        return;
    }

    if (periodType === 'custom' && (!startDate || !endDate)) {
        showWarning('Tafadhali jaza tarehe ya kuanzia na ya mwisho.', 'Tarehe Zinahitajika');
        return;
    }

    // Gather form options
    const formData = {
        type: exportType,
        period: periodType,
        format: exportFormat,
        start_date: startDate,
        end_date: endDate,
        include_logo: document.querySelector('input[name="include_logo"]')?.checked ?? true,
        include_header: document.querySelector('input[name="include_header"]')?.checked ?? true,
        include_signature: document.querySelector('input[name="include_signature"]')?.checked ?? false,
        include_watermark: document.querySelector('input[name="include_watermark"]')?.checked ?? false,
        include_charts: document.querySelector('input[name="include_charts"]')?.checked ?? false,
        include_summary: document.querySelector('input[name="include_summary"]')?.checked ?? true,
        group_by_category: document.querySelector('input[name="group_by_category"]')?.checked ?? true,
        include_totals: document.querySelector('input[name="include_totals"]')?.checked ?? true
    };

    // Show loading
    const loadingModal = document.getElementById('loadingModal');
    const progressBar = document.getElementById('progressBar');
    const loadingMessage = document.getElementById('loadingMessage');

    loadingMessage.textContent = exportFormat === 'pdf' ? 'Inatengeneza ripoti ya PDF...' : 'Inatengeneza faili...';
    loadingModal.classList.remove('hidden');

    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;
        progressBar.style.width = progress + '%';
    }, 200);

    fetch('/panel/reports/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        clearInterval(interval);
        progressBar.style.width = '100%';

        setTimeout(() => {
            loadingModal.classList.add('hidden');
            progressBar.style.width = '0%';
            if (data.success) {
                if (data.download_url && data.download_url !== '#') {
                    window.location.href = data.download_url;
                }
                setTimeout(() => location.reload(), 500);
            } else {
                showError(data.message || 'Hitilafu imetokea!');
            }
        }, 500);
    })
    .catch(error => {
        clearInterval(interval);
        loadingModal.classList.add('hidden');
        progressBar.style.width = '0%';
        showError('Hitilafu ya mtandao!', 'Hitilafu ya Uunganisho');
    });
}

// Delete single export
async function deleteExport(id) {
    const confirmed = await showConfirm('Una uhakika unataka kufuta faili hii?', 'Thibitisha Ufutaji');
    if (confirmed) {
        fetch(`/panel/export-excel/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Faili imefutwa kikamilifu!');
                setTimeout(() => location.reload(), 1000);
            } else {
                showError('Hitilafu imetokea wakati wa kufuta faili.');
            }
        });
    }
}

// Bulk delete functions
function openBulkDeleteModal() {
    const count = document.querySelectorAll('.export-checkbox:checked').length;
    if (count === 0) {
        showWarning('Tafadhali chagua angalau faili moja.', 'Hakuna Faili Iliyochaguliwa');
        return;
    }
    document.getElementById('bulkDeleteCount').textContent = count;
    document.getElementById('bulkDeleteModal').classList.remove('hidden');
}

function closeBulkDeleteModal() {
    document.getElementById('bulkDeleteModal').classList.add('hidden');
}

function confirmBulkDelete() {
    const selectedIds = Array.from(document.querySelectorAll('.export-checkbox:checked')).map(cb => cb.value);

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('export.excel.bulk-delete') }}';

    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);

    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);

    const idsInput = document.createElement('input');
    idsInput.type = 'hidden';
    idsInput.name = 'export_ids';
    idsInput.value = selectedIds.join(',');
    form.appendChild(idsInput);

    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
