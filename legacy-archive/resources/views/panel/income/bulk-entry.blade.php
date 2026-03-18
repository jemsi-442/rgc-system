@extends('layouts.app')

@section('title', 'Ingiza Mapato Kwa Wingi - Mfumo wa Kanisa')
@section('page-title', 'Ingiza Mapato Kwa Wingi')
@section('page-subtitle', 'Ongeza mapato mengi kwa wakati mmoja')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('income.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Rudi Orodhani
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Ingiza Mapato Kwa Wingi</h1>
            </div>
            <p class="text-gray-600">Ongeza mapato mengi kwa wakati mmoja</p>
        </div>
        <button type="button" id="addRowBtn" class="inline-flex items-center px-5 py-3 bg-secondary-500 hover:bg-secondary-600 text-white font-bold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg" style="background-color: #efc120;">
            <i class="fas fa-plus mr-2"></i>
            Ongeza Safu
        </button>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('income.bulk-store') }}" id="bulkIncomeForm">
            @csrf

            <!-- Table Section -->
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-table text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Orodha ya Mapato</h3>
                        <p class="text-sm text-gray-600">Jaza taarifa za kila mstari wa mapato</p>
                    </div>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200" id="incomeTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aina ya Mapato <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tarehe <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kiasi (TSh) <span class="text-red-500">*</span></th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Muumini</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Maelezo</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Vitendo</th>
                            </tr>
                        </thead>
                        <tbody id="incomeTableBody" class="bg-white divide-y divide-gray-200">
                            <!-- Initial Row -->
                            <tr class="income-row hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900 row-number">1</td>
                                <td class="px-4 py-3">
                                    <select name="entries[0][income_category_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm text-gray-900">
                                        <option value="">Chagua Aina</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="date" name="entries[0][collection_date]" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm text-gray-900">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="entries[0][amount]" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm text-gray-900" placeholder="0.00">
                                </td>
                                <td class="px-4 py-3">
                                    <select name="entries[0][member_id]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm text-gray-900">
                                        <option value="">Chagua Muumini</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}">{{ $member->member_number }} - {{ $member->first_name }} {{ $member->last_name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="entries[0][notes]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm text-gray-900" placeholder="Maelezo...">
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" class="text-red-600 hover:text-red-800 hover:bg-red-50 p-2 rounded-lg transition-all duration-200 removeRowBtn" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-list-ol text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Jumla ya Safu</p>
                            <p class="text-2xl font-bold text-gray-900" id="totalRows">1</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle text-primary-500 mr-1"></i>
                        Bonyeza "Ongeza Safu" kuongeza safu mpya
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="sticky bottom-0 bg-white px-6 py-5 border-t border-gray-200 flex justify-end space-x-4">
                <a href="{{ route('income.index') }}"
                   class="px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Ghairi</span>
                </a>
                <button type="submit"
                        class="px-8 py-3 bg-primary-600 text-white font-bold rounded-lg hover:bg-primary-700 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fas fa-save"></i>
                    <span>Hifadhi Zote</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Help Text -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-400 mt-1 mr-3"></i>
            <div>
                <p class="text-sm text-blue-700">
                    <strong>Maelekezo:</strong> Tumia form hii kuingiza mapato mengi kwa wakati mmoja. Bofya "Ongeza Safu" kuongeza rekodi mpya, na bofya ikoni ya takataka kufuta safu usiyoihitaji.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    let rowIndex = 1;

    document.getElementById('addRowBtn').addEventListener('click', function() {
        const tableBody = document.getElementById('incomeTableBody');
        const newRow = tableBody.querySelector('.income-row').cloneNode(true);

        // Update row number
        newRow.querySelector('.row-number').textContent = rowIndex + 1;

        // Update input names
        newRow.querySelectorAll('input, select').forEach(function(input) {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace(/\[\d+\]/, '[' + rowIndex + ']'));
            }
            // Clear values except date
            if (input.type !== 'date') {
                input.value = '';
            } else {
                input.value = '{{ date("Y-m-d") }}';
            }
        });

        // Enable remove button
        newRow.querySelector('.removeRowBtn').disabled = false;

        tableBody.appendChild(newRow);
        rowIndex++;
        updateTotalRows();
    });

    document.getElementById('incomeTableBody').addEventListener('click', function(e) {
        if (e.target.closest('.removeRowBtn')) {
            const rows = document.querySelectorAll('.income-row');
            if (rows.length > 1) {
                e.target.closest('.income-row').remove();
                updateRowNumbers();
                updateTotalRows();
            }
        }
    });

    function updateRowNumbers() {
        document.querySelectorAll('.income-row').forEach(function(row, index) {
            row.querySelector('.row-number').textContent = index + 1;
        });
    }

    function updateTotalRows() {
        const totalRows = document.querySelectorAll('.income-row').length;
        document.getElementById('totalRows').textContent = totalRows;
    }
</script>
@endsection
