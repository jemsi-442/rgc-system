@extends('layouts.app')

@section('title', 'Waumini - Mfumo wa Kanisa')
@section('page-title', 'Waumini')
@section('page-subtitle', 'Usimamizi wa taarifa za waumini wa kanisa')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Waumini wa Kanisa</h1>
            <p class="text-gray-600 mt-2">Usimamizi kamili wa orodha ya waumini wote</p>
        </div>
        @if(Auth::user()->isMchungaji() || Auth::user()->isMhasibu())
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('quick-entry.login') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700" target="_blank">
                <i class="fas fa-qrcode"></i>
                <span class="font-medium">Quick Entry</span>
            </a>
            <a href="{{ route('members.import.form') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700">
                <i class="fas fa-file-upload"></i>
                <span class="font-medium">Ingiza Kwa Wingi</span>
            </a>
            <a href="{{ route('members.create') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800">
                <i class="fas fa-user-plus"></i>
                <span class="font-medium">Sajili Muumini</span>
            </a>
        </div>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Jumla ya Waumini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-xl text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Waumini Hai</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-check text-xl text-green-600"></i>
                </div>
            </div>
        </div>

        <a href="javascript:void(0)" onclick="filterByStatus('pending')" class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md hover:border-yellow-300 transition-all duration-200 block {{ request('status') === 'pending' ? 'ring-2 ring-yellow-400 border-yellow-400' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Wanaosubiri Idhini</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-clock text-xl text-yellow-600"></i>
                </div>
            </div>
        </a>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Wanaume</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['male'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-primary-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-male text-xl text-primary-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Wanawake</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['female'] ?? 0 }}</p>
                </div>
                <div class="h-12 w-12 bg-pink-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-female text-xl text-pink-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="fas fa-filter text-primary-500 mr-2"></i> Chuja Waumini
            </h3>
        </div>
        <form id="filterForm" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search Field -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tafuta</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="pl-10 w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Tafuta jina, namba, simu, email...">
                    </div>
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jinsia</label>
                    <select name="gender" id="genderFilter" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Wote</option>
                        @foreach($genders as $gender)
                            <option value="{{ $gender }}" {{ request('gender') == $gender ? 'selected' : '' }}>
                                {{ $gender == 'Mme' ? 'Me' : ($gender == 'Mke' ? 'Ke' : $gender) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hali</label>
                    <select name="is_active" id="statusFilter" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Wote</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Hai</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inasubiri Idhini</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Age Group -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kundi la Umri</label>
                    <select name="age_group" id="ageGroupFilter" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Wote</option>
                        @foreach($ageGroups as $ageGroup)
                            @php
                                $ageLabel = match($ageGroup) {
                                    'Watoto' => 'Watoto (Chini ya 18)',
                                    'Vijana' => 'Vijana (18-34)',
                                    'Wazima' => 'Wazima (35-59)',
                                    'Wazee' => 'Wazee (60+)',
                                    default => $ageGroup
                                };
                            @endphp
                            <option value="{{ $ageGroup }}" {{ request('age_group') == $ageGroup ? 'selected' : '' }}>
                                {{ $ageLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Special Group -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kundi Maalum</label>
                    <select name="special_group" id="specialGroupFilter" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Wote</option>
                        @foreach($specialGroups as $group)
                            <option value="{{ $group }}" {{ request('special_group') == $group ? 'selected' : '' }}>{{ $group }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Marital Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hali ya Ndoa</label>
                    <select name="marital_status" id="maritalStatusFilter" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Wote</option>
                        @foreach($maritalStatuses as $maritalStatus)
                            <option value="{{ $maritalStatus }}" {{ request('marital_status') == $maritalStatus ? 'selected' : '' }}>
                                {{ $maritalStatus }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Clear Filter Link -->
            <div class="flex justify-end pt-2">
                <button type="button" onclick="clearFilters()" class="text-sm text-gray-500 hover:text-primary-600 transition-colors flex items-center gap-1">
                    <i class="fas fa-redo text-xs"></i>
                    <span>Futa Chujio</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Members Table -->
    <div class="mt-6 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden" id="membersTableContainer">
        @include('panel.members._table')
    </div>
</div>

<!-- Confirm Action Modal -->
<div id="confirmActionModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[9999]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95" id="confirmActionModalContent">
        <div class="p-6 text-center">
            <div id="confirmActionIcon" class="h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i id="confirmActionIconClass" class="text-3xl"></i>
            </div>
            <h3 id="confirmActionTitle" class="text-xl font-bold text-gray-900 mb-2">Thibitisha</h3>
            <p id="confirmActionMessage" class="text-gray-600 mb-2">Je, una uhakika?</p>
            <p id="confirmActionName" class="text-lg font-semibold mb-6"></p>
            <div class="flex gap-3">
                <button onclick="closeConfirmActionModal()" class="flex-1 px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all">
                    <i class="fas fa-times mr-2"></i>Ghairi
                </button>
                <button id="confirmActionBtn" class="flex-1 px-5 py-2.5 text-sm font-medium text-white rounded-lg transition-all flex items-center justify-center gap-2">
                    <i id="confirmActionBtnIcon" class="fas fa-check"></i>
                    <span id="confirmActionBtnText">Thibitisha</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Alert Modal -->
<div id="memberAlertModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[10000]">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95" id="memberAlertModalContent">
        <div class="p-6 text-center">
            <div id="memberAlertIcon" class="h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i id="memberAlertIconClass" class="text-3xl"></i>
            </div>
            <h3 id="memberAlertTitle" class="text-xl font-bold text-gray-900 mb-2">Ujumbe</h3>
            <p id="memberAlertMessage" class="text-gray-600 mb-6">Ujumbe hapa</p>
            <button onclick="closeMemberAlertModal()" class="w-full px-5 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 transition-all">
                <i class="fas fa-check mr-2"></i>Sawa, Nimeelewa
            </button>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div id="qrCodeModal" class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 hidden z-[9999]">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95">
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-xl border-b border-gray-200 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-qrcode text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">QR Code ya Muumini</h3>
                        <p class="text-sm text-gray-600">Scan kupata taarifa za muumini</p>
                    </div>
                </div>
                <button type="button" onclick="closeQrModal()" class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div class="p-6 text-center">
            <div class="mb-4">
                <p class="text-gray-700 mb-1">Namba ya Muumini:</p>
                <p class="text-2xl font-bold text-primary-600" id="qrMemberNumber"></p>
            </div>
            <div id="qrCodeContainer" class="flex justify-center items-center bg-white p-6 rounded-xl border-2 border-gray-200 mb-4"></div>
            <p class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i> Tumia QR code hii kwa ajili ya usajili wa haraka
            </p>
        </div>
        <div class="sticky bottom-0 bg-gray-50 px-6 py-5 rounded-b-xl border-t border-gray-200 flex justify-end space-x-3">
            <button onclick="printQrCode()" class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-all duration-200 flex items-center gap-2">
                <i class="fas fa-print"></i>
                <span>Print</span>
            </button>
            <button onclick="closeQrModal()" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-200">
                Funga
            </button>
        </div>
    </div>
</div>

@include('partials.loading-modal')
@endsection

@section('scripts')
<script>
// ========================================
// Global Variables
// ========================================
let currentAction = null;
let currentMemberId = null;
let searchTimeout;
let currentPage = 1;

// ========================================
// Confirm Action Modal Functions
// ========================================
function confirmAction(action, memberId, memberName) {
    currentAction = action;
    currentMemberId = memberId;

    const modal = document.getElementById('confirmActionModal');
    const content = document.getElementById('confirmActionModalContent');
    const iconContainer = document.getElementById('confirmActionIcon');
    const iconClass = document.getElementById('confirmActionIconClass');
    const title = document.getElementById('confirmActionTitle');
    const message = document.getElementById('confirmActionMessage');
    const nameEl = document.getElementById('confirmActionName');
    const btn = document.getElementById('confirmActionBtn');
    const btnIcon = document.getElementById('confirmActionBtnIcon');
    const btnText = document.getElementById('confirmActionBtnText');

    if (action === 'delete') {
        iconContainer.className = 'h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-red-100';
        iconClass.className = 'fas fa-trash-alt text-3xl text-red-600';
        title.textContent = 'Thibitisha Kufuta';
        message.textContent = 'Je, una uhakika unataka kufuta muumini huyu? Hatua hii haiwezi kutenduliwa.';
        nameEl.textContent = memberName;
        nameEl.className = 'text-lg font-semibold mb-6 text-red-600';
        btn.className = 'flex-1 px-5 py-2.5 text-sm font-medium text-white rounded-lg transition-all flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700';
        btnIcon.className = 'fas fa-trash';
        btnText.textContent = 'Futa';
    } else if (action === 'deactivate') {
        iconContainer.className = 'h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-yellow-100';
        iconClass.className = 'fas fa-user-slash text-3xl text-yellow-600';
        title.textContent = 'Thibitisha Kusimamisha';
        message.textContent = 'Je, una uhakika unataka kumsimamisha muumini huyu?';
        nameEl.textContent = memberName;
        nameEl.className = 'text-lg font-semibold mb-6 text-yellow-600';
        btn.className = 'flex-1 px-5 py-2.5 text-sm font-medium text-white rounded-lg transition-all flex items-center justify-center gap-2 bg-yellow-600 hover:bg-yellow-700';
        btnIcon.className = 'fas fa-user-slash';
        btnText.textContent = 'Simamisha';
    } else if (action === 'activate') {
        iconContainer.className = 'h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-4 bg-green-100';
        iconClass.className = 'fas fa-user-check text-3xl text-green-600';
        title.textContent = 'Thibitisha Kuanzisha';
        message.textContent = 'Je, una uhakika unataka kumwanzisha muumini huyu?';
        nameEl.textContent = memberName;
        nameEl.className = 'text-lg font-semibold mb-6 text-green-600';
        btn.className = 'flex-1 px-5 py-2.5 text-sm font-medium text-white rounded-lg transition-all flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700';
        btnIcon.className = 'fas fa-user-check';
        btnText.textContent = 'Anzisha';
    }

    btn.onclick = executeAction;
    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function closeConfirmActionModal() {
    const modal = document.getElementById('confirmActionModal');
    const content = document.getElementById('confirmActionModalContent');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
        currentAction = null;
        currentMemberId = null;
    }, 200);
}

function executeAction() {
    if (!currentAction || !currentMemberId) return;

    const btn = document.getElementById('confirmActionBtn');
    const btnIcon = document.getElementById('confirmActionBtnIcon');
    const btnText = document.getElementById('confirmActionBtnText');

    btn.disabled = true;
    btnIcon.className = 'fas fa-spinner fa-spin';
    btnText.textContent = 'Inatuma...';

    const formId = `${currentAction}-form-${currentMemberId}`;
    const form = document.getElementById(formId);

    if (form) {
        const formData = new FormData(form);
        const csrf = form.querySelector('input[name="_token"]')?.value;

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {})
            },
            body: formData
        })
        .then(async (res) => {
            const contentType = res.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw data;
                return data;
            } else {
                if (!res.ok) throw new Error('Hitilafu imetokea');
                return { success: true, message: 'Imefanikiwa' };
            }
        })
        .then((data) => {
            showMemberAlert('success', 'Imefanikiwa', data.message || 'Imefanikiwa');
            closeConfirmActionModal();
            setTimeout(() => filterMembers(), 500);
        })
        .catch((err) => {
            const msg = err?.message || 'Hitilafu imetokea. Tafadhali jaribu tena.';
            showMemberAlert('error', 'Hitilafu', msg);
        })
        .finally(() => {
            btn.disabled = false;
            btnIcon.className = 'fas fa-check';
            btnText.textContent = 'Thibitisha';
        });
    } else {
        showMemberAlert('error', 'Hitilafu', 'Fomu haijapatikana. Tafadhali jaribu tena.');
        closeConfirmActionModal();
    }
}

// ========================================
// Alert Modal Functions
// ========================================
function showMemberAlert(type, title, message) {
    const modal = document.getElementById('memberAlertModal');
    const content = document.getElementById('memberAlertModalContent');
    const iconContainer = document.getElementById('memberAlertIcon');
    const iconClass = document.getElementById('memberAlertIconClass');
    const titleEl = document.getElementById('memberAlertTitle');
    const messageEl = document.getElementById('memberAlertMessage');

    if (!modal) {
        alert(title + ': ' + message);
        return;
    }

    const configs = {
        'success': { bgColor: 'bg-green-100', iconColor: 'text-green-600', icon: 'fas fa-check-circle' },
        'error': { bgColor: 'bg-red-100', iconColor: 'text-red-600', icon: 'fas fa-times-circle' },
        'warning': { bgColor: 'bg-yellow-100', iconColor: 'text-yellow-600', icon: 'fas fa-exclamation-triangle' },
        'info': { bgColor: 'bg-blue-100', iconColor: 'text-blue-600', icon: 'fas fa-info-circle' }
    };

    const config = configs[type] || configs['info'];
    iconContainer.className = `h-16 w-16 rounded-full flex items-center justify-center mx-auto mb-4 ${config.bgColor}`;
    iconClass.className = `${config.icon} text-3xl ${config.iconColor}`;
    titleEl.textContent = title;
    messageEl.textContent = message;

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95');
        content.classList.add('scale-100');
    }, 10);
}

