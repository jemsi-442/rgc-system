<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Scanner - Quick Entry</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#faf5ff',
                            100: '#f3e8ff',
                            500: '#360958',
                            600: '#2a0745',
                            700: '#1f0533',
                            800: '#150324',
                        },
                        secondary: {
                            50: '#fefce8',
                            100: '#fef9c3',
                            500: '#efc120',
                            600: '#d4a81c',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        @keyframes scan-line {
            0% { top: 0%; }
            50% { top: 100%; }
            100% { top: 0%; }
        }
        .scan-line {
            position: absolute;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #efc120, transparent);
            animation: scan-line 2s linear infinite;
            box-shadow: 0 0 10px #efc120;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .scanning-indicator {
            animation: pulse 1.5s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="text-white shadow-lg" style="background: linear-gradient(135deg, #360958 0%, #2a0745 100%);">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-qrcode text-2xl mr-3 text-secondary-500"></i>
                <div>
                    <h1 class="text-xl font-bold">Quick Entry Scanner</h1>
                    <p class="text-sm text-primary-100">{{ Auth::user()->name }}</p>
                </div>
            </div>
            <form action="{{ route('quick-entry.logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-sign-out-alt mr-2"></i>Toka
                </button>
            </form>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 py-6">
        <!-- Scanner Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Scan QR Code</h2>
                <p class="text-gray-600">Elekeza camera kwenye QR code ya muumini</p>
            </div>

            <!-- Camera/Scanner Section -->
            <div class="mb-6">
                <div class="relative w-full max-w-md mx-auto">
                    <div id="reader" class="border-4 border-primary-500 rounded-lg overflow-hidden"></div>
                    <!-- Scanning Indicator -->
                    <div id="scanningIndicator" class="absolute inset-0 pointer-events-none hidden">
                        <div class="scan-line"></div>
                    </div>
                </div>
                <!-- Scanning Status -->
                <div class="text-center mt-3">
                    <p id="scanStatus" class="text-sm font-semibold text-primary-600 scanning-indicator">
                        <i class="fas fa-camera mr-2"></i>Inasubiri kuscan...
                    </p>
                </div>
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

        <!-- Member Found Section -->
        <div id="memberFoundSection" class="hidden">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-check text-green-500 mr-2"></i>
                    Muumini Amepatikana
                </h3>
                <div id="memberBasicInfo" class="mb-6"></div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-4 border-t">
                    <button onclick="viewMemberInfo()" class="flex-1 bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center shadow-lg">
                        <i class="fas fa-eye mr-2"></i>
                        Angalia Taarifa
                    </button>
                    <button onclick="addContribution()" class="flex-1 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #efc120 0%, #d4a81c 100%);">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Ongeza Mchango
                    </button>
                </div>
            </div>

            <!-- Member Detailed Info (Hidden by default) -->
            <div id="memberDetailedInfo" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-user-circle text-primary-500 mr-2"></i>
                        Taarifa za Muumini
                    </h3>
                    <button onclick="hideDetailedInfo()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="memberDetails"></div>
                <div class="mt-4 pt-4 border-t">
                    <button onclick="restartScanner()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition duration-200">
                        <i class="fas fa-qrcode mr-2"></i> Scan Tena
                    </button>
                </div>
            </div>
        </div>

        <!-- Error Message -->
        <div id="errorMessage" class="hidden bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-lg mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-circle mt-1 mr-2"></i>
                <p id="errorText"></p>
            </div>
        </div>
    </div>

    <!-- Include HTML5 QR Code Scanner Library -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
    let html5QrCode;
    let currentMember = null;

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
        ).then(() => {
            // Show scanning indicator when camera starts
            document.getElementById('scanningIndicator').classList.remove('hidden');
            document.getElementById('scanStatus').innerHTML = '<i class="fas fa-camera mr-2"></i>Inascan... Elekeza camera kwenye QR code';
        }).catch(err => {
            console.error("Error starting scanner:", err);
            document.getElementById('scanStatus').innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Camera haifanyi kazi';
            showError("Kuna tatizo la camera. Tafadhali ingiza namba kwa mkono.");
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Scanned: ${decodedText}`);

        // Update scan status
        document.getElementById('scanStatus').innerHTML = '<i class="fas fa-check-circle mr-2 text-green-600"></i>QR Code imesomwa!';
        document.getElementById('scanningIndicator').classList.add('hidden');

        // Extract member number from URL
        try {
            const url = new URL(decodedText);
            const pathParts = url.pathname.split('/');
            const memberNumber = pathParts[pathParts.length - 1];

            // Fetch member info
            fetchMemberInfo(memberNumber);

            // Stop scanner after successful scan
            if (html5QrCode) {
                html5QrCode.stop();
            }
        } catch (e) {
            // If not a URL, treat as member number directly
            fetchMemberInfo(decodedText);
            if (html5QrCode) {
                html5QrCode.stop();
            }
        }
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

    let currentPledges = null;

    function fetchMemberInfo(memberNumber) {
        // Show loading
        hideError();
        document.getElementById('memberFoundSection').classList.add('hidden');
        document.getElementById('memberDetailedInfo').classList.add('hidden');

        fetch(`/quick-entry/member/${memberNumber}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentMember = data.member;
                    currentPledges = data.pledges;
                    displayMemberFound(data.member, data.pledges);
                } else {
                    showError(data.message || 'Muumini hajapatikana');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Hitilafu imetokea. Tafadhali jaribu tena.');
            });
    }

    function displayMemberFound(member, pledges) {
        // Determine debt status
        const debtStatus = pledges.has_debt
            ? `<span class="text-orange-600 font-bold"><i class="fas fa-exclamation-circle"></i> Ana Deni: TZS ${formatNumber(pledges.total_remaining)}</span>`
            : `<span class="text-green-600 font-bold"><i class="fas fa-check-circle"></i> Hakuna Deni</span>`;

        const html = `
            <div class="space-y-4">
                <div class="bg-primary-50 border border-primary-200 rounded-lg p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Namba ya Muumini</p>
                            <p class="text-lg font-bold text-primary-700">${member.member_number}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Jina Kamili</p>
                            <p class="text-lg font-bold text-gray-800">${member.full_name}</p>
                        </div>
                    </div>
                </div>

                <!-- Pledge Status Card -->
                <div class="bg-white border ${pledges.has_debt ? 'border-orange-400' : 'border-green-400'} rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-semibold text-gray-700">Hali ya Ahadi</h4>
                        ${debtStatus}
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-blue-50 p-2 rounded">
                            <p class="text-xs text-gray-600">Ahadi</p>
                            <p class="text-sm font-bold text-blue-600">TZS ${formatNumber(pledges.total_pledged)}</p>
                        </div>
                        <div class="bg-green-50 p-2 rounded">
                            <p class="text-xs text-gray-600">Imelipwa</p>
                            <p class="text-sm font-bold text-green-600">TZS ${formatNumber(pledges.total_paid)}</p>
                        </div>
                        <div class="bg-orange-50 p-2 rounded">
                            <p class="text-xs text-gray-600">Baki</p>
                            <p class="text-sm font-bold text-orange-600">TZS ${formatNumber(pledges.total_remaining)}</p>
                        </div>
                    </div>
                    <div class="mt-3 text-center text-xs text-gray-600">
                        ${pledges.active_count} ahadi zinazoendelea | ${pledges.completed_count} zimekamilika
                    </div>
                </div>
            </div>
        `;

        document.getElementById('memberBasicInfo').innerHTML = html;
        document.getElementById('memberFoundSection').classList.remove('hidden');
        hideError();
    }

    function formatNumber(num) {
        return new Intl.NumberFormat('en-US').format(num);
    }

    function viewMemberInfo() {
        if (!currentMember || !currentPledges) return;

        const genderText = currentMember.gender === 'Mme' ? 'Mwanaume' : 'Mwanamke';
        const statusText = currentMember.is_active ?
            '<span class="text-green-600"><i class="fas fa-check-circle"></i> Hai</span>' :
            '<span class="text-red-600"><i class="fas fa-times-circle"></i> Si Hai</span>';

        // Build pledges list HTML
        let pledgesListHtml = '';
        if (currentPledges.list && currentPledges.list.length > 0) {
            pledgesListHtml = currentPledges.list.map(pledge => {
                const statusBadge = pledge.status === 'Completed'
                    ? '<span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">Imekamilika</span>'
                    : pledge.status === 'Partial'
                    ? '<span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">Inaendelea</span>'
                    : '<span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded-full">Bado</span>';

                return `
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-900">${pledge.type}</span>
                            ${statusBadge}
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-xs mb-2">
                            <div>
                                <p class="text-gray-600">Ahadi</p>
                                <p class="font-semibold">TZS ${formatNumber(pledge.amount)}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Imelipwa</p>
                                <p class="font-semibold text-green-600">TZS ${formatNumber(pledge.amount_paid)}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Baki</p>
                                <p class="font-semibold text-orange-600">TZS ${formatNumber(pledge.remaining)}</p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: ${pledge.progress}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">${pledge.progress}% Imekamilika | Tarehe: ${pledge.pledge_date}</p>
                    </div>
                `;
            }).join('');
        } else {
            pledgesListHtml = '<p class="text-gray-500 text-center py-4">Hakuna ahadi zilizorekodiwa</p>';
        }

        const html = `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-600 mb-3">Taarifa za Kibinafsi</h4>
                        <div class="space-y-2">
                            <p><span class="font-medium">Namba:</span> ${currentMember.member_number}</p>
                            <p><span class="font-medium">Jina Kamili:</span> ${currentMember.full_name}</p>
                            <p><span class="font-medium">Jinsia:</span> ${genderText}</p>
                            <p><span class="font-medium">Umri:</span> ${currentMember.age || 'N/A'} miaka (${currentMember.age_group || 'N/A'})</p>
                            <p><span class="font-medium">Simu:</span> ${currentMember.phone || '-'}</p>
                            <p><span class="font-medium">Email:</span> ${currentMember.email || '-'}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-600 mb-3">Taarifa za Kanisa</h4>
                        <div class="space-y-2">
                            <p><span class="font-medium">Hali ya Ndoa:</span> ${currentMember.marital_status || '-'}</p>
                            <p><span class="font-medium">Kundi Maalum:</span> ${currentMember.special_group || '-'}</p>
                            <p><span class="font-medium">Hali:</span> ${statusText}</p>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <h4 class="text-sm font-semibold text-gray-600 mb-3">Jumla ya Michango</h4>
                        <p class="text-2xl font-bold text-primary-600">TZS ${currentMember.total_contributions}</p>
                    </div>
                </div>

                <!-- Detailed Pledge Information -->
                <div class="border-t pt-4">
                    <h4 class="text-sm font-semibold text-gray-600 mb-3 flex items-center">
                        <i class="fas fa-handshake text-primary-500 mr-2"></i>
                        Ahadi za Muumini
                    </h4>
                    <div class="space-y-3">
                        ${pledgesListHtml}
                    </div>
                </div>
            </div>
        `;

        document.getElementById('memberDetails').innerHTML = html;
        document.getElementById('memberDetailedInfo').classList.remove('hidden');
    }

    function hideDetailedInfo() {
        document.getElementById('memberDetailedInfo').classList.add('hidden');
    }

    function addContribution() {
        if (!currentMember) return;
        window.location.href = `/quick-entry/contribution/${currentMember.member_number}`;
    }

    function showError(message) {
        document.getElementById('errorText').textContent = message;
        document.getElementById('errorMessage').classList.remove('hidden');
    }

    function hideError() {
        document.getElementById('errorMessage').classList.add('hidden');
    }

    function restartScanner() {
        currentMember = null;
        document.getElementById('memberFoundSection').classList.add('hidden');
        document.getElementById('memberDetailedInfo').classList.add('hidden');
        document.getElementById('memberNumberInput').value = '';
        document.getElementById('scanStatus').innerHTML = '<i class="fas fa-camera mr-2"></i>Inasubiri kuscan...';
        hideError();
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
</body>
</html>
