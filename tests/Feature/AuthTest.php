<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    /**
     * Test Register - Success
     */
    public function test_register_success()
    {
        $timestamp = time();
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john' . $timestamp . '@example.com',
            'phone' => '0812345678' . rand(100, 999),
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john' . $timestamp . '@example.com',
        ]);
    }

    /**
     * Test Register - Email Already Exists
     */
    public function test_register_email_already_exists()
    {
        $email = 'existing' . time() . '@example.com';
        
        User::create([
            'name' => 'Existing User',
            'email' => $email,
            'phone' => '0812345678' . rand(100, 999),
            'password' => bcrypt('password123'),
            'role' => 'peserta',
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => $email,
            'phone' => '0812345678' . rand(100, 999),
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors('email');
    }

    /**
     * Test Register - Phone Already Exists
     */
    public function test_register_phone_already_exists()
    {
        $phone = '0812345678' . rand(100, 999);
        $email = 'existing' . time() . '@example.com';
        
        User::create([
            'name' => 'Existing User',
            'email' => $email,
            'phone' => $phone,
            'password' => bcrypt('password123'),
            'role' => 'peserta',
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john' . time() . '@example.com',
            'phone' => $phone,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors('phone');
    }

    /**
     * Test Login - Success
     */
    public function test_login_success()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0812345678' . rand(100, 999),
            'password' => bcrypt('password123'),
            'role' => 'peserta',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                ]);
    }

    /**
     * Test Login - Invalid Email
     */
    public function test_login_invalid_email()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'notfound@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
                ->assertJson(['message' => 'Email atau password salah']);
    }

    /**
     * Test Login - Invalid Password
     */
    public function test_login_invalid_password()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0812345678' . rand(100, 999),
            'password' => bcrypt('password123'),
            'role' => 'peserta',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                ->assertJson(['message' => 'Email atau password salah']);
    }

    /**
     * Test Get Current User
     */
    public function test_get_current_user()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0812345678' . rand(100, 999),
            'password' => bcrypt('password123'),
            'role' => 'peserta',
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                        ->getJson('/api/auth/user');

        $response->assertStatus(200)
                ->assertJson(['user' => ['id' => $user->id]]);
    }

    /**
     * Test Logout
     */
    public function test_logout()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0812345678' . rand(100, 999),
            'password' => bcrypt('password123'),
            'role' => 'peserta',
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                        ->postJson('/api/auth/logout');

        $response->assertStatus(200)
                ->assertJson(['message' => 'Logout berhasil']);
    }

    /**
     * Test Change Password
     */
    public function test_change_password()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0812345678' . rand(100, 999),
            'password' => bcrypt('password123'),
            'role' => 'peserta',
        ]);

        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                        ->putJson('/api/auth/change-password', [
                            'current_password' => 'password123',
                            'new_password' => 'newpassword123',
                            'new_password_confirmation' => 'newpassword123',
                        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'Password berhasil diubah']);
    }

    /**
     * Test Unauthorized Access (No Token)
     */
    public function test_unauthorized_no_token()
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertStatus(401);
    }
}