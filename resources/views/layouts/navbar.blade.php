<nav class="shadow-md" style="background: linear-gradient(to right, #0F2854, #1C4D8D);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <img src="{{ asset('images/logo-dishub.png') }}" alt="Logo Dishub" class="w-10 h-10 mr-3 object-contain">
                    <span class="text-xl font-bold text-white tracking-wide">KIR Antrean</span>
                </a>
            </div>

            <!-- Global Search -->
            <div class="hidden sm:block flex-1 max-w-md ml-8">
                <form action="{{ route('queues.index') }}" method="GET" class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-300"></i>
                    </div>
                    <input type="text" name="search" placeholder="Cari antrean (Plat/Nama/No. Antrean)..." value="{{ request('search') }}"
                           class="block w-full pl-10 pr-3 py-2 border border-white/20 rounded-full leading-5 bg-white/10 text-white placeholder-gray-300 focus:outline-none focus:bg-white focus:text-gray-900 focus:placeholder-gray-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors duration-200">
                </form>
            </div>

            <!-- Right side -->
            <div class="flex items-center space-x-4 ml-auto">
                <!-- Notifications -->
                <div class="relative group">
                    <button class="text-gray-200 hover:text-white transition-colors">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-[10px] font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full border-2 border-[#1C4D8D]">3</span>
                    </button>
                </div>

                <!-- User Dropdown -->
                <div class="relative group ml-4">
                    <button class="flex items-center text-gray-200 hover:text-white transition-colors">
                        <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=EBF4FF&color=0F2854&bold=true" 
                             alt="{{ auth()->user()->name }}" 
                             class="w-9 h-9 rounded-full mr-2 border-2 border-white/20">
                        <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                        <i class="fas fa-chevron-down text-xs ml-2 opacity-80"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 w-48 mt-2 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-600">{{ auth()->user()->email }}</p>
                            <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold text-white bg-blue-600 rounded-full capitalize">
                                {{ auth()->user()->role }}
                            </span>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-user mr-2"></i>Profile
                        </a>
                        <a href="{{ route('profile.change-password') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-lock mr-2"></i>Ubah Password
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>