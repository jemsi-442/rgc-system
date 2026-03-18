@extends('layouts.app')

@section('title', 'Hariri Jumuiya - Mfumo wa Kanisa')
@section('page-title', 'Hariri Jumuiya')
@section('page-subtitle', 'Badilisha taarifa za jumuiya')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Hariri Jumuiya</h1>
            <p class="text-gray-600 mt-2">Badilisha taarifa za {{ $jumuiya->name }}</p>
        </div>
        <a href="{{ route('jumuiyas.index') }}"
           class="text-gray-700 px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gray-200 hover:bg-gray-300">
            <i class="fas fa-arrow-left"></i>
            <span class="font-medium">Rudi Nyuma</span>
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-primary-50 to-white">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-edit text-primary-500 mr-2"></i> Taarifa za Jumuiya
            </h3>
        </div>

        <form action="{{ route('jumuiyas.update', $jumuiya->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Jina la Jumuiya -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Jina la Jumuiya <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $jumuiya->name) }}" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('name') border-red-500 @enderror"
                           placeholder="Mfano: Jumuiya Israel">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Eneo -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                        Eneo
                    </label>
                    <input type="text" name="location" id="location" value="{{ old('location', $jumuiya->location) }}"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('location') border-red-500 @enderror"
                           placeholder="Mfano: Eneo la Kaskazini">
                    @error('location')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kiongozi -->
                <div>
                    <label for="leader_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Kiongozi wa Jumuiya
                    </label>
                    <select name="leader_id" id="leader_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('leader_id') border-red-500 @enderror">
                        <option value="">-- Chagua Kiongozi --</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('leader_id', $jumuiya->leader_id) == $member->id ? 'selected' : '' }}>
                                {{ $member->full_name }} ({{ $member->member_number }})
                            </option>
                        @endforeach
                    </select>
                    @error('leader_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Hali
                    </label>
                    <div class="flex items-center mt-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $jumuiya->is_active) ? 'checked' : '' }}
                               class="h-5 w-5 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-3 text-sm text-gray-700">
                            Jumuiya inafanya kazi
                        </label>
                    </div>
                </div>

                <!-- Maelezo -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Maelezo
                    </label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('description') border-red-500 @enderror"
                              placeholder="Maelezo mafupi kuhusu jumuiya hii...">{{ old('description', $jumuiya->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('jumuiyas.index') }}"
                   class="px-6 py-2.5 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition-all duration-200">
                    Ghairi
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Hifadhi Mabadiliko</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
