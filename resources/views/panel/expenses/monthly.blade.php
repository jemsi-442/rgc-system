@extends('layouts.app')

@section('title', 'Matumizi - ' . $monthName . ' ' . $year)
@section('page-title', 'Matumizi ya ' . $monthName)
@section('page-subtitle', 'Rekodi za matumizi ya mwezi wa ' . $monthName . ' ' . $year)

@php
    $dayNames = ['Jumapili', 'Jumatatu', 'Jumanne', 'Jumatano', 'Alhamisi', 'Ijumaa', 'Jumamosi'];
    $colorClasses = [
        'red' => ['bg' => 'bg-gradient-to-br from-red-50/80 to-red-100/80', 'text' => 'text-red-700', 'border' => 'border-red-200', 'dot' => 'bg-red-500', 'gradient' => 'from-red-500 to-red-600', 'light' => 'bg-red-50'],
        'blue' => ['bg' => 'bg-gradient-to-br from-blue-50/80 to-blue-100/80', 'text' => 'text-blue-700', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500', 'gradient' => 'from-blue-500 to-blue-600', 'light' => 'bg-blue-50'],
        'green' => ['bg' => 'bg-gradient-to-br from-green-50/80 to-emerald-100/80', 'text' => 'text-green-700', 'border' => 'border-green-200', 'dot' => 'bg-green-500', 'gradient' => 'from-green-500 to-emerald-600', 'light' => 'bg-green-50'],
        'purple' => ['bg' => 'bg-gradient-to-br from-purple-50/80 to-violet-100/80', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'dot' => 'bg-purple-500', 'gradient' => 'from-purple-500 to-violet-600', 'light' => 'bg-purple-50'],
        'orange' => ['bg' => 'bg-gradient-to-br from-orange-50/80 to-amber-100/80', 'text' => 'text-orange-700', 'border' => 'border-orange-200', 'dot' => 'bg-orange-500', 'gradient' => 'from-orange-500 to-amber-600', 'light' => 'bg-orange-50'],
        'pink' => ['bg' => 'bg-gradient-to-br from-pink-50/80 to-rose-100/80', 'text' => 'text-pink-700', 'border' => 'border-pink-200', 'dot' => 'bg-pink-500', 'gradient' => 'from-pink-500 to-rose-600', 'light' => 'bg-pink-50'],
        'teal' => ['bg' => 'bg-gradient-to-br from-teal-50/80 to-cyan-100/80', 'text' => 'text-teal-700', 'border' => 'border-teal-200', 'dot' => 'bg-teal-500', 'gradient' => 'from-teal-500 to-cyan-600', 'light' => 'bg-teal-50'],
        'indigo' => ['bg' => 'bg-gradient-to-br from-indigo-50/80 to-blue-100/80', 'text' => 'text-indigo-700', 'border' => 'border-indigo-200', 'dot' => 'bg-indigo-500', 'gradient' => 'from-indigo-500 to-blue-600', 'light' => 'bg-indigo-50'],
        'amber' => ['bg' => 'bg-gradient-to-br from-amber-50/80 to-yellow-100/80', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'dot' => 'bg-amber-500', 'gradient' => 'from-amber-500 to-yellow-600', 'light' => 'bg-amber-50'],
        'cyan' => ['bg' => 'bg-gradient-to-br from-cyan-50/80 to-sky-100/80', 'text' => 'text-cyan-700', 'border' => 'border-cyan-200', 'dot' => 'bg-cyan-500', 'gradient' => 'from-cyan-500 to-sky-600', 'light' => 'bg-cyan-50'],
        'lime' => ['bg' => 'bg-gradient-to-br from-lime-50/80 to-green-100/80', 'text' => 'text-lime-700', 'border' => 'border-lime-200', 'dot' => 'bg-lime-500', 'gradient' => 'from-lime-500 to-green-600', 'light' => 'bg-lime-50'],
        'emerald' => ['bg' => 'bg-gradient-to-br from-emerald-50/80 to-green-100/80', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500', 'gradient' => 'from-emerald-500 to-green-600', 'light' => 'bg-emerald-50'],
        'violet' => ['bg' => 'bg-gradient-to-br from-violet-50/80 to-purple-100/80', 'text' => 'text-violet-700', 'border' => 'border-violet-200', 'dot' => 'bg-violet-500', 'gradient' => 'from-violet-500 to-purple-600', 'light' => 'bg-violet-50'],
        'fuchsia' => ['bg' => 'bg-gradient-to-br from-fuchsia-50/80 to-pink-100/80', 'text' => 'text-fuchsia-700', 'border' => 'border-fuchsia-200', 'dot' => 'bg-fuchsia-500', 'gradient' => 'from-fuchsia-500 to-pink-600', 'light' => 'bg-fuchsia-50'],
        'rose' => ['bg' => 'bg-gradient-to-br from-rose-50/80 to-red-100/80', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'dot' => 'bg-rose-500', 'gradient' => 'from-rose-500 to-red-600', 'light' => 'bg-rose-50'],
        'sky' => ['bg' => 'bg-gradient-to-br from-sky-50/80 to-blue-100/80', 'text' => 'text-sky-700', 'border' => 'border-sky-200', 'dot' => 'bg-sky-500', 'gradient' => 'from-sky-500 to-blue-600', 'light' => 'bg-sky-50'],
    ];
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('expenses.index', ['year' => $year]) }}"
                   class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg
                          hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Rudi Orodhani
                </a>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                <span class="bg-gradient-to-r from-primary-600 to-primary-800 bg-clip-text text-transparent">
                    {{ $monthName }} {{ $year }}
                </span>
                <i class="fas fa-calendar-alt text-primary-500"></i>
            </h1>
            <p class="text-gray-600 mt-2">Kalenda ya matumizi kwa mwezi wa {{ $monthName }}</p>
        </div>

        <!-- Clean Navigation Buttons -->
        <div class="flex flex-wrap gap-3 items-center">
            @php
                $prevMonth = $month - 1;
                $prevYear = $year;
                if ($prevMonth < 1) {
                    $prevMonth = 12;
                    $prevYear--;
                }
                $nextMonth = $month + 1;
                $nextYear = $year;
                if ($nextMonth > 12) {
                    $nextMonth = 1;
                    $nextYear++;
                }
            @endphp

            <!-- Previous Month Button - Clean Design -->
            <a href="{{ route('expenses.monthly', ['year' => $prevYear, 'month' => $prevMonth]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-50 text-primary-700 border border-primary-200
                      rounded-lg hover:bg-primary-100 hover:border-primary-300 hover:text-primary-800
                      transition-all duration-200 shadow-sm hover:shadow">
                <i class="fas fa-chevron-left text-sm"></i>
                <span class="font-medium">Mwezi Uliopita</span>
            </a>

            <!-- Current Month Display -->
            <div class="hidden sm:flex items-center gap-3">
                <div class="h-9 w-0.5 bg-gray-300"></div>
                <div class="text-center px-3">
                    <div class="text-lg font-bold text-gray-900">{{ $monthName }}</div>
                    <div class="text-xs text-gray-500">{{ $year }}</div>
                </div>
                <div class="h-9 w-0.5 bg-gray-300"></div>
            </div>

            <!-- Next Month Button - Clean Design -->
            <a href="{{ route('expenses.monthly', ['year' => $nextYear, 'month' => $nextMonth]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-50 text-primary-700 border border-primary-200
                      rounded-lg hover:bg-primary-100 hover:border-primary-300 hover:text-primary-800
                      transition-all duration-200 shadow-sm hover:shadow">
                <span class="font-medium">Mwezi Ujao</span>
                <i class="fas fa-chevron-right text-sm"></i>
            </a>

            <!-- Add Expense Button (Admin only) -->
            @if(Auth::user()->isMchungaji() || Auth::user()->isMhasibu())
            <a href="{{ route('expenses.create', ['year' => $year, 'month' => $month]) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary-600 text-white border border-primary-600
                      rounded-lg hover:bg-primary-700 hover:border-primary-700 transition-all duration-200
                      shadow-sm hover:shadow">
                <i class="fas fa-plus"></i>
                <span class="font-medium">Ongeza Matumizi</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Amount -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jumla ya Matumizi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalAmount, 0) }} TSh</p>
                </div>
                <div class="h-12 w-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-money-bill-wave text-xl text-red-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Records -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Idadi ya Rekodi</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $totalCount }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-receipt text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <!-- Categories Count -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Aina za Matumizi</p>
                    <p class="text-2xl font-bold text-green-600">{{ $categoryCount }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-tags text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Average per Record -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Wastani kwa Rekodi</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $totalCount > 0 ? number_format($totalAmount / $totalCount, 0) : 0 }} TSh</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Calendar Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <!-- Calendar Header -->
        <div class="bg-gradient-to-r from-primary-600 via-primary-700 to-primary-800 text-white p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 bg-white/15 rounded-xl flex items-center justify-center border border-white/20">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">{{ $monthName }} {{ $year }}</h2>
                        <p class="text-primary-100 text-sm">Kalenda ya Matumizi</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-primary-100 text-sm">Jumla ya Mwezi</p>
                    <p class="text-xl font-bold">{{ number_format($totalAmount, 0) }} TSh</p>
                </div>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="p-4 md:p-6">
            <!-- Day Names Header -->
            <div class="grid grid-cols-7 gap-1 mb-2">
                @foreach($dayNames as $dayName)
                    <div class="text-center py-2 text-sm font-semibold text-gray-600 bg-gray-50 rounded-lg border border-gray-100">
                        {{ $dayName }}
                    </div>
                @endforeach
            </div>

            <!-- Calendar Days -->
            <div class="grid grid-cols-7 gap-1">
                <!-- Empty cells before first day -->
                @for($i = 0; $i < $startDayOfWeek; $i++)
                    <div class="aspect-square bg-gray-50 rounded-lg border border-gray-100"></div>
                @endfor

                <!-- Days of the month -->
                @for($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $hasExpenses = isset($daysWithExpenses[$day]);
                        $dayData = $hasExpenses ? $daysWithExpenses[$day] : null;
                        $isToday = ($year == date('Y') && $month == date('n') && $day == date('j'));
                        $isWeekend = (($startDayOfWeek + $day - 1) % 7) >= 5;
                    @endphp

                    <div class="calendar-day relative aspect-square rounded-lg border transition-all duration-200
                        {{ $hasExpenses ?
                            'bg-gradient-to-b from-white to-gray-50 hover:bg-gray-50 hover:shadow-md hover:border-gray-300 cursor-pointer ' .
                            ($isToday ? 'border-primary-300 bg-primary-50/30' : 'border-gray-200')
                            :
                            'bg-white ' .
                            ($isToday ? 'border-primary-200 bg-primary-50/20' : 'border-gray-100')
                        }}
                        {{ $isWeekend && !$hasExpenses ? 'bg-gray-50/50' : '' }}
                        group"
                        @if($hasExpenses)
                            data-day="{{ $day }}"
                        @endif
                    >
                        <!-- Day number -->
                        <div class="absolute top-1.5 left-1.5">
                            <span class="text-sm font-medium {{ $isToday ? 'text-primary-600' : ($hasExpenses ? 'text-gray-800' : ($isWeekend ? 'text-gray-500' : 'text-gray-700')) }}">
                                {{ $day }}
                            </span>
                            @if($isToday)
                                <span class="ml-1 h-1.5 w-1.5 bg-primary-500 rounded-full inline-block"></span>
                            @endif
                        </div>

                        @if($hasExpenses)
                            <!-- Expense indicators - VISIBLE DESIGN -->
                            <div class="absolute bottom-1.5 left-1.5 right-1.5">
                                <!-- Expense total amount - ALWAYS VISIBLE -->
                                <div class="mb-1">
                                    <div class="text-xs font-semibold text-gray-900 text-right">
                                        {{ number_format($dayData['total'], 0) }}
                                        <span class="text-[10px] text-gray-500">TSh</span>
                                    </div>
                                </div>

                                <!-- Expense category dots - ALWAYS VISIBLE -->
                                <div class="flex flex-wrap gap-0.5 justify-end">
                                    @foreach($dayData['expenses']->take(3) as $expense)
                                        @php
                                            $catColor = $categoriesWithColors[$expense->expense_category_id]['color'] ?? 'blue';
                                        @endphp
                                        <span class="h-2 w-2 rounded-full {{ $colorClasses[$catColor]['dot'] ?? 'bg-blue-500' }}"
                                              title="{{ $expense->category->name ?? 'Bila Aina' }}: {{ number_format($expense->amount, 0) }} TSh">
                                        </span>
                                    @endforeach
                                    @if($dayData['count'] > 3)
                                        <span class="text-[10px] text-gray-400">+{{ $dayData['count'] - 3 }}</span>
                                    @endif
                                </div>

                                <!-- Mini expense breakdown - VISIBLE ON HOVER -->
                                <div class="absolute -top-8 left-0 right-0 bg-white rounded-lg border border-gray-200 shadow-lg p-2
                                            opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10">
                                    <div class="text-xs font-medium text-gray-900 mb-1">Matumizi ya {{ $day }}</div>
                                    @foreach($dayData['expenses']->take(2) as $expense)
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-gray-600 truncate">{{ $expense->category->name ?? 'Bila Aina' }}</span>
                                            <span class="font-medium text-gray-900">{{ number_format($expense->amount, 0) }}</span>
                                        </div>
                                    @endforeach
                                    @if($dayData['count'] > 2)
                                        <div class="text-xs text-gray-400 text-center mt-1">
                                            +{{ $dayData['count'] - 2 }} zaidi
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Tooltip Card - Using ACTUAL DATA from Database -->
                            <div class="tooltip-container absolute z-50 w-80 bg-white rounded-xl border border-gray-200 shadow-2xl
                                transform -translate-x-1/2 left-1/2 opacity-0 pointer-events-none
                                group-hover:opacity-100 group-hover:pointer-events-auto transition-all duration-200"
                                style="bottom: calc(100% + 10px);">

                                <!-- Tooltip Arrow -->
                                <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-4 h-4 bg-white border-r border-b border-gray-200 transform rotate-45"></div>

                                <!-- Tooltip Header - SAME COLOR AS CALENDAR HEADER -->
                                <div class="bg-gradient-to-r from-primary-600 via-primary-700 to-primary-800 text-white p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 bg-white/15 rounded-lg flex items-center justify-center">
                                                <span class="font-bold">{{ $day }}</span>
                                            </div>
                                            <div>
                                                <div class="font-bold text-sm">{{ $monthName }}</div>
                                                <div class="text-primary-100 text-xs">{{ $dayData['count'] }} rekodi</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs text-primary-100">Jumla</div>
                                            <div class="font-bold">{{ number_format($dayData['total'], 0) }} TSh</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tooltip Content - REAL DATA -->
                                <div class="p-4 max-h-64 overflow-y-auto">
                                    <div class="space-y-3">
                                        @foreach($dayData['expenses'] as $expense)
                                            @php
                                                $catColor = $categoriesWithColors[$expense->expense_category_id]['color'] ?? 'blue';
                                            @endphp
                                            <div class="flex items-start gap-3 p-3 rounded-lg {{ $colorClasses[$catColor]['light'] ?? 'bg-gray-50' }} border border-gray-100">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-lg bg-gradient-to-br {{ $colorClasses[$catColor]['gradient'] ?? 'from-blue-500 to-blue-600' }} flex items-center justify-center text-white">
                                                    <i class="fas fa-receipt text-xs"></i>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between mb-1">
                                                        <span class="text-xs font-semibold {{ $colorClasses[$catColor]['text'] ?? 'text-gray-700' }} truncate">
                                                            {{ $expense->category->name ?? 'Bila Aina' }}
                                                        </span>
                                                        <span class="font-bold text-gray-900 text-sm">{{ number_format($expense->amount, 0) }} TSh</span>
                                                    </div>
                                                    @if($expense->payee)
                                                        <div class="text-xs text-gray-600 flex items-center gap-1">
                                                            <i class="fas fa-user text-gray-400 text-[10px]"></i>
                                                            <span class="truncate">{{ $expense->payee }}</span>
                                                        </div>
                                                    @endif
                                                    @if($expense->notes)
                                                        <div class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $expense->notes }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Tooltip Footer (Admin only) -->
                                @if(Auth::user()->isMchungaji() || Auth::user()->isMhasibu())
                                <div class="p-3 bg-gray-50 border-t border-gray-100">
                                    <a href="{{ route('expenses.create', ['year' => $year, 'month' => $month, 'day' => $day]) }}"
                                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                                        <i class="fas fa-plus"></i>
                                        Ongeza Matumizi
                                    </a>
                                </div>
                                @endif
                            </div>
                        @else
                            <!-- Empty day state -->
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-gray-300 group-hover:text-gray-400 transition-colors">
                                    <i class="fas fa-plus text-sm"></i>
                                </div>
                            </div>
                        @endif
                    </div>
                @endfor

                <!-- Empty cells after last day -->
                @php
                    $totalCells = $startDayOfWeek + $daysInMonth;
                    $remainingCells = (7 - ($totalCells % 7)) % 7;
                @endphp
                @for($i = 0; $i < $remainingCells; $i++)
                    <div class="aspect-square bg-gray-50 rounded-lg border border-gray-100"></div>
                @endfor
            </div>
        </div>

        <!-- Calendar Legend -->
        @if($totalCount > 0)
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-4 flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 bg-primary-500 rounded-full"></div>
                        <span class="text-xs text-gray-600">Leo</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 bg-blue-500 rounded-full"></div>
                        <span class="text-xs text-gray-600">Na Matumizi</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 bg-gray-300 rounded-full"></div>
                        <span class="text-xs text-gray-600">Bila Matumizi</span>
                    </div>
                </div>
                <div class="text-sm font-medium text-gray-700">
                    <span class="text-gray-500">Jumla:</span>
                    <span class="ml-2 font-bold text-primary-600">{{ number_format($totalAmount, 0) }} TSh</span>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Empty State -->
    @if($totalCount == 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
        <div class="mx-auto w-20 h-20 mb-6 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
            <i class="fas fa-calendar-times text-gray-400 text-3xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Hakuna Matumizi Yaliyopatikana</h3>
        @if(Auth::user()->isMchungaji() || Auth::user()->isMhasibu())
        <p class="text-gray-500 mb-6 max-w-md mx-auto">Hakuna matumizi yaliyorekodiwa kwa mwezi wa {{ $monthName }} {{ $year }}. Bofya kitufe hapa chini kuongeza matumizi ya kwanza.</p>
        <a href="{{ route('expenses.create', ['year' => $year, 'month' => $month]) }}"
           class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition-all duration-200 shadow-lg hover:shadow-xl">
            <i class="fas fa-plus mr-2"></i> Ongeza Matumizi ya Kwanza
        </a>
        @else
        <p class="text-gray-500 max-w-md mx-auto">Hakuna matumizi yaliyorekodiwa kwa mwezi wa {{ $monthName }} {{ $year }}.</p>
        @endif
    </div>
    @endif

    <!-- Category Summary Card -->
    @if($categoryCount > 0)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Muhtasari wa Aina za Matumizi</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($categoriesWithColors as $category)
                    @if(isset($category['total']) && $category['total'] > 0)
                        <div class="p-4 rounded-lg border border-gray-200 hover:border-gray-300 transition-colors">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="h-10 w-10 rounded-lg {{ $colorClasses[$category['color'] ?? 'blue']['bg'] }} flex items-center justify-center">
                                    <i class="fas {{ $category['icon'] ?? 'fa-tag' }} {{ $colorClasses[$category['color'] ?? 'blue']['text'] }}"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $category['name'] }}</div>
                                    <div class="text-sm text-gray-500">{{ $category['count'] }} rekodi</div>
                                </div>
                            </div>
                            <div class="text-lg font-bold {{ $colorClasses[$category['color'] ?? 'blue']['text'] }}">
                                {{ number_format($category['total'], 0) }} TSh
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<style>
    /* Remove horizontal overflow */
    html, body {
        overflow-x: hidden !important;
    }

    /* Calendar styles */
    .calendar-day {
        min-height: 0;
        aspect-ratio: 1;
    }

    /* Smooth tooltip transitions */
    .tooltip-container {
        transition: opacity 200ms ease-out, transform 200ms ease-out;
        border-radius: 12px;
        z-index: 99999 !important;
    }

    /* Custom scrollbar for tooltip */
    .tooltip-container .overflow-y-auto {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
    }

    .tooltip-container .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }

    .tooltip-container .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .tooltip-container .overflow-y-auto::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 3px;
    }

    .tooltip-container .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }

    /* Mobile responsive adjustments */
    @media (max-width: 640px) {
        .calendar-day {
            min-height: 60px;
        }

        .tooltip-container {
            position: fixed !important;
            left: 50% !important;
            top: 50% !important;
            bottom: auto !important;
            transform: translate(-50%, -50%) !important;
            width: 90vw !important;
            max-width: 320px;
            max-height: 70vh;
            z-index: 9999 !important;
        }

        .tooltip-container .absolute.-bottom-2 {
            display: none;
        }
    }

    /* Print styles */
    @media print {
        .calendar-day {
            break-inside: avoid;
        }

        .tooltip-container {
            display: none !important;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle mobile tooltips
    let activeTooltip = null;
    const calendarDays = document.querySelectorAll('.calendar-day[data-day]');

    calendarDays.forEach(day => {
        // Touch support for mobile
        day.addEventListener('touchstart', function(e) {
            e.preventDefault();

            // Close any open tooltip
            if (activeTooltip) {
                activeTooltip.classList.remove('opacity-100', 'pointer-events-auto');
            }

            // Open this tooltip
            const tooltip = this.querySelector('.tooltip-container');
            if (tooltip) {
                tooltip.classList.add('opacity-100', 'pointer-events-auto');
                activeTooltip = tooltip;

                // Position tooltip for mobile
                if (window.innerWidth <= 640) {
                    tooltip.style.position = 'fixed';
                    tooltip.style.left = '50%';
                    tooltip.style.top = '50%';
                    tooltip.style.bottom = 'auto';
                    tooltip.style.transform = 'translate(-50%, -50%)';
                }
            }
        });
    });

    // Close tooltip when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 640 && activeTooltip &&
            !activeTooltip.contains(e.target) &&
            !e.target.closest('.calendar-day[data-day]')) {
            activeTooltip.classList.remove('opacity-100', 'pointer-events-auto');
            activeTooltip = null;
        }
    });

    // Adjust tooltip position near edges on desktop
    function adjustTooltipPosition() {
        if (window.innerWidth > 640) {
            document.querySelectorAll('.tooltip-container').forEach(tooltip => {
                const rect = tooltip.getBoundingClientRect();

                if (rect.right > window.innerWidth - 20) {
                    tooltip.style.left = 'auto';
                    tooltip.style.right = '0';
                    tooltip.style.transform = 'translateX(0)';
                } else if (rect.left < 20) {
                    tooltip.style.left = '0';
                    tooltip.style.right = 'auto';
                    tooltip.style.transform = 'translateX(0)';
                } else {
                    tooltip.style.left = '50%';
                    tooltip.style.right = 'auto';
                    tooltip.style.transform = 'translateX(-50%)';
                }
            });
        }
    }

    // Initialize and adjust on resize
    adjustTooltipPosition();
    window.addEventListener('resize', adjustTooltipPosition);
});
</script>
@endsection
