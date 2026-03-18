@extends('layouts.app')

@section('title', 'Maombi ya Fedha - Mfumo wa Kanisa')
@section('page-title', 'Maombi ya Fedha')
@section('page-subtitle', 'Usimamizi wa maombi ya fedha za kanisa')

@section('content')
@php
// Helper function to format money with M, B, K
function formatRequestMoney($amount) {
    if ($amount >= 1000000000) { // Billions
        return number_format($amount / 1000000000, 2) . 'B';
    } elseif ($amount >= 1000000) { // Millions
        return number_format($amount / 1000000, 2) . 'M';
    } elseif ($amount >= 1000) { // Thousands
        return number_format($amount / 1000, 1) . 'K';
    } else {
        return number_format($amount, 0);
    }
}
@endphp

<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Maombi ya Fedha</h1>
            <p class="text-gray-600 mt-2">Usimamizi kamili wa maombi ya fedha za kanisa</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="exportRequestsExcel()" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700">
                <i class="fas fa-file-excel"></i>
                <span class="font-medium">Export Excel</span>
            </button>
            <a href="{{ route('requests.create') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800">
                <i class="fas fa-plus"></i>
                <span class="font-medium">Omba Fedha</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jumla ya Maombi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Zinasubiri</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-clock text-xl text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Zimeidhinishwa</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['approved'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Zimekataliwa</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['rejected'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter text-primary-500 mr-2"></i> Chuja Maombi
            </h3>
        </div>
        <form method="GET" action="{{ route('requests.index') }}" data-auto-filter="true" data-ajax-target="#requestsTableContainer" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search Field -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tafuta</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Tafuta namba ya ombi, kichwa, idara...">
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hali</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Zote</option>
                        <option value="Inasubiri" {{ request('status') == 'Inasubiri' ? 'selected' : '' }}>Zinasubiri</option>
                        <option value="Imeidhinishwa" {{ request('status') == 'Imeidhinishwa' ? 'selected' : '' }}>Zimeidhinishwa</option>
                        <option value="Imekataliwa" {{ request('status') == 'Imekataliwa' ? 'selected' : '' }}>Zimekataliwa</option>
                    </select>
                </div>

                <!-- Department -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Idara</label>
                    <select name="department" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Zote</option>
                        @if(isset($departments))
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarehe Kuanzia</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarehe Hadi</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Amount Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kiasi (TSh)</label>
                    <select name="amount_range" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Vyote</option>
                        <option value="0-500000" {{ request('amount_range') == '0-500000' ? 'selected' : '' }}>Chini ya 500K</option>
                        <option value="500000-1000000" {{ request('amount_range') == '500000-1000000' ? 'selected' : '' }}>500K - 1M</option>
                        <option value="1000000-5000000" {{ request('amount_range') == '1000000-5000000' ? 'selected' : '' }}>1M - 5M</option>
                        <option value="5000000-999999999" {{ request('amount_range') == '5000000-999999999' ? 'selected' : '' }}>Zaidi ya 5M</option>
                    </select>
                </div>
            </div>

            <!-- Clear Filter Link -->
            <div class="flex justify-end pt-2">
                <a href="{{ route('requests.index') }}" class="text-sm text-gray-500 hover:text-primary-600 transition-colors flex items-center gap-1">
                    <i class="fas fa-redo text-xs"></i>
                    <span>Futa Chujio</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Requests Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" id="requestsTableContainer">
        <!-- Table Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 sm:p-6 border-b border-gray-200">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list text-primary-500 mr-2"></i> Orodha ya Maombi
                    <span class="ml-3 text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                        {{ $requests->total() }} maombi
                    </span>
                </h3>
            </div>
            <div class="mt-3 sm:mt-0 hidden sm:block">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-info-circle text-primary-500"></i>
                    <span>Bofya vitendo kuangalia au kuidhinisha maombi</span>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px]">
                <thead>
                    <tr class="bg-primary-600 text-white text-xs sm:text-sm">
                        <th class="py-3 px-3 sm:px-4 text-left font-semibold uppercase tracking-wider whitespace-nowrap">
                            Namba
                        </th>
                        <th class="py-3 px-3 sm:px-4 text-left font-semibold uppercase tracking-wider">
                            Kichwa/Idara
                        </th>
                        <th class="py-3 px-3 sm:px-4 text-left font-semibold uppercase tracking-wider whitespace-nowrap">
                            Kiasi
                        </th>
                        <th class="py-3 px-3 sm:px-4 text-left font-semibold uppercase tracking-wider whitespace-nowrap">
                            Hali
                        </th>
                        <th class="py-3 px-3 sm:px-4 text-left font-semibold uppercase tracking-wider whitespace-nowrap hidden md:table-cell">
                            Tarehe
                        </th>
                        <th class="py-3 px-3 sm:px-4 text-center font-semibold uppercase tracking-wider whitespace-nowrap">
                            Vitendo
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($requests as $request)
                    <tr class="bg-white hover:bg-gray-50 transition-all duration-200">
                        <!-- Request Number -->
                        <td class="py-3 px-3 sm:px-4 text-sm">
                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $request->request_number }}</span>
                        </td>

                        <!-- Title & Department Combined -->
                        <td class="py-3 px-3 sm:px-4">
                            <div class="text-sm text-gray-900 font-medium truncate max-w-[200px]">{{ $request->title }}</div>
                            <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                {{ $request->department }}
                            </span>
                        </td>

                        <!-- Amount (Combined) -->
                        <td class="py-3 px-3 sm:px-4 text-sm">
                            <div class="font-bold text-gray-900">{{ formatRequestMoney($request->amount_requested) }}</div>
                            @if($request->amount_approved)
                                <div class="text-xs text-green-600 mt-0.5">
                                    <i class="fas fa-check text-[10px]"></i> {{ formatRequestMoney($request->amount_approved) }}
                                </div>
                            @endif
                        </td>

                        <!-- Status -->
                        <td class="py-3 px-3 sm:px-4 text-sm">
                            @if($request->status == 'Inasubiri')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1 text-[10px]"></i> Inasubiri
                                </span>
                            @elseif($request->status == 'Imeidhinishwa')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1 text-[10px]"></i> Imeidhinishwa
                                </span>
                            @elseif($request->status == 'Imekataliwa')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times mr-1 text-[10px]"></i> Imekataliwa
                                </span>
                            @endif
                        </td>

                        <!-- Date (Hidden on mobile) -->
                        <td class="py-3 px-3 sm:px-4 text-sm text-gray-600 hidden md:table-cell">
                            {{ \Carbon\Carbon::parse($request->requested_date)->format('d/m/Y') }}
                        </td>

                        <!-- Actions -->
                        <td class="py-3 px-3 sm:px-4 text-sm">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('requests.show', $request->id) }}"
                                   class="h-8 w-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-200 transition-all duration-200"
                                   title="Angalia">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @if($request->status == 'Inasubiri')
                                <a href="{{ route('requests.edit', $request->id) }}"
                                   class="h-8 w-8 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center hover:bg-primary-200 transition-all duration-200"
                                   title="Hariri">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @endif
                                <button type="button"
                                        onclick="confirmDelete({{ $request->id }}, '{{ $request->request_number }}')"
                                        class="h-8 w-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200 transition-all duration-200"
                                        title="Futa">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 px-6 text-center">
                            <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-file-invoice-dollar text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna maombi yaliyopatikana</h3>
                            <p class="text-gray-500 mb-6">Hakuna maombi yanayolingana na vichujio vyako.</p>
                            <a href="{{ route('requests.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                                <i class="fas fa-plus mr-2"></i> Ongeza Ombi Jipya
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-xl border-b border-gray-200 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-red-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Thibitisha Kufuta</h3>
                        <p class="text-sm text-gray-600">Hatua hii haiwezi kurudishwa</p>
                    </div>
                </div>
                <button type="button" onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Modal Content -->
        <div class="p-6">
            <p class="text-gray-700 mb-2">Je, una uhakika unataka kufuta ombi hili?</p>
            <p class="text-gray-900 font-semibold" id="deleteRequestNumber"></p>
            <p class="text-sm text-red-600 mt-3">
                <i class="fas fa-warning mr-1"></i>
                Taarifa zote za ombi hili zitafutwa kabisa.
            </p>
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-gray-50 px-6 py-5 rounded-b-xl border-t border-gray-200 flex justify-end space-x-3">
            <button type="button" onclick="closeDeleteModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">
                Ghairi
            </button>
            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    <span>Futa Ombi</span>
                </button>
            </form>
        </div>
    </div>