function closeMemberAlertModal() {
    const modal = document.getElementById('memberAlertModal');
    const content = document.getElementById('memberAlertModalContent');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 200);
}

// ========================================
// Filter & Search Functions (AJAX without page reload)
// ========================================
function filterMembers(page = 1) {
    clearTimeout(searchTimeout);
    currentPage = page;

    searchTimeout = setTimeout(() => {
        const params = new URLSearchParams();

        const search = document.getElementById('searchInput')?.value;
        const gender = document.getElementById('genderFilter')?.value;
        const isActive = document.getElementById('statusFilter')?.value;
        const ageGroup = document.getElementById('ageGroupFilter')?.value;
        const specialGroup = document.getElementById('specialGroupFilter')?.value;
        const maritalStatus = document.getElementById('maritalStatusFilter')?.value;

        if (search) params.append('search', search);
        if (gender) params.append('gender', gender);
        if (isActive !== '') params.append('is_active', isActive);
        if (ageGroup) params.append('age_group', ageGroup);
        if (specialGroup) params.append('special_group', specialGroup);
        if (maritalStatus) params.append('marital_status', maritalStatus);
        if (page > 1) params.append('page', page);

        const container = document.getElementById('membersTableContainer');
        if (container) {
            container.style.opacity = '0.5';
            container.style.pointerEvents = 'none';
        }

        // Update URL without reloading
        const newUrl = '{{ route("members.index") }}' + (params.toString() ? '?' + params.toString() : '');
        window.history.pushState({}, '', newUrl);

        fetch(newUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            if (container) {
                container.innerHTML = html;
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
                attachPaginationListeners();
            }
        })
        .catch(error => {
            console.error('Error filtering members:', error);
            if (container) {
                container.style.opacity = '1';
                container.style.pointerEvents = 'auto';
            }
        });
    }, 300);
}

