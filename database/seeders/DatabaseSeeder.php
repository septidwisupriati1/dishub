<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\TestSchedule;
use App\Models\Queue;
use App\Models\TestResult;
use App\Models\WhatsappConfig;
use App\Models\WhatsappMessage;
use App\Models\AuditLog;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Disable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data (urutan penting: dari child ke parent)
        TestResult::truncate();
        WhatsappMessage::truncate();
        AuditLog::truncate();
        Queue::truncate();
        TestSchedule::truncate();
        Vehicle::truncate();
        User::truncate();
        WhatsappConfig::truncate();

        // Re-enable foreign key constraints
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('🗑️  Database cleared');

        // ========== CREATE ADMIN USER ==========
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '628123456789',
            'role' => 'admin',
            'nip' => 'ADM001',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $this->command->info('✅ Admin created: admin@example.com');

        // ========== CREATE PENGUJI USERS ==========
        $penguji1 = User::create([
            'name' => 'Penguji Satu',
            'email' => 'penguji1@example.com',
            'phone' => '628123456790',
            'role' => 'penguji',
            'nip' => 'PGJ001',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $this->command->info('✅ Penguji 1 created: penguji1@example.com');

        $penguji2 = User::create([
            'name' => 'Penguji Dua',
            'email' => 'penguji2@example.com',
            'phone' => '628123456791',
            'role' => 'penguji',
            'nip' => 'PGJ002',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $this->command->info('✅ Penguji 2 created: penguji2@example.com');

        $penguji3 = User::create([
            'name' => 'Penguji Tiga',
            'email' => 'penguji3@example.com',
            'phone' => '628123456792',
            'role' => 'penguji',
            'nip' => 'PGJ003',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);
        $this->command->info('✅ Penguji 3 created: penguji3@example.com');

        // ========== CREATE PESERTA USERS ==========
        $pesertaUsers = [];
        for ($i = 1; $i <= 10; $i++) {
            $peserta = User::create([
                'name' => "Peserta {$i}",
                'email' => "peserta{$i}@example.com",
                'phone' => '6281234567' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'role' => 'peserta',
                'password' => bcrypt('password123'),
                'is_active' => true,
            ]);
            $pesertaUsers[] = $peserta;
        }
        $this->command->info('✅ 10 Peserta users created');

        // ========== CREATE VEHICLES ==========
        $vehicles = [];
        $vehicleData = [
            ['B 1234 ABC', 'motorcycle', 'Honda', 'Supra', 2020, 'Black'],
            ['B 5678 DEF', 'car', 'Toyota', 'Avanza', 2019, 'Silver'],
            ['B 9012 GHI', 'car', 'Honda', 'Civic', 2021, 'Red'],
            ['B 3456 JKL', 'truck', 'Hino', '500', 2018, 'White'],
            ['B 7890 MNO', 'bus', 'Isuzu', 'Elf', 2017, 'Blue'],
            ['B 2345 PQR', 'car', 'Daihatsu', 'Xenia', 2020, 'Gray'],
            ['B 6789 STU', 'motorcycle', 'Yamaha', 'Vixion', 2021, 'Red'],
            ['B 0123 VWX', 'car', 'Suzuki', 'Swift', 2019, 'White'],
            ['B 4567 YZA', 'truck', 'Mitsubishi', 'Canter', 2020, 'Yellow'],
            ['B 8901 BCD', 'car', 'Hyundai', 'Tucson', 2022, 'Black'],
        ];

        foreach ($vehicleData as $index => $data) {
            $vehicle = Vehicle::create([
                'user_id' => $pesertaUsers[$index]->id,
                'vehicle_number' => $data[0],
                'vehicle_type' => $data[1],
                'brand' => $data[2],
                'model' => $data[3],
                'year' => $data[4],
                'color' => $data[5],
                'engine_number' => 'ENG' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'chassis_number' => 'CHS' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'test_count' => 0,
                'status' => 'active',
            ]);
            $vehicles[] = $vehicle;
        }
        $this->command->info('✅ 10 Vehicles created');

        // ========== CREATE TEST SCHEDULES ==========
        $schedules = [];
        $startDate = Carbon::now()->addDay();
        
        // Buat jadwal untuk 5 hari kerja ke depan (skip weekend)
        $dayCount = 0;
        $currentDate = $startDate->clone();
        
        while ($dayCount < 5) {
            if (!$currentDate->isWeekend()) {
                $schedule = TestSchedule::create([
                    'test_date' => $currentDate,
                    'day_of_week' => $currentDate->englishDayOfWeek,
                    'max_queue' => 20,
                    'current_queue' => 0,
                    'status' => 'open',
                    'start_time' => '08:00',
                    'end_time' => '16:00',
                    'notes' => 'Jadwal rutin ujian emisi kendaraan',
                ]);
                $schedules[] = $schedule;
                $dayCount++;
            }
            $currentDate->addDay();
        }
        $this->command->info('✅ 5 Test Schedules created');

        // ========== CREATE QUEUES ==========
        $queues = [];
        $scheduleQueueCounts = [];
        
        // Initialize queue counts for each schedule
        foreach ($schedules as $schedule) {
            $scheduleQueueCounts[$schedule->id] = 0;
        }
        
        for ($i = 0; $i < 10; $i++) {
            $schedule = $schedules[$i % count($schedules)];
            $scheduleQueueCounts[$schedule->id]++;
            $queueNumber = $scheduleQueueCounts[$schedule->id];

            $queue = Queue::create([
                'vehicle_id' => $vehicles[$i]->id,
                'user_id' => $pesertaUsers[$i]->id,
                'test_schedule_id' => $schedule->id,
                'queue_date' => $schedule->test_date,
                'queue_number' => $queueNumber,
                'status' => $i < 3 ? 'waiting' : ($i < 6 ? 'in_progress' : 'completed'),
                'called_at' => $i >= 3 ? now()->subHours(rand(1, 5)) : null,
                'started_at' => $i >= 3 ? now()->subHours(rand(1, 4)) : null,
                'completed_at' => $i >= 6 ? now()->subHours(rand(0, 2)) : null,
            ]);
            $queues[] = $queue;
        }

        // Update schedule current_queue count
        foreach ($schedules as $schedule) {
            $schedule->update([
                'current_queue' => Queue::where('test_schedule_id', $schedule->id)->count(),
            ]);
        }
        $this->command->info('✅ 10 Queues created');

        // ========== CREATE TEST RESULTS ==========
        for ($i = 6; $i < 10; $i++) {
            $queue = $queues[$i];
            
            TestResult::create([
                'queue_id' => $queue->id,
                'vehicle_id' => $queue->vehicle_id,
                'penguji_id' => $i % 3 === 0 ? $penguji1->id : ($i % 3 === 1 ? $penguji2->id : $penguji3->id),
                'emission_status' => rand(0, 1) ? 'pass' : 'fail',
                'emission_notes' => 'Emisi normal',
                'brake_status' => rand(0, 1) ? 'pass' : 'fail',
                'brake_notes' => 'Rem berfungsi baik',
                'light_status' => 'pass',
                'light_notes' => 'Lampu semua berfungsi',
                'horn_status' => 'pass',
                'horn_notes' => 'Klakson berbunyi jelas',
                'suspension_status' => rand(0, 1) ? 'pass' : 'fail',
                'suspension_notes' => 'Suspensi dalam kondisi baik',
                'tire_status' => 'pass',
                'tire_notes' => 'Kondisi ban baik, tekanan normal',
                'overall_status' => rand(0, 1) ? 'pass' : 'fail',
                'overall_notes' => 'Kendaraan lulus ujian',
                'tested_at' => $queue->completed_at,
            ]);
        }
        $this->command->info('✅ 4 Test Results created');

        // ========== CREATE WHATSAPP CONFIG ==========
        WhatsappConfig::create([
            'gateway_provider' => 'fonnte',
            'api_key' => '1xj7Tc8bd7hvemnCtkcA',
            'api_secret' => null,
            'phone_number' => '628123456789',
            'is_active' => true,
            'daily_limit' => 1000,
            'current_daily_count' => 0,
            'reset_date' => now(),
            'notes' => 'Konfigurasi Fonnte Aktif',
        ]);
        $this->command->info('✅ WhatsApp Config created');

        // ========== SUMMARY ==========
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('🎉 SEEDER BERHASIL DIJALANKAN!');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('📊 DATA SUMMARY:');
        $this->command->info('   • 1 Admin User');
        $this->command->info('   • 3 Penguji Users');
        $this->command->info('   • 10 Peserta Users');
        $this->command->info('   • 10 Vehicles');
        $this->command->info('   • 5 Test Schedules');
        $this->command->info('   • 10 Queues');
        $this->command->info('   • 4 Test Results');
        $this->command->info('   • 1 WhatsApp Config');
        $this->command->info('');
        $this->command->info('🔐 TEST CREDENTIALS:');
        $this->command->info('');
        $this->command->info('   ADMIN:');
        $this->command->info('   Email: admin@example.com');
        $this->command->info('   Password: password123');
        $this->command->info('');
        $this->command->info('   PENGUJI:');
        $this->command->info('   Email: penguji1@example.com');
        $this->command->info('   Email: penguji2@example.com');
        $this->command->info('   Email: penguji3@example.com');
        $this->command->info('   Password: password123 (untuk semua)');
        $this->command->info('');
        $this->command->info('   PESERTA:');
        $this->command->info('   Email: peserta1@example.com - peserta10@example.com');
        $this->command->info('   Password: password123 (untuk semua)');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════════════════════');
    }
}