<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('penguji_id')->constrained('users')->onDelete('cascade'); // User dengan role penguji
            
            // Komponen Ujian
            $table->enum('emission_status', ['pass', 'fail'])->nullable();
            $table->text('emission_notes')->nullable();
            
            $table->enum('brake_status', ['pass', 'fail'])->nullable();
            $table->text('brake_notes')->nullable();
            
            $table->enum('light_status', ['pass', 'fail'])->nullable();
            $table->text('light_notes')->nullable();
            
            $table->enum('horn_status', ['pass', 'fail'])->nullable();
            $table->text('horn_notes')->nullable();
            
            $table->enum('suspension_status', ['pass', 'fail'])->nullable();
            $table->text('suspension_notes')->nullable();
            
            $table->enum('tire_status', ['pass', 'fail'])->nullable();
            $table->text('tire_notes')->nullable();
            
            // Status Keseluruhan
            $table->enum('overall_status', ['pass', 'fail'])->nullable();
            $table->text('overall_notes')->nullable();
            
            $table->timestamp('tested_at')->nullable();
            $table->timestamps();
            $table->index('penguji_id');
            $table->index('overall_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};