@extends('layouts.app')

@section('title', 'Hariri Muumini - Mfumo wa Kanisa')
@section('page-title', 'Hariri Muumini')
@section('page-subtitle', 'Sasisha taarifa za muumini')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('members.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Rudi Orodhani
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Hariri Muumini</h1>
            </div>
            <div class="flex items-center gap-2">
                <p class="text-gray-600">Sasisha taarifa za</p>
                <span class="font-bold text-primary-600">{{ $member->first_name }} {{ $member->last_name }}</span>
            </div>
        </div>

        <!-- Member Number Badge -->
        <div class="bg-primary-50 border border-primary-200 rounded-xl px-5 py-3">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hashtag text-primary-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Namba ya Muumini:</p>
                    <p class="text-lg font-bold text-primary-600">{{ $member->member_number }}</p>
                    <p class="text-xs text-gray-500 mt-1">Imehifadhiwa kikamilifu katika mfumo</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('members.update', $member->id) }}" class="divide-y divide-gray-200">
            @csrf
            @method('PUT')

            <!-- Personal Information Section -->
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Kibinafsi</h3>
                        <p class="text-sm text-gray-600">Sasisha taarifa za mtu binafsi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-semibold text-gray-900 mb-2">
                            Jina la Kwanza <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $member->first_name) }}" required
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('first_name') border-red-500 @enderror"
                                   placeholder="Jina la kwanza">
                        </div>
                        @error('first_name')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label for="middle_name" class="block text-sm font-semibold text-gray-900 mb-2">
                            Jina la Kati
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user-circle text-gray-400"></i>
                            </div>
                            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $member->middle_name) }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('middle_name') border-red-500 @enderror"
                                   placeholder="Jina la kati">
                        </div>
                        @error('middle_name')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-semibold text-gray-900 mb-2">
                            Jina la Ukoo <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-users text-gray-400"></i>
                            </div>
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $member->last_name) }}" required
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('last_name') border-red-500 @enderror"
                                   placeholder="Jina la ukoo">
                        </div>
                        @error('last_name')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="date_of_birth" class="block text-sm font-semibold text-gray-900 mb-2">
                            Tarehe ya Kuzaliwa <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-birthday-cake text-gray-400"></i>
                            </div>
                            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth) }}" required
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('date_of_birth') border-red-500 @enderror">
                        </div>
                        @error('date_of_birth')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            Jinsia <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-3 mt-2">
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-all duration-200">
                                <input type="radio" name="gender" value="Mme" {{ old('gender', $member->gender) == 'Mme' ? 'checked' : '' }} required
                                       class="h-5 w-5 text-primary-600 focus:ring-primary-500 border-gray-300">
                                <div class="ml-3 flex items-center">
                                    <i class="fas fa-male text-blue-600 mr-2"></i>
                                    <span class="text-gray-700 font-medium">Mwanaume</span>
                                </div>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-all duration-200">
                                <input type="radio" name="gender" value="Mke" {{ old('gender', $member->gender) == 'Mke' ? 'checked' : '' }} required
                                       class="h-5 w-5 text-primary-600 focus:ring-primary-500 border-gray-300">
                                <div class="ml-3 flex items-center">
                                    <i class="fas fa-female text-pink-600 mr-2"></i>
                                    <span class="text-gray-700 font-medium">Mwanamke</span>
                                </div>
                            </label>
                        </div>
                        @error('gender')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- ID Number -->
                    <div>
                        <label for="id_number" class="block text-sm font-semibold text-gray-900 mb-2">
                            Namba ya Kitambulisho
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-id-card text-gray-400"></i>
                            </div>
                            <input type="text" id="id_number" name="id_number" value="{{ old('id_number', $member->id_number) }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('id_number') border-red-500 @enderror"
                                   placeholder="Namba ya kitambulisho">
                        </div>
                        @error('id_number')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="p-6 bg-gray-50">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-phone-alt text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Mawasiliano</h3>
                        <p class="text-sm text-gray-600">Sasisha mawasiliano na anwani</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-900 mb-2">
                            Namba ya Simu <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-mobile-alt text-gray-400"></i>
                            </div>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $member->phone) }}" required
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('phone') border-red-500 @enderror"
                                   placeholder="0712345678">
                        </div>
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-900 mb-2">
                            Barua pepe
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email', $member->email) }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('email') border-red-500 @enderror"
                                   placeholder="email@example.com">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-semibold text-gray-900 mb-2">
                            Anwani
                        </label>
                        <div class="relative">
                            <div class="absolute top-3 left-3">
                                <i class="fas fa-map-marker-alt text-gray-400"></i>
                            </div>
                            <textarea id="address" name="address" rows="2"
                                      class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('address') border-red-500 @enderror"
                                      placeholder="Anwani kamili">{{ old('address', $member->address) }}</textarea>
                        </div>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <label for="city" class="block text-sm font-semibold text-gray-900 mb-2">
                            Jiji
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-city text-gray-400"></i>
                            </div>
                            <input type="text" id="city" name="city" value="{{ old('city', $member->city) }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('city') border-red-500 @enderror"
                                   placeholder="Jiji">
                        </div>
                        @error('city')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Region -->
                    <div>
                        <label for="region" class="block text-sm font-semibold text-gray-900 mb-2">
                            Mkoa
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-map text-gray-400"></i>
                            </div>
                            <input type="text" id="region" name="region" value="{{ old('region', $member->region) }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('region') border-red-500 @enderror"
                                   placeholder="Mkoa">
                        </div>
                        @error('region')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Occupation -->
                    <div>
                        <label for="occupation" class="block text-sm font-semibold text-gray-900 mb-2">
                            Kazi
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-briefcase text-gray-400"></i>
                            </div>
                            <input type="text" id="occupation" name="occupation" value="{{ old('occupation', $member->occupation) }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('occupation') border-red-500 @enderror"
                                   placeholder="Kazi yako">
                        </div>
                        @error('occupation')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Christian Information Section -->
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-church text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Kikristo</h3>
                        <p class="text-sm text-gray-600">Sasisha taarifa za uanachama na ibada</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Baptism Date -->
                    <div>
                        <label for="baptism_date" class="block text-sm font-semibold text-gray-900 mb-2">
                            Tarehe ya Ubatizo
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-water text-gray-400"></i>
                            </div>
                            <input type="date" id="baptism_date" name="baptism_date" value="{{ old('baptism_date', $member->baptism_date) }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('baptism_date') border-red-500 @enderror">
                        </div>
                        @error('baptism_date')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmation Date -->
                    <div>
                        <label for="confirmation_date" class="block text-sm font-semibold text-gray-900 mb-2">
                            Tarehe ya Uthibitisho
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-hands-praying text-gray-400"></i>
                            </div>
                            <input type="date" id="confirmation_date" name="confirmation_date" value="{{ old('confirmation_date', $member->confirmation_date) }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('confirmation_date') border-red-500 @enderror">
                        </div>
                        @error('confirmation_date')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Membership Date -->
                    <div>
                        <label for="membership_date" class="block text-sm font-semibold text-gray-900 mb-2">
                            Tarehe ya Ujumbe <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="date" id="membership_date" name="membership_date" value="{{ old('membership_date', $member->membership_date) }}" required
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('membership_date') border-red-500 @enderror">
                        </div>
                        @error('membership_date')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Marital Status -->
                    <div>
                        <label for="marital_status" class="block text-sm font-semibold text-gray-900 mb-2">
                            Hali ya Ndoa <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-heart text-gray-400"></i>
                            </div>
                            <select id="marital_status" name="marital_status" required
                                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('marital_status') border-red-500 @enderror">
                                <option value="">Chagua Hali ya Ndoa</option>
                                <option value="Hajaoa/Hajaolewa" {{ old('marital_status', $member->marital_status) == 'Hajaoa/Hajaolewa' ? 'selected' : '' }}>Hajaoa/Hajaolewa</option>
                                <option value="Ameoa/Ameolewa" {{ old('marital_status', $member->marital_status) == 'Ameoa/Ameolewa' ? 'selected' : '' }}>Ameoa/Ameolewa</option>
                                <option value="Mjane/Mgane" {{ old('marital_status', $member->marital_status) == 'Mjane/Mgane' ? 'selected' : '' }}>Mjane/Mgane</option>
                                <option value="Talaka" {{ old('marital_status', $member->marital_status) == 'Talaka' ? 'selected' : '' }}>Talaka</option>
                            </select>
                        </div>
                        @error('marital_status')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Member Status -->
                    <div class="md:col-span-2">
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" name="is_active" id="is_active" value="1"
                                           {{ old('is_active', $member->is_active) ? 'checked' : '' }} class="sr-only">
                                    <div class="block bg-gray-300 w-10 h-6 rounded-full"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                                </div>
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-900">Muumini Hai</span>
                                    <p class="text-xs text-gray-600 mt-1">
                                        Weka kuwa hai ikiwa muumini bado anahudhuria kanisa
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="p-6 bg-gray-50">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-sticky-note text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Maelezo Mengine</h3>
                        <p class="text-sm text-gray-600">Ongeza au sasisha maelezo mengine muhimu</p>
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-semibold text-gray-900 mb-2">
                        Maelezo (Hiari)
                    </label>
                    <div class="relative">
                        <div class="absolute top-3 left-3">
                            <i class="fas fa-edit text-gray-400"></i>
                        </div>
                        <textarea id="notes" name="notes" rows="3"
                                  class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('notes') border-red-500 @enderror"
                                  placeholder="Andika maelezo yoyote mengine muhimu...">{{ old('notes', $member->notes) }}</textarea>
                    </div>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="sticky bottom-0 bg-white px-6 py-5 border-t border-gray-200 flex justify-end space-x-4">
                <a href="{{ route('members.index') }}"
                   class="px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Ghairi</span>
                </a>
                <button type="submit"
                        class="px-8 py-3 bg-primary-600 text-white font-bold rounded-lg hover:bg-primary-700 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fas fa-save"></i>
                    <span>Sasisha Taarifa</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Toggle Switch Style */
    input:checked ~ .dot {
        transform: translateX(100%);
        background-color: #360958;
    }
    input:checked ~ .block {
        background-color: #360958;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle switch functionality for is_active
        const isActiveCheckbox = document.getElementById('is_active');
        const toggleSwitch = isActiveCheckbox.parentElement.parentElement;

        isActiveCheckbox.addEventListener('change', function() {
            const dot = toggleSwitch.querySelector('.dot');
            const block = toggleSwitch.querySelector('.block');

            if (this.checked) {
                dot.style.backgroundColor = '#360958';
                block.style.backgroundColor = '#4c1d95';
            } else {
                dot.style.backgroundColor = '#fff';
                block.style.backgroundColor = '#d1d5db';
            }
        });

        // Initialize toggle switch state
        if (isActiveCheckbox.checked) {
            const dot = toggleSwitch.querySelector('.dot');
            const block = toggleSwitch.querySelector('.block');
            dot.style.backgroundColor = '#360958';
            block.style.backgroundColor = '#4c1d95';
        }

        // Format dates for better display
        const dateInputs = document.querySelectorAll('input[type="date"]');
        dateInputs.forEach(input => {
            if (input.value) {
                // Convert from database format to display format if needed
                const date = new Date(input.value);
                if (!isNaN(date.getTime())) {
                    input.value = date.toISOString().split('T')[0];
                }
            }
        });
    });
</script>
@endsection
