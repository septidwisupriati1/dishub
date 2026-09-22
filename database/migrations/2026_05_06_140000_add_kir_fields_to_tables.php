<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            if (!Schema::hasColumn('test_results', 'test_number')) {
                $table->string('test_number')->nullable()->after('id');
            }
            if (!Schema::hasColumn('test_results', 'valid_until')) {
                $table->date('valid_until')->nullable()->after('overall_notes');
            }
        });

        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'owner_name')) {
                $table->string('owner_name')->nullable()->after('vehicle_number');
            }
            if (!Schema::hasColumn('vehicles', 'address')) {
                $table->text('address')->nullable()->after('owner_name');
            }
            if (!Schema::hasColumn('vehicles', 'usage_type')) {
                $table->enum('usage_type', ['umum', 'tidak umum'])->nullable()->after('model');
            }
            if (!Schema::hasColumn('vehicles', 'fuel_type')) {
                $table->enum('fuel_type', ['bensin', 'solar', 'listrik'])->nullable()->after('usage_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->dropColumn(['test_number', 'valid_until']);
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['owner_name', 'address', 'usage_type', 'fuel_type']);
        });
    }
};
