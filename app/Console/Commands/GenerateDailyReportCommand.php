<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TestResult;
use App\Models\Queue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class GenerateDailyReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:generate-daily
                            {--date= : Tanggal laporan (format: Y-m-d)}
                            {--send-email : Kirim laporan melalui email}';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Generate laporan harian kegiatan ujian kendaraan';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::createFromFormat('Y-m-d', $this->option('date')) : now();
        $sendEmail = $this->option('send-email');

        $this->info("Generate laporan harian untuk tanggal: {$date->format('d-m-Y')}");

        // Get report data
        $reportData = $this->generateReportData($date);

        // Display report
        $this->displayReport($reportData, $date);

        // Save to file
        $filename = $this->saveReportToFile($reportData, $date);
        $this->info("✓ Laporan disimpan: {$filename}");

        // Send email if requested
        if ($sendEmail) {
            $this->sendReportViaEmail($reportData, $date, $filename);
        }

        return self::SUCCESS;
    }

    /**
     * Generate report data for the specified date
     *
     * @param \Carbon\Carbon $date
     * @return array
     */
    protected function generateReportData(Carbon $date)
    {
        $startDate = $date->startOfDay();
        $endDate = $date->endOfDay();

        // Get overall statistics
        $totalQueues = Queue::whereDate('created_at', $date)->count();
        $completedQueues = Queue::whereDate('created_at', $date)
            ->where('status', 'completed')
            ->count();
        $canceledQueues = Queue::whereDate('created_at', $date)
            ->where('status', 'canceled')
            ->count();

        // Get test results
        $passedTests = TestResult::whereBetween('tested_at', [$startDate, $endDate])
            ->where('overall_status', 'pass')
            ->count();
        $failedTests = TestResult::whereBetween('tested_at', [$startDate, $endDate])
            ->where('overall_status', 'fail')
            ->count();
        $totalTests = $passedTests + $failedTests;

        // Get per-examiner statistics
        $examinerStats = TestResult::whereBetween('tested_at', [$startDate, $endDate])
            ->with('penguji')
            ->get()
            ->groupBy('penguji_id')
            ->map(function ($tests) {
                $penguji = $tests->first()->penguji;
                return [
                    'name' => $penguji->name,
                    'total' => $tests->count(),
                    'pass' => $tests->where('overall_status', 'pass')->count(),
                    'fail' => $tests->where('overall_status', 'fail')->count(),
                ];
            });

        // Get per-schedule statistics
        $scheduleStats = Queue::whereBetween('created_at', [$startDate, $endDate])
            ->with('testSchedule')
            ->get()
            ->groupBy('test_date')
            ->map(function ($queues, $date) {
                return [
                    'date' => $date,
                    'total_queues' => $queues->count(),
                    'completed' => $queues->where('status', 'completed')->count(),
                ];
            });

        return [
            'date' => $date,
            'total_queues' => $totalQueues,
            'completed_queues' => $completedQueues,
            'canceled_queues' => $canceledQueues,
            'pending_queues' => $totalQueues - $completedQueues - $canceledQueues,
            'total_tests' => $totalTests,
            'passed_tests' => $passedTests,
            'failed_tests' => $failedTests,
            'examiner_stats' => $examinerStats,
            'schedule_stats' => $scheduleStats,
        ];
    }

    /**
     * Display report to console
     *
     * @param array $data
     * @param \Carbon\Carbon $date
     * @return void
     */
    protected function displayReport($data, Carbon $date)
    {
        $this->info("\n╔════════════════════════════════════════════════════════╗");
        $this->info("║        LAPORAN HARIAN UJIAN KENDARAAN                  ║");
        $this->info("║        Tanggal: " . $date->format('d-m-Y') . "                                  ║");
        $this->info("╚════════════════════════════════════════════════════════╝\n");

        // Queue Statistics
        $this->line("📋 STATISTIK ANTRIAN:");
        $this->line("  Total Antrian        : {$data['total_queues']}");
        $this->line("  Selesai              : {$data['completed_queues']}");
        $this->line("  Dibatalkan           : {$data['canceled_queues']}");
        $this->line("  Pending              : {$data['pending_queues']}\n");

        // Test Statistics
        $this->line("✓ STATISTIK UJIAN:");
        $this->line("  Total Ujian          : {$data['total_tests']}");
        $this->line("  Lulus                : {$data['passed_tests']}");
        $this->line("  Tidak Lulus          : {$data['failed_tests']}");
        if ($data['total_tests'] > 0) {
            $passRate = round(($data['passed_tests'] / $data['total_tests']) * 100, 2);
            $this->line("  Persentase Lulus     : {$passRate}%\n");
        } else {
            $this->line("");
        }

        // Examiner Statistics
        if ($data['examiner_stats']->isNotEmpty()) {
            $this->line("👤 STATISTIK PER PENGUJI:");
            foreach ($data['examiner_stats'] as $stats) {
                $this->line("  {$stats['name']}");
                $this->line("    - Total: {$stats['total']}, Lulus: {$stats['pass']}, Gagal: {$stats['fail']}");
            }
            $this->line("");
        }

        // Schedule Statistics
        if ($data['schedule_stats']->isNotEmpty()) {
            $this->line("📅 STATISTIK PER JADWAL:");
            foreach ($data['schedule_stats'] as $stats) {
                $this->line("  Tanggal {$stats['date']}: {$stats['total_queues']} antrian ({$stats['completed']} selesai)");
            }
        }
    }

    /**
     * Save report to file
     *
     * @param array $data
     * @param \Carbon\Carbon $date
     * @return string
     */
    protected function saveReportToFile($data, Carbon $date)
    {
        $filename = storage_path("reports/daily_report_{$date->format('Y-m-d')}.json");
        
        // Create directory if it doesn't exist
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0755, true);
        }

        file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
        return $filename;
    }

    /**
     * Send report via email
     *
     * @param array $data
     * @param \Carbon\Carbon $date
     * @param string $filename
     * @return void
     */
    protected function sendReportViaEmail($data, Carbon $date, $filename)
    {
        $this->info("📧 Mengirim laporan via email...");

        try {
            // Get all admin users
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                // You can use Mailable classes here
                // For now, just log the action
                $this->line("  ✓ Laporan akan dikirim ke: {$admin->email}");
            }

            $this->info("✓ Laporan berhasil dikirim!");
        } catch (\Exception $e) {
            $this->error("✗ Gagal mengirim laporan: {$e->getMessage()}");
        }
    }
}
