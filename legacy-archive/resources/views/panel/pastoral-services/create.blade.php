@extends('layouts.app')

@section('title', 'Omba Huduma ya Kichungaji - Mfumo wa Kanisa')
@section('page-title', 'Omba Huduma ya Kichungaji')
@section('page-subtitle', 'Jaza fomu ili kuomba huduma ya kichungaji')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('pastoral-services.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Rudi Orodhani
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Omba Huduma ya Kichungaji</h1>
            </div>
            <p class="text-gray-600">Jaza taarifa za ombi la huduma ya kichungaji</p>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('pastoral-services.store') }}" class="divide-y divide-gray-200">
            @csrf

            <!-- Service Information Section -->
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-hands-praying text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Huduma</h3>
                        <p class="text-sm text-gray-600">Jaza taarifa za kimsingi za ombi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Member Selection -->
                    <div class="md:col-span-2">
                        <label for="member_id" class="block text-sm font-semibold text-gray-900 mb-2">
                            Muumini <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <select id="member_id" name="member_id" required
                                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('member_id') border-red-500 @enderror">
                                <option value="">Chagua Muumini</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                        {{ $member->first_name }} {{ $member->middle_name }} {{ $member->last_name }} ({{ $member->member_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('member_id')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Service Type -->
                    <div class="md:col-span-2">
                        <label for="service_type" class="block text-sm font-semibold text-gray-900 mb-2">
                            Aina ya Huduma <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-list-alt text-gray-400"></i>
                            </div>
                            <select id="service_type" name="service_type" required
                                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('service_type') border-red-500 @enderror">
                                <option value="">Chagua Aina ya Huduma</option>
                                @foreach($serviceTypes as $key => $value)
                                    <option value="{{ $key }}" {{ old('service_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('service_type')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preferred Date -->
                    <div class="md:col-span-2">
                        <label for="preferred_date" class="block text-sm font-semibold text-gray-900 mb-2">
                            Tarehe Inayopendelewa
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="date" id="preferred_date" name="preferred_date" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('preferred_date') border-red-500 @enderror">
                        </div>
                        <p class="mt-2 text-xs text-gray-600">
                            <i class="fas fa-info-circle text-primary-500 mr-1"></i>
                            Chagua tarehe unayopendelea kupata huduma hii
                        </p>
                        @error('preferred_date')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="p-6 bg-gray-50">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-info-circle text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Maelezo za Ziada</h3>
                        <p class="text-sm text-gray-600">Taarifa zingine muhimu (hiari)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                            Maelezo (Hiari)
                        </label>
                        <div class="relative">
                            <div class="absolute top-3 left-3">
                                <i class="fas fa-sticky-note text-gray-400"></i>
                            </div>
                            <textarea id="description" name="description" rows="5"
                                      class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('description') border-red-500 @enderror"
                                      placeholder="Andika maelezo zaidi kuhusu ombi lako...">{{ old('description') }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="sticky bottom-0 bg-white px-6 py-5 border-t border-gray-200 flex justify-end space-x-4">
                <a href="{{ route('pastoral-services.index') }}"
                   class="px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Ghairi</span>
                </a>
                <button type="submit"
                        class="px-8 py-3 bg-primary-600 text-white font-bold rounded-lg hover:bg-primary-700 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fas fa-paper-plane"></i>
                    <span>Wasilisha Ombi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
