<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_configs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway_provider'); // 'twilio', 'fonnte', 'ultramsg', 'wablas'
            $table->string('api_key');
            $table->string('api_secret')->nullable();
            $table->string('phone_number'); // Nomor WhatsApp bisnis
            $table->string('webhook_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('daily_limit')->default(1000);
            $table->integer('current_daily_count')->default(0);
            $table->date('reset_date');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_configs');
    }
};