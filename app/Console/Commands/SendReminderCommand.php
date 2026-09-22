<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Queue;
use App\Models\TestResult;
use App\Services\WhatsappService;
use Carbon\Carbon;

class SendReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send
                            {--type=pending : Tipe reminder (pending, incomplete)}
                            {--hours=24 : Jam sebelum pengiriman reminder}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Mengirim pengingat kepada pengguna tentang antrian atau ujian yang tertunda';

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
        $type = $this->option('type');
        $hours = (int) $this->option('hours');

        $this->info("Mengirim reminder tipe '{$type}' untuk antrian dalam {$hours} jam terakhir...");

        switch ($type) {
            case 'pending':
                return $this->sendPendingQueueReminders($hours);
            case 'incomplete':
                return $this->sendIncompleteTestReminders($hours);
            default:
                $this->error("Tipe reminder tidak dikenal: {$type}");
                return self::FAILURE;
        }
    }

    /**
     * Send reminders for pending queues
     *
     * @param int $hours
     * @return int
     */
    protected function sendPendingQueueReminders($hours)
    {
        $fromDate = now()->subHours($hours);

        $pendingQueues = Queue::with(['vehicle.user', 'testSchedule'])
            ->where('status', 'open')
            ->where('created_at', '>=', $fromDate)
            ->doesntHave('testResult')
            ->get();

        if ($pendingQueues->isEmpty()) {
            $this->info('Tidak ada antrian yang tertunda dalam periode tersebut.');
            return self::SUCCESS;
        }

        $successCount = 0;

        foreach ($pendingQueues as $queue) {
            try {
                $user = $queue->vehicle->user;
                $message = "Pengingat: Antrian Anda untuk ujian kendaraan {$queue->vehicle->brand} {$queue->vehicle->model} masih pending. " .
                           "Jadwal: {$queue->testSchedule->test_date->format('d-m-Y')} jam {$queue->testSchedule->start_time}. " .
                           "Harap segera menghubungi penguji jika ada perubahan.";

                if ($user->phone) {
                    $whatsappService = app(WhatsappService::class);
                    $whatsappService->sendMessage($user->phone, $message);
                    $successCount++;
                    $this->line("✓ Reminder tertunda dikirim ke {$user->name}");
                }
            } catch (\Exception $e) {
                $this->error("✗ Gagal mengirim reminder: {$e->getMessage()}");
            }
        }

        $this->info("\nTotal reminder tertunda terkirim: {$successCount}");
        return self::SUCCESS;
    }

    /**
     * Send reminders for incomplete tests
     *
     * @param int $hours
     * @return int
     */
    protected function sendIncompleteTestReminders($hours)
    {
        $fromDate = now()->subHours($hours);

        $incompleteTests = TestResult::with(['queue.vehicle.user', 'penguji'])
            ->where('created_at', '>=', $fromDate)
            ->where(function ($query) {
                $query->whereNull('emission_status')
                      ->orWhereNull('brake_status')
                      ->orWhereNull('light_status')
                      ->orWhereNull('horn_status')
                      ->orWhereNull('suspension_status')
                      ->orWhereNull('tire_status');
            })
            ->get();

        if ($incompleteTests->isEmpty()) {
            $this->info('Tidak ada hasil ujian yang tidak lengkap dalam periode tersebut.');
            return self::SUCCESS;
        }

        $successCount = 0;

        foreach ($incompleteTests as $test) {
            try {
                $penguji = $test->penguji;
                $message = "Pengingat: Hasil ujian untuk kendaraan {$test->queue->vehicle->brand} {$test->queue->vehicle->model} " .
                           "masih belum lengkap. Harap segera selesaikan pengisian data ujian.";

                if ($penguji->phone) {
                    $whatsappService = app(WhatsappService::class);
                    $whatsappService->sendMessage($penguji->phone, $message);
                    $successCount++;
                    $this->line("✓ Reminder tidak lengkap dikirim ke {$penguji->name}");
                }
            } catch (\Exception $e) {
                $this->error("✗ Gagal mengirim reminder: {$e->getMessage()}");
            }
        }

        $this->info("\nTotal reminder tidak lengkap terkirim: {$successCount}");
        return self::SUCCESS;
    }
}
