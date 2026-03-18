@extends('layouts.app')

@section('title', 'Ripoti ya Huduma za Kichungaji - Mfumo wa Kanisa')
@section('page-title', 'Ripoti ya Huduma za Kichungaji')
@section('page-subtitle', 'Takwimu za huduma za kichungaji kwa wiki, mwezi na mwaka')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Ripoti ya Huduma za Kichungaji</h1>
            <p class="text-gray-600 mt-2">Takwimu za huduma kwa {{ $year }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('pastoral-services.index') }}" class="px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gray-200 text-gray-800 hover:bg-gray-300">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Rudi Nyuma</span>
            </a>
            <button onclick="openExportModal()" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800">
                <i class="fas fa-file-pdf"></i>
                <span class="font-medium">Export PDF</span>
            </button>
        </div>
    </div>

    <!-- Period Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Weekly Stats -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold opacity-90">Wiki Hii</h3>
                <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-week text-2xl"></i>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="opacity-80">Jumla</span>
                    <span class="text-2xl font-bold">{{ $weeklyStats['total'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="opacity-80">Zimekamilika</span>
                    <span class="text-xl font-semibold">{{ $weeklyStats['completed'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="opacity-80">Zinasubiri</span>
                    <span class="text-xl font-semibold">{{ $weeklyStats['pending'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="opacity-80">Zimeidhinishwa</span>
                    <span class="text-xl font-semibold">{{ $weeklyStats['approved'] }}</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20">
                <button onclick="quickExport('week')" class="w-full flex items-center justify-center gap-2 text-sm bg-white/20 hover:bg-white/30 rounded-lg py-2 transition-all">
                    <i class="fas fa-file-pdf"></i>
                    <span>Export Wiki (PDF)</span>
                </button>
            </div>
        </div>

        <!-- Monthly Stats -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold opacity-90">Mwezi Huu</h3>
                <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-2xl"></i>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="opacity-80">Jumla</span>
                    <span class="text-2xl font-bold">{{ $monthlyStats['total'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="opacity-80">Zimekamilika</span>
                    <span class="text-xl font-semibold">{{ $monthlyStats['completed'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="opacity-80">Zinasubiri</span>
                    <span class="text-xl font-semibold">{{ $monthlyStats['pending'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="opacity-80">Zimeidhinishwa</span>
                    <span class="text-xl font-semibold">{{ $monthlyStats['approved'] }}</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20">
                <button onclick="quickExport('month')" class="w-full flex items-center justify-center gap-2 text-sm bg-white/20 hover:bg-white/30 rounded-lg py-2 transition-all">
                    <i class="fas fa-file-pdf"></i>
                    <span>Export Mwezi (PDF)</span>
                </button>
            </div>
        </div>

        <!-- Yearly Stats -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold opacity-90">Mwaka {{ $year }}</h3>
                <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar text-2xl"></i>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="opacity-80">Jumla</span>
                    <span class="text-2xl font-bold">{{ $yearlyStats['total'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="opacity-80">Zimekamilika</span>
                    <span class="text-xl font-semibold">{{ $yearlyStats['completed'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="opacity-80">Zinasubiri</span>
                    <span class="text-xl font-semibold">{{ $yearlyStats['pending'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="opacity-80">Zimeidhinishwa</span>
                    <span class="text-xl font-semibold">{{ $yearlyStats['approved'] }}</span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/20">
                <button onclick="quickExport('year')" class="w-full flex items-center justify-center gap-2 text-sm bg-white/20 hover:bg-white/30 rounded-lg py-2 transition-all">
                    <i class="fas fa-file-pdf"></i>
                    <span>Export Mwaka (PDF)</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Services by Type -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-chart-pie text-primary-500 mr-2"></i>
                Huduma kwa Aina ({{ $year }})
            </h3>
            <div class="space-y-4">
                @php
                    $colors = [
                        'Ubatizo' => 'bg-blue-500',
                        'Uthibitisho' => 'bg-green-500',
                        'Ndoa' => 'bg-pink-500',
                        'Wakfu' => 'bg-yellow-500',
                        'Mazishi' => 'bg-gray-500',
                        'Ushauri wa Kichungaji' => 'bg-purple-500',
                        'Nyingine' => 'bg-indigo-500',
                    ];
                    $totalServices = $servicesByType->sum('total') ?: 1;
                @endphp
                @forelse($servicesByType as $type)
                    @php
                        $percentage = round(($type->total / $totalServices) * 100, 1);
                        $colorClass = $colors[$type->service_type] ?? 'bg-gray-400';
                    @endphp
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $type->service_type }}</span>
                            <span class="text-sm text-gray-500">{{ $type->total }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="{{ $colorClass }} h-3 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-chart-pie text-4xl mb-3 opacity-50"></i>
                        <p>Hakuna huduma kwa mwaka {{ $year }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Monthly Breakdown -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-chart-bar text-primary-500 mr-2"></i>
                Muhtasari wa Kila Mwezi ({{ $year }})
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="py-2 text-left font-semibold text-gray-700">Mwezi</th>
                            <th class="py-2 text-center font-semibold text-gray-700">Jumla</th>
                            <th class="py-2 text-center font-semibold text-gray-700">Zimekamilika</th>
                            <th class="py-2 text-right font-semibold text-gray-700">%</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($monthlyData as $data)
                            @php
                                $completionRate = $data['total'] > 0 ? round(($data['completed'] / $data['total']) * 100) : 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 text-gray-700">{{ $data['name'] }}</td>
                                <td class="py-2 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $data['total'] }}
                                    </span>
                                </td>
                                <td class="py-2 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $data['completed'] }}
                                    </span>
                                </td>
                                <td class="py-2 text-right">
                                    <span class="text-xs {{ $completionRate >= 70 ? 'text-green-600' : ($completionRate >= 40 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $completionRate }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-gray-200">
                        <tr class="font-semibold">
                            <td class="py-2 text-gray-900">Jumla</td>
                            <td class="py-2 text-center text-gray-900">{{ collect($monthlyData)->sum('total') }}</td>
                            <td class="py-2 text-center text-green-600">{{ collect($monthlyData)->sum('completed') }}</td>
                            <td class="py-2 text-right">
                                @php
                                    $totalAll = collect($monthlyData)->sum('total');
                                    $totalCompleted = collect($monthlyData)->sum('completed');
                                    $overallRate = $totalAll > 0 ? round(($totalCompleted / $totalAll) * 100) : 0;
                                @endphp
                                <span class="text-primary-600">{{ $overallRate }}%</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Completed Services -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-check-double text-green-500 mr-2"></i>
                Huduma Zilizokamilika Hivi Karibuni
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-sm text-gray-600">
                        <th class="py-3 px-6 text-left font-semibold">Namba</th>
                        <th class="py-3 px-6 text-left font-semibold">Muumini</th>
                        <th class="py-3 px-6 text-left font-semibold">Aina ya Huduma</th>
                        <th class="py-3 px-6 text-left font-semibold">Tarehe ya Kukamilika</th>
                        <th class="py-3 px-6 text-left font-semibold">Vitendo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentCompleted as $service)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6">
                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $service->service_number }}</span>
                            </td>
                            <td class="py-3 px-6">
                                <div class="text-sm text-gray-900">{{ $service->member->first_name }} {{ $service->member->last_name }}</div>
                                <div class="text-xs text-gray-500">{{ $service->member->member_number }}</div>
                            </td>
                            <td class="py-3 px-6">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $service->service_type }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-sm text-gray-600">
                                {{ $service->updated_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-6">
                                <a href="{{ route('pastoral-services.show', $service->id) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                                    <i class="fas fa-eye mr-1"></i> Angalia
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 px-6 text-center text-gray-500">
                                <i class="fas fa-check-double text-4xl mb-3 opacity-50"></i>
                                <p>Hakuna huduma zilizokamilika hivi karibuni</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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

                <!-- Service Type Filter -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Aina ya Huduma (Hiari)</label>
                    <select name="service_type" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Huduma Zote</option>
                        @foreach($serviceTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
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
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-red-600 to-red-700 rounded-xl hover:from-red-700 hover:to-red-800 transition-all flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i>
                    <span>Download PDF</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
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

function quickExport(period) {
    window.location.href = `{{ route('pastoral-services.export') }}?period=${period}&format=pdf`;
}

// Close modal on backdrop click
document.getElementById('exportModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeExportModal();
});

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeExportModal();
});
</script>
@endsection
