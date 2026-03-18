@extends('layouts.app')

@section('content')
<div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Historia ya Michango</h1>
            <p class="text-gray-600 mt-2">{{ $member->first_name }} {{ $member->last_name }} ({{ $member->member_number }})</p>
        </div>
        <a href="{{ route('members.show', $member->id) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Rudi</span>
        </a>
    </div>

    <!-- Member Summary Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="flex items-center gap-4">
                <div class="bg-primary-100 p-3 rounded-full" style="background-color: rgba(54, 9, 88, 0.1);">
                    <i class="fas fa-user text-2xl" style="color: #360958;"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Jina Kamili</p>
                    <p class="text-base font-semibold text-gray-900">{{ $member->first_name }} {{ $member->last_name }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-phone text-2xl text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Simu</p>
                    <p class="text-base font-semibold text-gray-900">{{ $member->phone }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-calendar text-2xl text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tarehe ya Ujumbe</p>
                    <p class="text-base font-semibold text-gray-900">{{ \Carbon\Carbon::parse($member->membership_date)->format('d/m/Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-coins text-2xl text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Jumla ya Michango</p>
                    <p class="text-base font-semibold text-gray-900">{{ number_format($totalAmount, 0) }} TSh</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('members.contributions', $member->id) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tarehe Kuanzia</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tarehe Mwisho</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Aina ya Mchango</label>
                <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Zote</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-primary-500 hover:bg-primary-600 text-white px-6 py-2 rounded-lg transition duration-200" style="background-color: #360958;">
                    <i class="fas fa-filter"></i> Chuja
                </button>
                <a href="{{ route('members.contributions', $member->id) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-redo"></i> Futa
                </a>
            </div>
        </form>
    </div>

    <!-- Contribution Summary by Category -->
    @if(isset($categoryTotals) && count($categoryTotals) > 0)
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
        @foreach($categoryTotals as $total)
        <div class="bg-white rounded-lg shadow-md p-4">
            <p class="text-sm text-gray-600">{{ $total->category_name }}</p>
            <p class="text-xl font-bold text-gray-900">{{ number_format($total->total, 0) }} TSh</p>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Contributions Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarehe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aina ya Mchango</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kiasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Namba ya Risiti</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($contributions as $contribution)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($contribution->collection_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $contribution->category->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            {{ number_format($contribution->amount, 0) }} TSh
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $contribution->receipt_number ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center">
                            <i class="fas fa-inbox text-gray-300 text-4xl mb-3"></i>
                            <p class="text-gray-500">Hakuna michango iliyopatikana</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($contributions) > 0)
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-sm font-bold text-gray-900">JUMLA</td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ number_format($totalAmount, 0) }} TSh</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        @if($contributions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $contributions->links() }}
        </div>
        @endif
    </div>

    <!-- Export Button (Future Feature) -->
    <div class="mt-6 flex justify-end">
        <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition duration-200 flex items-center gap-2" disabled>
            <i class="fas fa-file-excel"></i>
            <span>Pakua Excel (Inakuja)</span>
        </button>
    </div>
@endsection
