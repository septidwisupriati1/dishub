<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\TestSchedule;
use App\Models\Queue;
use Carbon\Carbon;

class QueueTest extends TestCase
{
    private $pesertaUser;
    private $pengujiUser;
    private $pesertaToken;
    private $pengujiToken;
    private $vehicle;
    private $schedule;

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
        $this->vehicle = Vehicle::create([
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

        $this->schedule = TestSchedule::create([
            'test_date' => Carbon::now()->addDays(15 + rand(1, 10)),
            'day_of_week' => 'Monday',
            'max_queue' => 20,
            'current_queue' => 0,
            'status' => 'open',
            'start_time' => '08:00',
            'end_time' => '16:00',
        ]);
    }

    /**
     * Test Create Queue - Success
     */
    public function test_create_queue_success()
    {
        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->postJson('/api/queues', [
                            'vehicle_id' => $this->vehicle->id,
                            'queue_date' => $this->schedule->test_date->format('Y-m-d'),
                        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => ['id', 'queue_number', 'status'],
                ]);

        $this->assertDatabaseHas('queues', [
            'vehicle_id' => $this->vehicle->id,
        ]);
    }

    /**
     * Test Create Queue - Schedule Full
     */
    public function test_create_queue_schedule_full()
    {
        $this->schedule->update(['status' => 'full', 'current_queue' => 20]);

        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->postJson('/api/queues', [
                            'vehicle_id' => $this->vehicle->id,
                            'queue_date' => $this->schedule->test_date->format('Y-m-d'),
                        ]);

        $response->assertStatus(422);
    }

    /**
     * Test List Queues
     */
    public function test_list_queues()
    {
        Queue::create([
            'vehicle_id' => $this->vehicle->id,
            'user_id' => $this->pesertaUser->id,
            'test_schedule_id' => $this->schedule->id,
            'queue_date' => $this->schedule->test_date,
            'queue_number' => 1,
            'status' => 'waiting',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->getJson('/api/queues');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'data' => [
                            ['id', 'queue_number', 'status'],
                        ],
                    ],
                ]);
    }

    /**
     * Test Call Queue - Success
     */
    public function test_call_queue_success()
    {
        $queue = Queue::create([
            'vehicle_id' => $this->vehicle->id,
            'user_id' => $this->pesertaUser->id,
            'test_schedule_id' => $this->schedule->id,
            'queue_date' => $this->schedule->test_date,
            'queue_number' => 1,
            'status' => 'waiting',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->pengujiToken")
                        ->postJson("/api/queues/{$queue->id}/call");

        $response->assertStatus(200)
                ->assertJson(['message' => 'Antrian berhasil dipanggil']);

        $this->assertDatabaseHas('queues', [
            'id' => $queue->id,
            'status' => 'in_progress',
        ]);
    }

    /**
     * Test Call Queue - Unauthorized (Peserta)
     */
    public function test_call_queue_unauthorized_peserta()
    {
        $queue = Queue::create([
            'vehicle_id' => $this->vehicle->id,
            'user_id' => $this->pesertaUser->id,
            'test_schedule_id' => $this->schedule->id,
            'queue_date' => $this->schedule->test_date,
            'queue_number' => 1,
            'status' => 'waiting',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->postJson("/api/queues/{$queue->id}/call");

        $response->assertStatus(403);
    }

    /**
     * Test Cancel Queue - Success
     */
    public function test_cancel_queue_success()
    {
        $queue = Queue::create([
            'vehicle_id' => $this->vehicle->id,
            'user_id' => $this->pesertaUser->id,
            'test_schedule_id' => $this->schedule->id,
            'queue_date' => $this->schedule->test_date,
            'queue_number' => 1,
            'status' => 'waiting',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->postJson("/api/queues/{$queue->id}/cancel");

        $response->assertStatus(200)
                ->assertJson(['message' => 'Antrian berhasil dibatalkan']);

        $this->assertDatabaseHas('queues', [
            'id' => $queue->id,
            'status' => 'cancelled',
        ]);
    }

    /**
     * Test Queue Stats
     */
    public function test_get_queue_stats()
    {
        Queue::create([
            'vehicle_id' => $this->vehicle->id,
            'user_id' => $this->pesertaUser->id,
            'test_schedule_id' => $this->schedule->id,
            'queue_date' => $this->schedule->test_date,
            'queue_number' => 1,
            'status' => 'waiting',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->getJson("/api/queues/stats?date={$this->schedule->test_date->format('Y-m-d')}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'total_queue',
                        'waiting',
                        'in_progress',
                        'completed',
                        'cancelled',
                        'available_slots',
                        'occupancy_percentage',
                    ],
                ]);
    }
}