</div>

@include('partials.loading-modal')

<script>
// Delete confirmation modal functions
function confirmDelete(requestId, requestNumber) {
    document.getElementById('deleteRequestNumber').textContent = 'Ombi: ' + requestNumber;
    document.getElementById('deleteForm').action = '/panel/requests/' + requestId;

    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('div').classList.remove('scale-95');
    }, 10);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.querySelector('div').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        closeDeleteModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('deleteModal');
        if (!modal.classList.contains('hidden')) {
            closeDeleteModal();
        }
    }
});

// Export to Excel function
function exportRequestsExcel() {
    const params = new URLSearchParams(window.location.search);
    const exportUrl = '{{ route("requests.index") }}/export?' + params.toString();

    if (typeof showInfo === 'function') {
        showInfo('Inaandaa faili ya Excel...');
    }

    // Try to export, or show message if route not available
    fetch(exportUrl, { method: 'HEAD' })
        .then(response => {
            if (response.ok) {
                window.location.href = exportUrl;
            } else {
                if (typeof showWarning === 'function') {
                    showWarning('Export ya Excel bado haijaandaliwa');
                }
            }
        })
        .catch(() => {
            if (typeof showWarning === 'function') {
                showWarning('Export ya Excel bado haijaandaliwa');
            }
        });
}

// Auto-refresh request statistics every 30 seconds
function refreshRequestsStats() {
    fetch('/api/dashboard-stats')
        .then(response => response.json())
        .then(data => {
            // Update stats if available
            console.log('Stats refreshed:', data);
        })
        .catch(error => {
            console.error('Error refreshing request stats:', error);
        });
}

// Refresh on page load
document.addEventListener('DOMContentLoaded', function() {
    refreshRequestsStats();
});

// Refresh every 30 seconds
setInterval(refreshRequestsStats, 30000);

// Refresh when page becomes visible again
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        refreshRequestsStats();
    }
});
</script>
@endsection