function filterByStatus(status) {
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.value = status === 'pending' ? '0' : '';
        filterMembers();
    }
}

function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('genderFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('ageGroupFilter').value = '';
    document.getElementById('specialGroupFilter').value = '';
    document.getElementById('maritalStatusFilter').value = '';
    filterMembers();
}

function attachPaginationListeners() {
    // Intercept pagination links for AJAX navigation
    document.querySelectorAll('#membersTableContainer .pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = new URL(this.href);
            const page = url.searchParams.get('page') || 1;
            filterMembers(parseInt(page));
        });
    });
}

// ========================================
// QR Code Functions
// ========================================
function viewQrCode(memberId, memberNumber) {
    document.getElementById('qrMemberNumber').textContent = memberNumber;
    const qrContainer = document.getElementById('qrCodeContainer');
    qrContainer.innerHTML = '<div class="text-gray-500 py-8"><i class="fas fa-spinner fa-spin text-4xl"></i><p class="mt-2 text-sm">Inapakia QR code...</p></div>';

    fetch(`/panel/members/${memberId}/qrcode`)
        .then(response => response.text())
        .then(svg => {
            qrContainer.innerHTML = svg;
            document.getElementById('qrCodeModal').classList.remove('hidden');
            setTimeout(() => {
                document.querySelector('#qrCodeModal > div').classList.remove('scale-95');
            }, 10);
        })
        .catch(error => {
            console.error('Error loading QR code:', error);
            qrContainer.innerHTML = '<p class="text-red-500 py-4"><i class="fas fa-exclamation-triangle mr-2"></i>Kuna hitilafu katika kupakua QR code</p>';
        });
}

