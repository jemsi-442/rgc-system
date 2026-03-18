@extends('layouts.app')

@section('title', 'Mipangilio - Mfumo wa Kanisa')
@section('page-title', 'Mipangilio')
@section('page-subtitle', 'Simamia mipangilio ya mfumo')

@section('styles')
<style>
    /* Prevent FOUC - Critical inline styles */
    .settings-container {
        opacity: 1;
        transition: opacity 0.2s ease;
    }
    .tab-btn {
        display: inline-flex;
        align-items: center;
        padding: 1rem 1.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-bottom-width: 2px;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    .tab-content {
        padding: 1.5rem;
    }
    .settings-card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    .settings-header {
        padding: 1rem 1.5rem;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
    .settings-form-group {
        margin-bottom: 1rem;
    }
    .settings-form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    .settings-form-group input,
    .settings-form-group textarea,
    .settings-form-group select {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }
    .settings-form-group input:focus,
    .settings-form-group textarea:focus,
    .settings-form-group select:focus {
        outline: none;
        border-color: #360958;
        box-shadow: 0 0 0 3px rgba(54, 9, 88, 0.1);
    }
</style>
@endsection

@section('content')
<div class="space-y-6 settings-container">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-2">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Mipangilio</h1>
            <p class="text-gray-600 mt-2">Simamia mipangilio ya akaunti yako na mfumo</p>
        </div>
    </div>

    <!-- Tabs Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 bg-gray-50">
            <nav class="flex -mb-px overflow-x-auto">
                @if(!Auth::user()->isMwanachama())
                <button onclick="switchTab('church')" id="tab-church" class="tab-btn py-4 px-6 text-sm font-medium border-b-2 border-primary-500 text-primary-600 bg-white whitespace-nowrap transition-all duration-200">
                    <i class="fas fa-church mr-2"></i>Kanisa
                </button>
                <button onclick="switchTab('profile')" id="tab-profile" class="tab-btn py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                    <i class="fas fa-user mr-2"></i>Wasifu
                </button>
                @else
                <button onclick="switchTab('profile')" id="tab-profile" class="tab-btn py-4 px-6 text-sm font-medium border-b-2 border-primary-500 text-primary-600 bg-white whitespace-nowrap transition-all duration-200">
                    <i class="fas fa-user mr-2"></i>Wasifu
                </button>
                @endif
                <button onclick="switchTab('password')" id="tab-password" class="tab-btn py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                    <i class="fas fa-key mr-2"></i>Nywila
                </button>
                @if(Auth::user()->isMchungaji())
                <button onclick="switchTab('jumuiya')" id="tab-jumuiya" class="tab-btn py-4 px-6 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100 whitespace-nowrap transition-all duration-200">
                    <i class="fas fa-users-cog mr-2"></i>Jumuiya
                </button>
                @endif
            </nav>
        </div>

        <!-- Church Settings Tab (hidden for members) -->
        @if(!Auth::user()->isMwanachama())
        <div id="content-church" class="tab-content p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Taarifa za Kanisa</h3>
            <form action="{{ route('settings.church.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jina la Kanisa *</label>
                        <input type="text" name="church_name" value="{{ old('church_name', \App\Models\Setting::get('church_name', 'KANISA LA KIINJILI LA KILUTHERI TANZANIA')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Dayosisi</label>
                        <input type="text" name="diocese" value="{{ old('diocese', \App\Models\Setting::get('diocese', 'DAYOSISI YA MASHARIKI NA PWANI')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jimbo</label>
                        <input type="text" name="district" value="{{ old('district', \App\Models\Setting::get('district', 'JIMBO LA MAGHARIBI')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usharika</label>
                        <input type="text" name="parish" value="{{ old('parish', \App\Models\Setting::get('parish', 'USHARIKA WA MAKABE')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mtaa</label>
                        <input type="text" name="mtaa" value="{{ old('mtaa', \App\Models\Setting::get('mtaa', 'MTAA WA RGC')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Simu</label>
                        <input type="text" name="church_phone" value="{{ old('church_phone', \App\Models\Setting::get('church_phone')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Barua Pepe</label>
                        <input type="email" name="church_email" value="{{ old('church_email', \App\Models\Setting::get('church_email')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tovuti</label>
                        <input type="url" name="church_website" value="{{ old('church_website', \App\Models\Setting::get('church_website')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Anwani</label>
                    <textarea name="church_address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">{{ old('church_address', \App\Models\Setting::get('church_address')) }}</textarea>
                </div>

                <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg">
                    <i class="fas fa-save mr-2"></i>Hifadhi Mabadiliko
                </button>
            </form>
        </div>
        @endif

        <!-- Profile Settings Tab -->
        <div id="content-profile" class="tab-content p-6 {{ Auth::user()->isMwanachama() ? '' : 'hidden' }}">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Wasifu Wako</h3>
            <form action="{{ route('settings.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jina Kamili *</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Barua Pepe *</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                </div>

                <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg">
                    <i class="fas fa-save mr-2"></i>Sasisha Wasifu
                </button>
            </form>
        </div>

        <!-- Password Settings Tab -->
        <div id="content-password" class="tab-content p-6 hidden">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Badilisha Nywila</h3>

            @if(Auth::user()->needsPasswordChange())
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-lg"></i>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-yellow-800">Badilisha Nywila Yako</h4>
                        <p class="mt-1 text-sm text-yellow-700">Unatumia nywila ya msingi. Tafadhali badilisha nywila yako kwa usalama zaidi.</p>
                    </div>
                </div>
            </div>
            @endif

            <form action="{{ route('settings.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nywila ya Sasa *</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password" required
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 @error('current_password') border-red-500 @enderror">
                        <button type="button" onclick="togglePassword('current_password')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="current_password_icon"></i>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nywila Mpya *</label>
                    <div class="relative">
                        <input type="password" name="password" id="new_password" required
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500 @error('password') border-red-500 @enderror">
                        <button type="button" onclick="togglePassword('new_password')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="new_password_icon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-xs font-medium text-gray-700 mb-2">Nywila lazima iwe na:</p>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li id="req-length" class="flex items-center"><i class="fas fa-circle text-gray-300 mr-2 text-[6px]"></i>Angalau herufi 6</li>
                            <li id="req-upper" class="flex items-center"><i class="fas fa-circle text-gray-300 mr-2 text-[6px]"></i>Herufi kubwa (A-Z)</li>
                            <li id="req-lower" class="flex items-center"><i class="fas fa-circle text-gray-300 mr-2 text-[6px]"></i>Herufi ndogo (a-z)</li>
                            <li id="req-number" class="flex items-center"><i class="fas fa-circle text-gray-300 mr-2 text-[6px]"></i>Nambari (0-9)</li>
                            <li id="req-special" class="flex items-center"><i class="fas fa-circle text-gray-300 mr-2 text-[6px]"></i>Alama maalum (!@#$%^&*)</li>
                        </ul>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thibitisha Nywila Mpya *</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-primary-500 focus:border-primary-500">
                        <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-eye" id="password_confirmation_icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-lg">
                    <i class="fas fa-key mr-2"></i>Badilisha Nywila
                </button>
            </form>
        </div>

        <!-- Jumuiya Settings Tab (Admin Only) -->
        @if(Auth::user()->isMchungaji())
        <div id="content-jumuiya" class="tab-content p-6 hidden">
            <!-- Header Section -->
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-users-cog bg-primary-100 p-4 rounded-xl text-primary-500 mr-2"></i>
                        Usimamizi wa Jumuiya
                    </h3>
                    <p class="text-gray-600 mt-1">Ongeza, hariri au futa jumuiya za kanisa</p>
                </div>
                <button onclick="openCreateJumuiyaModal()" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800">
                    <i class="fas fa-plus"></i>
                    <span class="font-medium">Ongeza Jumuiya</span>
                </button>
            </div>

            <!-- Stats Cards -->
            @php
                $totalJumuiyas = count($jumuiyas ?? []);
                $activeJumuiyas = collect($jumuiyas ?? [])->where('is_active', true)->count();
                $totalMembersInJumuiyas = collect($jumuiyas ?? [])->sum(function($j) { return $j->members()->count(); });
                $jumuiyasWithLeaders = collect($jumuiyas ?? [])->whereNotNull('leader_id')->count();
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Jumla ya Jumuiya</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalJumuiyas }}</p>
                        </div>
                        <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-layer-group text-xl text-blue-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Jumuiya Hai</p>
                            <p class="text-2xl font-bold text-green-600">{{ $activeJumuiyas }}</p>
                        </div>
                        <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-xl text-green-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Wanachama Wote</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalMembersInJumuiyas }}</p>
                        </div>
                        <div class="h-12 w-12 bg-primary-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-users text-xl text-primary-600"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Zina Viongozi</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $jumuiyasWithLeaders }}</p>
                        </div>
                        <div class="h-12 w-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-tie text-xl text-yellow-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jumuiya Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <!-- Table Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 border-b border-gray-200 bg-gray-50">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-list text-primary-500 mr-2"></i> Orodha ya Jumuiya
                            <span class="ml-3 text-sm text-gray-600 bg-gray-200 px-3 py-1 rounded-full">
                                {{ $totalJumuiyas }} jumuiya
                            </span>
                        </h4>
                    </div>
                    <div class="mt-2 sm:mt-0">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fas fa-info-circle text-primary-500"></i>
                            <span>Bofya vitendo kuangalia au kuhariri</span>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full" id="jumuiyaTable">
                        <thead>
                            <tr class="bg-primary-600 text-white text-sm">
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-users mr-2"></i>
                                        Jina la Jumuiya
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-tie mr-2"></i>
                                        Kiongozi
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-friends mr-2"></i>
                                        Wanachama
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-circle mr-2"></i>
                                        Hali
                                    </div>
                                </th>
                                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-cogs mr-2"></i>
                                        Vitendo
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="jumuiyaTableBody">
                            @forelse($jumuiyas ?? [] as $jumuiya)
                            <tr class="bg-white hover:bg-gray-50 transition-all duration-200" id="jumuiya-row-{{ $jumuiya->id }}">
                                <!-- Jumuiya Name -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                            <i class="fas fa-users text-primary-600"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $jumuiya->name }}</div>
                                            <div class="text-sm text-gray-500 flex items-center">
                                                <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                                                {{ $jumuiya->location ?? 'Hakuna eneo' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Leader -->
                                <td class="py-4 px-6">
                                    @if($jumuiya->leader)
                                        <div class="text-sm text-gray-900 font-medium">{{ $jumuiya->leader->full_name }}</div>
                                        <div class="text-xs text-gray-500 flex items-center">
                                            <i class="fas fa-phone mr-1"></i>
                                            {{ $jumuiya->leader_phone ?? '-' }}
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Hakuna Kiongozi
                                        </span>
                                    @endif
                                </td>

                                <!-- Members Count -->
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-users mr-1"></i>
                                        {{ $jumuiya->members_count ?? $jumuiya->members()->count() }}
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="py-4 px-6">
                                    @if($jumuiya->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i> Hai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i> Si Hai
                                        </span>
                                    @endif
                                </td>

                                <!-- Actions -->
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="openShowJumuiyaModal({{ $jumuiya->id }})"
                                           class="h-8 w-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-200 transition-all duration-200"
                                           title="Angalia Maelezo">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        <button onclick="openEditJumuiyaModal({{ $jumuiya->id }})"
                                           class="h-8 w-8 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center hover:bg-primary-200 transition-all duration-200"
                                           title="Hariri">
                                            <i class="fas fa-pencil-alt text-sm"></i>
                                        </button>
                                        <button onclick="confirmDeleteJumuiya({{ $jumuiya->id }}, '{{ $jumuiya->name }}', {{ $jumuiya->members()->count() }})"
                                                class="h-8 w-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200 transition-all duration-200"
                                                title="Futa">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="empty-jumuiya-row">
                                <td colspan="5" class="py-12 px-6 text-center">
                                    <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-users-slash text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna jumuiya zilizosajiliwa</h3>
                                    <p class="text-gray-500 mb-6">Anza kwa kuongeza jumuiya ya kwanza</p>
                                    <button onclick="openCreateJumuiyaModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                                        <i class="fas fa-plus mr-2"></i> Ongeza Jumuiya Mpya
                                    </button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('modals')
@if(Auth::user()->isMchungaji())
<!-- Create Jumuiya Modal -->
<div id="createJumuiyaModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all duration-300 scale-95" id="createJumuiyaModalContent">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-plus text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Ongeza Jumuiya Mpya</h3>
                </div>
                <button onclick="closeCreateJumuiyaModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="createJumuiyaForm" onsubmit="submitCreateJumuiya(event)">
            @csrf
            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jina la Jumuiya *</label>
                    <input type="text" name="name" id="create_jumuiya_name" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Mfano: Jumuiya ya Upendo">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Eneo/Mtaa</label>
                    <input type="text" name="location" id="create_jumuiya_location"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Mfano: Mtaa wa RGC">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kiongozi wa Jumuiya</label>
                    <select name="leader_id" id="create_jumuiya_leader"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Chagua Kiongozi --</option>
                        @foreach(\App\Models\Member::where('is_active', true)->orderBy('first_name')->get() as $member)
                            <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->phone }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maelezo</label>
                    <textarea name="description" id="create_jumuiya_description" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Maelezo mafupi kuhusu jumuiya..."></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="create_jumuiya_active" value="1" checked
                           class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <label for="create_jumuiya_active" class="ml-2 text-sm text-gray-700">Jumuiya hai (inafanya kazi)</label>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeCreateJumuiyaModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all">
                    Ghairi
                </button>
                <button type="submit" id="createJumuiyaBtn" class="px-5 py-2.5 text-sm font-medium text-white bg-primary-500 rounded-lg hover:bg-primary-600 transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Hifadhi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Jumuiya Modal -->
<div id="editJumuiyaModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all duration-300 scale-95" id="editJumuiyaModalContent">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-edit text-primary-600"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Hariri Jumuiya</h3>
                </div>
                <button onclick="closeEditJumuiyaModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <form id="editJumuiyaForm" onsubmit="submitEditJumuiya(event)">
            @csrf
            @method('PUT')
            <input type="hidden" id="edit_jumuiya_id" name="id">
            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jina la Jumuiya *</label>
                    <input type="text" name="name" id="edit_jumuiya_name" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Eneo/Mtaa</label>
                    <input type="text" name="location" id="edit_jumuiya_location"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kiongozi wa Jumuiya</label>
                    <select name="leader_id" id="edit_jumuiya_leader"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Chagua Kiongozi --</option>
                        @foreach(\App\Models\Member::where('is_active', true)->orderBy('first_name')->get() as $member)
                            <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->phone }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Maelezo</label>
                    <textarea name="description" id="edit_jumuiya_description" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="edit_jumuiya_active" value="1"
                           class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <label for="edit_jumuiya_active" class="ml-2 text-sm text-gray-700">Jumuiya hai (inafanya kazi)</label>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeEditJumuiyaModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all">
                    Ghairi
                </button>
                <button type="submit" id="editJumuiyaBtn" class="px-5 py-2.5 text-sm font-medium text-white bg-primary-500 rounded-lg hover:bg-primary-600 transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Sasisha</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Show Jumuiya Modal -->
<div id="showJumuiyaModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all duration-300 scale-95" id="showJumuiyaModalContent">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900" id="show_jumuiya_name">Jumuiya</h3>
                        <p class="text-sm text-gray-500" id="show_jumuiya_location"></p>
                    </div>
                </div>
                <button onclick="closeShowJumuiyaModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6 max-h-[70vh] overflow-y-auto">
            <!-- Jumuiya Info -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Kiongozi</div>
                    <div class="font-medium text-gray-900" id="show_jumuiya_leader">-</div>
                    <div class="text-sm text-gray-500" id="show_jumuiya_leader_phone"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Wanachama</div>
                    <div class="font-medium text-gray-900" id="show_jumuiya_members_count">0</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Hali</div>
                    <div id="show_jumuiya_status"></div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="text-sm text-gray-500 mb-1">Tarehe ya Kuundwa</div>
                    <div class="font-medium text-gray-900" id="show_jumuiya_created">-</div>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6" id="show_jumuiya_description_section">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Maelezo</h4>
                <p class="text-gray-600 bg-gray-50 rounded-lg p-4" id="show_jumuiya_description">-</p>
            </div>

            <!-- Members List -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-3">Orodha ya Wanachama</h4>
                <div class="bg-gray-50 rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jina</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Namba</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Simu</th>
                            </tr>
                        </thead>
                        <tbody id="show_jumuiya_members_list" class="divide-y divide-gray-200">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-gray-500">Inapakia...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="px-6 py-4 bg-gray-50 rounded-b-2xl border-t border-gray-200 flex justify-end">
            <button type="button" onclick="closeShowJumuiyaModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all">
                Funga
            </button>
        </div>
    </div>
</div>

<!-- Alert Modal (Success/Warning/Error) -->
<div id="jumuiyaAlertModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[10000]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95" id="jumuiyaAlertModalContent">
        <div class="p-6 text-center">
            <div id="jumuiyaAlertIcon" class="h-16 w-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl"></i>
            </div>
            <h3 id="jumuiyaAlertTitle" class="text-lg font-bold text-gray-900 mb-2">Onyo</h3>
            <p id="jumuiyaAlertMessage" class="text-gray-600 mb-6">Ujumbe wa onyo</p>
            <button onclick="closeJumuiyaAlertModal()" class="w-full px-5 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all">
                Sawa, Nimeelewa
            </button>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="jumuiyaConfirmModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[10000]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95" id="jumuiyaConfirmModalContent">
        <div class="p-6 text-center">
            <div class="h-16 w-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash-alt text-red-600 text-3xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Thibitisha Kufuta</h3>
            <p class="text-gray-600 mb-2">Je, una uhakika unataka kufuta jumuiya:</p>
            <p class="text-lg font-semibold text-red-600 mb-6" id="confirmJumuiyaName">Jina la Jumuiya</p>
            <div class="flex gap-3">
                <button onclick="closeJumuiyaConfirmModal()" class="flex-1 px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all">
                    <i class="fas fa-times mr-2"></i>Ghairi
                </button>
                <button id="confirmDeleteBtn" class="flex-1 px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-all">
                    <i class="fas fa-trash mr-2"></i>Futa Jumuiya
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
// Toggle Password Visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '_icon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Tab switching with URL parameter support
function switchTab(tabName) {
    // Hide all tabs and reset tab button styles
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('border-primary-500', 'text-primary-600', 'bg-white');
        el.classList.add('border-transparent', 'text-gray-500');
    });

    // Show selected tab
    const content = document.getElementById('content-' + tabName);
    const tab = document.getElementById('tab-' + tabName);

    if (content && tab) {
        content.classList.remove('hidden');
        tab.classList.remove('border-transparent', 'text-gray-500');
        tab.classList.add('border-primary-500', 'text-primary-600', 'bg-white');
    }

    // Update URL without reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.replaceState({}, '', url);
}

