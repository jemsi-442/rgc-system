<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weka Nenosiri Jipya - RGC</title>
    <link rel="icon" type="image/png" href="{{ asset('images/rgc_logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#c00000',
                            600: '#a40000',
                            700: '#7f0000',
                            800: '#5e0000',
                            900: '#3f0000',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .btn-primary {
            background: #c00000;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background: #8f1111;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(192, 0, 0, 0.24);
        }
        .input-focus:focus {
            border-color: #c00000;
            box-shadow: 0 0 0 3px rgba(192, 0, 0, 0.14);
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-8">
    <div class="w-full max-w-md mx-auto px-4">
        <!-- Logo and Title -->
        <div class="text-center mb-6">
            <div class="flex justify-center items-center mb-4">
                <img src="{{ asset('images/rgc_logo.png') }}" alt="RGC Logo" class="w-24 h-24 object-contain">
            </div>
            <span class="text-2xl font-bold text-primary-600 block">RGC</span>
            <h1 class="text-xl font-bold text-gray-800 mt-2">Weka Nenosiri Jipya</h1>
            <p class="text-gray-600 text-sm mt-1">Ingiza nenosiri lako jipya hapa chini</p>
        </div>

        <!-- Display Errors -->
        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4">
            <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span class="font-semibold">Kuna makosa yafuatayo:</span>
            </div>
            <ul class="list-disc list-inside ml-4 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
        @endif

        <!-- Reset Password Form -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-5">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-key text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-white">Badilisha Nenosiri</h2>
                        <p class="text-white/80 text-sm">Tengeneza nenosiri jipya salama</p>
                    </div>
                </div>
            </div>

            <!-- Form Body -->
            <form action="{{ route('password.update') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Barua Pepe
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" name="email" value="{{ $email ?? old('email') }}" required readonly
                               class="pl-10 input-focus w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none bg-gray-50 text-gray-600"
                               placeholder="barua@pepe.com">
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Nenosiri Jipya <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                               class="pl-10 pr-10 input-focus w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                               placeholder="Ingiza nenosiri jipya">
                        <button type="button" onclick="togglePassword('password', 'toggleIcon1')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i id="toggleIcon1" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle"></i> Nenosiri lazima liwe na herufi 6 au zaidi
                    </p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Thibitisha Nenosiri <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="pl-10 pr-10 input-focus w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none"
                               placeholder="Rudia nenosiri jipya">
                        <button type="button" onclick="togglePassword('password_confirmation', 'toggleIcon2')" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i id="toggleIcon2" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-primary w-full text-white py-3 rounded-lg font-medium mt-4 flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Hifadhi Nenosiri Jipya</span>
                </button>
            </form>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="text-center text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="text-primary-600 hover:text-primary-700 font-medium">
                        <i class="fas fa-arrow-left mr-1"></i> Rudi kwenye ukurasa wa kuingia
                    </a>
                </div>
            </div>
        </div>

        <!-- Security Notice -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start">
                <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 shrink-0">
                    <i class="fas fa-shield-alt text-blue-600"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 text-sm mb-1">Usalama wa Nenosiri</h4>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li><i class="fas fa-check text-green-500 mr-1"></i> Tumia herufi kubwa na ndogo</li>
                        <li><i class="fas fa-check text-green-500 mr-1"></i> Ongeza namba na alama maalum</li>
                        <li><i class="fas fa-check text-green-500 mr-1"></i> Usitumie taarifa za kibinafsi</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
