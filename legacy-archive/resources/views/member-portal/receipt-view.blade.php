@extends('layouts.app')

@section('title', 'Risiti ya Malipo')
@section('page-title', 'Risiti ya Malipo')
@section('page-subtitle', 'Angalia maelezo kamili ya risiti yako')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Risiti ya Malipo</h1>
            <p class="text-gray-600 mt-2">Angalia maelezo kamili ya risiti yako</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('member.receipt.download', $payment->id) }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700">
                <i class="fas fa-download"></i>
                <span class="font-medium">Pakua PDF</span>
            </a>
            <a href="{{ route('member.receipts') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Rudi Orodhani</span>
            </a>
        </div>
    </div>

    <!-- Receipt Profile Header -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 p-8">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Receipt Icon -->
                <div class="h-24 w-24 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-invoice text-white text-5xl"></i>
                </div>

                <!-- Receipt Info -->
                <div class="flex-1 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <p class="text-primary-100 text-sm mb-1">KANISA LA RGC</p>
                            <h2 class="text-3xl font-bold font-mono">{{ $payment->receipt_number }}</h2>
                            <div class="flex items-center gap-4 mt-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar text-white opacity-80"></i>
                                    <span class="text-lg opacity-90">{{ $payment->payment_date->format('d M Y') }}</span>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-full text-sm font-semibold">
                                    <i class="fas fa-check-circle mr-1.5"></i>Imelipwa
                                </span>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex gap-3">
                            <button onclick="window.print()"
                                    class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2">
                                <i class="fas fa-print"></i>
                                <span>Chapisha</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Payment Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payment Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-money-bill-wave text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Maelezo ya Malipo</h3>
                        <p class="text-sm text-gray-600">Taarifa za malipo yaliyofanywa</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Payment Amount -->
                    <div class="md:col-span-2 bg-green-50 p-4 rounded-lg border-2 border-green-200">
                        <p class="text-sm text-gray-600 mb-1">Kiasi Kilicholipwa</p>
                        <div class="flex items-center">
                            <i class="fas fa-coins text-green-500 mr-2"></i>
                            <p class="text-3xl font-bold text-green-600">TZS {{ number_format($payment->amount, 2) }}</p>
                        </div>
                    </div>

                    <!-- Pledge Type -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Aina ya Ahadi</p>
                        <div class="flex items-center">
                            <i class="fas fa-handshake text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $payment->pledge->pledge_type }}</p>
                        </div>
                    </div>

                    <!-- Payment Date -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tarehe ya Malipo</p>
                        <div class="flex items-center">
                            <i class="fas fa-calendar-check text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $payment->payment_date->format('d F Y') }}</p>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Njia ya Malipo</p>
                        <div class="flex items-center">
                            <i class="fas fa-credit-card text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $payment->payment_method ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Receipt Number -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Namba ya Risiti</p>
                        <div class="flex items-center">
                            <i class="fas fa-hashtag text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900 font-mono">{{ $payment->receipt_number }}</p>
                        </div>
                    </div>

                    @if($payment->reference_number)
                    <!-- Reference Number -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Namba ya Kumbukumbu</p>
                        <div class="flex items-center">
                            <i class="fas fa-barcode text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $payment->reference_number }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Pledge Summary Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-chart-pie text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Muhtasari wa Ahadi</h3>
                        <p class="text-sm text-gray-600">Hali ya ahadi baada ya malipo haya</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Pledge -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Jumla ya Ahadi</p>
                        <div class="flex items-center">
                            <i class="fas fa-coins text-blue-500 mr-2"></i>
                            <p class="text-xl font-bold text-gray-900">TZS {{ number_format($payment->pledge->amount, 0) }}</p>
                        </div>
                    </div>

                    <!-- Paid Amount -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Kilicholipwa Hadi Sasa</p>
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            <p class="text-xl font-bold text-green-600">TZS {{ number_format($payment->pledge->amount_paid, 0) }}</p>
                        </div>
                    </div>

                    <!-- Remaining Amount -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Kiasi Kilichobaki</p>
                        <div class="flex items-center">
                            <i class="fas fa-hourglass-half text-orange-500 mr-2"></i>
                            <p class="text-xl font-bold text-orange-600">TZS {{ number_format($payment->pledge->remaining_amount, 0) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-700 font-medium flex items-center">
                            <i class="fas fa-chart-line mr-2 text-primary-500"></i>
                            Maendeleo ya Malipo
                        </span>
                        <span class="text-gray-900 font-bold">{{ number_format($payment->pledge->progress_percentage, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-500
                            {{ $payment->pledge->progress_percentage >= 100 ? 'bg-green-600' : ($payment->pledge->progress_percentage >= 50 ? 'bg-yellow-500' : 'bg-primary-600') }}"
                             style="width: {{ min($payment->pledge->progress_percentage, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            @if($payment->notes)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-sticky-note text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Maelezo Mengine</h3>
                        <p class="text-sm text-gray-600">Taarifa zingine muhimu</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-edit text-primary-500 mr-2 mt-1"></i>
                        <p class="text-gray-700">{{ $payment->notes }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Member Info & Quick Stats -->
        <div class="space-y-6">
            <!-- Member Information Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Mlipaji</h3>
                        <p class="text-sm text-gray-600">Maelezo ya muumini</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Full Name -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-user text-primary-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Jina</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">{{ $member->full_name }}</span>
                    </div>

                    <!-- Member Number -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-id-card text-blue-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Namba ya Muumini</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">{{ $member->member_number }}</span>
                    </div>

                    <!-- Envelope Number -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-green-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Namba ya Bahasha</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">{{ $member->envelope_number }}</span>
                    </div>

                    @if($member->phone)
                    <!-- Phone -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-phone text-purple-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Simu</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">{{ $member->phone }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-bolt text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Hatua za Haraka</h3>
                        <p class="text-sm text-gray-600">Vitendo vya haraka</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <a href="{{ route('member.receipt.download', $payment->id) }}"
                       class="block w-full text-center text-white px-4 py-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700">
                        <i class="fas fa-download"></i>
                        <span>Pakua Risiti (PDF)</span>
                    </a>

                    <button onclick="window.print()"
                            class="block w-full text-center text-white px-4 py-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700">
                        <i class="fas fa-print"></i>
                        <span>Chapisha Risiti</span>
                    </button>

                    <a href="{{ route('member.pledges') }}"
                       class="block w-full text-center text-white px-4 py-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700">
                        <i class="fas fa-handshake"></i>
                        <span>Angalia Ahadi</span>
                    </a>
                </div>
            </div>

            <!-- Church Info Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-church text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Kanisa</h3>
                        <p class="text-sm text-gray-600">Mawasiliano ya kanisa</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-church text-primary-500 mr-2"></i>
                        <span class="text-sm text-gray-900 font-medium">KANISA LA RGC</span>
                    </div>

                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                        <span class="text-sm text-gray-700">Dar es Salaam, Tanzania</span>
                    </div>

                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-phone text-green-500 mr-2"></i>
                        <span class="text-sm text-gray-700">+255 XXX XXX XXX</span>
                    </div>
                </div>

                <!-- Thank You Message -->
                <div class="mt-6 bg-green-50 border-2 border-green-200 p-4 rounded-lg">
                    <div class="flex items-center justify-center text-center">
                        <div>
                            <i class="fas fa-praying-hands text-green-600 text-2xl mb-2"></i>
                            <p class="text-sm font-medium text-green-800">Asante kwa mchango wako.</p>
                            <p class="text-sm text-green-700">Mungu akubariki!</p>
                        </div>
                    </div>
                </div>

                <!-- Generated Date -->
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-500 flex items-center justify-center gap-1">
                        <i class="fas fa-clock"></i>
                        Risiti imeundwa: {{ now()->format('d F Y, H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
