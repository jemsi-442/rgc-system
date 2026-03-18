@extends('layouts.app')

@section('title', 'Jumuiya - Mfumo wa Kanisa')
@section('page-title', 'Jumuiya')
@section('page-subtitle', 'Usimamizi wa jumuiya za kanisa')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Jumuiya za Kanisa</h1>
            <p class="text-gray-600 mt-2">Usimamizi wa jumuiya zote za kanisa na viongozi wao</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('jumuiyas.create') }}"
               class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800">
                <i class="fas fa-plus"></i>
                <span class="font-medium">Ongeza Jumuiya</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Total Jumuiyas -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jumla ya Jumuiya</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $jumuiyas->total() }}</p>
                </div>
                <div class="h-12 w-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-xl text-primary-600"></i>
                </div>
            </div>
        </div>

        <!-- Active Jumuiyas -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jumuiya Zinazofanya Kazi</p>
                    <p class="text-2xl font-bold text-green-600">{{ $jumuiyas->where('is_active', true)->count() }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Members in Jumuiyas -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jumla ya Wanachama</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $jumuiyas->sum('members_count') }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-friends text-xl text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Jumuiya Grid -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <!-- Table Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 border-b border-gray-200">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list text-primary-500 mr-2"></i> Orodha ya Jumuiya
                    <span class="ml-3 text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                        {{ $jumuiyas->total() }} jumuiya
                    </span>
                </h3>
            </div>
        </div>

        <!-- Jumuiya Grid -->
        <div class="p-6">
            @if($jumuiyas->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($jumuiyas as $jumuiya)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-lg transition-all duration-300">
                        <!-- Jumuiya Header -->
                        <div class="p-5 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-white">
                            <div class="flex justify-between items-start mb-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $jumuiya->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas {{ $jumuiya->is_active ? 'fa-check-circle' : 'fa-pause-circle' }} mr-1"></i>
                                    {{ $jumuiya->is_active ? 'Inafanya kazi' : 'Haifanyi kazi' }}
                                </span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-users mr-1"></i>
                                    {{ $jumuiya->members_count }} wanachama
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $jumuiya->name }}</h3>
                        </div>

                        <!-- Jumuiya Details -->
                        <div class="p-5">
                            <div class="space-y-3 mb-4">
                                @if($jumuiya->leader)
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="h-8 w-8 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-user-tie text-primary-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">Kiongozi</div>
                                        <div>{{ $jumuiya->leader->full_name }}</div>
                                    </div>
                                </div>
                                @else
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="h-8 w-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-user-times text-yellow-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-yellow-600">Hakuna Kiongozi</div>
                                        <div class="text-xs text-gray-500">Teua kiongozi</div>
                                    </div>
                                </div>
                                @endif

                                @if($jumuiya->leader_phone)
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="h-8 w-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-phone text-green-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">Simu</div>
                                        <div>{{ $jumuiya->leader_phone }}</div>
                                    </div>
                                </div>
                                @endif

                                @if($jumuiya->location)
                                <div class="flex items-center text-sm text-gray-600">
                                    <div class="h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">Eneo</div>
                                        <div>{{ $jumuiya->location }}</div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if($jumuiya->description)
                            <div class="mb-4">
                                <div class="text-sm font-medium text-gray-900 mb-1">Maelezo</div>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $jumuiya->description }}</p>
                            </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex gap-2 pt-4 border-t border-gray-200">
                                <a href="{{ route('jumuiyas.show', $jumuiya->id) }}"
                                   class="flex-1 text-center px-3 py-2.5 bg-blue-100 text-blue-700 font-medium rounded-lg hover:bg-blue-200 transition-all duration-200 flex items-center justify-center gap-2">
                                    <i class="fas fa-eye"></i>
                                    <span>Angalia</span>
                                </a>
                                <a href="{{ route('jumuiyas.edit', $jumuiya->id) }}"
                                   class="flex-1 text-center px-3 py-2.5 bg-primary-100 text-primary-700 font-medium rounded-lg hover:bg-primary-200 transition-all duration-200 flex items-center justify-center gap-2">
                                    <i class="fas fa-edit"></i>
                                    <span>Hariri</span>
                                </a>
                                <form action="{{ route('jumuiyas.destroy', $jumuiya->id) }}" method="POST" class="flex-1"
                                      onsubmit="return confirm('Je, una uhakika unataka kufuta jumuiya hii?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full px-3 py-2.5 bg-red-100 text-red-700 font-medium rounded-lg hover:bg-red-200 transition-all duration-200 flex items-center justify-center gap-2">
                                        <i class="fas fa-trash"></i>
                                        <span>Futa</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-users-slash text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna Jumuiya Zilizosajiliwa</h3>
                    <p class="text-gray-500 mb-6">Anza kwa kuongeza jumuiya ya kwanza.</p>
                    <a href="{{ route('jumuiyas.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i> Ongeza Jumuiya ya Kwanza
                    </a>
                </div>
            @endif

            <!-- Pagination -->
            @if($jumuiyas->hasPages())
            <div class="mt-6 pt-6 border-t border-gray-200">
                {{ $jumuiyas->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
