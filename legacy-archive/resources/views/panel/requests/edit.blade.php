@extends('layouts.app')

@section('title', 'Hariri Ombi - Mfumo wa Kanisa')
@section('page-title', 'Hariri Ombi')
@section('page-subtitle', 'Badilisha taarifa za ombi')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('requests.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Rudi Orodhani
                </a>
                <h1 class="text-3xl font-bold text-gray-900">Hariri Ombi</h1>
            </div>
            <p class="text-gray-600">{{ $request->request_number }} - Sasisha taarifa za ombi</p>
        </div>
    </div>

    <!-- Warning Message -->
    @if($request->status !== 'Inasubiri')
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-yellow-400 mt-1 mr-3"></i>
            <div>
                <p class="text-sm text-yellow-700">
                    <strong>Onyo:</strong> Ombi hili haliruhusiwi kuhaririwa kwa sababu hali yake ni {{ $request->status }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Container -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('requests.update', $request->id) }}" class="divide-y divide-gray-200">
            @csrf
            @method('PUT')

            <!-- Request Information Section -->
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-file-alt text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Ombi</h3>
                        <p class="text-sm text-gray-600">Sasisha taarifa za kimsingi za ombi</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Request Number (Disabled) -->
                    <div class="md:col-span-2">
                        <label for="request_number" class="block text-sm font-semibold text-gray-900 mb-2">
                            Namba ya Ombi
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-hashtag text-gray-400"></i>
                            </div>
                            <input type="text" id="request_number" value="{{ $request->request_number }}" disabled
                                   class="pl-10 w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600">
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">
                            Kichwa cha Ombi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-heading text-gray-400"></i>
                            </div>
                            <input type="text" id="title" name="title" value="{{ old('title', $request->title) }}" required {{ $request->status !== 'Inasubiri' ? 'disabled' : '' }}
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('title') border-red-500 @enderror {{ $request->status !== 'Inasubiri' ? 'bg-gray-100' : '' }}"
                                   placeholder="Kwa mfano: Ukarabati wa Kanisa">
                        </div>
                        @error('title')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department" class="block text-sm font-semibold text-gray-900 mb-2">
                            Idara <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-sitemap text-gray-400"></i>
                            </div>
                            <input type="text" id="department" name="department" value="{{ old('department', $request->department) }}" required {{ $request->status !== 'Inasubiri' ? 'disabled' : '' }}
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('department') border-red-500 @enderror {{ $request->status !== 'Inasubiri' ? 'bg-gray-100' : '' }}"
                                   placeholder="Kwa mfano: Ujenzi">
                        </div>
                        @error('department')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount Requested -->
                    <div>
                        <label for="amount_requested" class="block text-sm font-semibold text-gray-900 mb-2">
                            Kiasi Kinachoombwa (TSh) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-money-bill-wave text-gray-400"></i>
                            </div>
                            <input type="number" id="amount_requested" name="amount_requested" value="{{ old('amount_requested', $request->amount_requested) }}" step="0.01" min="0" required {{ $request->status !== 'Inasubiri' ? 'disabled' : '' }}
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('amount_requested') border-red-500 @enderror {{ $request->status !== 'Inasubiri' ? 'bg-gray-100' : '' }}"
                                   placeholder="0.00">
                        </div>
                        @error('amount_requested')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Requested Date -->
                    <div class="md:col-span-2">
                        <label for="requested_date" class="block text-sm font-semibold text-gray-900 mb-2">
                            Tarehe ya Ombi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-check text-gray-400"></i>
                            </div>
                            <input type="date" id="requested_date" name="requested_date" value="{{ old('requested_date', $request->requested_date->format('Y-m-d')) }}" required {{ $request->status !== 'Inasubiri' ? 'disabled' : '' }}
                                   class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('requested_date') border-red-500 @enderror {{ $request->status !== 'Inasubiri' ? 'bg-gray-100' : '' }}">
                        </div>
                        @error('requested_date')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Request Details Section -->
            <div class="p-6 bg-gray-50">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-info-circle text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Maelezo ya Ombi</h3>
                        <p class="text-sm text-gray-600">Eleza kwa undani kuhusu ombi lako</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                            Maelezo <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute top-3 left-3">
                                <i class="fas fa-align-left text-gray-400"></i>
                            </div>
                            <textarea id="description" name="description" rows="6" required {{ $request->status !== 'Inasubiri' ? 'disabled' : '' }}
                                      class="pl-10 w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-gray-900 @error('description') border-red-500 @enderror {{ $request->status !== 'Inasubiri' ? 'bg-gray-100' : '' }}"
                                      placeholder="Eleza kwa undani kuhusu ombi lako, sababu za kuhitaji fedha hizi, na jinsi fedha zitatumika...">{{ old('description', $request->description) }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="sticky bottom-0 bg-white px-6 py-5 border-t border-gray-200 flex justify-end space-x-4">
                <a href="{{ route('requests.show', $request->id) }}"
                   class="px-6 py-3 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Ghairi</span>
                </a>
                @if($request->status === 'Inasubiri')
                <button type="submit"
                        class="px-8 py-3 bg-primary-600 text-white font-bold rounded-lg hover:bg-primary-700 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fas fa-save"></i>
                    <span>Sasisha Ombi</span>
                </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
