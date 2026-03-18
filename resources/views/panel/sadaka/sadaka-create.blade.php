@extends('layouts.app')

@section('title', 'Ongeza Sadaka - Mfumo wa Kanisa')
@section('page-title', 'Ongeza Sadaka Mpya')
@section('page-subtitle', 'Jaza fomu ili kuongeza rekodi mpya ya sadaka')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Ongeza Sadaka Mpya</h1>
            <p class="text-gray-600 mt-2">Jaza taarifa za sadaka mpya</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('offerings.index') }}" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-xl transition-all duration-200 flex items-center gap-2 hover:bg-gray-300">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Rudi Nyuma</span>
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 bg-gradient-to-r from-primary-600 to-primary-700">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-hand-holding-heart mr-2"></i> Taarifa za Sadaka
            </h3>
        </div>

        <form action="{{ route('offerings.store') }}" method="POST" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Collection Date -->
                <div>
                    <label for="collection_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-primary-500 mr-1"></i> Tarehe ya Kukusanya
                    </label>
                    <input type="date"
                           id="collection_date"
                           name="collection_date"
                           value="{{ old('collection_date', date('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('collection_date') border-red-500 @enderror"
                           required>
                    @error('collection_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="income_category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag text-primary-500 mr-1"></i> Aina ya Sadaka
                    </label>
                    <select id="income_category_id"
                            name="income_category_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('income_category_id') border-red-500 @enderror"
                            required>
                        <option value="">-- Chagua Aina ya Sadaka --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('income_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('income_category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave text-primary-500 mr-1"></i> Kiasi (TZS)
                    </label>
                    <input type="number"
                           id="amount"
                           name="amount"
                           value="{{ old('amount') }}"
                           placeholder="0"
                           min="0"
                           step="1"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-xl font-bold text-primary-600 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('amount') border-red-500 @enderror"
                           required>
                    @error('amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Member (Optional) -->
                <div>
                    <label for="member_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-primary-500 mr-1"></i> Muumini (Hiari)
                    </label>
                    <select id="member_id"
                            name="member_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Chagua Muumini (Hiari) --</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                {{ $member->full_name }} {{ $member->phone ? '- ' . $member->phone : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sticky-note text-primary-500 mr-1"></i> Maelezo (Hiari)
                    </label>
                    <textarea id="notes"
                              name="notes"
                              rows="3"
                              placeholder="Maelezo ya ziada kuhusu sadaka hii..."
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('offerings.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Ghairi</span>
                </a>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Hifadhi Sadaka</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
