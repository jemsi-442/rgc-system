@extends('layouts.app')

@section('title', 'Aina za Sadaka - Mfumo wa Kanisa')
@section('page-title', 'Aina za Sadaka')
@section('page-subtitle', 'Usimamizi wa aina za sadaka')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Aina za Sadaka</h1>
            <p class="text-gray-600 mt-2">Usimamizi wa aina za sadaka za kanisa</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="openAddTypeModal()" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800">
                <i class="fas fa-plus"></i>
                <span class="font-medium">Ongeza Aina Mpya</span>
            </button>
            <a href="{{ route('offerings.index') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Rudi</span>
            </a>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-list text-primary-500 mr-2"></i> Orodha ya Aina za Sadaka
                <span class="ml-3 text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                    {{ $categories->count() }} aina
                </span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-primary-600 text-white text-sm">
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">Code</th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">Jina</th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">Maelezo</th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider">Hali</th>
                        <th class="py-4 px-6 text-left font-semibold uppercase tracking-wider sticky right-0 bg-primary-600">Vitendo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                    <tr class="bg-white hover:bg-gray-50 transition-all duration-200">
                        <td class="py-4 px-6">
                            <span class="font-mono text-sm text-gray-900">{{ $category->code ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="text-sm font-semibold text-gray-900">{{ $category->name }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="text-sm text-gray-600">{{ $category->description ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-6">
                            @if($category->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Hai
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Si Hai
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-sm sticky right-0 bg-white">
                            <div class="flex items-center space-x-2">
                                <button onclick="editCategory({{ $category->id }})" class="h-8 w-8 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center hover:bg-primary-200 transition-all duration-200" title="Hariri">
                                    <i class="fas fa-pencil-alt text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-6 text-center">
                            <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-list text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna Aina za Sadaka</h3>
                            <p class="text-gray-500 mb-6">Bado hujaongeza aina yoyote ya sadaka.</p>
                            <button onclick="openAddTypeModal()" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                                <i class="fas fa-plus mr-2"></i> Ongeza Aina Mpya
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Type Modal -->
<div id="addTypeModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Ongeza Aina Mpya ya Sadaka</h3>
        </div>
        <form method="POST" action="{{ route('offerings.types.store') }}" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">
                        Jina <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="code" class="block text-sm font-semibold text-gray-900 mb-2">
                        Code
                    </label>
                    <input type="text" id="code" name="code"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                        Maelezo
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                </div>
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="ml-2 text-sm text-gray-700">Aina hii ni hai</span>
                    </label>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeAddTypeModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition-all duration-200">
                    Ghairi
                </button>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                    Hifadhi
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openAddTypeModal() {
    document.getElementById('addTypeModal').classList.remove('hidden');
}

function closeAddTypeModal() {
    document.getElementById('addTypeModal').classList.add('hidden');
}

function editCategory(id) {
    // TODO: Implement edit functionality
    alert('Hariri utendakazi utaongezwa hivi karibuni');
}
</script>
@endpush
@endsection



