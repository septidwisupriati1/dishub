<?php

namespace App\Http\Controllers;

use App\Models\WhatsappConfig;
use Illuminate\Http\Request;

class WhatsappConfigController extends Controller
{
    /**
     * Middleware untuk hanya admin
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'message' => 'Hanya admin yang dapat mengakses konfigurasi WhatsApp',
                ], 403);
            }
            return $next($request);
        });
    }

    /**
     * Tampilkan semua konfigurasi WhatsApp
     */
    public function index()
    {
        try {
            $configs = WhatsappConfig::get()->makeVisible(['api_key', 'api_secret']);

            return response()->json([
                'message' => 'Daftar konfigurasi WhatsApp berhasil diambil',
                'data' => $configs,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buat konfigurasi WhatsApp baru
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'gateway_provider' => 'required|in:twilio,fonnte,ultramsg,wablas',
                'api_key' => 'required|string',
                'api_secret' => 'nullable|string',
                'phone_number' => 'required|string',
                'webhook_url' => 'nullable|url',
                'daily_limit' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);

            $config = WhatsappConfig::create([
                ...$validated,
                'reset_date' => now(),
            ]);

            return response()->json([
                'message' => 'Konfigurasi WhatsApp berhasil ditambahkan',
                'data' => $config->makeVisible(['api_key', 'api_secret']),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Tambah konfigurasi gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tampilkan detail konfigurasi WhatsApp
     */
    public function show(WhatsappConfig $whatsappConfig)
    {
        try {
            return response()->json([
                'message' => 'Detail konfigurasi WhatsApp berhasil diambil',
                'data' => $whatsappConfig->makeVisible(['api_key', 'api_secret']),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update konfigurasi WhatsApp
     */
    public function update(Request $request, WhatsappConfig $whatsappConfig)
    {
        try {
            $validated = $request->validate([
                'gateway_provider' => 'sometimes|in:twilio,fonnte,ultramsg,wablas',
                'api_key' => 'sometimes|string',
                'api_secret' => 'nullable|string',
                'phone_number' => 'sometimes|string',
                'webhook_url' => 'nullable|url',
                'daily_limit' => 'sometimes|integer|min:1',
                'is_active' => 'sometimes|boolean',
                'notes' => 'nullable|string',
            ]);

            $whatsappConfig->update($validated);

            return response()->json([
                'message' => 'Konfigurasi WhatsApp berhasil diperbarui',
                'data' => $whatsappConfig->makeVisible(['api_key', 'api_secret']),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Update konfigurasi gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus konfigurasi WhatsApp
     */
    public function destroy(WhatsappConfig $whatsappConfig)
    {
        try {
            $whatsappConfig->delete();

            return response()->json([
                'message' => 'Konfigurasi WhatsApp berhasil dihapus',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hapus konfigurasi gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test WhatsApp connection
     */
    public function testConnection(WhatsappConfig $whatsappConfig)
    {
        try {
            if (!$whatsappConfig->is_active) {
                return response()->json([
                    'message' => 'Konfigurasi tidak aktif',
                    'success' => false,
                ], 422);
            }

            // Test dengan kirim pesan test
            $testPhone = request('phone');

            if (!$testPhone) {
                return response()->json([
                    'message' => 'Nomor telepon tujuan belum diisi',
                    'success' => false,
                ], 422);
            }

            $schedule = \App\Models\TestSchedule::whereDate('test_date', '>=', now()->toDateString())
                ->where('status', 'open')
                ->orderBy('test_date')
                ->first();

            $queueInfo = "";
            if ($schedule) {
                $available = max(0, $schedule->max_queue - $schedule->current_queue);
                $queueInfo = "\n\nInformasi Antrean Uji KIR:\nJadwal Terdekat: " . $schedule->test_date->format('d-m-Y') . "\nSisa Slot Tersedia: " . $available . " kendaraan.";
            } else {
                $queueInfo = "\n\nSaat ini belum ada jadwal antrean yang dibuka.";
            }

            $messageText = "Halo! Ini adalah pesan dari Sistem Antrean Uji Kendaraan.\nKoneksi WhatsApp Gateway Anda telah berhasil terhubung dengan sistem." . $queueInfo;

            $whatsappService = new \App\Services\WhatsappService();
            $result = $whatsappService->sendMessage(
                $testPhone,
                $messageText,
                'other',
                auth()->id()
            );

            return response()->json([
                'message' => $result ? 'Koneksi berhasil! Pesan test telah dikirim ke nomor Anda.' : 'Gagal mengirim pesan test.',
                'success' => $result,
            ], $result ? 200 : 500);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
                'success' => false,
            ], 500);
        }
    }
}