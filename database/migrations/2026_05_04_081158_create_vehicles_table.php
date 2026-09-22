<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('vehicle_number')->unique();
            $table->enum('vehicle_type', ['motorcycle', 'car', 'truck', 'bus'])->default('car');
            $table->string('brand');
            $table->string('model');
            $table->integer('year');
            $table->string('color');
            $table->string('engine_number')->unique();
            $table->string('chassis_number')->unique();
            $table->integer('test_count')->default(0); // Jumlah pengujian
            $table->timestamp('last_test_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'under_maintenance'])->default('active');
            $table->timestamps();
            $table->index('user_id');
            $table->index('vehicle_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};