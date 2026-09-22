<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('test_date');
            $table->enum('day_of_week', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']);
            $table->integer('max_queue');
            $table->integer('current_queue')->default(0);
            $table->enum('status', ['open', 'closed', 'full'])->default('open');
            $table->time('start_time')->default('08:00');
            $table->time('end_time')->default('16:00');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['test_date']);
            $table->index('test_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_schedules');
    }
};