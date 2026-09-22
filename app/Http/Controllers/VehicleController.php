<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    /**
     * Tampilkan semua kendaraan milik user
     */
    public function index(Request $request)
    {
        try {
            $query = Vehicle::with('testResults');

            // Filter untuk peserta hanya kendaraan miliknya
            if (auth()->user()->isPeserta()) {
                $query->where('user_id', auth()->id());
            }

            // Filter by status
            if ($request->status) {
                $query->where('status', $request->status);
            }

            // Filter by type
            if ($request->vehicle_type) {
                $query->where('vehicle_type', $request->vehicle_type);
            }

            // Search by vehicle number
            if ($request->search) {
                $query->where('vehicle_number', 'like', '%' . $request->search . '%');
            }

            $vehicles = $query->paginate($request->per_page ?? 10);

            return response()->json([
                'message' => 'Daftar kendaraan berhasil diambil',
                'data' => $vehicles,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Simpan kendaraan baru
     */
    public function store(StoreVehicleRequest $request)
    {
        try {
            // Authorize
            $this->authorize('create', Vehicle::class);

            $vehicle = Vehicle::create($request->validated());

            return response()->json([
                'message' => 'Kendaraan berhasil ditambahkan',
                'data' => $vehicle,
            ], 201);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'message' => 'Anda tidak berhak menambahkan kendaraan',
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Tambah kendaraan gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tampilkan detail kendaraan
     */
    public function show(Vehicle $vehicle)
    {
        try {
            $vehicle->load('user', 'queues', 'testResults');

            return response()->json([
                'message' => 'Detail kendaraan berhasil diambil',
                'data' => $vehicle,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update kendaraan
     */
    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        try {
            // Authorize menggunakan policy
            $this->authorize('update', $vehicle);

            $vehicle->update($request->validated());

            return response()->json([
                'message' => 'Kendaraan berhasil diperbarui',
                'data' => $vehicle,
            ], 200);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'message' => 'Anda tidak berhak mengubah kendaraan ini',
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Update kendaraan gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus kendaraan
     */
    public function destroy(Vehicle $vehicle)
    {
        try {
            // Authorize menggunakan policy
            $this->authorize('delete', $vehicle);

            // Cek apakah ada antrian aktif
            if ($vehicle->queues()->where('status', '!=', 'completed')->exists()) {
                return response()->json([
                    'message' => 'Tidak bisa menghapus kendaraan yang masih memiliki antrian aktif',
                ], 422);
            }

            $vehicle->delete();

            return response()->json([
                'message' => 'Kendaraan berhasil dihapus',
            ], 200);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'message' => 'Anda tidak berhak menghapus kendaraan ini',
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hapus kendaraan gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tampilkan riwayat ujian kendaraan
     */
    public function getTestHistory(Vehicle $vehicle)
    {
        try {
            if ($vehicle->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
                return response()->json([
                    'message' => 'Anda tidak memiliki akses',
                ], 403);
            }

            $testResults = $vehicle->testResults()
                ->with('penguji', 'queue')
                ->orderBy('tested_at', 'desc')
                ->paginate(10);

            return response()->json([
                'message' => 'Riwayat ujian berhasil diambil',
                'data' => $testResults,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}