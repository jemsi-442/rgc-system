@auth
@extends('layouts.app')

@section('title', '500 - Hitilafu ya Seva')
@section('page-title', 'Hitilafu ya Seva')
@section('page-subtitle', 'Kuna tatizo la kiufundi')

@section('content')
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="max-w-lg w-full text-center">
        <!-- Error Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8 md:p-12">
            <!-- 500 Number -->
            <div class="relative mb-6">
                <span class="text-9xl font-bold text-red-100">500</span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="h-20 w-20 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-4xl text-red-600"></i>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                Hitilafu ya Seva
            </h1>
            <p class="text-gray-600 mb-8">
                Samahani, kuna tatizo la kiufundi. Timu yetu inafanya kazi kurekebisha tatizo hili. Tafadhali jaribu tena baadaye.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-home mr-2"></i>
                    Rudi Dashboard
                </a>
                <button onclick="location.reload()" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition-all duration-200">
                    <i class="fas fa-redo mr-2"></i>
                    Jaribu Tena
                </button>
            </div>
        </div>

        <!-- Help Text -->
        <p class="mt-6 text-sm text-gray-500">
            <i class="fas fa-info-circle mr-1"></i>
            Kama tatizo linaendelea, tafadhali wasiliana na msimamizi wa mfumo
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
    <title>500 - Hitilafu ya Seva</title>
    <link rel="icon" type="image/png" href="{{ asset('images/rgc_logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
            <!-- 500 Number -->
            <div class="relative mb-6">
                <span class="text-9xl font-bold text-red-100">500</span>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-5xl text-red-500"></i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                Hitilafu ya Seva
            </h1>
            <p class="text-gray-600 mb-8">
                Samahani, kuna tatizo la kiufundi. Timu yetu inafanya kazi kurekebisha tatizo hili. Tafadhali jaribu tena baadaye.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition-all duration-200 shadow-md hover:shadow-lg">
                    <i class="fas fa-home mr-2"></i>
                    Rudi Nyumbani
                </a>
                <button onclick="location.reload()" class="inline-flex items-center justify-center px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-300 transition-all duration-200">
                    <i class="fas fa-redo mr-2"></i>
                    Jaribu Tena
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
