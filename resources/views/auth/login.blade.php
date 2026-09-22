@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 bg-[url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%231C4D8D\' fill-opacity=\'0.05\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'3\'/%3E%3Ccircle cx=\'13\' cy=\'13\' r=\'3\'/%3E%3C/g%3E%3C/svg%3E')]">
    <div class="max-w-4xl w-full mx-4 sm:mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row transform transition-all hover:shadow-3xl">
        
        <!-- Left Side: Branding & Info -->
        <div class="md:w-5/12 bg-gradient-to-br from-[#0F2854] to-[#1C4D8D] p-10 text-white flex flex-col justify-between relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-10 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 rounded-full bg-blue-300 opacity-20 blur-2xl pointer-events-none"></div>
            
            <div class="relative z-10 text-center md:text-left">
                <img src="{{ asset('images/logo-dishub.png') }}" alt="Logo Dishub" class="w-24 h-24 mb-6 object-contain mx-auto md:mx-0 drop-shadow-lg bg-white/10 p-2 rounded-xl backdrop-blur-sm">
                <h1 class="text-3xl font-bold mb-2 tracking-wide font-sans">KIR Antrean</h1>
                <p class="text-blue-100 text-sm font-medium mb-8">Sistem Informasi Manajemen Pengujian Kendaraan Bermotor Terpadu</p>
            </div>
            
            <div class="hidden md:block relative z-10">
                <div class="space-y-4 text-sm text-blue-100">
                    <div class="flex items-center gap-3 bg-white/10 p-3 rounded-lg backdrop-blur-sm border border-white/5 hover:bg-white/20 transition-colors">
                        <i class="fas fa-clock text-blue-300 text-xl w-6"></i>
                        <span>Pelayanan Cepat & Akurat</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/10 p-3 rounded-lg backdrop-blur-sm border border-white/5 hover:bg-white/20 transition-colors">
                        <i class="fas fa-mobile-alt text-blue-300 text-xl w-6"></i>
                        <span>Notifikasi WhatsApp Real-time</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/10 p-3 rounded-lg backdrop-blur-sm border border-white/5 hover:bg-white/20 transition-colors">
                        <i class="fas fa-certificate text-blue-300 text-xl w-6"></i>
                        <span>Sertifikat Digital Terintegrasi</span>
                    </div>
                </div>
                <div class="mt-8 text-xs text-blue-200 opacity-80 border-t border-white/10 pt-4">
                    &copy; {{ date('Y') }} Dinas Perhubungan. All rights reserved.
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="md:w-7/12 p-8 md:p-12 bg-white relative">
            <div class="text-center md:text-left mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2 font-sans">Selamat Datang 👋</h2>
                <p class="text-gray-500 text-sm">Silakan masuk ke akun Anda untuk melanjutkan.</p>
            </div>

            <!-- Session Messages -->
            @if($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 p-4 border-l-4 border-red-500 animate-pulse">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3 text-lg"></i>
                        <div class="text-sm text-red-700">
                            <strong class="block mb-1 font-bold">Login Gagal!</strong>
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 rounded-xl bg-green-50 p-4 border-l-4 border-green-500">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                        <span class="text-sm text-green-700 font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div class="group">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors group-focus-within:text-blue-600">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required autofocus
                            class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-800 shadow-sm @error('email') border-red-300 ring-red-100 @enderror"
                            placeholder="nama@email.com"
                            value="{{ old('email') }}">
                    </div>
                </div>

                <!-- Password Input -->
                <div class="group">
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors group-focus-within:text-blue-600">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-800 shadow-sm @error('password') border-red-300 ring-red-100 @enderror"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between pt-2">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" value="1"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer transition-colors">
                        <label for="remember" class="ml-2 block text-sm text-gray-600 cursor-pointer select-none hover:text-gray-900 transition-colors">
                            Ingat saya
                        </label>
                    </div>
                    <!-- Placeholder Lupa Password -->
                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-[#0F2854] transition-colors">Lupa sandi?</a>
                </div>

                <!-- Login Button -->
                <div class="pt-2">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-[#0F2854] hover:from-blue-700 hover:to-blue-900 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-blue-500/30 transform transition-all hover:-translate-y-0.5 active:translate-y-0">
                        Masuk Sekarang <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-sm text-gray-600">
                Belum memiliki akun? 
                <a href="{{ route('register') }}" class="font-bold text-blue-600 hover:text-[#0F2854] transition-colors hover:underline underline-offset-4">Daftar sebagai Peserta</a>
            </div>
        </div>
    </div>
</div>
@endsection