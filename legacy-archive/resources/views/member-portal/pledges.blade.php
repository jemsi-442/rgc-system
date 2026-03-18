@extends('layouts.app')

@section('title', 'Ahadi Zangu')
@section('page-title', 'Ahadi Zangu')
@section('page-subtitle', 'Fuatilia ahadi zako na maendeleo ya malipo')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Ahadi Zangu</h1>
            <p class="text-gray-600 mt-2">Fuatilia ahadi zako na maendeleo ya malipo</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('member.portal') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Rudi Nyumbani</span>
            </a>
        </div>
    </div>

    @if($pledges->count() > 0)
        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @php
                $totalPledged = $pledges->sum('amount');
                $totalPaid = $pledges->sum('amount_paid');
                $totalRemaining = $pledges->sum('remaining_amount');
            @endphp
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Jumla ya Ahadi</p>
                        <p class="text-2xl font-bold text-blue-600">TZS {{ number_format($totalPledged, 0) }}</p>
                    </div>
                    <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-handshake text-xl text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Jumla Iliyolipwa</p>
                        <p class="text-2xl font-bold text-green-600">TZS {{ number_format($totalPaid, 0) }}</p>
                    </div>
                    <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-xl text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Jumla Iliyobaki</p>
                        <p class="text-2xl font-bold text-orange-600">TZS {{ number_format($totalRemaining, 0) }}</p>
                    </div>
                    <div class="h-12 w-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-xl text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pledges List -->
        <div class="space-y-6">
            @foreach($pledges as $pledge)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <!-- Pledge Header -->
                    <div class="bg-gradient-to-r from-primary-50 to-primary-100 p-6 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="flex items-center">
                                <div class="h-12 w-12 bg-primary-600 rounded-xl flex items-center justify-center mr-4">
                                    <i class="fas fa-handshake text-white text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">{{ $pledge->pledge_type }}</h3>
                                    <p class="text-sm text-gray-600 mt-1 flex items-center gap-3">
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar-alt mr-1"></i>
                                            Tarehe: {{ $pledge->pledge_date->format('d M Y') }}
                                        </span>
                                        @if($pledge->due_date)
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar-check mr-1"></i>
                                                Mwisho: {{ $pledge->due_date->format('d M Y') }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                                {{ $pledge->status === 'Completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $pledge->status === 'Partial' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $pledge->status === 'Pending' ? 'bg-gray-200 text-gray-700' : '' }}">
                                <i class="fas {{ $pledge->status === 'Completed' ? 'fa-check-circle' : ($pledge->status === 'Partial' ? 'fa-hourglass-half' : 'fa-clock') }} mr-1.5"></i>
                                {{ $pledge->status === 'Completed' ? 'Imekamilika' : ($pledge->status === 'Partial' ? 'Inaendelea' : 'Bado') }}
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-6">
                            <div class="flex items-center justify-between text-sm mb-2">
                                <span class="text-gray-700 font-medium flex items-center">
                                    <i class="fas fa-chart-line mr-2 text-primary-500"></i>
                                    Maendeleo ya Malipo
                                </span>
                                <span class="text-gray-900 font-bold">{{ number_format($pledge->progress_percentage, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="h-3 rounded-full transition-all duration-500
                                    {{ $pledge->progress_percentage >= 100 ? 'bg-green-600' : ($pledge->progress_percentage >= 50 ? 'bg-yellow-500' : 'bg-primary-600') }}"
                                     style="width: {{ min($pledge->progress_percentage, 100) }}%"></div>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-coins text-blue-500 mr-2"></i>
                                    <p class="text-xs text-gray-600">Jumla ya Ahadi</p>
                                </div>
                                <p class="text-xl font-bold text-gray-900">TZS {{ number_format($pledge->amount, 0) }}</p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <p class="text-xs text-gray-600">Kiasi Kilicholipwa</p>
                                </div>
                                <p class="text-xl font-bold text-green-600">TZS {{ number_format($pledge->amount_paid, 0) }}</p>
                            </div>
                            <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-sm">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-hourglass-half text-orange-500 mr-2"></i>
                                    <p class="text-xs text-gray-600">Kiasi Kilichobaki</p>
                                </div>
                                <p class="text-xl font-bold text-orange-600">TZS {{ number_format($pledge->remaining_amount, 0) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment History -->
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-history text-primary-600"></i>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900">Historia ya Malipo</h4>
                                <p class="text-sm text-gray-600">Malipo yaliyofanywa kwa ahadi hii</p>
                            </div>
                        </div>

                        @if($pledge->payments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-primary-600 text-white text-sm">
                                            <th class="py-3 px-4 text-left font-semibold uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <i class="fas fa-calendar mr-2"></i>
                                                    Tarehe
                                                </div>
                                            </th>
                                            <th class="py-3 px-4 text-left font-semibold uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <i class="fas fa-money-bill-wave mr-2"></i>
                                                    Kiasi
                                                </div>
                                            </th>
                                            <th class="py-3 px-4 text-left font-semibold uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <i class="fas fa-credit-card mr-2"></i>
                                                    Njia ya Malipo
                                                </div>
                                            </th>
                                            <th class="py-3 px-4 text-left font-semibold uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <i class="fas fa-receipt mr-2"></i>
                                                    Namba ya Risiti
                                                </div>
                                            </th>
                                            <th class="py-3 px-4 text-left font-semibold uppercase tracking-wider">
                                                <div class="flex items-center">
                                                    <i class="fas fa-cogs mr-2"></i>
                                                    Hatua
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($pledge->payments as $payment)
                                            <tr class="bg-white hover:bg-gray-50 transition-all duration-200">
                                                <td class="py-3 px-4 text-sm text-gray-900">
                                                    <div class="flex items-center gap-2">
                                                        <i class="fas fa-calendar-day text-gray-400"></i>
                                                        {{ $payment->payment_date->format('d M Y') }}
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-sm font-bold text-green-600">
                                                    TZS {{ number_format($payment->amount, 0) }}
                                                </td>
                                                <td class="py-3 px-4 text-sm text-gray-600">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        {{ $payment->payment_method ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-sm text-gray-600">
                                                    <span class="font-mono bg-gray-100 px-2 py-1 rounded">
                                                        {{ $payment->receipt_number }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-sm">
                                                    <div class="flex items-center space-x-2">
                                                        <a href="{{ route('member.receipt.view', $payment->id) }}"
                                                           class="h-8 w-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-200 transition-all duration-200"
                                                           title="Angalia Risiti">
                                                            <i class="fas fa-eye text-sm"></i>
                                                        </a>
                                                        <a href="{{ route('member.receipt.download', $payment->id) }}"
                                                           class="h-8 w-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-200 transition-all duration-200"
                                                           title="Pakua Risiti">
                                                            <i class="fas fa-download text-sm"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-xl border border-gray-200">
                                <div class="mx-auto w-12 h-12 mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-inbox text-gray-400 text-xl"></i>
                                </div>
                                <p class="text-gray-600">Hakuna malipo bado kwa ahadi hii</p>
                            </div>
                        @endif

                        @if($pledge->notes)
                            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                                <div class="flex items-start">
                                    <i class="fas fa-sticky-note text-blue-600 mr-2 mt-0.5"></i>
                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold">Maelezo:</span> {{ $pledge->notes }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
            <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-handshake text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna Ahadi</h3>
            <p class="text-gray-500 mb-6">Bado hujaweka ahadi yoyote</p>
            <a href="{{ route('member.portal') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                <i class="fas fa-home mr-2"></i> Rudi Nyumbani
            </a>
        </div>
    @endif
</div>
@endsection
