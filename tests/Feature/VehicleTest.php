<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;

class VehicleTest extends TestCase
{
    private $pesertaUser;
    private $adminUser;
    private $pesertaToken;
    private $adminToken;

    /**
     * Setup test
     */
    public function setUp(): void
    {
        parent::setUp();

        $timestamp = time();

        $this->pesertaUser = User::create([
            'name' => 'Peserta Test',
            'email' => 'peserta' . $timestamp . '@test.com',
            'phone' => '0812345678' . rand(100, 999),
            'password' => bcrypt('password123'),
            'role' => 'peserta',
        ]);

        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admin' . $timestamp . '@test.com',
            'phone' => '0812345678' . rand(100, 999),
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $this->pesertaToken = $this->pesertaUser->createToken('test')->plainTextToken;
        $this->adminToken = $this->adminUser->createToken('test')->plainTextToken;
    }

    /**
     * Test Create Vehicle - Success
     */
    public function test_create_vehicle_success()
    {
        $timestamp = time();
        
        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->postJson('/api/vehicles', [
                            'vehicle_number' => 'B ' . rand(1000, 9999) . ' ' . chr(65 + rand(0, 25)) . chr(65 + rand(0, 25)) . chr(65 + rand(0, 25)),
                            'vehicle_type' => 'car',
                            'brand' => 'Toyota',
                            'model' => 'Avanza',
                            'year' => 2020,
                            'color' => 'Silver',
                            'engine_number' => 'ENGINE' . $timestamp,
                            'chassis_number' => 'CHASSIS' . $timestamp,
                        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => ['id', 'vehicle_number', 'brand', 'model'],
                ]);
    }

    /**
     * Test Create Vehicle - Duplicate Vehicle Number
     */
    public function test_create_vehicle_duplicate_number()
    {
        $vehicleNumber = 'B ' . rand(1000, 9999) . ' ' . chr(65 + rand(0, 25)) . chr(65 + rand(0, 25)) . chr(65 + rand(0, 25));
        
        Vehicle::create([
            'user_id' => $this->pesertaUser->id,
            'vehicle_number' => $vehicleNumber,
            'vehicle_type' => 'car',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'year' => 2020,
            'color' => 'Silver',
            'engine_number' => 'ENGINE111',
            'chassis_number' => 'CHASSIS111',
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->postJson('/api/vehicles', [
                            'vehicle_number' => $vehicleNumber,
                            'vehicle_type' => 'car',
                            'brand' => 'Honda',
                            'model' => 'Civic',
                            'year' => 2021,
                            'color' => 'Red',
                            'engine_number' => 'ENGINE222',
                            'chassis_number' => 'CHASSIS222',
                        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors('vehicle_number');
    }

    /**
     * Test List Vehicles - Peserta
     */
    public function test_list_vehicles_peserta()
    {
        $timestamp = time();
        
        Vehicle::create([
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

        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->getJson('/api/vehicles');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'data' => [
                            ['id', 'vehicle_number', 'brand', 'model'],
                        ],
                    ],
                ]);
    }

    /**
     * Test Get Vehicle Detail
     */
    public function test_get_vehicle_detail()
    {
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

        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->getJson("/api/vehicles/{$vehicle->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => ['id', 'vehicle_number'],
                ]);
    }

    /**
     * Test Update Vehicle - Success
     */
    public function test_update_vehicle_success()
    {
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

        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->putJson("/api/vehicles/{$vehicle->id}", [
                            'brand' => 'Honda',
                            'model' => 'Civic',
                            'color' => 'Red',
                        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Kendaraan berhasil diperbarui']);

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'brand' => 'Honda',
        ]);
    }

    /**
     * Test Update Vehicle - Unauthorized
     */
    public function test_update_vehicle_unauthorized()
    {
        $timestamp = time();
        
        $otherPeserta = User::create([
            'name' => 'Other Peserta',
            'email' => 'other' . $timestamp . '@test.com',
            'phone' => '0812345678' . rand(100, 999),
            'password' => bcrypt('password123'),
            'role' => 'peserta',
        ]);

        $vehicle = Vehicle::create([
            'user_id' => $otherPeserta->id,
            'vehicle_number' => 'B ' . rand(1000, 9999) . ' ' . chr(65 + rand(0, 25)) . chr(65 + rand(0, 25)) . chr(65 + rand(0, 25)),
            'vehicle_type' => 'car',
            'brand' => 'Toyota',
            'model' => 'Avanza',
            'year' => 2020,
            'color' => 'Silver',
            'engine_number' => 'ENGINE' . $timestamp,
            'chassis_number' => 'CHASSIS' . $timestamp,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->putJson("/api/vehicles/{$vehicle->id}", [
                            'brand' => 'Honda',
                        ]);

        $response->assertStatus(403);
    }

    /**
     * Test Delete Vehicle - Success
     */
    public function test_delete_vehicle_success()
    {
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

        $response = $this->withHeader('Authorization', "Bearer $this->pesertaToken")
                        ->deleteJson("/api/vehicles/{$vehicle->id}");

        $response->assertStatus(200)
                ->assertJson(['message' => 'Kendaraan berhasil dihapus']);

        $this->assertDatabaseMissing('vehicles', [
            'id' => $vehicle->id,
        ]);
    }
}