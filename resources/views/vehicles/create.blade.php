@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ route('vehicles.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Tambah Kendaraan Baru</h1>
        <p class="mt-1 text-sm text-gray-500">Masukkan informasi kendaraan Anda dengan lengkap</p>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form method="POST" action="{{ route('vehicles.store') }}" class="divide-y divide-gray-200">
            @csrf

            <!-- Section 1: Informasi Dasar -->
            <div class="p-6 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-car text-blue-600 mr-2"></i>Informasi Dasar
                </h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Nomor Kendaraan -->
                    <div>
                        <label for="vehicle_number" class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Kendaraan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="vehicle_number" id="vehicle_number" 
                            value="{{ old('vehicle_number') }}"
                            placeholder="Cth: AB-1234-XYZ" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('vehicle_number') border-red-500 @enderror"
                            required>
                        @error('vehicle_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipe Kendaraan -->
                    <div>
                        <label for="vehicle_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipe Kendaraan <span class="text-red-500">*</span>
                        </label>
                        <select name="vehicle_type" id="vehicle_type" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('vehicle_type') border-red-500 @enderror"
                            required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="mobil" {{ old('vehicle_type') === 'mobil' ? 'selected' : '' }}>Mobil</option>
                            <option value="motor" {{ old('vehicle_type') === 'motor' ? 'selected' : '' }}>Motor</option>
                            <option value="bus" {{ old('vehicle_type') === 'bus' ? 'selected' : '' }}>Bus</option>
                            <option value="truck" {{ old('vehicle_type') === 'truck' ? 'selected' : '' }}>Truk</option>
                        </select>
                        @error('vehicle_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Merek -->
                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">
                            Merek <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="brand" id="brand" 
                            value="{{ old('brand') }}"
                            placeholder="Cth: Toyota, Honda, Daihatsu" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('brand') border-red-500 @enderror"
                            required>
                        @error('brand')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Model -->
                    <div>
                        <label for="model" class="block text-sm font-medium text-gray-700 mb-1">
                            Model <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="model" id="model" 
                            value="{{ old('model') }}"
                            placeholder="Cth: Avanza, CR-V, Ertiga" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('model') border-red-500 @enderror"
                            required>
                        @error('model')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tahun -->
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">
                            Tahun <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="year" id="year" 
                            value="{{ old('year', date('Y')) }}"
                            min="1950" 
                            max="{{ date('Y') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('year') border-red-500 @enderror"
                            required>
                        @error('year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Warna -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-1">
                            Warna <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="color" id="color" 
                            value="{{ old('color') }}"
                            placeholder="Cth: Merah, Hitam, Putih" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('color') border-red-500 @enderror"
                            required>
                        @error('color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Nomor Identitas -->
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-fingerprint text-blue-600 mr-2"></i>Nomor Identitas
                </h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Nomor Mesin -->
                    <div>
                        <label for="engine_number" class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Mesin <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="engine_number" id="engine_number" 
                            value="{{ old('engine_number') }}"
                            placeholder="Nomor mesin kendaraan" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('engine_number') border-red-500 @enderror"
                            required>
                        @error('engine_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nomor Rangka -->
                    <div>
                        <label for="chassis_number" class="block text-sm font-medium text-gray-700 mb-1">
                            Nomor Rangka <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="chassis_number" id="chassis_number" 
                            value="{{ old('chassis_number') }}"
                            placeholder="Nomor rangka/chassis kendaraan" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('chassis_number') border-red-500 @enderror"
                            required>
                        @error('chassis_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 3: Status -->
            <div class="p-6 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-clipboard-check text-blue-600 mr-2"></i>Status Kendaraan
                </h2>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror"
                            required>
                            <option value="">-- Pilih Status --</option>
                            <option value="layak" {{ old('status') === 'layak' ? 'selected' : '' }}>Layak</option>
                            <option value="tidak_layak" {{ old('status') === 'tidak_layak' ? 'selected' : '' }}>Tidak Layak</option>
                            <option value="dalam_perbaikan" {{ old('status') === 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-md">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        Status kendaraan akan diperbarui setelah dilakukan pengujian oleh penguji.
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                <a href="{{ route('vehicles.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 font-medium hover:bg-gray-50 transition">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-save mr-2"></i>Simpan Kendaraan
                </button>
            </div>
        </form>
    </div>

    <!-- Info Box -->
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex gap-3">
            <i class="fas fa-lightbulb text-yellow-600 mt-1"></i>
            <div>
                <h3 class="font-semibold text-yellow-900">Tips:</h3>
                <ul class="text-sm text-yellow-800 mt-2 list-disc list-inside gap-2">
                    <li>Pastikan semua data yang Anda masukkan sesuai dengan dokumen kendaraan</li>
                    <li>Nomor mesin dan rangka harus match dengan STNK</li>
                    <li>Setelah mendaftar, kendaraan Anda akan siap untuk dilakukan pengujian</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
