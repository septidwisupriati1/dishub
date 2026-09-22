<?php

namespace App\Services;

use App\Models\WhatsappConfig;
use App\Models\WhatsappMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    private ?WhatsappConfig $config = null;

    public function __construct()
    {
        $this->config = WhatsappConfig::where('is_active', true)->first();
        
        if (!$this->config) {
            throw new \Exception('WhatsApp config tidak ditemukan atau tidak aktif');
        }
    }

    /**
     * Notifikasi peserta saat antrian dipanggil
     */
    public function notifyQueueCalled($queue, $estimatedTime = null): bool
    {
        try {
            $user = $queue->user;
            if (!$user || !$user->phone) {
                Log::warning('User atau phone number tidak ditemukan untuk queue: ' . $queue->id);
                return false;
            }

            $message = $this->formatQueueCalledMessage($queue, $estimatedTime);
            
            return $this->sendMessage(
                $user->phone,
                $message,
                'queue_called',
                $user->id
            );
        } catch (\Exception $e) {
            Log::error('Error notifying queue called: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notifikasi peserta saat antrian dibuat
     */
    public function notifyQueueCreated($queue, ?string $customPhone = null): bool
    {
        try {
            $user = $queue->user;
            $phone = $customPhone ?? ($user ? $user->phone : null);
            
            if (!$phone) {
                Log::warning('User atau phone number tidak ditemukan untuk queue: ' . $queue->id);
                return false;
            }

            $message = $this->formatQueueCreatedMessage($queue);
            
            return $this->sendMessage(
                $phone,
                $message,
                'other',
                $user ? $user->id : null
            );
        } catch (\Exception $e) {
            Log::error('Error notifying queue created: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notifikasi hasil ujian ke peserta
     */
    public function notifyTestResult($testResult): bool
    {
        try {
            $user = $testResult->queue->user;
            if (!$user || !$user->phone) {
                Log::warning('User atau phone number tidak ditemukan untuk test result: ' . $testResult->id);
                return false;
            }

            $message = $this->formatTestResultMessage($testResult);
            
            return $this->sendMessage(
                $user->phone,
                $message,
                'queue_result',
                $user->id
            );
        } catch (\Exception $e) {
            Log::error('Error notifying test result: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notifikasi statistik harian ke penguji
     */
    public function notifyPengujiDailyStats($penguji, $testDate, $stats): bool
    {
        try {
            if (!$penguji || !$penguji->phone) {
                Log::warning('Penguji atau phone number tidak ditemukan');
                return false;
            }

            $message = $this->formatPengujiStatsMessage($penguji, $testDate, $stats);
            
            return $this->sendMessage(
                $penguji->phone,
                $message,
                'daily_stats',
                $penguji->id
            );
        } catch (\Exception $e) {
            Log::error('Error notifying penguji daily stats: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Notifikasi laporan harian ke admin
     */
    public function notifyAdminDailyReport($admin, $testDate, $report): bool
    {
        try {
            if (!$admin || !$admin->phone) {
                Log::warning('Admin atau phone number tidak ditemukan');
                return false;
            }

            $message = $this->formatAdminReportMessage($testDate, $report);
            
            return $this->sendMessage(
                $admin->phone,
                $message,
                'admin_report',
                $admin->id
            );
        } catch (\Exception $e) {
            Log::error('Error notifying admin daily report: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Pengingat ujian besok ke peserta
     */
    public function sendQueueReminder($queue): bool
    {
        try {
            $user = $queue->user;
            if (!$user || !$user->phone) {
                Log::warning('User atau phone number tidak ditemukan untuk reminder: ' . $queue->id);
                return false;
            }

            $message = $this->formatReminderMessage($queue);
            
            return $this->sendMessage(
                $user->phone,
                $message,
                'reminder',
                $user->id
            );
        } catch (\Exception $e) {
            Log::error('Error sending queue reminder: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fungsi utama mengirim pesan
     * 
     * @param string $phone
     * @param string $message
     * @param string $messageType
     * @param int|null $userId
     * @return bool
     */
    public function sendMessage(string $phone, string $message, string $messageType = 'other', ?int $userId = null): bool
    {
        try {
            // Validasi config
            if (!$this->config) {
                throw new \Exception('WhatsApp config tidak tersedia');
            }

            // Validasi nomor telepon
            $phone = $this->sanitizePhone($phone);
            if (!$phone) {
                throw new \Exception('Nomor telepon tidak valid');
            }

            // Cek quota
            if ($this->config->hasReachedLimit()) {
                throw new \Exception('Daily quota WhatsApp sudah habis');
            }

            // Simpan record pesan ke database dengan status pending
            $whatsappMessage = WhatsappMessage::create([
                'user_id' => $userId,
                'recipient_phone' => $phone,
                'message_content' => $message,
                'message_type' => $messageType,
                'status' => 'pending',
            ]);

            // Kirim pesan sesuai provider
            $response = $this->sendByProvider($phone, $message);

            // Update status pesan
            if ($response['success']) {
                $whatsappMessage->update([
                    'status' => 'sent',
                    'external_id' => $response['external_id'] ?? null,
                    'sent_at' => now(),
                ]);

                // Increment counter
                $this->incrementDailyCount();

                Log::info('WhatsApp message sent', [
                    'phone' => $phone,
                    'type' => $messageType,
                    'external_id' => $response['external_id'] ?? null,
                ]);

                return true;
            } else {
                $whatsappMessage->update([
                    'status' => 'failed',
                    'error_message' => $response['error'] ?? 'Unknown error',
                    'retry_count' => 0,
                ]);

                Log::error('WhatsApp message failed', [
                    'phone' => $phone,
                    'type' => $messageType,
                    'error' => $response['error'] ?? 'Unknown error',
                ]);

                return false;
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp service error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim pesan berdasarkan provider yang dipilih
     */
    private function sendByProvider(string $phone, string $message): array
    {
        if (!$this->config) {
            return [
                'success' => false,
                'error' => 'WhatsApp config tidak tersedia',
            ];
        }

        return match($this->config->gateway_provider) {
            'twilio' => $this->sendViaTwilio($phone, $message),
            'fonnte' => $this->sendViaFonnte($phone, $message),
            'ultramsg' => $this->sendViaUltramsg($phone, $message),
            'wablas' => $this->sendViaWablas($phone, $message),
            default => [
                'success' => false,
                'error' => 'Provider tidak dikenali: ' . $this->config->gateway_provider,
            ],
        };
    }

    /**
     * Kirim via Twilio
     */
    private function sendViaTwilio(string $phone, string $message): array
    {
        try {
            if (!$this->config || !$this->config->api_key || !$this->config->api_secret) {
                return [
                    'success' => false,
                    'error' => 'Twilio credentials tidak lengkap',
                ];
            }

            $response = Http::timeout(5)->post(
                "https://api.twilio.com/2010-04-01/Accounts/{$this->config->api_key}/Messages.json",
                [
                    'From' => 'whatsapp:' . $this->config->phone_number,
                    'To' => 'whatsapp:' . $phone,
                    'Body' => $message,
                ]
            )->withBasicAuth($this->config->api_key, $this->config->api_secret);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'external_id' => $response->json('sid'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message') ?? 'Send failed',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim via Fonnte
     */
    private function sendViaFonnte(string $phone, string $message): array
    {
        try {
            if (!$this->config || !$this->config->api_key) {
                return [
                    'success' => false,
                    'error' => 'Fonnte API key tidak ditemukan',
                ];
            }

            $response = Http::timeout(5)->withHeaders([
                'Authorization' => $this->config->api_key,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'external_id' => $response->json('data.id'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('reason') ?? 'Send failed',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim via Ultramsg
     */
    private function sendViaUltramsg(string $phone, string $message): array
    {
        try {
            if (!$this->config || !$this->config->api_key || !$this->config->api_secret) {
                return [
                    'success' => false,
                    'error' => 'Ultramsg credentials tidak lengkap',
                ];
            }

            $response = Http::timeout(5)->post(
                "https://api.ultramsg.com/instance{$this->config->api_key}/messages/chat",
                [
                    'token' => $this->config->api_secret,
                    'to' => $phone,
                    'body' => $message,
                ]
            );

            if ($response->successful()) {
                return [
                    'success' => true,
                    'external_id' => $response->json('id'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message') ?? 'Send failed',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim via Wablas
     */
    private function sendViaWablas(string $phone, string $message): array
    {
        try {
            if (!$this->config || !$this->config->api_key) {
                return [
                    'success' => false,
                    'error' => 'Wablas API key tidak ditemukan',
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => $this->config->api_key,
            ])->post('https://api.wablas.com/api/send-message', [
                'phone' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'external_id' => $response->json('data.id'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('message') ?? 'Send failed',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format pesan antrian dipanggil
     */
    private function formatQueueCalledMessage($queue, ?int $estimatedTime = null): string
    {
        $estimatedTimeText = $estimatedTime ? "Estimasi waktu: {$estimatedTime} menit\n" : '';
        
        return <<<TEXT
Halo {$queue->user->name},

Kendaraan Anda dengan nomor polisi {$queue->vehicle->vehicle_number} telah dipanggil untuk melakukan uji emisi.

Nomor Antrian: {$queue->formatted_queue_number}
Tanggal Ujian: {$queue->queue_date->format('d-m-Y')}
{$estimatedTimeText}
Silakan segera menuju lokasi pengujian.

Terima kasih
TEXT;
    }

    /**
     * Format pesan antrian dibuat
     */
    private function formatQueueCreatedMessage($queue): string
    {
        return <<<TEXT
Halo {$queue->user->name},

Anda telah berhasil mengambil antrian uji emisi kendaraan untuk nomor polisi {$queue->vehicle->vehicle_number}.

Nomor Antrian: *{$queue->formatted_queue_number}*
Tanggal Ujian: {$queue->queue_date->format('d-m-Y')}

Silakan simpan nomor antrian ini dan datang sesuai tanggal yang ditentukan.

Terima kasih
TEXT;
    }

    /**
     * Format pesan hasil ujian
     */
    private function formatTestResultMessage($testResult): string
    {
        $status = $testResult->overall_status === 'pass' ? '✅ LULUS' : '❌ TIDAK LULUS';
        $vehicle = $testResult->vehicle;
        
        $componentsText = "
Hasil Komponen Ujian:
• Emisi: " . ($testResult->emission_status === 'pass' ? '✅ Lulus' : '❌ Tidak Lulus') . "
• Rem: " . ($testResult->brake_status === 'pass' ? '✅ Lulus' : '❌ Tidak Lulus') . "
• Lampu: " . ($testResult->light_status === 'pass' ? '✅ Lulus' : '❌ Tidak Lulus') . "
• Klakson: " . ($testResult->horn_status === 'pass' ? '✅ Lulus' : '❌ Tidak Lulus') . "
• Suspensi: " . ($testResult->suspension_status === 'pass' ? '✅ Lulus' : '❌ Tidak Lulus') . "
• Ban: " . ($testResult->tire_status === 'pass' ? '✅ Lulus' : '❌ Tidak Lulus');

        return <<<TEXT
Halo {$testResult->queue->user->name},

Hasil Ujian Kendaraan Anda:

$status

Kendaraan: {$vehicle->brand} {$vehicle->model}
Nomor Polisi: {$vehicle->vehicle_number}
Tanggal Ujian: {$testResult->tested_at->format('d-m-Y H:i')}
{$componentsText}

Keterangan: {$testResult->overall_notes}

Terima kasih telah melakukan uji emisi.
TEXT;
    }

    /**
     * Format pesan statistik penguji
     */
    private function formatPengujiStatsMessage($penguji, $testDate, array $stats): string
    {
        return <<<TEXT
Halo {$penguji->name},

Berikut adalah statistik ujian Anda untuk hari ini:

Tanggal: {$testDate->format('d-m-Y')}
Total Diuji: {$stats['total_tested']} kendaraan
Lulus: {$stats['total_passed']} kendaraan ({$stats['pass_percentage']}%)
Tidak Lulus: {$stats['total_failed']} kendaraan
Rata-rata Durasi: {$stats['avg_duration_minutes']} menit per kendaraan

Terima kasih atas kerja keras Anda.
TEXT;
    }

    /**
     * Format pesan laporan admin
     */
    private function formatAdminReportMessage($testDate, array $report): string
    {
        return <<<TEXT
📊 LAPORAN HARIAN UJIAN KENDARAAN

Tanggal: {$testDate->format('d-m-Y')}
Total Antrian: {$report['total_queue']}
Total Selesai: {$report['total_completed']}
Total Lulus: {$report['total_passed']}
Total Tidak Lulus: {$report['total_failed']}
Persentase Lulus: {$report['pass_percentage']}%

Penguji Aktif: {$report['active_penguji']}

Terima kasih
TEXT;
    }

    /**
     * Format pesan pengingat
     */
    private function formatReminderMessage($queue): string
    {
        return <<<TEXT
Halo {$queue->user->name},

Pengingat: Anda memiliki jadwal ujian emisi kendaraan besok.

Tanggal: {$queue->queue_date->format('d-m-Y')}
Nomor Polisi: {$queue->vehicle->vehicle_number}
Nomor Antrian: {$queue->formatted_queue_number}

Silakan datang tepat waktu dan bawa semua dokumen kendaraan Anda.

Terima kasih
TEXT;
    }

    /**
     * Sanitasi nomor telepon
     */
    private function sanitizePhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Remove all non-digit characters except + at the beginning
        $phone = preg_replace('/[^\d+]/', '', $phone);
        
        // If starts with 0, replace with 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // If doesn't start with +, add it
        if (substr($phone, 0, 1) !== '+') {
            $phone = '+' . $phone;
        }
        
        // Validate length (should be between 10-15 digits)
        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) < 10 || strlen($digits) > 15) {
            return null;
        }
        
        return $phone;
    }

    /**
     * Increment daily counter
     */
    private function incrementDailyCount(): void
    {
        if (!$this->config) {
            return;
        }

        $today = now()->toDateString();
        $resetDate = $this->config->reset_date?->format('Y-m-d');
        
        if ($resetDate !== $today) {
            $this->config->update([
                'current_daily_count' => 1,
                'reset_date' => now(),
            ]);
        } else {
            $this->config->increment('current_daily_count');
        }
    }
}