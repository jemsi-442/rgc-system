@extends('layouts.app')

@section('title', 'Portal ya Muumini')
@section('page-title', 'Portal ya Muumini')
@section('page-subtitle', 'Orodha ya taarifa zako binafsi na matumizi')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-primary-600 to-primary-700 rounded-xl shadow-lg p-6 mb-6 text-white">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
            <div class="flex-1">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold mb-2">Karibu, {{ $member->full_name }}</h1>
                        <div class="flex flex-wrap gap-3 mt-3">
                            <div class="flex items-center bg-white bg-opacity-20 px-3 py-1.5 rounded-full">
                                <i class="fas fa-id-card mr-2"></i>
                                <span class="text-sm font-medium">#{{ $member->member_number }}</span>
                            </div>
                            <div class="flex items-center bg-white bg-opacity-20 px-3 py-1.5 rounded-full">
                                <i class="fas fa-envelope mr-2"></i>
                                <span class="text-sm font-medium">Bahasha: {{ $member->envelope_number }}</span>
                            </div>
                            @if($member->phone)
                            <div class="flex items-center bg-white bg-opacity-20 px-3 py-1.5 rounded-full">
                                <i class="fas fa-phone mr-2"></i>
                                <span class="text-sm font-medium">{{ $member->phone }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Taarifa Zangu Button -->
                    <div class="mt-3 sm:mt-0">
                        <a href="{{ route('member.profile.edit') }}" 
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium rounded-xl shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                            <i class="fas fa-user-edit"></i>
                            <span>Taarifa Zangu</span>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Profile Picture -->
            <div class="hidden lg:block">
                <div class="relative">
                    <div class="h-20 w-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white text-4xl"></i>
                    </div>
                    @if($member->is_active)
                    <div class="absolute -bottom-1 -right-1 h-6 w-6 bg-green-400 rounded-full border-2 border-white flex items-center justify-center">
                        <i class="fas fa-check text-xs text-white"></i>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Pledged -->
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
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-500 flex items-center">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Ahadi {{ $pledges->count() }} zilizofanywa
                </p>
            </div>
        </div>

        <!-- Total Paid -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jumla Yaliyolipwa</p>
                    <p class="text-2xl font-bold text-green-600">TZS {{ number_format($totalPaid, 0) }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-green-600"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-500 flex items-center">
                    <i class="fas fa-money-bill-wave mr-1"></i>
                    Malipo {{ $recentPayments->count() }} ya hivi karibuni
                </p>
            </div>
        </div>

        <!-- Total Remaining -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Baki</p>
                    <p class="text-2xl font-bold text-orange-600">TZS {{ number_format($totalRemaining, 0) }}</p>
                </div>
                <div class="h-12 w-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-xl text-orange-600"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-500 flex items-center">
                    <i class="fas fa-hourglass-half mr-1"></i>
                    Bado kulipa
                </p>
            </div>
        </div>

        <!-- Completed Pledges -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Ahadi Zilizokamilika</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $completedPledges }} / {{ $pledges->count() }}</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-trophy text-xl text-purple-600"></i>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-gray-100">
                <p class="text-xs text-gray-500 flex items-center">
                    <i class="fas fa-percentage mr-1"></i>
                    {{ $pledges->count() > 0 ? round(($completedPledges/$pledges->count())*100, 1) : 0 }}% ya mafanikio
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-rocket text-primary-500 mr-2"></i> Hatua za Haraka
            </h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('member.contributions') }}" class="group bg-white rounded-xl border border-gray-200 p-5 hover:border-primary-300 hover:shadow-md transition-all duration-200">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-primary-100 rounded-xl flex items-center justify-center mr-4 group-hover:bg-primary-200 transition-colors duration-200">
                        <i class="fas fa-hand-holding-heart text-primary-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 group-hover:text-primary-700 transition-colors duration-200">Michango Yangu</div>
                        <div class="text-sm text-gray-500 mt-1">Angalia historia ya michango</div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-primary-600 transition-colors duration-200"></i>
                </div>
            </a>

            <a href="{{ route('member.pledges') }}" class="group bg-white rounded-xl border border-gray-200 p-5 hover:border-blue-300 hover:shadow-md transition-all duration-200">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4 group-hover:bg-blue-200 transition-colors duration-200">
                        <i class="fas fa-handshake text-blue-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 group-hover:text-blue-700 transition-colors duration-200">Ahadi Zangu</div>
                        <div class="text-sm text-gray-500 mt-1">Fuatilia ahadi na malipo</div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600 transition-colors duration-200"></i>
                </div>
            </a>

            <a href="{{ route('member.receipts') }}" class="group bg-white rounded-xl border border-gray-200 p-5 hover:border-green-300 hover:shadow-md transition-all duration-200">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center mr-4 group-hover:bg-green-200 transition-colors duration-200">
                        <i class="fas fa-file-invoice text-green-600 text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900 group-hover:text-green-700 transition-colors duration-200">Risiti Zangu</div>
                        <div class="text-sm text-gray-500 mt-1">Pakua na angalia risiti</div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-600 transition-colors duration-200"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Payments -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <!-- Table Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-money-bill-wave text-green-600 mr-2"></i> Malipo ya Hivi Karibuni
                        <span class="ml-3 text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded-full">
                            {{ $recentPayments->count() }} malipo
                        </span>
                    </h3>
                </div>
                <div class="mt-3 sm:mt-0">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-calendar-day text-primary-500 mr-2"></i>
                        <span>Siku 30 zilizopita</span>
                    </div>
                </div>
            </div>

            <!-- Table Content -->
            <div class="p-6">
                @if($recentPayments->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentPayments as $payment)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200 hover:bg-gray-100 transition-all duration-200 group">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                            {{ $payment->pledge->pledge_type }}
                                        </span>
                                        <span class="text-xs text-gray-500">{{ $payment->payment_date->format('d M Y') }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500 flex items-center">
                                        <i class="fas fa-receipt mr-1"></i>
                                        Risiti: {{ $payment->receipt_number }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600 text-lg">TZS {{ number_format($payment->amount, 0) }}</p>
                                    <a href="{{ route('member.receipt.view', $payment->id) }}" class="text-xs text-primary-600 hover:text-primary-800 flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <i class="fas fa-eye"></i>
                                        <span>Angalia Risiti</span>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <a href="{{ route('member.receipts') }}" class="text-primary-600 hover:text-primary-800 font-medium flex items-center justify-center gap-2">
                            <span>Angalia Risiti Zote</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna Malipo ya Hivi Karibuni</h3>
                        <p class="text-gray-500 mb-6">Hakuna malipo yaliyopatikana kwenye kipindi cha siku 30 zilizopita.</p>
                        <a href="{{ route('member.pledges') }}" class="text-primary-600 hover:text-primary-800 font-medium flex items-center justify-center gap-2">
                            <span>Tazama Ahadi Zako</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Active Pledges -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <!-- Table Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b border-gray-200">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-tasks text-blue-600 mr-2"></i> Ahadi Zinazoendelea
                        <span class="ml-3 text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded-full">
                            @php
                                $activePledges = $pledges->whereIn('status', ['Pending', 'Partial'])->take(5);
                            @endphp
                            {{ $activePledges->count() }} zinazoendelea
                        </span>
                    </h3>
                </div>
                <div class="mt-3 sm:mt-0">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle text-primary-500 mr-2"></i>
                        <span>Ahadi ambazo bado hazijakamilika</span>
                    </div>
                </div>
            </div>

            <!-- Table Content -->
            <div class="p-6">
                @if($activePledges->count() > 0)
                    <div class="space-y-4">
                        @foreach($activePledges as $pledge)
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 hover:bg-gray-100 transition-all duration-200">
                                <div class="flex items-center justify-between mb-3">
                                    <p class="font-semibold text-gray-900">{{ $pledge->pledge_type }}</p>
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pledge->status === 'Pending' ? 'bg-gray-200 text-gray-700' : 'bg-yellow-100 text-yellow-800' }}">
                                        <i class="fas {{ $pledge->status === 'Pending' ? 'fa-clock' : 'fa-hourglass-half' }} mr-1"></i>
                                        {{ $pledge->status === 'Pending' ? 'Bado' : 'Nusu' }}
                                    </span>
                                </div>
                                <div class="space-y-2 mb-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Kiasi cha Ahadi</span>
                                        <span class="font-medium">TZS {{ number_format($pledge->amount, 0) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Imelipwa</span>
                                        <span class="text-green-600 font-medium">TZS {{ number_format($pledge->paid_amount, 0) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Baki</span>
                                        <span class="text-orange-600 font-medium">TZS {{ number_format($pledge->remaining_amount, 0) }}</span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                    <div class="bg-green-600 h-2 rounded-full transition-all duration-500" style="width: {{ $pledge->progress_percentage }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs">
                                    <span class="text-gray-500">{{ number_format($pledge->progress_percentage, 1) }}% Imekamilika</span>
                                    <a href="{{ route('member.pledges') }}" class="text-primary-600 hover:text-primary-800">
                                        <i class="fas fa-plus-circle mr-1"></i>Lipia
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <a href="{{ route('member.pledges') }}" class="text-primary-600 hover:text-primary-800 font-medium flex items-center justify-center gap-2">
                            <span>Angalia Ahadi Zote</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-green-100 flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Umekamilisha Ahadi Zote!</h3>
                        <p class="text-gray-500 mb-6">Hakuna ahadi zinazoendelea au zilizobaki kulipwa.</p>
                        <a href="{{ route('member.contributions') }}" class="text-primary-600 hover:text-primary-800 font-medium flex items-center justify-center gap-2">
                            <span>Tazama Michango Yako</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection