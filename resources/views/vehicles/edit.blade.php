@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-2">
            <a href="{{ route('vehicles.show', $vehicle) }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left"></i> Kembali ke Detail
            </a>
        </div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Kendaraan</h1>
        <p class="mt-1 text-sm text-gray-500">Perbarui data kendaraan dengan hati-hati</p>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form method="POST" action="{{ route('web.vehicles.update', $vehicle) }}" class="divide-y divide-gray-200">
            @csrf
            @method('PUT')

            <div class="p-6 bg-gray-50">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Kendaraan</label>
                        <input type="text" value="{{ $vehicle->vehicle_number }}" disabled class="w-full px-4 py-2 border border-gray-200 rounded-md bg-gray-100 text-gray-600">
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror" required>
                            <option value="active" {{ old('status', $vehicle->status) === 'active' ? 'selected' : '' }}>Layak</option>
                            <option value="inactive" {{ old('status', $vehicle->status) === 'inactive' ? 'selected' : '' }}>Tidak Layak</option>
                            <option value="under_maintenance" {{ old('status', $vehicle->status) === 'under_maintenance' ? 'selected' : '' }}>Dalam Perbaikan</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Merek</label>
                        <input type="text" name="brand" id="brand" value="{{ old('brand', $vehicle->brand) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('brand') border-red-500 @enderror" required>
                        @error('brand')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="model" class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                        <input type="text" name="model" id="model" value="{{ old('model', $vehicle->model) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('model') border-red-500 @enderror" required>
                        @error('model')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <input type="number" name="year" id="year" value="{{ old('year', $vehicle->year) }}" min="1950" max="{{ date('Y') }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('year') border-red-500 @enderror" required>
                        @error('year')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                        <input type="text" name="color" id="color" value="{{ old('color', $vehicle->color) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('color') border-red-500 @enderror" required>
                        @error('color')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="engine_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Mesin</label>
                        <input type="text" name="engine_number" id="engine_number" value="{{ old('engine_number', $vehicle->engine_number) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('engine_number') border-red-500 @enderror" required>
                        @error('engine_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="chassis_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Rangka</label>
                        <input type="text" name="chassis_number" id="chassis_number" value="{{ old('chassis_number', $vehicle->chassis_number) }}" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('chassis_number') border-red-500 @enderror" required>
                        @error('chassis_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-3">
                <a href="{{ route('vehicles.show', $vehicle) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 font-medium hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="px-6 py-2 bg-yellow-600 text-white font-medium rounded-md hover:bg-yellow-700 transition">Perbarui Kendaraan</button>
            </div>
        </form>
    </div>
</div>
@endsection
