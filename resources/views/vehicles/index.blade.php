@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manajemen Kendaraan</h1>
            <p class="mt-1 text-sm text-gray-500">
                @if(auth()->user()->isAdmin())
                    Kelola semua kendaraan dalam sistem
                @elseif(auth()->user()->isPenguji())
                    Daftar kendaraan yang siap untuk pengujian
                @else
                    Kelola kendaraan Anda
                @endif
            </p>
        </div>

        @if(auth()->user()->isPeserta() || auth()->user()->isAdmin())
            <div class="mt-4 md:mt-0">
                <a href="{{ route('vehicles.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Kendaraan
                </a>
            </div>
        @endif
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6 p-4">
        <form method="GET" class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Nomor Kendaraan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cth: AB-1234-XYZ" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Vehicle Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Kendaraan</label>
                <select name="vehicle_type" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Tipe</option>
                    <option value="mobil" {{ request('vehicle_type') === 'mobil' ? 'selected' : '' }}>Mobil</option>
                    <option value="motor" {{ request('vehicle_type') === 'motor' ? 'selected' : '' }}>Motor</option>
                    <option value="bus" {{ request('vehicle_type') === 'bus' ? 'selected' : '' }}>Bus</option>
                    <option value="truck" {{ request('vehicle_type') === 'truck' ? 'selected' : '' }}>Truk</option>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Layak</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Layak</option>
                    <option value="under_maintenance" {{ request('status') === 'under_maintenance' ? 'selected' : '' }}>Dalam Perbaikan</option>
                </select>
            </div>

            <!-- Submit -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-1"></i> Cari
                </button>
                <a href="{{ route('vehicles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition">
                    <i class="fas fa-redo mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Kendaraan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merek / Model</th>
                        @if(auth()->user()->isAdmin())
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemilik</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ujian Terakhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($vehicles as $vehicle)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-gray-900">{{ $vehicle->vehicle_number }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($vehicle->vehicle_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $vehicle->brand }} {{ $vehicle->model }} ({{ $vehicle->year }})
                            </td>
                            @if(auth()->user()->isAdmin())
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $vehicle->user->name }}
                                </td>
                            @endif
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($vehicle->status === 'active')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Layak
                                    </span>
                                @elseif($vehicle->status === 'inactive')
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i> Tidak Layak
                                    </span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-wrench mr-1"></i> Dalam Perbaikan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($vehicle->last_test_date)
                                    <span>{{ $vehicle->last_test_date->format('d M Y') }}</span>
                                    <div class="text-xs text-gray-500">{{ $vehicle->test_count }} ujian</div>
                                @else
                                    <span class="text-gray-400">Belum diuji</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    <!-- View Detail -->
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded transition text-xs font-medium">
                                        <i class="fas fa-eye mr-1"></i> Lihat
                                    </a>

                                    <!-- Edit (Peserta/Admin) -->
                                    @if(auth()->user()->isAdmin() || (auth()->user()->isPeserta() && $vehicle->user_id === auth()->id()))
                                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center px-3 py-1 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 rounded transition text-xs font-medium">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                    @endif

                                    <!-- Delete (Peserta/Admin) -->
                                    @if(auth()->user()->isAdmin() || (auth()->user()->isPeserta() && $vehicle->user_id === auth()->id()))
                                        <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus kendaraan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-50 text-red-600 hover:bg-red-100 rounded transition text-xs font-medium">
                                                <i class="fas fa-trash mr-1"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500 font-medium">Tidak ada kendaraan</p>
                                    @if(auth()->user()->isPeserta() || auth()->user()->isAdmin())
                                        <p class="text-gray-400 text-sm mt-1">Mulai dengan <a href="{{ route('vehicles.create') }}" class="text-blue-600 hover:underline">menambah kendaraan baru</a></p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($vehicles->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $vehicles->links() }}
            </div>
        @endif
    </div>

    <!-- Statistics (for Admin and Penguji) -->
    @if(auth()->user()->isAdmin() || auth()->user()->isPenguji())
        <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-4">
            @php
                $totalVehicles = auth()->user()->isAdmin() ? \App\Models\Vehicle::count() : \App\Models\Vehicle::where('status', 'active')->count();
                $passedCount = \App\Models\Vehicle::where('status', 'active')->count();
                $failedCount = \App\Models\Vehicle::where('status', 'inactive')->count();
                $underRepairCount = \App\Models\Vehicle::where('status', 'under_maintenance')->count();
            @endphp
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total Kendaraan</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalVehicles }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-car text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Layak Uji</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $passedCount }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Tidak Layak</p>
                        <p class="text-3xl font-bold text-red-600 mt-2">{{ $failedCount }}</p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Dalam Perbaikan</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $underRepairCount }}</p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-wrench text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
