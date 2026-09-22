<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Queue;
use App\Models\TestSchedule;
use App\Services\WhatsappService;
use Carbon\Carbon;

class SendDailyNotificationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-daily
                            {--date= : Tanggal untuk mengirim notifikasi (format: Y-m-d)}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Mengirim notifikasi harian kepada peserta tentang jadwal ujian mereka';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::createFromFormat('Y-m-d', $this->option('date')) : now()->addDay();

        $this->info("Mengirim notifikasi harian untuk tanggal: {$date->format('Y-m-d')}");

        // Get all queues for the specified date
        $queues = Queue::with(['vehicle.user', 'testSchedule'])
            ->whereHas('testSchedule', function ($query) use ($date) {
                $query->whereDate('test_date', $date->format('Y-m-d'));
            })
            ->where('status', 'open')
            ->get();

        if ($queues->isEmpty()) {
            $this->info('Tidak ada antrian untuk tanggal tersebut.');
            return self::SUCCESS;
        }

        $successCount = 0;
        $failureCount = 0;

        foreach ($queues as $queue) {
            try {
                $user = $queue->vehicle->user;
                $message = $this->buildNotificationMessage($queue);

                if ($user->phone) {
                    $whatsappService = app(WhatsappService::class);
                    $whatsappService->sendMessage(
                        $user->phone,
                        $message
                    );
                    $successCount++;
                    $this->line("✓ Notifikasi dikirim ke {$user->name} ({$user->phone})");
                } else {
                    $failureCount++;
                    $this->warn("⚠ Nomor telepon tidak ada untuk {$user->name}");
                }
            } catch (\Exception $e) {
                $failureCount++;
                $this->error("✗ Gagal mengirim notifikasi: {$e->getMessage()}");
            }
        }

        $this->info("\n=== Ringkasan ===");
        $this->info("Total terkirim: {$successCount}");
        $this->info("Total gagal: {$failureCount}");
        $this->info("Total antrian: {$queues->count()}");

        return self::SUCCESS;
    }

    /**
     * Build notification message for queue
     *
     * @param \App\Models\Queue $queue
     * @return string
     */
    protected function buildNotificationMessage(Queue $queue)
    {
        $schedule = $queue->testSchedule;
        $vehicle = $queue->vehicle;

        return <<<MESSAGE
Assalamu'alaikum {$vehicle->user->name},

Anda mempunyai jadwal ujian kendaraan:
📅 Tanggal: {$schedule->test_date->format('d-m-Y')}
⏰ Jam: {$schedule->start_time} - {$schedule->end_time}
🚗 Kendaraan: {$vehicle->brand} {$vehicle->model} ({$vehicle->vehicle_number})
🔢 Nomor Antrian: {$queue->queue_number}

Harap datang 15 menit lebih awal. Terima kasih.
MESSAGE;
    }
}
