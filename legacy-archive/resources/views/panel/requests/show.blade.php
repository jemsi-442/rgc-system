@extends('layouts.app')

@section('title', 'Taarifa za Ombi - Mfumo wa Kanisa')
@section('page-title', 'Taarifa za Ombi')
@section('page-subtitle', 'Angalia maelezo kamili ya ombi')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Taarifa za Ombi</h1>
            <p class="text-gray-600 mt-2">Angalia maelezo kamili ya ombi la fedha</p>
        </div>
        <div class="flex flex-wrap gap-3">
            @if($request->status === 'Inasubiri')
            <a href="{{ route('requests.edit', $request->id) }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700">
                <i class="fas fa-edit"></i>
                <span class="font-medium">Hariri</span>
            </a>
            @endif
            <a href="{{ route('requests.index') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Rudi Orodhani</span>
            </a>
        </div>
    </div>

    <!-- Request Profile Header -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 p-8">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Profile Icon -->
                <div class="h-24 w-24 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-file-invoice-dollar text-white text-5xl"></i>
                </div>

                <!-- Request Info -->
                <div class="flex-1 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-3xl font-bold">{{ $request->title }}</h2>
                            <div class="flex items-center gap-4 mt-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-hashtag text-white opacity-80"></i>
                                    <span class="text-lg opacity-90">{{ $request->request_number }}</span>
                                </div>
                                @if($request->status === 'Inasubiri')
                                    <span class="inline-flex items-center px-3 py-1 bg-yellow-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-clock mr-1.5"></i>Inasubiri
                                    </span>
                                @elseif($request->status === 'Imeidhinishwa')
                                    <span class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-check-circle mr-1.5"></i>Imeidhinishwa
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 bg-red-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-times-circle mr-1.5"></i>Imekataliwa
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Request Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Request Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-info-circle text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Ombi</h3>
                        <p class="text-sm text-gray-600">Maelezo ya ombi la fedha</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Department -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Idara</p>
                        <div class="flex items-center">
                            <i class="fas fa-building text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $request->department }}</p>
                        </div>
                    </div>

                    <!-- Request Date -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tarehe ya Kuomba</p>
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $request->requested_date->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <!-- Requester -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Ameomba</p>
                        <div class="flex items-center">
                            <i class="fas fa-user text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $request->requester->name }}</p>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Hali</p>
                        <div class="flex items-center">
                            @if($request->status === 'Inasubiri')
                                <i class="fas fa-clock text-yellow-500 mr-2"></i>
                                <p class="text-base font-medium text-yellow-600">Inasubiri</p>
                            @elseif($request->status === 'Imeidhinishwa')
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <p class="text-base font-medium text-green-600">Imeidhinishwa</p>
                            @else
                                <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                <p class="text-base font-medium text-red-600">Imekataliwa</p>
                            @endif
                        </div>
                    </div>

                    @if($request->approved_by)
                    <!-- Approver -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Ameidhinisha</p>
                        <div class="flex items-center">
                            <i class="fas fa-user-check text-green-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $request->approver->name }}</p>
                        </div>
                    </div>

                    <!-- Approval Date -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tarehe ya Uidhinishaji</p>
                        <div class="flex items-center">
                            <i class="fas fa-calendar-check text-green-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $request->approved_date->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Description Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-align-left text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Maelezo ya Ombi</h3>
                        <p class="text-sm text-gray-600">Maelezo kamili kuhusu ombi hili</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-quote-left text-primary-500 mr-2 mt-1"></i>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $request->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Approval Notes Card -->
            @if($request->approval_notes)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-comment-dots text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Maoni ya Uidhinishaji</h3>
                        <p class="text-sm text-gray-600">Maoni kutoka kwa msimamizi</p>
                    </div>
                </div>

                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mr-2 mt-1"></i>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $request->approval_notes }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Amount & Actions -->
        <div class="space-y-6">
            <!-- Amount Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-money-bill-wave text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Fedha</h3>
                        <p class="text-sm text-gray-600">Kiasi kilichoombwa na kuidhinishwa</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Amount Requested -->
                    <div class="bg-primary-50 border-2 border-primary-200 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <i class="fas fa-hand-holding-usd text-primary-600 mr-2"></i>
                                <span class="font-medium text-gray-900">Kiasi Kilichoombwa</span>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-primary-600 mt-2">TZS {{ number_format($request->amount_requested, 2) }}</p>
                    </div>

                    @if($request->amount_approved)
                    <!-- Amount Approved -->
                    <div class="bg-green-50 border-2 border-green-200 p-4 rounded-lg">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                <span class="font-medium text-gray-900">Kiasi Kilichoidhinishwa</span>
                            </div>
                        </div>
                        <p class="text-2xl font-bold text-green-600 mt-2">TZS {{ number_format($request->amount_approved, 2) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions Card -->
            @if($request->status === 'Inasubiri' && (auth()->user()->isMchungaji() || auth()->user()->isMhasibu()))
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-tasks text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Vitendo</h3>
                        <p class="text-sm text-gray-600">Chagua kitendo kwa ombi hili</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <button onclick="openModal('approveModal')" class="w-full text-white px-5 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700">
                        <i class="fas fa-check"></i>
                        <span class="font-medium">Idhinisha Ombi</span>
                    </button>
                    <button onclick="openModal('rejectModal')" class="w-full text-white px-5 py-3 rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700">
                        <i class="fas fa-times"></i>
                        <span class="font-medium">Kataa Ombi</span>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
@if($request->status === 'Inasubiri' && (auth()->user()->isMchungaji() || auth()->user()->isMhasibu()))
<div id="approveModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95">
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-2xl border-b border-gray-200 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Idhinisha Ombi</h3>
                        <p class="text-sm text-gray-600">{{ $request->title }}</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('approveModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('requests.approve', $request->id) }}" method="POST">
            @csrf
            <div class="p-6 space-y-5">
                <div>
                    <label for="amount_approved" class="block text-gray-700 text-sm font-medium mb-2">Kiasi Kilichoidhinishwa <span class="text-red-500">*</span></label>
                    <input type="number" id="amount_approved" name="amount_approved" step="0.01" min="0" max="{{ $request->amount_requested }}"
                           value="{{ old('amount_approved', $request->amount_requested) }}" required
                           class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 text-xl font-bold text-green-700">
                    <p class="mt-1 text-sm text-gray-600">Juu zaidi: TZS {{ number_format($request->amount_requested, 2) }}</p>
                </div>
                <div>
                    <label for="approval_notes" class="block text-gray-700 text-sm font-medium mb-2">Maoni (Si lazima)</label>
                    <textarea id="approval_notes" name="approval_notes" rows="4" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200" placeholder="Andika maoni yako hapa...">{{ old('approval_notes') }}</textarea>
                </div>
            </div>

            <div class="sticky bottom-0 bg-gray-50 px-6 py-5 rounded-b-xl border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('approveModal')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">
                    Funga
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-check"></i>
                    <span>Idhinisha</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95">
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-2xl border-b border-gray-200 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-red-100 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Kataa Ombi</h3>
                        <p class="text-sm text-gray-600">{{ $request->title }}</p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('rejectModal')" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('requests.reject', $request->id) }}" method="POST">
            @csrf
            <div class="p-6 space-y-5">
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                        <p class="text-sm text-yellow-700">
                            Una uhakika unataka kukataa ombi hili? Hatua hii haiwezi kufutwa.
                        </p>
                    </div>
                </div>
                <div>
                    <label for="rejection_notes" class="block text-gray-700 text-sm font-medium mb-2">Sababu ya Kukataa <span class="text-red-500">*</span></label>
                    <textarea id="rejection_notes" name="approval_notes" rows="5" required class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" placeholder="Eleza sababu ya kukataa ombi hili...">{{ old('approval_notes') }}</textarea>
                </div>
            </div>

            <div class="sticky bottom-0 bg-gray-50 px-6 py-5 rounded-b-xl border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('rejectModal')" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">
                    Funga
                </button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-times"></i>
                    <span>Kataa Ombi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            modal.querySelector('div').classList.remove('scale-95');
        }, 10);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.querySelector('div').classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }, 300);
    }
}

// Close modals when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed') && event.target.id.includes('Modal')) {
        closeModal(event.target.id);
    }
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modals = ['approveModal', 'rejectModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (modal && !modal.classList.contains('hidden')) {
                closeModal(modalId);
            }
        });
    }
});
</script>
@endsection
