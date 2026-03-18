<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ongeza Mchango - Quick Entry</title>
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="text-white shadow-lg" style="background: linear-gradient(135deg, #360958 0%, #2a0745 100%);">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-plus-circle text-2xl mr-3 text-secondary-500"></i>
                <div>
                    <h1 class="text-xl font-bold">Ongeza Mchango</h1>
                    <p class="text-sm text-primary-100">{{ Auth::user()->name }}</p>
                </div>
            </div>
            <a href="{{ route('quick-entry.scanner') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Rudi
            </a>
        </div>
    </header>

    <div class="max-w-3xl mx-auto px-4 py-6">
        <!-- Member Info Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user text-primary-500 mr-2"></i>
                Taarifa za Muumini
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-primary-50 border border-primary-200 rounded-lg p-4">
                <div>
                    <p class="text-sm text-gray-600">Namba ya Muumini</p>
                    <p class="text-lg font-bold text-primary-700">{{ $member->member_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Jina Kamili</p>
                    <p class="text-lg font-bold text-gray-800">
                        {{ $member->first_name }} {{ $member->middle_name }} {{ $member->last_name }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Contribution Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                Ingiza Taarifa za Mchango
            </h3>

            <!-- Success Message -->
            <div id="successMessage" class="hidden bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-lg mb-4">
                <div class="flex items-start">
                    <i class="fas fa-check-circle mt-1 mr-2"></i>
                    <p id="successText"></p>
                </div>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="hidden bg-red-50 border-l-4 border-red-400 text-red-700 p-4 rounded-lg mb-4">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-circle mt-1 mr-2"></i>
                    <div id="errorText"></div>
                </div>
            </div>

            <form id="contributionForm" onsubmit="submitContribution(event)">
                @csrf

                <!-- Income Category -->
                <div class="mb-6">
                    <label for="income_category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-list mr-2 text-primary-500"></i>Aina ya Mchango *
                    </label>
                    <select name="income_category_id" id="income_category_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Chagua Aina ya Mchango --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->code }} - {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount -->
                <div class="mb-6">
                    <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-dollar-sign mr-2 text-primary-500"></i>Kiasi (TZS) *
                    </label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                           placeholder="Ingiza kiasi">
                </div>

                <!-- Collection Date -->
                <div class="mb-6">
                    <label for="collection_date" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2 text-primary-500"></i>Tarehe ya Kukusanya *
                    </label>
                    <input type="date" name="collection_date" id="collection_date" required
                           value="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sticky-note mr-2 text-primary-500"></i>Maelezo (Si Lazima)
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                              placeholder="Ongeza maelezo yoyote..."></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-4 border-t">
                    <button type="submit" id="submitBtn"
                            class="flex-1 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, #efc120 0%, #d4a81c 100%);">
                        <i class="fas fa-save mr-2"></i>
                        Hifadhi Mchango
                    </button>
                    <a href="{{ route('quick-entry.scanner') }}"
                       class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center shadow-lg">
                        <i class="fas fa-times mr-2"></i>
                        Ghairi
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
    function submitContribution(event) {
        event.preventDefault();

        const form = document.getElementById('contributionForm');
        const formData = new FormData(form);
        const submitBtn = document.getElementById('submitBtn');

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Inahifadhi...';

        // Hide previous messages
        document.getElementById('successMessage').classList.add('hidden');
        document.getElementById('errorMessage').classList.add('hidden');

        // Convert FormData to JSON
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        // Send request
        fetch(`/quick-entry/contribution/{{ $member->member_number }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                               document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                document.getElementById('successText').textContent = data.message;
                document.getElementById('successMessage').classList.remove('hidden');

                // Reset form
                form.reset();

                // Redirect to scanner after 1.5 seconds
                setTimeout(() => {
                    window.location.href = '{{ route('quick-entry.scanner') }}';
                }, 1500);
            } else {
                throw new Error(data.message || 'Hitilafu imetokea');
            }
        })
        .catch(error => {
            console.error('Error:', error);

            // Show error message
            const errorDiv = document.getElementById('errorText');
            if (error.message) {
                errorDiv.innerHTML = '<p>' + error.message + '</p>';
            } else {
                errorDiv.innerHTML = '<p>Hitilafu imetokea. Tafadhali jaribu tena.</p>';
            }
            document.getElementById('errorMessage').classList.remove('hidden');

            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save mr-2"></i>Hifadhi Mchango';
        });
    }
    </script>
</body>
</html>