function closeQrModal() {
    const modal = document.getElementById('qrCodeModal');
    modal.querySelector('div').classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 300);
}

function printQrCode() {
    const memberNumber = document.getElementById('qrMemberNumber').textContent;
    const qrCode = document.getElementById('qrCodeContainer').innerHTML;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>QR Code - ${memberNumber}</title>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; text-align: center; padding: 40px 20px; background: white; }
                    h1 { color: #360958; font-size: 24px; margin-bottom: 10px; }
                    h2 { color: #666; font-size: 16px; margin-bottom: 30px; }
                    .qr-container { display: inline-block; border: 3px solid #360958; padding: 30px; margin: 30px 0; border-radius: 10px; background: white; }
                    .footer { color: #666; font-size: 14px; margin-top: 20px; }
                    @media print { @page { margin: 20mm; } }
                </style>
            </head>
            <body>
                <h1>RGC Makabe RGC</h1>
                <h2>QR Code ya Muumini</h2>
                <h1 style="font-size: 28px; color: #360958; font-weight: bold; margin: 10px 0;">${memberNumber}</h1>
                <div class="qr-container">${qrCode}</div>
                <p class="footer">Scan QR code hii kupata taarifa za muumini</p>
                <script>window.onload = function() { window.print(); setTimeout(function() { window.close(); }, 500); }<\/script>
            </body>
        </html>
    `);
    printWindow.document.close();
}

// ========================================
// Event Listeners
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    // Live search
    document.getElementById('searchInput')?.addEventListener('input', () => filterMembers());

    // Filter changes
    document.getElementById('genderFilter')?.addEventListener('change', () => filterMembers());
    document.getElementById('statusFilter')?.addEventListener('change', () => filterMembers());
    document.getElementById('ageGroupFilter')?.addEventListener('change', () => filterMembers());
    document.getElementById('specialGroupFilter')?.addEventListener('change', () => filterMembers());
    document.getElementById('maritalStatusFilter')?.addEventListener('change', () => filterMembers());

    // Prevent form submission
    document.getElementById('filterForm')?.addEventListener('submit', e => e.preventDefault());

    // Prevent Enter key submission in search
    document.getElementById('searchInput')?.addEventListener('keypress', e => {
        if (e.key === 'Enter') e.preventDefault();
    });

    // Attach pagination listeners
    attachPaginationListeners();
});

// Close modals on backdrop click
document.getElementById('confirmActionModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeConfirmActionModal();
});
document.getElementById('memberAlertModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeMemberAlertModal();
});
document.getElementById('qrCodeModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeQrModal();
});

// Close modals with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeConfirmActionModal();
        closeMemberAlertModal();
        closeQrModal();
    }
});

// Handle browser back/forward
window.addEventListener('popstate', function() {
    const urlParams = new URLSearchParams(window.location.search);
    document.getElementById('searchInput').value = urlParams.get('search') || '';
    document.getElementById('genderFilter').value = urlParams.get('gender') || '';
    document.getElementById('statusFilter').value = urlParams.get('is_active') || '';
    document.getElementById('ageGroupFilter').value = urlParams.get('age_group') || '';
    document.getElementById('specialGroupFilter').value = urlParams.get('special_group') || '';
    document.getElementById('maritalStatusFilter').value = urlParams.get('marital_status') || '';
    filterMembers(parseInt(urlParams.get('page')) || 1);
});

// Show flash messages
@if(session('success'))
showMemberAlert('success', 'Imefanikiwa!', '{{ session('success') }}');
@endif

@if(session('error'))
showMemberAlert('error', 'Hitilafu!', '{{ session('error') }}');
@endif

@if(session('warning'))
showMemberAlert('warning', 'Onyo!', '{{ session('warning') }}');
@endif
</script>
@endsection
