<!-- Table Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b border-gray-200">
    <div>
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <i class="fas fa-list text-primary-500 mr-2"></i> Orodha ya Mapato
            <span class="ml-3 text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                {{ $incomes->total() }} rekodi
            </span>
        </h3>
    </div>
    <div class="mt-3 sm:mt-0">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <i class="fas fa-info-circle text-primary-500"></i>
            <span>Angalia au hariri rekodi kwa kubofya vitendo</span>
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
                        <i class="fas fa-calendar mr-2"></i>
                        Tarehe
                    </div>
                </th>
                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-tag mr-2"></i>
                        Aina ya Mapato
                    </div>
                </th>
                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">
                    <div class="flex items-center">
                        <i class="fas fa-money-bill-wave mr-2"></i>
                        Kiasi
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
                        <i class="fas fa-receipt mr-2"></i>
                        Namba ya Risiti
                    </div>
                </th>
                @if(Auth::user()->isMchungaji() || Auth::user()->isMhasibu())
                <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider sticky right-0 bg-primary-600">
                    <div class="flex items-center">
                        <i class="fas fa-cogs mr-2"></i>
                        Vitendo
                    </div>
                </th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($incomes as $income)
            <tr class="bg-white hover:bg-gray-50 transition-all duration-200">
                <!-- Date -->
                <td class="py-4 px-6">
                    <div class="text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($income->collection_date)->format('d/m/Y') }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ \Carbon\Carbon::parse($income->collection_date)->format('l') }}
                    </div>
                </td>

                <!-- Category -->
                <td class="py-4 px-6">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                        <i class="fas fa-tag mr-1"></i>
                        {{ $income->category->name ?? '-' }}
                    </span>
                </td>

                <!-- Amount -->
                <td class="py-4 px-6">
                    <div class="text-lg font-bold text-green-600">
                        {{ number_format($income->amount, 0) }} TSh
                    </div>
                </td>

                <!-- Member -->
                <td class="py-4 px-6">
                    @if($income->member)
                    <div class="text-sm text-gray-900">
                        {{ $income->member->first_name }} {{ $income->member->last_name }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $income->member->member_number }}
                    </div>
                    @else
                    <span class="text-sm text-gray-400 italic">-</span>
                    @endif
                </td>

                <!-- Receipt Number -->
                <td class="py-4 px-6">
                    @if($income->receipt_number)
                    <div class="flex items-center gap-2">
                        <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $income->receipt_number }}</span>
                    </div>
                    @else
                    <span class="text-sm text-gray-400">-</span>
                    @endif
                </td>

                <!-- Actions -->
                @if(Auth::user()->isMchungaji() || Auth::user()->isMhasibu())
                <td class="py-4 px-6 text-sm sticky right-0 bg-white">
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('income.edit', $income->id) }}"
                           class="h-8 w-8 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center hover:bg-primary-200 transition-all duration-200"
                           title="Hariri">
                            <i class="fas fa-pencil-alt text-sm"></i>
                        </a>
                        <button type="button"
                                onclick="confirmDelete({{ $income->id }}, '{{ $income->category->name ?? '' }}', '{{ number_format($income->amount, 0) }}')"
                                class="h-8 w-8 bg-red-100 text-red-600 rounded-lg flex items-center justify-center hover:bg-red-200 transition-all duration-200"
                                title="Futa">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </td>
                @endif
            </tr>
            @empty
            <tr>
                <td colspan="{{ Auth::user()->isMchungaji() || Auth::user()->isMhasibu() ? 6 : 5 }}" class="py-12 px-6 text-center">
                    <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-chart-bar text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna Mapato Yaliyopatikana</h3>
                    <p class="text-gray-500 {{ Auth::user()->isMchungaji() || Auth::user()->isMhasibu() ? 'mb-6' : '' }}">Hakuna rekodi za mapato zilizopatikana kwa mujibu wa chujio lako.</p>
                    @if(Auth::user()->isMchungaji() || Auth::user()->isMhasibu())
                    <a href="{{ route('income.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i> Ongeza Mapato
                    </a>
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($incomes->hasPages())
<div class="px-6 py-4 border-t border-gray-200">
    <div class="flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Inaonyesha <span class="font-semibold">{{ $incomes->firstItem() }}</span> hadi
            <span class="font-semibold">{{ $incomes->lastItem() }}</span> ya
            <span class="font-semibold">{{ $incomes->total() }}</span> matokeo
        </div>
        <div>
            {{ $incomes->links() }}
        </div>
    </div>
</div>
@endif
