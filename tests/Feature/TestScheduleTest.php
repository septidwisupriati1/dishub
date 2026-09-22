<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\TestSchedule;
use Carbon\Carbon;

class TestScheduleTest extends TestCase
{
    private $adminUser;
    private $adminToken;

    /**
     * Setup test
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'phone' => '0812345678' . rand(100, 999),
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $this->adminToken = $this->adminUser->createToken('test')->plainTextToken;
    }

    /**
     * Test Create Test Schedule - Success
     */
    public function test_create_test_schedule_success()
    {
        $testDate = Carbon::now()->addDays(100 + 1);

        $response = $this->withHeader('Authorization', "Bearer $this->adminToken")
                        ->postJson('/api/test-schedules', [
                            'test_date' => $testDate->format('Y-m-d'),
                            'max_queue' => 20,
                            'start_time' => '08:00',
                            'end_time' => '16:00',
                            'notes' => 'Jadwal rutin',
                        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => ['id', 'test_date', 'max_queue', 'status'],
                ]);

        $this->assertDatabaseHas('test_schedules', [
            'max_queue' => 20,
        ]);
    }

    /**
     * Test Create Test Schedule - Weekend
     */
    public function test_create_test_schedule_weekend()
    {
        $saturdayDate = Carbon::now()->next('Saturday');

        $response = $this->withHeader('Authorization', "Bearer $this->adminToken")
                        ->postJson('/api/test-schedules', [
                            'test_date' => $saturdayDate->format('Y-m-d'),
                            'max_queue' => 20,
                            'start_time' => '08:00',
                            'end_time' => '16:00',
                        ]);

        $response->assertStatus(422);
    }

    /**
     * Test List Test Schedules
     */
    public function test_list_test_schedules()
    {
        TestSchedule::create([
            'test_date' => Carbon::now()->addDays(100 + 2),
            'day_of_week' => 'Tuesday',
            'max_queue' => 20,
            'current_queue' => 0,
            'status' => 'open',
            'start_time' => '08:00',
            'end_time' => '16:00',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->adminToken")
                        ->getJson('/api/test-schedules');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'data' => [
                            ['id', 'test_date', 'status'],
                        ],
                    ],
                ]);
    }

    /**
     * Test Update Test Schedule
     */
    public function test_update_test_schedule()
    {
        $schedule = TestSchedule::create([
            'test_date' => Carbon::now()->addDays(100 + 3),
            'day_of_week' => 'Tuesday',
            'max_queue' => 20,
            'current_queue' => 0,
            'status' => 'open',
            'start_time' => '08:00',
            'end_time' => '16:00',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->adminToken")
                        ->putJson("/api/test-schedules/{$schedule->id}", [
                            'max_queue' => 25,
                            'status' => 'closed',
                        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Jadwal ujian berhasil diperbarui']);

        $this->assertDatabaseHas('test_schedules', [
            'id' => $schedule->id,
            'max_queue' => 25,
        ]);
    }

    /**
     * Test Delete Test Schedule
     */
    public function test_delete_test_schedule()
    {
        $schedule = TestSchedule::create([
            'test_date' => Carbon::now()->addDays(100 + 4),
            'day_of_week' => 'Tuesday',
            'max_queue' => 20,
            'current_queue' => 0,
            'status' => 'open',
            'start_time' => '08:00',
            'end_time' => '16:00',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->adminToken")
                        ->deleteJson("/api/test-schedules/{$schedule->id}");

        $response->assertStatus(200)
                ->assertJson(['message' => 'Jadwal ujian berhasil dihapus']);

        $this->assertDatabaseMissing('test_schedules', [
            'id' => $schedule->id,
        ]);
    }
}