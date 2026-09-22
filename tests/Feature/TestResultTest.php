<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\TestSchedule;
use App\Models\Queue;
use App\Models\TestResult;
use Carbon\Carbon;

class TestResultTest extends TestCase
{
    use RefreshDatabase;
    private $pesertaUser;
    private $pengujiUser;
    private $pesertaToken;
    private $pengujiToken;
    private $queue;

    /**
     * Setup test
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->pesertaUser = User::create([
            'name' => 'Peserta Test',
            'email' => 'peserta@test.com',
            'phone' => '0812345678' . rand(100, 999),
            'password' => bcrypt('password123'),
            'role' => 'peserta',
        ]);

        $this->pengujiUser = User::create([
            'name' => 'Penguji Test',
            'email' => 'penguji@test.com',
            'phone' => '0812345678' . rand(100, 999),
            'password' => bcrypt('password123'),
            'role' => 'penguji',
        ]);

        $this->pesertaToken = $this->pesertaUser->createToken('test')->plainTextToken;
        $this->pengujiToken = $this->pengujiUser->createToken('test')->plainTextToken;

        $timestamp = time();
        $vehicle = Vehicle::create([
            'user_id' => $this->pesertaUser->id,
            'vehicle_number' => 'B ' . rand(1000, 9999) . ' ' . chr(65 + rand(0, 25)) . chr(65 + rand(0, 25)) . chr(65 + rand(0, 25)),
            'vehicle_type' => 'car',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'year' => 2020,
            'color' => 'Silver',
            'engine_number' => 'ENGINE' . $timestamp,
            'chassis_number' => 'CHASSIS' . $timestamp,
        ]);

        $schedule = TestSchedule::create([
            'test_date' => Carbon::now()->addDays(300 + rand(1, 10)),
            'day_of_week' => 'Monday',
            'max_queue' => 20,
            'current_queue' => 1,
            'status' => 'open',
            'start_time' => '08:00',
            'end_time' => '16:00',
        ]);

        $this->queue = Queue::create([
            'vehicle_id' => $vehicle->id,
            'user_id' => $this->pesertaUser->id,
            'test_schedule_id' => $schedule->id,
            'queue_date' => $schedule->test_date,
            'queue_number' => 1,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    /**
     * Test Create Test Result - Success
     */
    public function test_create_test_result_success()
    {
        $response = $this->withHeader('Authorization', "Bearer $this->pengujiToken")
                        ->postJson('/api/test-results', [
                            'queue_id' => $this->queue->id,
                            'emission_status' => 'pass',
                            'emission_notes' => 'Emisi normal',
                            'brake_status' => 'pass',
                            'brake_notes' => 'Rem baik',
                            'light_status' => 'pass',
                            'light_notes' => 'Lampu baik',
                            'horn_status' => 'pass',
                            'horn_notes' => 'Klakson baik',
                            'suspension_status' => 'pass',
                            'suspension_notes' => 'Suspensi baik',
                            'tire_status' => 'pass',
                            'tire_notes' => 'Ban baik',
                            'overall_notes' => 'Kendaraan lulus',
                        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'overall_status',
                        'tested_at',
                    ],
                ]);

        $this->assertDatabaseHas('test_results', [
            'queue_id' => $this->queue->id,
            'overall_status' => 'pass',
        ]);
    }

    /**
     * Test Create Test Result - Unauthorized (Peserta)
     */
    public function test_create_test_result_unauthorized_peserta()
    {
        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->postJson('/api/test-results', [
                            'queue_id' => $this->queue->id,
                            'emission_status' => 'pass',
                            'brake_status' => 'pass',
                            'light_status' => 'pass',
                            'horn_status' => 'pass',
                            'suspension_status' => 'pass',
                            'tire_status' => 'pass',
                        ]);

        $response->assertStatus(403);
    }

    /**
     * Test List Test Results
     */
    public function test_list_test_results()
    {
        TestResult::create([
            'queue_id' => $this->queue->id,
            'vehicle_id' => $this->queue->vehicle_id,
            'penguji_id' => $this->pengujiUser->id,
            'emission_status' => 'pass',
            'brake_status' => 'pass',
            'light_status' => 'pass',
            'horn_status' => 'pass',
            'suspension_status' => 'pass',
            'tire_status' => 'pass',
            'overall_status' => 'pass',
            'tested_at' => now(),
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->pengujiToken")
                        ->getJson('/api/test-results');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'data' => [
                            ['id', 'overall_status'],
                        ],
                    ],
                ]);
    }

    /**
     * Test Get Test Result Detail
     */
    public function test_get_test_result_detail()
    {
        $testResult = TestResult::create([
            'queue_id' => $this->queue->id,
            'vehicle_id' => $this->queue->vehicle_id,
            'penguji_id' => $this->pengujiUser->id,
            'emission_status' => 'pass',
            'brake_status' => 'pass',
            'light_status' => 'pass',
            'horn_status' => 'pass',
            'suspension_status' => 'pass',
            'tire_status' => 'pass',
            'overall_status' => 'pass',
            'tested_at' => now(),
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->pengujiToken")
                        ->getJson("/api/test-results/{$testResult->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $testResult->id,
                        'overall_status' => 'pass',
                    ],
                ]);
    }
}