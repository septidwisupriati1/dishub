<div class="hidden md:flex md:flex-col md:w-64 bg-gray-900 text-gray-100">
    <div class="flex-1 overflow-y-auto px-2 py-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="block px-4 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
            <i class="fas fa-chart-line mr-3"></i>
            <span>Dashboard</span>
        </a>

        @if(auth()->user()->isPeserta())
            <!-- Peserta Menu -->
            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kendaraan</p>
                <a href="{{ route('vehicles.index') }}" 
                   class="block px-4 py-2 rounded-lg {{ request()->routeIs('vehicles.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-car mr-3"></i>
                    <span>Kendaraan Saya</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ujian</p>
                <a href="{{ route('queues.index') }}" 
                   class="block px-4 py-2 rounded-lg {{ request()->routeIs('queues.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-list-ol mr-3"></i>
                    <span>Antrian Ujian</span>
                </a>
                <a href="{{ route('test-results.index') }}" 
                   class="block px-4 py-2 rounded-lg {{ request()->routeIs('test-results.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-check-square mr-3"></i>
                    <span>Hasil Ujian</span>
                </a>
            </div>
        @endif

        @if(auth()->user()->isPenguji())
            <!-- Penguji Menu -->
            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Ujian</p>
                <a href="{{ route('queues.index') }}" 
                   class="block px-4 py-2 rounded-lg {{ request()->routeIs('queues.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-list-ol mr-3"></i>
                    <span>Antrian</span>
                </a>
                <a href="{{ route('test-results.create') }}" 
                   class="block px-4 py-2 rounded-lg {{ request()->routeIs('test-results.create') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-clipboard-check mr-3"></i>
                    <span>Input Hasil Ujian</span>
                </a>
                <a href="{{ route('test-results.index') }}" 
                   class="block px-4 py-2 rounded-lg {{ request()->routeIs('test-results.index') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-chart-bar mr-3"></i>
                    <span>Statistik Saya</span>
                </a>
            </div>
        @endif

        @if(auth()->user()->isAdmin())
            <!-- Admin Menu -->
            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Manajemen</p>
                <a href="{{ route('test-schedules.index') }}" 
                   class="block px-4 py-2 rounded-lg {{ request()->routeIs('test-schedules.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-calendar mr-3"></i>
                    <span>Jadwal Ujian</span>
                </a>
                <a href="{{ route('users.index') }}" 
                   class="block px-4 py-2 rounded-lg {{ request()->routeIs('users.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-users mr-3"></i>
                    <span>User</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Laporan</p>
                <a href="{{ route('reports.daily') }}" 
                   class="block px-4 py-2 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-file-alt mr-3"></i>
                    <span>Laporan Harian</span>
                </a>
            </div>

            <div class="pt-4">
                <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Konfigurasi</p>
                <a href="{{ route('settings.whatsapp') }}" 
                   class="block px-4 py-2 rounded-lg {{ request()->routeIs('settings.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Pengaturan</span>
                </a>
            </div>
        @endif

        <!-- Bantuan -->
        <div class="pt-4 border-t border-gray-800 mt-4">
            <a href="{{ route('help') }}" 
               class="block px-4 py-2 rounded-lg {{ request()->routeIs('help') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800' }}">
                <i class="fas fa-question-circle mr-3"></i>
                <span>Bantuan</span>
            </a>
        </div>
    </div>
</div>