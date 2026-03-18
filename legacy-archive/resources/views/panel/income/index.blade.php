@extends('layouts.app')

@section('title', 'Mapato - Mfumo wa Kanisa')
@section('page-title', 'Mapato')
@section('page-subtitle', 'Rekodi na usimamizi wa mapato ya kanisa')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mapato ya Kanisa</h1>
            <p class="text-gray-600 mt-2">Usimamizi kamili wa rekodi za mapato ya kanisa</p>
        </div>
        @if(Auth::user()->isMchungaji() || Auth::user()->isMhasibu())
        <div class="flex flex-wrap gap-3">
            <button onclick="exportMapato()" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700">
                <i class="fas fa-file-excel"></i>
                <span class="font-medium">Export Excel</span>
            </button>
            <a href="{{ route('income.bulk-entry') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-600 hover:to-secondary-700">
                <i class="fas fa-list"></i>
                <span class="font-medium">Ingiza Kwa Wingi</span>
            </a>
            <a href="{{ route('income.create') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800">
                <i class="fas fa-plus"></i>
                <span class="font-medium">Ongeza Mapato</span>
            </a>
        </div>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jumla ya Mapato</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($grandTotal, 0) }} TSh</p>
                </div>
                <div class="h-12 w-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-line text-xl text-primary-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Mapato Ya Leo</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($todayTotal ?? 0, 0) }} TSh</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-day text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Weka Hivi Sasa</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($thisMonthTotal ?? 0, 0) }} TSh</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Weka Zamani</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($lastMonthTotal ?? 0, 0) }} TSh</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chart-bar text-xl text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter text-primary-500 mr-2"></i> Chuja Mapato
            </h3>
        </div>
        <form method="GET" action="{{ route('income.index') }}" data-auto-filter="true" data-ajax-target="#incomeTableContainer" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarehe Kuanzia</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarehe Mwisho</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Aina ya Mapato</label>
                    <select name="category_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Zote</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Member Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Muumini</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="member_search" value="{{ request('member_search') }}" class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Tafuta muumini...">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Year -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mwaka</label>
                    <select name="year" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Wote</option>
                        @for($y = date('Y'); $y >= 2020; $y--)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <!-- Month -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mwezi</label>
                    <select name="month" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Wote</option>
                        <option value="1" {{ request('month') == '1' ? 'selected' : '' }}>Januari</option>
                        <option value="2" {{ request('month') == '2' ? 'selected' : '' }}>Februari</option>
                        <option value="3" {{ request('month') == '3' ? 'selected' : '' }}>Machi</option>
                        <option value="4" {{ request('month') == '4' ? 'selected' : '' }}>Aprili</option>
                        <option value="5" {{ request('month') == '5' ? 'selected' : '' }}>Mei</option>
                        <option value="6" {{ request('month') == '6' ? 'selected' : '' }}>Juni</option>
                        <option value="7" {{ request('month') == '7' ? 'selected' : '' }}>Julai</option>
                        <option value="8" {{ request('month') == '8' ? 'selected' : '' }}>Agosti</option>
                        <option value="9" {{ request('month') == '9' ? 'selected' : '' }}>Septemba</option>
                        <option value="10" {{ request('month') == '10' ? 'selected' : '' }}>Oktoba</option>
                        <option value="11" {{ request('month') == '11' ? 'selected' : '' }}>Novemba</option>
                        <option value="12" {{ request('month') == '12' ? 'selected' : '' }}>Desemba</option>
                    </select>
                </div>

                <!-- Receipt Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Namba ya Risiti</label>
                    <input type="text" name="receipt_number" value="{{ request('receipt_number') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Ingiza namba ya risiti...">
                </div>
            </div>

            <!-- Clear Filter Link -->
            <div class="flex justify-end pt-2">
                <a href="{{ route('income.index') }}" class="text-sm text-gray-500 hover:text-primary-600 transition-colors flex items-center gap-1">
                    <i class="fas fa-redo text-xs"></i>
                    <span>Futa Chujio</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Income Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" id="incomeTableContainer">
        @include('panel.income._table')
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95" id="deleteModalContent">
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-2xl border-b border-gray-200 z-10">
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

        <div class="p-6">
            <p class="text-gray-700 mb-2">Je, una uhakika unataka kufuta rekodi hii ya mapato?</p>
            <div class="bg-gray-50 rounded-lg p-3 mb-4">
                <p class="text-sm text-gray-600">Aina: <span class="font-semibold text-gray-900" id="deleteCategory"></span></p>
                <p class="text-sm text-gray-600">Kiasi: <span class="font-bold text-green-600" id="deleteAmount"></span> TSh</p>
            </div>
            <p class="text-sm text-red-600">
                <i class="fas fa-warning mr-1"></i>
                Taarifa zote za mapato haya zitafutwa kabisa.
            </p>
        </div>

        <div class="sticky bottom-0 bg-gray-50 px-6 py-5 rounded-b-2xl border-t border-gray-200 flex justify-end space-x-3">
            <button type="button" onclick="closeDeleteModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-xl hover:bg-gray-300 transition-all duration-200">
                Ghairi
            </button>
            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    <span>Futa Mapato</span>
                </button>
            </form>
        </div>
    </div>
</div>

@include('partials.loading-modal')

<script>
// Export function - must be global
function exportMapato() {
    try {
        // Get current filter values
        const yearSelect = document.querySelector('select[name="year"]');
        const monthSelect = document.querySelector('select[name="month"]');
        const categorySelect = document.querySelector('select[name="category_id"]');
        
        const year = yearSelect ? yearSelect.value : '';
        const month = monthSelect ? monthSelect.value : '';
        const categoryId = categorySelect ? categorySelect.value : '';

        console.log('Filters:', { year, month, categoryId });

        // Build query string
        const params = new URLSearchParams();
        if (year) params.append('year', year);
        if (month) params.append('month', month);
        if (categoryId) params.append('category_id', categoryId);

        // Show loading
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inatengeneza...';
        button.disabled = true;

        // Redirect to export endpoint
        const exportUrl = '{{ route("export.mapato") }}?' + params.toString();
        console.log('Exporting to:', exportUrl);
        
        // Create a temporary link and click it
        const link = document.createElement('a');
        link.href = exportUrl;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Restore button after delay
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 2000);
        
    } catch (error) {
        console.error('Export error:', error);
        alert('Hitilafu wakati wa kutengeneza export: ' + error.message);
        
        // Restore button
        const button = event.target;
        button.innerHTML = '<i class="fas fa-file-excel"></i> <span class="font-medium">Export Excel</span>';
        button.disabled = false;
    }
}
</script>

@push('scripts')
<script>
// Delete modal functions
function confirmDelete(incomeId, category, amount) {
    document.getElementById('deleteCategory').textContent = category;
    document.getElementById('deleteAmount').textContent = amount;
    document.getElementById('deleteForm').action = '/panel/income/' + incomeId;

    const modal = document.getElementById('deleteModal');
    const content = document.getElementById('deleteModalContent');

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const content = document.getElementById('deleteModalContent');

    content.classList.remove('scale-100');
    content.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Handle delete form submission via AJAX
document.getElementById('deleteForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalHtml = submitBtn.innerHTML;

    // Show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inafuta...';

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
        },
        body: new FormData(form)
    })
    .then(async (res) => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw data;
        return data;
    })
    .then((data) => {
        closeDeleteModal();
        if (window.ajaxReloadContainer) {
            window.ajaxReloadContainer('#incomeTableContainer');
        }
        // Optional: show success toast/alert
        alert(data.message || 'Imefanikiwa');
    })
    .catch((err) => {
        alert(err.message || 'Hitilafu imetokea');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalHtml;
    });
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('deleteModal');
        if (modal && !modal.classList.contains('hidden')) {
            closeDeleteModal();
        }
    }
});
</script>
@endpush
@endsection
