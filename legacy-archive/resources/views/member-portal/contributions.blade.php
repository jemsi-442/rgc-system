@extends('layouts.app')

@section('title', 'Michango Yangu')
@section('page-title', 'Michango Yangu')
@section('page-subtitle', 'Historia kamili ya michango yako (isipokuwa shukrani ya wiki)')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Michango Yangu</h1>
            <p class="text-gray-600 mt-2">Historia kamili ya michango yako (isipokuwa shukrani ya wiki)</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('member.portal') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Rudi Nyumbani</span>
            </a>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 p-8">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="text-white">
                    <p class="text-primary-100 mb-2 flex items-center gap-2">
                        <i class="fas fa-hand-holding-heart"></i>
                        Jumla ya Michango
                    </p>
                    <p class="text-4xl font-bold">TZS {{ number_format($totalContributions, 0) }}</p>
                    <div class="flex items-center gap-2 mt-3">
                        <span class="inline-flex items-center px-3 py-1 bg-white bg-opacity-20 text-white rounded-full text-sm font-medium">
                            <i class="fas fa-receipt mr-1.5"></i>{{ $contributions->total() }} Michango
                        </span>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="h-20 w-20 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-hand-holding-heart text-white text-4xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contributions Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <!-- Table Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b border-gray-200">
            <div class="flex items-center">
                <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-list text-primary-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Historia ya Michango</h3>
                    <p class="text-sm text-gray-600">Orodha ya michango yako yote</p>
                </div>
            </div>
            <div class="mt-3 sm:mt-0">
                <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                    {{ $contributions->total() }} michango
                </span>
            </div>
        </div>

        @if($contributions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-primary-600 text-white text-sm">
                            <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt mr-2"></i>
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
                                    <i class="fas fa-money-bill-wave mr-2"></i>
                                    Kiasi
                                </div>
                            </th>
                            <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-receipt mr-2"></i>
                                    Namba ya Risiti
                                </div>
                            </th>
                            <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                <div class="flex items-center">
                                    <i class="fas fa-sticky-note mr-2"></i>
                                    Maelezo
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($contributions as $contribution)
                            <tr class="bg-white hover:bg-gray-50 transition-all duration-200">
                                <td class="py-4 px-6 text-sm text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar text-gray-400"></i>
                                        {{ \Carbon\Carbon::parse($contribution->collection_date)->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                        {{ $contribution->category->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm font-bold text-green-600">
                                    TZS {{ number_format($contribution->amount, 0) }}
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-600">
                                    <span class="font-mono bg-gray-100 px-2 py-1 rounded">
                                        {{ $contribution->receipt_number ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-600">
                                    {{ $contribution->notes ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($contributions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $contributions->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna Michango</h3>
                <p class="text-gray-500 mb-6">Bado hujaweka michango yoyote</p>
                <a href="{{ route('member.portal') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                    <i class="fas fa-home mr-2"></i> Rudi Nyumbani
                </a>
            </div>
        @endif
    </div>

    <!-- Info Box -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="flex items-start">
            <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600"></i>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-1">Kidokezo</h4>
                <p class="text-sm text-gray-600">
                    Ukurasa huu unaonyesha michango yako yote isipokuwa shukrani ya wiki. Kwa maelezo zaidi, wasiliana na ofisi ya kanisa.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
