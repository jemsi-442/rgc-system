@extends('layouts.app')

@section('title', 'Huduma za Kichungaji - Mfumo wa Kanisa')
@section('page-title', 'Huduma za Kichungaji')
@section('page-subtitle', 'Usimamizi wa maombi ya huduma za kichungaji')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Huduma za Kichungaji</h1>
            <p class="text-gray-600 mt-2">Usimamizi kamili wa maombi ya huduma za kichungaji</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if(!Auth::user()->isMwanachama())
            <a href="{{ route('pastoral-services.report') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800">
                <i class="fas fa-chart-bar"></i>
                <span class="font-medium">Ripoti</span>
            </a>
            <button onclick="openExportModal()" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800">
                <i class="fas fa-file-pdf"></i>
                <span class="font-medium">Export PDF</span>
            </button>
            @endif
            <a href="{{ route('pastoral-services.create') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800">
                <i class="fas fa-plus"></i>
                <span class="font-medium">Omba Huduma</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jumla ya Maombi</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-list text-xl text-blue-600"></i>
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
                    <p class="text-sm text-gray-600 mb-1">Zimekamilika</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['completed'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-double text-xl text-purple-600"></i>
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

    <!-- Filter Form (hidden for members) -->
    @if(!Auth::user()->isMwanachama())
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter text-primary-500 mr-2"></i> Chuja Maombi
            </h3>
        </div>
        <form method="GET" action="{{ route('pastoral-services.index') }}" data-auto-filter="true" data-ajax-target="#pastoralServicesTableContainer" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hali</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Zote</option>
                        <option value="Inasubiri" {{ request('status') == 'Inasubiri' ? 'selected' : '' }}>Zinasubiri</option>
                        <option value="Imeidhinishwa" {{ request('status') == 'Imeidhinishwa' ? 'selected' : '' }}>Zimeidhinishwa</option>
                        <option value="Imekamilika" {{ request('status') == 'Imekamilika' ? 'selected' : '' }}>Zimekamilika</option>
                        <option value="Imekataliwa" {{ request('status') == 'Imekataliwa' ? 'selected' : '' }}>Zimekataliwa</option>
                    </select>
                </div>

                <!-- Service Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Aina ya Huduma</label>
                    <select name="service_type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Zote</option>
                        @foreach($serviceTypes as $type)
                            <option value="{{ $type }}" {{ request('service_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarehe Kuanzia</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tarehe Mwisho</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            <!-- Clear Filter Link -->
            <div class="flex justify-end pt-2">
                <a href="{{ route('pastoral-services.index') }}" class="text-sm text-gray-500 hover:text-primary-600 transition-colors flex items-center gap-1">
                    <i class="fas fa-redo text-xs"></i>
                    <span>Futa Chujio</span>
                </a>
            </div>
        </form>
    </div>
    @endif

    <!-- Services Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" id="pastoralServicesTableContainer">
        <!-- Table Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b border-gray-200">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list text-primary-500 mr-2"></i> Orodha ya Maombi ya Huduma
                    <span class="ml-3 text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                        {{ $services->total() }} maombi
                    </span>
                </h3>
            </div>
            <div class="mt-3 sm:mt-0">
                <div class="flex items-center gap-2 text-sm text-gray-600">
                    <i class="fas fa-info-circle text-primary-500"></i>
                    <span>Angalia au hariri ombi kwa kubofya vitendo</span>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-primary-600 text-white text-sm">
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-hashtag mr-2"></i>
                                Namba ya Huduma
                            </div>
                        </th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-user mr-2"></i>
                                Muumini
                            </div>
                        </th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-hands-helping mr-2"></i>
                                Aina ya Huduma
                            </div>
                        </th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-calendar-day mr-2"></i>
                                Tarehe Inayopendelewa
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
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Tarehe ya Kuomba
                            </div>
                        </th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider sticky right-0 bg-primary-600">
                            <div class="flex items-center">
                                <i class="fas fa-cogs mr-2"></i>
                                Vitendo
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($services as $service)
                    <tr class="bg-white hover:bg-gray-50 transition-all duration-200">
                        <!-- Service Number -->
                        <td class="py-4 px-6">
                            <div class="font-mono text-sm font-medium text-gray-900 bg-gray-100 px-2 py-1 rounded inline-block">
                                {{ $service->service_number }}
                            </div>
                        </td>

                        <!-- Member Name -->
                        <td class="py-4 px-6">
                            <div class="text-sm text-gray-900">
                                {{ $service->member->first_name }} {{ $service->member->last_name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $service->member->member_number }}
                            </div>
                        </td>

                        <!-- Service Type -->
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-hands-helping mr-1"></i>
                                {{ $service->service_type }}
                            </span>
                        </td>

                        <!-- Preferred Date -->
                        <td class="py-4 px-6">
                            <div class="text-sm text-gray-600">
                                @if($service->preferred_date)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-day text-gray-400"></i>
                                    {{ \Carbon\Carbon::parse($service->preferred_date)->format('d/m/Y') }}
                                </div>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="py-4 px-6">
                            @if($service->status == 'Inasubiri')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Inasubiri
                                </span>
                            @elseif($service->status == 'Imeidhinishwa')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Imeidhinishwa
                                </span>
                            @elseif($service->status == 'Imekamilika')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="fas fa-check-double mr-1"></i>
                                    Imekamilika
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Imekataliwa
                                </span>
                            @endif
                        </td>

                        <!-- Application Date -->
                        <td class="py-4 px-6">
                            <div class="text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-gray-400"></i>
                                    {{ \Carbon\Carbon::parse($service->created_at)->format('d/m/Y') }}
                                </div>
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-6 text-sm sticky right-0 bg-white">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('pastoral-services.show', $service->id) }}"
                                   class="h-8 w-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center hover:bg-green-200 transition-all duration-200"
                                   title="Angalia Maelezo">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>

                                @if($service->status == 'Inasubiri')
                                <a href="{{ route('pastoral-services.edit', $service->id) }}"
                                   class="h-8 w-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-200 transition-all duration-200"
                                   title="Hariri">
                                    <i class="fas fa-pencil-alt text-sm"></i>
                                </a>
                                <button type="button"
                                        onclick="confirmDeleteService({{ $service->id }}, '{{ $service->service_type }}', '{{ $service->member ? $service->member->first_name . ' ' . $service->member->last_name : 'N/A' }}')"
                                        class="h-8 w-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200 transition-all duration-200"
                                        title="Futa">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 px-6 text-center">
                            <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-hands-helping text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna Maombi Yaliyopatikana</h3>
                            <p class="text-gray-500 mb-6">Hakuna maombi ya huduma yanayolingana na vichujio vyako.</p>
                            <a href="{{ route('pastoral-services.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                                <i class="fas fa-plus mr-2"></i> Omba Huduma Mpya
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($services->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $services->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteServiceModal" class="modal-overlay hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95" id="deleteServiceModalContent">
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
                <button type="button" onclick="closeDeleteServiceModal()" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <div class="p-6">
            <p class="text-gray-700 mb-2">Je, una uhakika unataka kufuta ombi hili la huduma?</p>
            <div class="bg-gray-50 rounded-lg p-3 mb-4">
                <p class="text-sm text-gray-600">Huduma: <span class="font-semibold text-gray-900" id="deleteServiceType"></span></p>
                <p class="text-sm text-gray-600">Muumini: <span class="font-semibold text-gray-900" id="deleteServiceMember"></span></p>
            </div>
            <p class="text-sm text-red-600">
                <i class="fas fa-warning mr-1"></i>
                Taarifa zote za ombi hili zitafutwa kabisa.
            </p>
        </div>

        <div class="sticky bottom-0 bg-gray-50 px-6 py-5 rounded-b-2xl border-t border-gray-200 flex justify-end space-x-3">
            <button type="button" onclick="closeDeleteServiceModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-xl hover:bg-gray-300 transition-all duration-200">
                Ghairi
            </button>
            <form id="deleteServiceForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-trash"></i>
                    <span>Futa Ombi</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Export PDF Modal -->
<div id="exportModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95" id="exportModalContent">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-16 w-16 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-file-pdf text-red-600 text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Export Ripoti (PDF)</h3>
                        <p class="text-sm text-gray-500">Chagua kipindi cha ripoti</p>
                    </div>
                </div>
                <button onclick="closeExportModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form id="exportForm" action="{{ route('pastoral-services.export') }}" method="GET">
            <input type="hidden" name="format" value="pdf">
            <div class="p-6 space-y-5">
                <!-- Period Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Kipindi cha Ripoti</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="period" value="week" class="peer sr-only">
                            <div class="flex flex-col items-center p-4 rounded-xl border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all hover:border-gray-300">
                                <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mb-2">
                                    <i class="fas fa-calendar-week text-blue-600"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Wiki Hii</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="period" value="month" class="peer sr-only" checked>
                            <div class="flex flex-col items-center p-4 rounded-xl border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 transition-all hover:border-gray-300">
                                <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center mb-2">
                                    <i class="fas fa-calendar-alt text-green-600"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Mwezi Huu</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="period" value="year" class="peer sr-only">
                            <div class="flex flex-col items-center p-4 rounded-xl border-2 border-gray-200 peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all hover:border-gray-300">
                                <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center mb-2">
                                    <i class="fas fa-calendar text-purple-600"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-700">Mwaka Huu</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Hali (Hiari)</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Hali Zote</option>
                        <option value="Inasubiri">Zinasubiri</option>
                        <option value="Imeidhinishwa">Zimeidhinishwa</option>
                        <option value="Imekamilika">Zimekamilika</option>
                        <option value="Imekataliwa">Zimekataliwa</option>
                    </select>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 rounded-b-2xl border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeExportModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-xl hover:bg-gray-300 transition-all">
                    Ghairi
                </button>
                <button type="button" onclick="exportPastoralServicesPDF()" class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 rounded-xl hover:from-red-700 hover:to-red-800 transition-all flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i>
                    <span>Download PDF</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@include('partials.loading-modal')

<script>
// Delete modal functions
function confirmDeleteService(serviceId, serviceType, memberName) {
    document.getElementById('deleteServiceType').textContent = serviceType;
    document.getElementById('deleteServiceMember').textContent = memberName;
    document.getElementById('deleteServiceForm').action = '/panel/pastoral-services/' + serviceId;

    const modal = document.getElementById('deleteServiceModal');
    const content = document.getElementById('deleteServiceModalContent');

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function closeDeleteServiceModal() {
    const modal = document.getElementById('deleteServiceModal');
    const content = document.getElementById('deleteServiceModalContent');

    content.classList.remove('scale-100');
    content.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Close delete modal on backdrop click
document.getElementById('deleteServiceModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteServiceModal();
});

function openExportModal() {
    const modal = document.getElementById('exportModal');
    const content = document.getElementById('exportModalContent');

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function closeExportModal() {
    const modal = document.getElementById('exportModal');
    const content = document.getElementById('exportModalContent');

    content.classList.remove('scale-100');
    content.classList.add('scale-95');

    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Export PDF function
function exportPastoralServicesPDF() {
    const form = document.getElementById('exportForm');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    
    const loadingModal = document.getElementById('loadingModal');
    const progressBar = document.getElementById('progressBar');
    const loadingMessage = document.getElementById('loadingMessage');

    loadingMessage.textContent = 'Inatengeneza ripoti ya PDF...';
    loadingModal.classList.remove('hidden');

    let progress = 0;
    const interval = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 90) progress = 90;
        progressBar.style.width = progress + '%';
    }, 200);

    fetch('{{ route('pastoral-services.export.pdf') }}?' + params.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(async (res) => {
        const data = await res.json().catch(() => ({}));
        clearInterval(interval);
        progressBar.style.width = '100%';

        setTimeout(() => {
            loadingModal.classList.add('hidden');
            progressBar.style.width = '0%';
            if (data.success) {
                if (data.download_url && data.download_url !== '#') {
                    const link = document.createElement('a');
                    link.href = data.download_url;
                    link.download = data.filename || 'huduma_za_kichungaji.pdf';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            } else {
                alert('Hitilafu: ' + (data.message || 'Tumeshindwa kutengeneza ripoti'));
            }
        }, 500);
    })
    .catch(error => {
        clearInterval(interval);
        loadingModal.classList.add('hidden');
        progressBar.style.width = '0%';
        alert('Hitilafu ya mtandao! Tafadhali jaribu tena.');
    });
}

// Close modal on backdrop click
document.getElementById('exportModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeExportModal();
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeExportModal();
        closeDeleteServiceModal();
    }
});
</script>
@endsection
