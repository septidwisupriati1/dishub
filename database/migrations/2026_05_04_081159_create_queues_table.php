<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('test_schedule_id')->constrained()->onDelete('cascade');
            $table->date('queue_date');
            $table->integer('queue_number');
            $table->enum('status', ['waiting', 'in_progress', 'completed', 'cancelled'])->default('waiting');
            $table->text('notes')->nullable();
            $table->timestamp('called_at')->nullable(); // Saat antrian dipanggil penguji
            $table->timestamp('started_at')->nullable(); // Saat ujian dimulai
            $table->timestamp('completed_at')->nullable(); // Saat ujian selesai
            $table->timestamps();
            $table->unique(['queue_date', 'queue_number']);
            $table->index('user_id');
            $table->index('vehicle_id');
            $table->index('queue_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};