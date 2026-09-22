<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penguji_id')->constrained('users')->onDelete('cascade');
            $table->date('test_date');
            $table->integer('total_tested')->default(0); // Total yang diuji hari ini
            $table->integer('total_passed')->default(0); // Total lulus
            $table->integer('total_failed')->default(0); // Total tidak lulus
            $table->decimal('pass_percentage', 5, 2)->default(0); // Persentase lulus
            $table->integer('avg_duration_minutes')->default(0); // Rata-rata durasi ujian
            $table->timestamps();
            $table->unique(['penguji_id', 'test_date']);
            $table->index('penguji_id');
            $table->index('test_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_statistics');
    }
};