// Check for tab parameter on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab && document.getElementById('tab-' + tab)) {
        switchTab(tab);
    }
});

@if(Auth::user()->isMchungaji())
// Create Jumuiya Modal Functions
function openCreateJumuiyaModal() {
    const modal = document.getElementById('createJumuiyaModal');
    const content = document.getElementById('createJumuiyaModalContent');

    // Reset form
    document.getElementById('createJumuiyaForm').reset();
    document.getElementById('create_jumuiya_active').checked = true;

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function closeCreateJumuiyaModal() {
    const modal = document.getElementById('createJumuiyaModal');
    const content = document.getElementById('createJumuiyaModalContent');

    content.classList.remove('scale-100');
    content.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

function submitCreateJumuiya(event) {
    event.preventDefault();

    const form = document.getElementById('createJumuiyaForm');
    const btn = document.getElementById('createJumuiyaBtn');
    const formData = new FormData(form);

    // Set is_active properly
    formData.set('is_active', document.getElementById('create_jumuiya_active').checked ? '1' : '0');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inahifadhi...';

    fetch('{{ route("jumuiyas.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeCreateJumuiyaModal();
            showJumuiyaWarningModal(
                'Imefanikiwa!',
                data.message || 'Jumuiya imeongezwa kikamilifu.',
                'success'
            );
            // Reload page to show new jumuiya
            setTimeout(() => location.reload(), 1500);
        } else {
            showJumuiyaWarningModal(
                'Hitilafu!',
                data.message || 'Hitilafu imetokea wakati wa kuongeza jumuiya.',
                'error'
            );
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showJumuiyaWarningModal(
            'Hitilafu ya Mtandao!',
            'Hitilafu ya mtandao imetokea. Tafadhali jaribu tena.',
            'error'
        );
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> <span>Hifadhi</span>';
    });
}

// Edit Jumuiya Modal Functions
function openEditJumuiyaModal(id) {
    const modal = document.getElementById('editJumuiyaModal');
    const content = document.getElementById('editJumuiyaModalContent');

    // Fetch jumuiya data
    fetch(`/panel/jumuiyas/${id}/edit`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        const jumuiya = data.jumuiya;
        document.getElementById('edit_jumuiya_id').value = jumuiya.id;
        document.getElementById('edit_jumuiya_name').value = jumuiya.name || '';
        document.getElementById('edit_jumuiya_location').value = jumuiya.location || '';
        document.getElementById('edit_jumuiya_leader').value = jumuiya.leader_id || '';
        document.getElementById('edit_jumuiya_description').value = jumuiya.description || '';
        document.getElementById('edit_jumuiya_active').checked = jumuiya.is_active;

        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Hitilafu ya kupakia data');
    });
}

