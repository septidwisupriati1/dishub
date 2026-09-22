@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 bg-[url('data:image/svg+xml,%3Csvg width=\'20\' height=\'20\' viewBox=\'0 0 20 20\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%231C4D8D\' fill-opacity=\'0.05\' fill-rule=\'evenodd\'%3E%3Ccircle cx=\'3\' cy=\'3\' r=\'3\'/%3E%3Ccircle cx=\'13\' cy=\'13\' r=\'3\'/%3E%3C/g%3E%3C/svg%3E')]">
    <div class="max-w-5xl w-full mx-4 sm:mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col-reverse md:flex-row transform transition-all hover:shadow-3xl my-8">
        
        <!-- Left Side: Register Form -->
        <div class="md:w-7/12 p-8 md:p-12 bg-white relative">
            <div class="text-center md:text-left mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2 font-sans">Buat Akun Baru ✨</h2>
                <p class="text-gray-500 text-sm">Daftar sebagai peserta untuk mulai mengambil antrean uji kendaraan.</p>
            </div>

            <!-- Session Messages -->
            @if($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 p-4 border-l-4 border-red-500 animate-pulse">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-circle text-red-500 mt-0.5 mr-3 text-lg"></i>
                        <div class="text-sm text-red-700">
                            <strong class="block mb-1 font-bold">Pendaftaran Gagal!</strong>
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name Input -->
                <div class="group">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors group-focus-within:text-blue-600">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input id="name" name="name" type="text" required autofocus
                            class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-800 shadow-sm @error('name') border-red-300 ring-red-100 @enderror"
                            placeholder="Contoh: Budi Santoso"
                            value="{{ old('name') }}">
                    </div>
                </div>

                <!-- Email Input -->
                <div class="group">
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors group-focus-within:text-blue-600">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-800 shadow-sm @error('email') border-red-300 ring-red-100 @enderror"
                            placeholder="nama@email.com"
                            value="{{ old('email') }}">
                    </div>
                </div>

                <!-- Phone Input -->
                <div class="group">
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors group-focus-within:text-blue-600">Nomor Telepon / WhatsApp</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-phone-alt text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                        </div>
                        <input id="phone" name="phone" type="text" required
                            class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-800 shadow-sm @error('phone') border-red-300 ring-red-100 @enderror"
                            placeholder="Contoh: 081234567890"
                            value="{{ old('phone') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Password Input -->
                    <div class="group">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors group-focus-within:text-blue-600">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                            </div>
                            <input id="password" name="password" type="password" required autocomplete="new-password"
                                class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-800 shadow-sm @error('password') border-red-300 ring-red-100 @enderror"
                                placeholder="Min. 8 karakter">
                        </div>
                    </div>

                    <!-- Confirm Password Input -->
                    <div class="group">
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1 transition-colors group-focus-within:text-blue-600">Konfirmasi Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-check-double text-gray-400 group-focus-within:text-blue-500 transition-colors"></i>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                class="block w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-gray-800 shadow-sm"
                                placeholder="Ulangi password">
                        </div>
                    </div>
                </div>

                <!-- Terms -->
                <div class="flex items-start pt-2">
                    <div class="flex items-center h-5">
                        <input id="terms" name="terms" type="checkbox" required
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer mt-0.5 transition-colors">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="terms" class="text-gray-600 cursor-pointer hover:text-gray-900 transition-colors">
                            Saya menyetujui <a href="#" class="text-blue-600 font-medium hover:text-[#0F2854] hover:underline">Syarat, Ketentuan</a>, dan <a href="#" class="text-blue-600 font-medium hover:text-[#0F2854] hover:underline">Kebijakan Privasi</a>.
                        </label>
                    </div>
                </div>

                <!-- Register Button -->
                <div class="pt-4">
                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-[#0F2854] hover:from-blue-700 hover:to-blue-900 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-blue-500/30 transform transition-all hover:-translate-y-0.5 active:translate-y-0">
                        Daftar Akun Sekarang <i class="fas fa-user-plus"></i>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-sm text-gray-600">
                Sudah memiliki akun? 
                <a href="{{ route('login') }}" class="font-bold text-blue-600 hover:text-[#0F2854] transition-colors hover:underline underline-offset-4">Masuk di sini</a>
            </div>
        </div>

        <!-- Right Side: Branding & Info (Mirrored) -->
        <div class="md:w-5/12 bg-gradient-to-tl from-[#0F2854] to-[#1C4D8D] p-10 text-white flex flex-col justify-between relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute bottom-0 right-0 -mr-16 -mb-16 w-64 h-64 rounded-full bg-white opacity-10 blur-3xl pointer-events-none"></div>
            <div class="absolute top-0 left-0 -ml-16 -mt-16 w-48 h-48 rounded-full bg-blue-300 opacity-20 blur-2xl pointer-events-none"></div>
            
            <div class="relative z-10 text-center md:text-right">
                <img src="{{ asset('images/logo-dishub.png') }}" alt="Logo Dishub" class="w-24 h-24 mb-6 object-contain mx-auto md:mr-0 md:ml-auto drop-shadow-lg bg-white/10 p-2 rounded-xl backdrop-blur-sm">
                <h1 class="text-3xl font-bold mb-2 tracking-wide font-sans">Satu Akun,</h1>
                <h2 class="text-2xl font-bold mb-4 tracking-wide font-sans text-blue-200">Beragam Kemudahan.</h2>
                <p class="text-blue-100 text-sm font-medium mb-8">Pendaftaran online untuk layanan uji emisi dan kelayakan kendaraan bermotor yang lebih transparan dan efisien.</p>
            </div>
            
            <div class="hidden md:block relative z-10">
                <div class="space-y-4 text-sm text-blue-100">
                    <div class="flex items-center justify-end gap-3 bg-white/10 p-3 rounded-lg backdrop-blur-sm border border-white/5 hover:bg-white/20 transition-colors text-right">
                        <span>Kelola Multi Kendaraan</span>
                        <i class="fas fa-car-side text-blue-300 text-xl w-6"></i>
                    </div>
                    <div class="flex items-center justify-end gap-3 bg-white/10 p-3 rounded-lg backdrop-blur-sm border border-white/5 hover:bg-white/20 transition-colors text-right">
                        <span>Pilih Jadwal Fleksibel</span>
                        <i class="fas fa-calendar-check text-blue-300 text-xl w-6"></i>
                    </div>
                    <div class="flex items-center justify-end gap-3 bg-white/10 p-3 rounded-lg backdrop-blur-sm border border-white/5 hover:bg-white/20 transition-colors text-right">
                        <span>Lacak Riwayat Pengujian</span>
                        <i class="fas fa-history text-blue-300 text-xl w-6"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection