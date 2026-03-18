@extends('layouts.app')

@section('title', 'Risiti Zangu')
@section('page-title', 'Risiti Zangu')
@section('page-subtitle', 'Angalia na pakua risiti zako zote')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Risiti Zangu</h1>
            <p class="text-gray-600 mt-2">Angalia na pakua risiti zako zote</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('member.portal') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Rudi Nyumbani</span>
            </a>
        </div>
    </div>

    <!-- Receipts Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <!-- Table Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-file-invoice text-green-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Orodha ya Risiti</h3>
                    <p class="text-sm text-gray-600">Risiti zote za malipo yako</p>
                </div>
            </div>
            <div class="mt-3 sm:mt-0">
                <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                    {{ $payments->total() }} risiti
                </span>
            </div>
        </div>

        @if($payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-primary-600 text-white text-sm">
                            <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-hashtag mr-2"></i>
                                    Namba ya Risiti
                                </div>
                            </th>
                            <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    Tarehe
                                </div>
                            </th>
                            <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-handshake mr-2"></i>
                                    Ahadi
                                </div>
                            </th>
                            <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-money-bill-wave mr-2"></i>
                                    Kiasi
                                </div>
                            </th>
                            <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Njia ya Malipo
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
                    <tbody class="divide-y divide-gray-100">
                        @foreach($payments as $payment)
                            <tr class="bg-white hover:bg-gray-50 transition-all duration-200">
                                <td class="py-4 px-6 text-sm text-gray-900">
                                    <span class="font-mono bg-gray-100 px-2 py-1 rounded font-medium">
                                        {{ $payment->receipt_number }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar text-gray-400"></i>
                                        {{ $payment->payment_date->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $payment->pledge->pledge_type }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm font-bold text-green-600">
                                    TZS {{ number_format($payment->amount, 0) }}
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-600">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $payment->payment_method ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm sticky right-0 bg-white">
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

            <!-- Pagination -->
            @if($payments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $payments->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-file-invoice text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna Risiti</h3>
                <p class="text-gray-500 mb-6">Bado huna risiti zozote za malipo</p>
                <a href="{{ route('member.portal') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                    <i class="fas fa-home mr-2"></i> Rudi Nyumbani
                </a>
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start">
            <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4 shrink-0">
                <i class="fas fa-info-circle text-blue-600"></i>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-1">Kidokezo</h4>
                <p class="text-sm text-gray-600">
                    Unaweza ku-download risiti zako wakati wowote. Risiti hizi zinaweza kuwa na umuhimu kwa madhumuni ya hesabu zako binafsi.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
