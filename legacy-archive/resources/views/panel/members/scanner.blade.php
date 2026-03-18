@extends('layouts.app')

@section('title', 'QR Scanner - Mfumo wa Kanisa')
@section('page-title', 'QR Code Scanner')
@section('page-subtitle', 'Scan QR code ya muumini kupata taarifa zake')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('members.index') }}" class="inline-flex items-center px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition duration-200">
            <i class="fas fa-arrow-left mr-2"></i>
            Rudi Orodha ya Waumini
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Scan QR Code</h2>
            <p class="text-gray-600">Elekeza camera kwenye QR code ya muumini</p>
        </div>

        <!-- Camera/Scanner Section -->
        <div class="mb-6">
            <div id="reader" class="w-full max-w-md mx-auto border-4 border-primary-500 rounded-lg overflow-hidden"></div>
        </div>

        <!-- Manual Input Section -->
        <div class="mt-6 border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 text-center">Au Ingiza Namba Kwa Mkono</h3>
            <form onsubmit="searchByNumber(event)" class="max-w-md mx-auto">
                <div class="flex gap-2">
                    <input type="text" id="memberNumberInput" placeholder="Ingiza namba ya muumini..."
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <button type="submit" class="px-6 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition duration-200">
                        <i class="fas fa-search"></i> Tafuta
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Member Info Display -->
    <div id="memberInfo" class="hidden">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user-circle text-primary-500 mr-2"></i>
                Taarifa za Muumini
            </h3>
            <div id="memberDetails"></div>
        </div>
    </div>

    <!-- Error Message -->
    <div id="errorMessage" class="hidden bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-lg mb-6">
        <p id="errorText"></p>
    </div>
</div>

<!-- Include HTML5 QR Code Scanner Library -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
let html5QrCode;

// Initialize QR Code Scanner
function startScanner() {
    html5QrCode = new Html5Qrcode("reader");

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    html5QrCode.start(
        { facingMode: "environment" }, // Use back camera
        config,
        onScanSuccess,
        onScanError
    ).catch(err => {
        console.error("Error starting scanner:", err);
        showError("Kuna tatizo la camera. Tafadhali ingiza namba kwa mkono.");
    });
}

function onScanSuccess(decodedText, decodedResult) {
    console.log(`Scanned: ${decodedText}`);

    // Extract member number from URL
    const url = new URL(decodedText);
    const pathParts = url.pathname.split('/');
    const memberNumber = pathParts[pathParts.length - 1];

    // Fetch member info
    fetchMemberInfo(memberNumber);

    // Stop scanner after successful scan
    html5QrCode.stop();
}

function onScanError(errorMessage) {
    // Ignore scan errors (normal when nothing is detected)
}

function searchByNumber(event) {
    event.preventDefault();
    const memberNumber = document.getElementById('memberNumberInput').value.trim();

    if (!memberNumber) {
        showError('Tafadhali ingiza namba ya muumini');
        return;
    }

    fetchMemberInfo(memberNumber);
}

function fetchMemberInfo(memberNumber) {
    // Show loading
    document.getElementById('memberInfo').classList.add('hidden');
    document.getElementById('errorMessage').classList.add('hidden');

    fetch(`/panel/members/scan/${memberNumber}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayMemberInfo(data.member);
            } else {
                showError(data.message || 'Muumini hajapatikana');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Hitilafu imetokea. Tafadhali jaribu tena.');
        });
}

function displayMemberInfo(member) {
    const genderText = member.gender === 'Mme' ? 'Mwanaume' : 'Mwanamke';
    const statusText = member.is_active ?
        '<span class="text-green-600"><i class="fas fa-check-circle"></i> Hai</span>' :
        '<span class="text-red-600"><i class="fas fa-times-circle"></i> Si Hai</span>';

    const html = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-semibold text-gray-600 mb-3">Taarifa za Kibinafsi</h4>
                <div class="space-y-2">
                    <p><span class="font-medium">Namba:</span> ${member.member_number}</p>
                    <p><span class="font-medium">Jina Kamili:</span> ${member.full_name}</p>
                    <p><span class="font-medium">Jinsia:</span> ${genderText}</p>
                    <p><span class="font-medium">Umri:</span> ${member.age || 'N/A'} miaka (${member.age_group || 'N/A'})</p>
                    <p><span class="font-medium">Simu:</span> ${member.phone || '-'}</p>
                    <p><span class="font-medium">Email:</span> ${member.email || '-'}</p>
                </div>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-gray-600 mb-3">Taarifa za Kanisa</h4>
                <div class="space-y-2">
                    <p><span class="font-medium">Hali ya Ndoa:</span> ${member.marital_status || '-'}</p>
                    <p><span class="font-medium">Kundi Maalum:</span> ${member.special_group || '-'}</p>
                    <p><span class="font-medium">Tarehe ya Ujumbe:</span> ${member.membership_date || '-'}</p>
                    <p><span class="font-medium">Tarehe ya Ubatizo:</span> ${member.baptism_date || '-'}</p>
                    <p><span class="font-medium">Tarehe ya Uthibitisho:</span> ${member.confirmation_date || '-'}</p>
                    <p><span class="font-medium">Hali:</span> ${statusText}</p>
                </div>
            </div>

            <div class="md:col-span-2">
                <h4 class="text-sm font-semibold text-gray-600 mb-3">Michango</h4>
                <p class="text-2xl font-bold text-primary-600">TZS ${member.total_contributions}</p>
            </div>

            <div class="md:col-span-2 flex gap-3 pt-4 border-t">
                <a href="/panel/members/${member.id}" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition duration-200">
                    <i class="fas fa-eye mr-2"></i> Angalia Zaidi
                </a>
                <button onclick="restartScanner()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition duration-200">
                    <i class="fas fa-qrcode mr-2"></i> Scan Tena
                </button>
            </div>
        </div>
    `;

    document.getElementById('memberDetails').innerHTML = html;
    document.getElementById('memberInfo').classList.remove('hidden');
    document.getElementById('errorMessage').classList.add('hidden');
}

function showError(message) {
    document.getElementById('errorText').textContent = message;
    document.getElementById('errorMessage').classList.remove('hidden');
    document.getElementById('memberInfo').classList.add('hidden');
}

function restartScanner() {
    document.getElementById('memberInfo').classList.add('hidden');
    document.getElementById('errorMessage').classList.add('hidden');
    document.getElementById('memberNumberInput').value = '';
    startScanner();
}

// Start scanner when page loads
document.addEventListener('DOMContentLoaded', function() {
    startScanner();
});

// Stop scanner when leaving page
window.addEventListener('beforeunload', function() {
    if (html5QrCode) {
        html5QrCode.stop();
    }
});
</script>
@endsection
