@extends('layouts.app')

@section('title', $jumuiya->name . ' - Mfumo wa Kanisa')
@section('page-title', $jumuiya->name)
@section('page-subtitle', 'Taarifa za jumuiya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $jumuiya->name }}</h1>
            <p class="text-gray-600 mt-2">Taarifa kamili za jumuiya na wanachama wake</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('jumuiyas.index') }}"
               class="text-gray-700 px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gray-200 hover:bg-gray-300">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Rudi Nyuma</span>
            </a>
            <a href="{{ route('jumuiyas.edit', $jumuiya->id) }}"
               class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800">
                <i class="fas fa-edit"></i>
                <span class="font-medium">Hariri</span>
            </a>
        </div>
    </div>

    <!-- Jumuiya Details Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-white">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle text-primary-500 mr-2"></i> Taarifa za Jumuiya
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Hali</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $jumuiya->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas {{ $jumuiya->is_active ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                            {{ $jumuiya->is_active ? 'Inafanya kazi' : 'Haifanyi kazi' }}
                        </span>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <div class="text-sm text-gray-600 mb-1">Kiongozi</div>
                        @if($jumuiya->leader)
                            <div class="flex items-center">
                                <div class="h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user-tie text-primary-600"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">{{ $jumuiya->leader->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $jumuiya->leader->member_number }}</div>
                                </div>
                            </div>
                        @else
                            <div class="text-yellow-600 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Hakuna kiongozi
                            </div>
                        @endif
                    </div>

                    @if($jumuiya->leader_phone)
                    <div class="border-t border-gray-100 pt-4">
                        <div class="text-sm text-gray-600 mb-1">Simu ya Kiongozi</div>
                        <div class="font-medium text-gray-900">
                            <i class="fas fa-phone text-green-500 mr-2"></i>
                            {{ $jumuiya->leader_phone }}
                        </div>
                    </div>
                    @endif

                    @if($jumuiya->location)
                    <div class="border-t border-gray-100 pt-4">
                        <div class="text-sm text-gray-600 mb-1">Eneo</div>
                        <div class="font-medium text-gray-900">
                            <i class="fas fa-map-marker-alt text-blue-500 mr-2"></i>
                            {{ $jumuiya->location }}
                        </div>
                    </div>
                    @endif

                    @if($jumuiya->description)
                    <div class="border-t border-gray-100 pt-4">
                        <div class="text-sm text-gray-600 mb-1">Maelezo</div>
                        <div class="text-gray-900">{{ $jumuiya->description }}</div>
                    </div>
                    @endif

                    <div class="border-t border-gray-100 pt-4">
                        <div class="text-sm text-gray-600 mb-1">Wanachama</div>
                        <div class="text-2xl font-bold text-primary-600">
                            <i class="fas fa-users mr-2"></i>
                            {{ $jumuiya->members_count }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Members List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-users text-blue-500 mr-2"></i> Wanachama wa Jumuiya
                        <span class="ml-3 text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                            {{ $members->total() }} wanachama
                        </span>
                    </h3>
                </div>

                <div class="p-6">
                    @if($members->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mwanachama</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nambari</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Simu</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hali</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Vitendo</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($members as $member)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-gray-500"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $member->full_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $member->member_number }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $member->phone ?? '-' }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $member->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $member->is_active ? 'Hai' : 'Hajai' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm">
                                            <form action="{{ route('jumuiyas.remove-member', [$jumuiya->id, $member->id]) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Je, una uhakika unataka kumwondoa mwanachama huyu kwenye jumuiya?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-user-minus"></i> Ondoa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($members->hasPages())
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            {{ $members->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-users-slash text-gray-400 text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna Wanachama</h3>
                            <p class="text-gray-500">Jumuiya hii haina wanachama bado.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