function closeEditJumuiyaModal() {
    const modal = document.getElementById('editJumuiyaModal');
    const content = document.getElementById('editJumuiyaModalContent');

    content.classList.remove('scale-100');
    content.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

function submitEditJumuiya(event) {
    event.preventDefault();

    const form = document.getElementById('editJumuiyaForm');
    const btn = document.getElementById('editJumuiyaBtn');
    const id = document.getElementById('edit_jumuiya_id').value;
    const formData = new FormData(form);

    // Set is_active properly
    formData.set('is_active', document.getElementById('edit_jumuiya_active').checked ? '1' : '0');

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inasasisha...';

    fetch(`/panel/jumuiyas/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeEditJumuiyaModal();
            showJumuiyaWarningModal(
                'Imefanikiwa!',
                data.message || 'Jumuiya imesasishwa kikamilifu.',
                'success'
            );
            // Reload page to show updated data
            setTimeout(() => location.reload(), 1500);
        } else {
            showJumuiyaWarningModal(
                'Hitilafu!',
                data.message || 'Hitilafu imetokea wakati wa kusasisha jumuiya.',
                'error'
            );
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showJumuiyaWarningModal(
            'Hitilafu ya Mtandao!',
            'Hitilafu ya mtandao imetokea. Tafadhali jaribu tena.',
            'error'
        );
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save"></i> <span>Sasisha</span>';
    });
}

// Show Jumuiya Modal Functions
function openShowJumuiyaModal(id) {
    const modal = document.getElementById('showJumuiyaModal');
    const content = document.getElementById('showJumuiyaModalContent');

    // Fetch jumuiya data
    fetch(`/panel/jumuiyas/${id}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        const jumuiya = data.jumuiya;
        const members = data.members;

        document.getElementById('show_jumuiya_name').textContent = jumuiya.name;
        document.getElementById('show_jumuiya_location').textContent = jumuiya.location || 'Hakuna eneo';
        document.getElementById('show_jumuiya_leader').textContent = jumuiya.leader ? jumuiya.leader.name : 'Hakuna kiongozi';
        document.getElementById('show_jumuiya_leader_phone').textContent = jumuiya.leader ? jumuiya.leader.phone : '';
        document.getElementById('show_jumuiya_members_count').textContent = jumuiya.members_count + ' wanachama';
        document.getElementById('show_jumuiya_created').textContent = jumuiya.created_at;

        // Status
        const statusHtml = jumuiya.is_active
            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i> Hai</span>'
            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i> Si Hai</span>';
        document.getElementById('show_jumuiya_status').innerHTML = statusHtml;

        // Description
        const descSection = document.getElementById('show_jumuiya_description_section');
        if (jumuiya.description) {
            descSection.classList.remove('hidden');
            document.getElementById('show_jumuiya_description').textContent = jumuiya.description;
        } else {
            descSection.classList.add('hidden');
        }

        // Members list
        const membersList = document.getElementById('show_jumuiya_members_list');
        if (members.length > 0) {
            membersList.innerHTML = members.map(member => `
                <tr class="hover:bg-gray-100">
                    <td class="px-4 py-2 text-sm text-gray-900">${member.name}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">${member.member_number}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">${member.phone || '-'}</td>
                </tr>
            `).join('');
        } else {
            membersList.innerHTML = '<tr><td colspan="3" class="px-4 py-4 text-center text-gray-500">Hakuna wanachama waliosajiliwa</td></tr>';
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    })
    .catch(error => {
        console.error('Error:', error);
        showError('Hitilafu ya kupakia data');
    });
}

function closeShowJumuiyaModal() {
    const modal = document.getElementById('showJumuiyaModal');
    const content = document.getElementById('showJumuiyaModalContent');

    content.classList.remove('scale-100');
    content.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Confirm Delete Jumuiya with warning modal
function confirmDeleteJumuiya(id, name, membersCount) {
    // Check if has members - show warning
    if (membersCount > 0) {
        showJumuiyaWarningModal(
            'Onyo: Jumuiya Ina Wanachama!',
            `Jumuiya "${name}" ina wanachama ${membersCount}. Huwezi kufuta jumuiya hii mpaka wanachama wote waondolewe.`,
            'warning'
        );
        return;
    }

    // Show confirmation modal
    showJumuiyaConfirmModal(id, name);
}

// Show Warning Modal
function showJumuiyaWarningModal(title, message, type = 'warning') {
    const modal = document.getElementById('jumuiyaAlertModal');
    const iconContainer = document.getElementById('jumuiyaAlertIcon');
    const titleEl = document.getElementById('jumuiyaAlertTitle');
    const messageEl = document.getElementById('jumuiyaAlertMessage');
    const content = document.getElementById('jumuiyaAlertModalContent');

    // Set icon based on type
    const icons = {
        'warning': '<i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>',
        'error': '<i class="fas fa-times-circle text-red-600 text-2xl"></i>',
        'success': '<i class="fas fa-check-circle text-green-600 text-2xl"></i>',
        'info': '<i class="fas fa-info-circle text-blue-600 text-2xl"></i>'
    };

    const bgColors = {
        'warning': 'bg-yellow-100',
        'error': 'bg-red-100',
        'success': 'bg-green-100',
        'info': 'bg-blue-100'
    };

    iconContainer.className = `h-12 w-12 rounded-full flex items-center justify-center mx-auto mb-4 ${bgColors[type]}`;
    iconContainer.innerHTML = icons[type];
    titleEl.textContent = title;
    messageEl.textContent = message;

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

// Close Alert Modal
function closeJumuiyaAlertModal() {
    const modal = document.getElementById('jumuiyaAlertModal');
    const content = document.getElementById('jumuiyaAlertModalContent');

    content.classList.remove('scale-100');
    content.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Show Confirm Delete Modal
function showJumuiyaConfirmModal(id, name) {
    const modal = document.getElementById('jumuiyaConfirmModal');
    const content = document.getElementById('jumuiyaConfirmModalContent');
    const nameEl = document.getElementById('confirmJumuiyaName');
    const deleteBtn = document.getElementById('confirmDeleteBtn');

    nameEl.textContent = name;
    deleteBtn.onclick = function() {
        executeDeleteJumuiya(id, name);
    };

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

// Close Confirm Modal
function closeJumuiyaConfirmModal() {
    const modal = document.getElementById('jumuiyaConfirmModal');
    const content = document.getElementById('jumuiyaConfirmModalContent');

    content.classList.remove('scale-100');
    content.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Execute Delete Jumuiya
function executeDeleteJumuiya(id, name) {
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    deleteBtn.disabled = true;
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Inafuta...';

    fetch(`/panel/jumuiyas/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        closeJumuiyaConfirmModal();

        if (data.success) {
            // Show success modal
            showJumuiyaWarningModal(
                'Imefanikiwa!',
                `Jumuiya "${name}" imefutwa kikamilifu.`,
                'success'
            );

            // Remove row from table
            const row = document.getElementById(`jumuiya-row-${id}`);
            if (row) row.remove();

            // Check if table is empty
            const tbody = document.getElementById('jumuiyaTableBody');
            if (tbody.children.length === 0) {
                tbody.innerHTML = `
                    <tr id="empty-jumuiya-row">
                        <td colspan="5" class="py-12 px-6 text-center">
                            <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-users-slash text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna jumuiya zilizosajiliwa</h3>
                            <p class="text-gray-500 mb-6">Anza kwa kuongeza jumuiya ya kwanza</p>
                            <button onclick="openCreateJumuiyaModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                                <i class="fas fa-plus mr-2"></i> Ongeza Jumuiya Mpya
                            </button>
                        </td>
                    </tr>
                `;
            }

            // Reload after 1.5 seconds to update stats
            setTimeout(() => location.reload(), 1500);
        } else {
            // Show error modal
            showJumuiyaWarningModal(
                'Hitilafu!',
                data.message || 'Hitilafu imetokea wakati wa kufuta jumuiya.',
                'error'
            );
        }
    })
    .catch(error => {
        closeJumuiyaConfirmModal();
        console.error('Error:', error);
        showJumuiyaWarningModal(
            'Hitilafu ya Mtandao!',
            'Hitilafu ya mtandao imetokea. Tafadhali jaribu tena.',
            'error'
        );
    })
    .finally(() => {
        deleteBtn.disabled = false;
        deleteBtn.innerHTML = '<i class="fas fa-trash mr-2"></i>Futa Jumuiya';
    });
}

// Close modals on backdrop click
document.getElementById('createJumuiyaModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeCreateJumuiyaModal();
});
document.getElementById('editJumuiyaModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeEditJumuiyaModal();
});
document.getElementById('showJumuiyaModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeShowJumuiyaModal();
});
document.getElementById('jumuiyaAlertModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeJumuiyaAlertModal();
});
document.getElementById('jumuiyaConfirmModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeJumuiyaConfirmModal();
});

// Close modals on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCreateJumuiyaModal();
        closeEditJumuiyaModal();
        closeShowJumuiyaModal();
        closeJumuiyaAlertModal();
        closeJumuiyaConfirmModal();
    }
});
@endif

// Real-time Password Validation
const passwordInput = document.getElementById('new_password');
if (passwordInput) {
    const requirements = {
        length: { el: document.getElementById('req-length'), regex: /.{6,}/ },
        upper: { el: document.getElementById('req-upper'), regex: /[A-Z]/ },
        lower: { el: document.getElementById('req-lower'), regex: /[a-z]/ },
        number: { el: document.getElementById('req-number'), regex: /[0-9]/ },
        special: { el: document.getElementById('req-special'), regex: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/ }
    };

    passwordInput.addEventListener('input', function() {
        const password = this.value;

        Object.keys(requirements).forEach(key => {
            const req = requirements[key];
            const icon = req.el.querySelector('i');

            if (req.regex.test(password)) {
                // Requirement met - show green check
                icon.className = 'fas fa-check-circle text-green-500 mr-2 text-xs';
                req.el.classList.remove('text-gray-600');
                req.el.classList.add('text-green-600');
            } else {
                // Requirement not met - show gray circle
                icon.className = 'fas fa-circle text-gray-300 mr-2 text-[6px]';
                req.el.classList.remove('text-green-600');
                req.el.classList.add('text-gray-600');
            }
        });
    });
}
</script>
@endsection
