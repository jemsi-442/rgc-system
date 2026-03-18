@extends('layouts.app')

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
                <h1 class="text-3xl font-bold text-gray-900">Sajili Muumini Mpya</h1>
            </div>
            <p class="text-gray-600">Jaza taarifa zote za muumini wa kanisa</p>
        </div>

        <!-- Member Number Badge -->
        <div class="bg-primary-50 border border-primary-200 rounded-xl px-5 py-3">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hashtag text-primary-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Namba ya Muumini/Bahasha:</p>
                    <p class="text-lg font-bold text-primary-600">{{ $nextMemberNumber }}</p>
                    <p class="text-xs text-gray-500 mt-1">Itazalishwa moja kwa moja na QR Code</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('members.store') }}" class="divide-y divide-gray-200">
            @csrf

            <!-- Personal Information Section -->
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Kibinafsi</h3>
                        <p class="text-sm text-gray-600">Jaza taarifa za mtu binafsi</p>
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
                            <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
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
                            <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
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
                            <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
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
                            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required
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
                                <input type="radio" name="gender" value="Mme" {{ old('gender') == 'Mme' ? 'checked' : '' }} required
                                       class="h-5 w-5 text-primary-600 focus:ring-primary-500 border-gray-300">
                                <div class="ml-3 flex items-center">
                                    <i class="fas fa-male text-blue-600 mr-2"></i>
                                    <span class="text-gray-700 font-medium">Mwanaume</span>
                                </div>
                            </label>
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-all duration-200">
                                <input type="radio" name="gender" value="Mke" {{ old('gender') == 'Mke' ? 'checked' : '' }} required
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
                            <input type="text" id="id_number" name="id_number" value="{{ old('id_number') }}"
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
                        <p class="text-sm text-gray-600">Jaza mawasiliano na anwani</p>
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
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
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
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
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
                                      placeholder="Anwani kamili">{{ old('address') }}</textarea>
                        </div>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- House Number -->
                    <div>
                        <label for="house_number" class="block text-sm font-semibold text-gray-900 mb-2">
                            Namba ya Nyumba
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-home text-gray-400"></i>
                            </div>
                            <input type="text" id="house_number" name="house_number" value="{{ old('house_number') }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('house_number') border-red-500 @enderror"
                                   placeholder="A123">
                        </div>
                        @error('house_number')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Block Number -->
                    <div>
                        <label for="block_number" class="block text-sm font-semibold text-gray-900 mb-2">
                            Namba ya Block
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-building text-gray-400"></i>
                            </div>
                            <input type="text" id="block_number" name="block_number" value="{{ old('block_number') }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('block_number') border-red-500 @enderror"
                                   placeholder="Block 5">
                        </div>
                        @error('block_number')
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
                            <input type="text" id="city" name="city" value="{{ old('city') }}"
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
                            <input type="text" id="region" name="region" value="{{ old('region') }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('region') border-red-500 @enderror"
                                   placeholder="Mkoa">
                        </div>
                        @error('region')
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
                        <p class="text-sm text-gray-600">Taarifa za uanachama na ibada</p>
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
                            <input type="date" id="baptism_date" name="baptism_date" value="{{ old('baptism_date') }}"
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
                            <input type="date" id="confirmation_date" name="confirmation_date" value="{{ old('confirmation_date') }}"
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
                            <input type="date" id="membership_date" name="membership_date" value="{{ old('membership_date', date('Y-m-d')) }}" required
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('membership_date') border-red-500 @enderror">
                        </div>
                        @error('membership_date')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Marital Status -->
                    <div>
                        <label for="marital_status" class="block text-sm font-semibold text-gray-900 mb-2">
                            Hali ya Ndoa
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-heart text-gray-400"></i>
                            </div>
                            <select id="marital_status" name="marital_status"
                                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('marital_status') border-red-500 @enderror">
                                <option value="">Chagua Hali ya Ndoa</option>
                                <option value="Hajaoa/Hajaolewa" {{ old('marital_status') == 'Hajaoa/Hajaolewa' ? 'selected' : '' }}>Hajaoa/Hajaolewa</option>
                                <option value="Ameoa/Ameolewa" {{ old('marital_status') == 'Ameoa/Ameolewa' ? 'selected' : '' }}>Ameoa/Ameolewa</option>
                                <option value="Mjane/Mgane" {{ old('marital_status') == 'Mjane/Mgane' ? 'selected' : '' }}>Mjane/Mgane</option>
                                <option value="Talaka" {{ old('marital_status') == 'Talaka' ? 'selected' : '' }}>Talaka</option>
                            </select>
                        </div>
                        @error('marital_status')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Special Group -->
                    <div>
                        <label for="special_group" class="block text-sm font-semibold text-gray-900 mb-2">
                            Kundi Maalum
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-users text-gray-400"></i>
                            </div>
                            <input type="text" id="special_group" name="special_group" value="{{ old('special_group') }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('special_group') border-red-500 @enderror"
                                   placeholder="Kwaya, Fellowship, etc.">
                        </div>
                        @error('special_group')
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
                            <input type="text" id="occupation" name="occupation" value="{{ old('occupation') }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('occupation') border-red-500 @enderror"
                                   placeholder="Kazi yako">
                        </div>
                        @error('occupation')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Spouse Information Section -->
            <div class="p-6 bg-gray-50">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-ring text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Mwenzi/Mke</h3>
                        <p class="text-sm text-gray-600">Jaza ikiwa muumini ameoa/ameolewa</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Spouse Name -->
                    <div>
                        <label for="spouse_name" class="block text-sm font-semibold text-gray-900 mb-2">
                            Jina la Mwenzi/Mke
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user-friends text-gray-400"></i>
                            </div>
                            <input type="text" id="spouse_name" name="spouse_name" value="{{ old('spouse_name') }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('spouse_name') border-red-500 @enderror"
                                   placeholder="Jina la mwenzi/mke">
                        </div>
                        @error('spouse_name')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Spouse Phone -->
                    <div>
                        <label for="spouse_phone" class="block text-sm font-semibold text-gray-900 mb-2">
                            Simu ya Mwenzi/Mke
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-mobile-alt text-gray-400"></i>
                            </div>
                            <input type="tel" id="spouse_phone" name="spouse_phone" value="{{ old('spouse_phone') }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('spouse_phone') border-red-500 @enderror"
                                   placeholder="0712345678">
                        </div>
                        @error('spouse_phone')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Neighbor Information Section -->
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-people-arrows text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Jirani</h3>
                        <p class="text-sm text-gray-600">Jaza jirani wa karibu kwa dharura</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Neighbor Name -->
                    <div>
                        <label for="neighbor_name" class="block text-sm font-semibold text-gray-900 mb-2">
                            Jina la Jirani
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user-tie text-gray-400"></i>
                            </div>
                            <input type="text" id="neighbor_name" name="neighbor_name" value="{{ old('neighbor_name') }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('neighbor_name') border-red-500 @enderror"
                                   placeholder="Jina la jirani">
                        </div>
                        @error('neighbor_name')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Neighbor Phone -->
                    <div>
                        <label for="neighbor_phone" class="block text-sm font-semibold text-gray-900 mb-2">
                            Simu ya Jirani
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                            <input type="tel" id="neighbor_phone" name="neighbor_phone" value="{{ old('neighbor_phone') }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('neighbor_phone') border-red-500 @enderror"
                                   placeholder="0712345678">
                        </div>
                        @error('neighbor_phone')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Church Leadership Section -->
            <div class="p-6 bg-gray-50">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user-tie text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Uongozi wa Kanisa</h3>
                        <p class="text-sm text-gray-600">Jaza ikiwa muumini ana jukumu maalum</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Church Elder -->
                    <div>
                        <label for="church_elder" class="block text-sm font-semibold text-gray-900 mb-2">
                            Mzee wa Kanisa
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user-graduate text-gray-400"></i>
                            </div>
                            <input type="text" id="church_elder" name="church_elder" value="{{ old('church_elder') }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('church_elder') border-red-500 @enderror"
                                   placeholder="Jina la mzee wa kanisa">
                        </div>
                        @error('church_elder')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Pledge Number -->
                    <div>
                        <label for="pledge_number" class="block text-sm font-semibold text-gray-900 mb-2">
                            Namba ya Ahadi
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-handshake text-gray-400"></i>
                            </div>
                            <input type="text" id="pledge_number" name="pledge_number" value="{{ old('pledge_number') }}"
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('pledge_number') border-red-500 @enderror"
                                   placeholder="AH001">
                        </div>
                        @error('pledge_number')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- System Access Section -->
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-lock text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Ufikiaji wa Mfumo</h3>
                        <p class="text-sm text-gray-600">Usanidi wa akaunti na majukumu</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Create User Account -->
                    <div>
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <label class="flex items-center cursor-pointer">
                                <div class="relative">
                                    <input type="checkbox" name="create_user_account" id="create_user_account" value="1"
                                           {{ old('create_user_account') ? 'checked' : '' }} class="sr-only">
                                    <div class="block bg-gray-300 w-10 h-6 rounded-full"></div>
                                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition"></div>
                                </div>
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-900">Tengeneza Akaunti ya Mtumiaji</span>
                                    <p class="text-xs text-gray-600 mt-1">
                                        Username = Namba ya Kadi, Password = Jina la Ukoo
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Role -->
                    <div>
                        <label for="role_id" class="block text-sm font-semibold text-gray-900 mb-2">
                            Jukumu (Role)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user-shield text-gray-400"></i>
                            </div>
                            <select id="role_id" name="role_id"
                                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('role_id') border-red-500 @enderror">
                                <option value="">Chagua Jukumu</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">
                            <i class="fas fa-info-circle text-primary-500 mr-1"></i>
                            Chagua ikiwa muumini atakuwa na majukumu maalum (Mhasibu, Mchungaji, n.k.)
                        </p>
                        @error('role_id')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department_id" class="block text-sm font-semibold text-gray-900 mb-2">
                            Idara (Department)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-sitemap text-gray-400"></i>
                            </div>
                            <select id="department_id" name="department_id"
                                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('department_id') border-red-500 @enderror">
                                <option value="">Chagua Idara</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">
                            <i class="fas fa-info-circle text-primary-500 mr-1"></i>
                            Chagua ikiwa muumini ni sehemu ya idara maalum (Uhasibu, Muziki, Ujenzi, n.k.)
                        </p>
                        @error('department_id')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumuiya -->
                    <div>
                        <label for="jumuiya_id" class="block text-sm font-semibold text-gray-900 mb-2">
                            Jumuiya
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-users text-gray-400"></i>
                            </div>
                            <select id="jumuiya_id" name="jumuiya_id"
                                    class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('jumuiya_id') border-red-500 @enderror">
                                <option value="">Chagua Jumuiya</option>
                                @foreach($jumuiyas as $jumuiya)
                                    <option value="{{ $jumuiya->id }}" {{ old('jumuiya_id') == $jumuiya->id ? 'selected' : '' }}>
                                        {{ $jumuiya->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">
                            <i class="fas fa-info-circle text-primary-500 mr-1"></i>
                            Chagua jumuiya ambayo muumini atakuwa mwanachama
                        </p>
                        @error('jumuiya_id')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
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
                        <p class="text-sm text-gray-600">Ongeza maelezo yoyote mengine muhimu</p>
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
                                  placeholder="Andika maelezo yoyote mengine muhimu...">{{ old('notes') }}</textarea>
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
                    <span>Hifadhi Muumini</span>
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
        // Auto-select today's date for membership date if not set
        const membershipDateInput = document.getElementById('membership_date');
        if (membershipDateInput && !membershipDateInput.value) {
            const today = new Date().toISOString().split('T')[0];
            membershipDateInput.value = today;
        }

        // Toggle switch functionality
        const createAccountCheckbox = document.getElementById('create_user_account');
        const toggleSwitch = createAccountCheckbox.parentElement.parentElement;

        createAccountCheckbox.addEventListener('change', function() {
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
        if (createAccountCheckbox.checked) {
            const dot = toggleSwitch.querySelector('.dot');
            const block = toggleSwitch.querySelector('.block');
            dot.style.backgroundColor = '#360958';
            block.style.backgroundColor = '#4c1d95';
        }
    });
</script>
@endsection
