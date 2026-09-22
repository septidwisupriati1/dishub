<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('recipient_phone');
            $table->text('message_content');
            $table->enum('message_type', ['queue_called', 'queue_result', 'daily_stats', 'reminder', 'admin_report', 'other']);
            $table->enum('status', ['pending', 'sent', 'failed', 'read'])->default('pending');
            $table->string('external_id')->nullable(); // ID dari provider (Twilio, etc)
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index('recipient_phone');
            $table->index('status');
            $table->index('message_type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};