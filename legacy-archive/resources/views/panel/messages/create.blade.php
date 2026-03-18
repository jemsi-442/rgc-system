@extends('layouts.app')

@section('title', 'Ujumbe Mpya - Mfumo wa Kanisa')
@section('page-title', 'Ujumbe Mpya')
@section('page-subtitle', 'Anza mazungumzo na kiongozi')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('messages.index') }}" class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors">
            <i class="fas fa-arrow-left text-gray-600"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Anza Mazungumzo Mapya</h1>
            <p class="text-gray-600">Chagua kiongozi na uandike ujumbe</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('messages.send') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Select Recipient -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user mr-2 text-primary-500"></i>
                    Chagua Mpokeaji *
                </label>
                <select name="receiver_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">-- Chagua Kiongozi --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->name ?? 'Kiongozi' }})</option>
                    @endforeach
                </select>
                @error('receiver_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message Content -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-comment mr-2 text-primary-500"></i>
                    Ujumbe *
                </label>
                <textarea name="content" rows="5" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none"
                    placeholder="Andika ujumbe wako hapa...">{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle"></i> Upeo wa herufi 2000
                </p>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('messages.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Ghairi
                </a>
                <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Tuma Ujumbe</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Quick Select Leaders -->
    @if($users->count() > 3)
    <div class="mt-6 bg-gray-50 rounded-xl p-6 border border-gray-200">
        <h3 class="text-sm font-medium text-gray-700 mb-4">
            <i class="fas fa-bolt text-secondary-500 mr-2"></i>
            Chagua Haraka
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($users->take(6) as $user)
            <button type="button" onclick="selectRecipient({{ $user->id }})" class="flex items-center gap-2 p-3 bg-white border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-all text-left">
                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-user text-primary-600 text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->role->name ?? 'Kiongozi' }}</p>
                </div>
            </button>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function selectRecipient(userId) {
    document.querySelector('select[name="receiver_id"]').value = userId;
    document.querySelector('textarea[name="content"]').focus();
}
</script>
@endsection
