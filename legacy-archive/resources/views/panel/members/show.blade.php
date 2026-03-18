@extends('layouts.app')

@section('title', 'Taarifa za Muumini - Mfumo wa Kanisa')
@section('page-title', 'Taarifa za Muumini')
@section('page-subtitle', 'Angalia taarifa kamili za muumini')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Taarifa za Muumini</h1>
            <p class="text-gray-600 mt-2">Angalia taarifa kamili za muumini wa kanisa</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('members.edit', $member->id) }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700">
                <i class="fas fa-edit"></i>
                <span class="font-medium">Hariri</span>
            </a>
            <a href="{{ route('members.index') }}" class="text-white px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700">
                <i class="fas fa-arrow-left"></i>
                <span class="font-medium">Rudi Orodhani</span>
            </a>
        </div>
    </div>

    <!-- Member Profile Header -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-primary-600 to-primary-800 p-8">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                <!-- Profile Icon -->
                <div class="h-24 w-24 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-user text-white text-5xl"></i>
                </div>

                <!-- Member Info -->
                <div class="flex-1 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-3xl font-bold">{{ $member->first_name }} {{ $member->middle_name }} {{ $member->last_name }}</h2>
                            <div class="flex items-center gap-4 mt-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-hashtag text-white opacity-80"></i>
                                    <span class="text-lg opacity-90">{{ $member->member_number }}</span>
                                </div>
                                @if($member->is_active)
                                    <span class="inline-flex items-center px-3 py-1 bg-green-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-check-circle mr-1.5"></i>Muumini Hai
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 bg-red-500 text-white rounded-full text-sm font-semibold">
                                        <i class="fas fa-times-circle mr-1.5"></i>Si Hai
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex gap-3">
                            <button onclick="viewQrCode('{{ $member->id }}', '{{ $member->member_number }}')"
                                    class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-all duration-200 flex items-center gap-2">
                                <i class="fas fa-qrcode"></i>
                                <span>QR Code</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Member Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Kibinafsi</h3>
                        <p class="text-sm text-gray-600">Taarifa za msingi za muumini</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Birth Date -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tarehe ya Kuzaliwa</p>
                        <div class="flex items-center">
                            <i class="fas fa-birthday-cake text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ \Carbon\Carbon::parse($member->date_of_birth)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <!-- Age -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Umri</p>
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ \Carbon\Carbon::parse($member->date_of_birth)->age }} miaka</p>
                        </div>
                    </div>

                    <!-- Gender -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Jinsia</p>
                        <div class="flex items-center">
                            @if($member->gender == 'Mme')
                                <i class="fas fa-male text-blue-500 mr-2"></i>
                            @else
                                <i class="fas fa-female text-pink-500 mr-2"></i>
                            @endif
                            <p class="text-base font-medium text-gray-900">{{ $member->gender == 'Mme' ? 'Mwanaume' : 'Mwanamke' }}</p>
                        </div>
                    </div>

                    <!-- Marital Status -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Hali ya Ndoa</p>
                        <div class="flex items-center">
                            <i class="fas fa-heart text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $member->marital_status }}</p>
                        </div>
                    </div>

                    <!-- ID Number -->
                    @if($member->id_number)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Namba ya Kitambulisho</p>
                        <div class="flex items-center">
                            <i class="fas fa-id-card text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $member->id_number }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Occupation -->
                    @if($member->occupation)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Kazi</p>
                        <div class="flex items-center">
                            <i class="fas fa-briefcase text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $member->occupation }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-phone-alt text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Mawasiliano</h3>
                        <p class="text-sm text-gray-600">Mawasiliano na anwani ya muumini</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Phone -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Namba ya Simu</p>
                        <div class="flex items-center">
                            <i class="fas fa-mobile-alt text-green-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $member->phone }}</p>
                        </div>
                    </div>

                    <!-- Email -->
                    @if($member->email)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Barua pepe</p>
                        <div class="flex items-center">
                            <i class="fas fa-envelope text-blue-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $member->email }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Address -->
                    @if($member->address)
                    <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Anwani</p>
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-red-500 mr-2 mt-1"></i>
                            <p class="text-base font-medium text-gray-900">{{ $member->address }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- City/Region -->
                    @if($member->city || $member->region)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Jiji/Mkoa</p>
                        <div class="flex items-center">
                            <i class="fas fa-city text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ $member->city }}{{ $member->city && $member->region ? ', ' : '' }}{{ $member->region }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Christian Information Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-church text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Taarifa za Kikristo</h3>
                        <p class="text-sm text-gray-600">Taarifa za uanachama na ibada</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Membership Date -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tarehe ya Ujumbe</p>
                        <div class="flex items-center">
                            <i class="fas fa-calendar-check text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ \Carbon\Carbon::parse($member->membership_date)->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <!-- Baptism Date -->
                    @if($member->baptism_date)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tarehe ya Ubatizo</p>
                        <div class="flex items-center">
                            <i class="fas fa-water text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ \Carbon\Carbon::parse($member->baptism_date)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Confirmation Date -->
                    @if($member->confirmation_date)
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Tarehe ya Uthibitisho</p>
                        <div class="flex items-center">
                            <i class="fas fa-hands-praying text-primary-500 mr-2"></i>
                            <p class="text-base font-medium text-gray-900">{{ \Carbon\Carbon::parse($member->confirmation_date)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Additional Notes -->
            @if($member->notes)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-sticky-note text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Maelezo Mengine</h3>
                        <p class="text-sm text-gray-600">Taarifa zingine muhimu</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-edit text-primary-500 mr-2 mt-1"></i>
                        <p class="text-gray-700">{{ $member->notes }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column: Contribution & Quick Stats -->
        <div class="space-y-6">
            <!-- Contribution Summary Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-hand-holding-usd text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Muhtasari wa Michango</h3>
                        <p class="text-sm text-gray-600">Jumla ya michango ya muumini</p>
                    </div>
                </div>

                @if(isset($contributionSummary) && count($contributionSummary) > 0)
                    <div class="space-y-4">
                        @foreach($contributionSummary as $summary)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-coins text-yellow-500 mr-2"></i>
                                <span class="text-sm text-gray-700">{{ $summary->category_name }}</span>
                            </div>
                            <span class="text-base font-bold text-gray-900">{{ number_format($summary->total, 0) }} TZS</span>
                        </div>
                        @endforeach

                        <!-- Total Contributions -->
                        <div class="bg-primary-50 border-2 border-primary-200 p-4 rounded-lg mt-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <i class="fas fa-calculator text-primary-600 mr-2"></i>
                                    <span class="font-medium text-gray-900">Jumla ya Michango</span>
                                </div>
                                <span class="text-xl font-bold text-primary-600">{{ number_format($totalContributions ?? 0, 0) }} TZS</span>
                            </div>
                        </div>

                        <!-- View All Contributions Button -->
                        <div class="pt-4 border-t border-gray-200">
                            <a href="{{ route('members.contributions', $member->id) }}"
                               class="block w-full text-center text-white px-4 py-3 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700">
                                <i class="fas fa-list"></i>
                                <span>Angalia Michango Yote</span>
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="mx-auto w-16 h-16 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                            <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Hakuna michango</h3>
                        <p class="text-gray-500 mb-4">Muumini hana michango bado</p>
                        <button onclick="openSadakaModal('{{ $member->id }}')"
                                class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all duration-200">
                            <i class="fas fa-plus mr-2"></i> Ongeza Sadaka
                        </button>
                    </div>
                @endif
            </div>

            <!-- Quick Stats Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center mb-6">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-chart-line text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Takwimu za Haraka</h3>
                        <p class="text-sm text-gray-600">Muhtasari wa taarifa za muumini</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Membership Duration -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-clock text-blue-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Muda wa Ujumbe</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($member->membership_date)->diffForHumans() }}
                        </span>
                    </div>

                    <!-- Contribution Count -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-receipt text-green-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Idadi ya Michango</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">{{ $contributionCount ?? 0 }}</span>
                    </div>

                    <!-- Age Group -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-users text-purple-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Kundi la Umri</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">
                            @php
                                $age = \Carbon\Carbon::parse($member->date_of_birth)->age;
                                if ($age < 18) echo 'Watoto';
                                elseif ($age < 35) echo 'Vijana';
                                elseif ($age < 60) echo 'Wazima';
                                else echo 'Wazee';
                            @endphp
                        </span>
                    </div>

                    <!-- Member Since -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-calendar text-red-500 mr-2"></i>
                            <span class="text-sm text-gray-700">Muumini Tangu</span>
                        </div>
                        <span class="text-base font-medium text-gray-900">{{ \Carbon\Carbon::parse($member->membership_date)->format('Y') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div id="qrCodeModal" class="modal-overlay hidden">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all duration-300 scale-95">
        <div class="sticky top-0 bg-white px-6 py-5 rounded-t-xl border-b border-gray-200 z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="h-10 w-10 bg-primary-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-qrcode text-primary-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">QR Code ya Muumini</h3>
                        <p class="text-sm text-gray-600">{{ $member->first_name }} {{ $member->last_name }}</p>
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
                <p class="text-2xl font-bold text-primary-600">{{ $member->member_number }}</p>
            </div>

            <div id="qrCodeContainer" class="flex justify-center items-center bg-white p-6 rounded-xl border-2 border-gray-200 mb-4">
                <!-- QR Code will be loaded here -->
            </div>

            <p class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i> Scan QR code kupata taarifa za muumini
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

<script>
// QR Code Functions
function viewQrCode(memberId, memberNumber) {
    document.getElementById('qrMemberNumber').textContent = memberNumber;
    const qrContainer = document.getElementById('qrCodeContainer');
    qrContainer.innerHTML = '<div class="text-gray-500 py-8"><i class="fas fa-spinner fa-spin text-4xl"></i><p class="mt-2 text-sm">Inapakia QR code...</p></div>';

    // Load QR code
    const qrUrl = `/panel/members/${memberId}/qrcode`;
    fetch(qrUrl)
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
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
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
                    body {
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        text-align: center;
                        padding: 40px 20px;
                        background: white;
                    }
                    h1 {
                        color: #360958;
                        font-size: 24px;
                        margin-bottom: 10px;
                    }
                    h2 {
                        color: #666;
                        font-size: 16px;
                        margin-bottom: 30px;
                    }
                    .qr-container {
                        display: inline-block;
                        border: 3px solid #360958;
                        padding: 30px;
                        margin: 30px 0;
                        border-radius: 10px;
                        background: white;
                    }
                    .footer {
                        color: #666;
                        font-size: 14px;
                        margin-top: 20px;
                    }
                    @media print {
                        @page { margin: 20mm; }
                    }
                </style>
            </head>
            <body>
                <h1>RGC Makabe RGC</h1>
                <h2>QR Code ya Muumini</h2>
                <h1 style="font-size: 28px; color: #360958; font-weight: bold; margin: 10px 0;">${memberNumber}</h1>
                <div class="qr-container">${qrCode}</div>
                <p class="footer">Scan QR code hii kupata taarifa za muumini</p>
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                        }, 500);
                    }
                <\/script>
            </body>
        </html>
    `);
    printWindow.document.close();
}

// Close QR modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('qrCodeModal');
    if (event.target === modal) {
        closeQrModal();
    }
});

// Close QR modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const modal = document.getElementById('qrCodeModal');
        if (!modal.classList.contains('hidden')) {
            closeQrModal();
        }
    }
});

// Open sadaka modal function (to be implemented in main system)
function openSadakaModal(memberId) {
    // This function should open the sadaka modal with member pre-selected
    console.log('Opening sadaka modal for member:', memberId);
    alert('Utendaji huu utaanzishwa baadaye. Tafadhali tumia ukurasa wa sadaka kuongeza sadaka ya muumini.');
}
</script>
@endsection
