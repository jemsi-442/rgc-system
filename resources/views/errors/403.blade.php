@auth
@extends('layouts.app')

@section('title', '403 - Huna Ruhusa')
@section('page-title', 'Huna Ruhusa')
@section('page-subtitle', 'Hauruhusiwi kuona ukurasa huu')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="max-w-lg w-full text-center">
        <!-- Error Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8 md:p-12">
            <!-- 403 Number -->
            <div class="relative mb-6">
                <span class="text-9xl font-bold text-yellow-100">403</span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="h-20 w-20 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-lock text-4xl text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                Huna Ruhusa
            </h1>
            <p class="text-gray-600 mb-8">
                Samahani, hauruhusiwi kufikia ukurasa huu. Tafadhali wasiliana na msimamizi kama unahitaji ufikiaji.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-home mr-2"></i>
                    Rudi Dashboard
                </a>
                <button onclick="history.back()" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Rudi Nyuma
                </button>
            </div>
        </div>

        <!-- Help Text -->
        <p class="mt-6 text-sm text-gray-500">
            <i class="fas fa-info-circle mr-1"></i>
            Kama unahitaji ufikiaji, tafadhali wasiliana na msimamizi wa mfumo
        </p>
    </div>
</div>
@endsection

@else
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Huna Ruhusa</title>
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
                            50: '#fff8df',
                            100: '#ffe9a8',
                            500: '#c00000',
                            600: '#8f1111',
                            700: '#6f0d0d',
                        },
                        secondary: {
                            500: '#ffd700',
                            600: '#d4aa00',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full text-center">
        <!-- Logo -->
        <div class="mb-8">
            <img src="{{ asset('images/rgc_logo.png') }}" alt="RGC Logo" class="w-24 h-24 mx-auto object-contain">
        </div>

        <!-- Error Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
            <!-- 403 Number -->
            <div class="relative mb-6">
                <span class="text-9xl font-bold text-yellow-100">403</span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-lock text-5xl text-yellow-500"></i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                Huna Ruhusa
            </h1>
            <p class="text-gray-600 mb-8">
                Samahani, hauruhusiwi kufikia ukurasa huu. Tafadhali ingia kwanza au wasiliana na msimamizi.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Ingia
                </a>
                <button onclick="history.back()" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition-all duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Rudi Nyuma
                </button>
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-sm text-gray-500">
            <i class="fas fa-church mr-1"></i> RGC - Mfumo wa Kanisa
        </p>
    </div>
</body>
</html>
@endauth
