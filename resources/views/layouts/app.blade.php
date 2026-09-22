<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KIR Antrean') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            blue: {
                                50: '#f0f5fa',
                                100: '#e1ebf4',
                                200: '#c5d8e9',
                                300: '#9abcda',
                                400: '#699cc8',
                                500: '#4680b3',
                                600: '#1C4D8D', // Primary theme color
                                700: '#27528e',
                                800: '#0F2854', // Dark navbar color
                                900: '#213a60',
                            }
                        }
                    }
                }
            }
        </script>

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Axios for API calls -->
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

        <!-- Notiflix for Modern Popups -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/src/notiflix.min.css" />
        <script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-aio-3.2.6.min.js"></script>

        <style>
            body {
                font-family: 'Roboto Slab', serif;
            }
        </style>

        @yield('styles')
    </head>
    <body class="bg-white">
        <div id="app">
            @if(auth()->check())
                <!-- Authenticated Layout -->
                @include('layouts.navbar')
                
                <div class="flex min-h-screen bg-gray-50">
                    @include('layouts.sidebar')
                    
                    <main class="flex-1">
                        <div class="py-6 px-4 sm:px-6 md:px-8">
                            @if(session('success'))
                                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center">
                                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                                    <span class="text-green-800">{{ session('success') }}</span>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center">
                                    <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                                    <span class="text-red-800">{{ session('error') }}</span>
                                </div>
                            @endif

                            @yield('content')
                        </div>
                    </main>
                </div>
            @else
                <!-- Guest Layout -->
                @yield('content')
            @endif
        </div>

        @yield('scripts')
    </body>
</html>