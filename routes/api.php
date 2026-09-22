<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\TestResultController;
use App\Http\Controllers\TestScheduleController;
use App\Http\Controllers\WhatsappConfigController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================================
// PUBLIC ROUTES (TIDAK PERLU AUTH)
// ============================================================

Route::prefix('auth')->group(function () {
    // Register user baru
    Route::post('/register', [AuthController::class, 'register'])
        ->name('register');

    // Login user
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login');
});

// ============================================================
// PROTECTED ROUTES (PERLU AUTH:SANCTUM)
// ============================================================

Route::middleware('auth:sanctum')->group(function () {

    // ========== AUTH ROUTES ==========
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');

        Route::get('/user', [AuthController::class, 'getUser'])
            ->name('get.user');

        Route::put('/profile', [AuthController::class, 'updateProfile'])
            ->name('update.profile');

        Route::put('/change-password', [AuthController::class, 'changePassword'])
            ->name('change.password');
    });

    // ========== VEHICLE ROUTES ==========
    Route::prefix('vehicles')->group(function () {
        // List kendaraan
        Route::get('/', [VehicleController::class, 'index'])
            ->name('vehicles.index');

        // Create kendaraan (hanya peserta)
        Route::post('/', [VehicleController::class, 'store'])
            ->middleware('can:create-vehicle')
            ->name('vehicles.store');

        // Detail kendaraan
        Route::get('/{vehicle}', [VehicleController::class, 'show'])
            ->name('vehicles.show');

        // Update kendaraan
        Route::put('/{vehicle}', [VehicleController::class, 'update'])
            ->middleware('can:update-vehicle,vehicle')
            ->name('vehicles.update');

        // Delete kendaraan
        Route::delete('/{vehicle}', [VehicleController::class, 'destroy'])
            ->middleware('can:delete-vehicle,vehicle')
            ->name('vehicles.destroy');

        // Riwayat ujian kendaraan
        Route::get('/{vehicle}/test-history', [VehicleController::class, 'getTestHistory'])
            ->name('vehicles.test-history');
    });

    // ========== QUEUE ROUTES ==========
    Route::prefix('queues')->group(function () {
        // List antrian
        Route::get('/', [QueueController::class, 'index'])
            ->name('queues.index');

        // Create antrian (hanya peserta)
        Route::post('/', [QueueController::class, 'store'])
            ->middleware('can:create-queue')
            ->name('queues.store');

        // Statistik antrian
        Route::get('/stats', [QueueController::class, 'getQueueStats'])
            ->name('queues.stats');

        // Detail antrian
        Route::get('/{queue}', [QueueController::class, 'show'])
            ->name('queues.show');

        // Panggil antrian (penguji & admin)
        Route::post('/{queue}/call', [QueueController::class, 'callQueue'])
            ->middleware('can:call-queue,queue')
            ->name('queues.call');

        // Batalkan antrian
        Route::post('/{queue}/cancel', [QueueController::class, 'cancelQueue'])
            ->middleware('can:cancel-queue,queue')
            ->name('queues.cancel');

        // Update status antrean (penguji & admin)
        Route::put('/{queue}/status', [QueueController::class, 'updateStatus'])
            ->name('queues.update-status');
    });

    // ========== TEST RESULT ROUTES ==========
    Route::prefix('test-results')->group(function () {
        // List hasil ujian
        Route::get('/', [TestResultController::class, 'index'])
            ->name('test-results.index');

        // Create hasil ujian (hanya penguji & admin)
        Route::post('/', [TestResultController::class, 'store'])
            ->middleware('can:create-test-result')
            ->name('test-results.store');

        // Detail hasil ujian
        Route::get('/{testResult}', [TestResultController::class, 'show'])
            ->name('test-results.show');
    });

    // ========== PENGUJI STATISTICS ROUTES ==========
    Route::prefix('penguji')->group(function () {
        // Statistik harian penguji
        Route::get('/daily-stats', [TestResultController::class, 'getPengujiDailyStats'])
            ->middleware('can:view-penguji-stats')
            ->name('penguji.daily-stats');
    });

    // ========== ADMIN REPORTS ROUTES ==========
    Route::prefix('admin')->group(function () {
        // Laporan harian admin
        Route::get('/daily-report', [TestResultController::class, 'getAdminDailyReport'])
            ->middleware('can:view-admin-report')
            ->name('admin.daily-report');
    });

    // ========== TEST SCHEDULE ROUTES (ADMIN ONLY) ==========
    Route::prefix('test-schedules')->middleware('can:manage-test-schedules')->group(function () {
        // List jadwal ujian
        Route::get('/', [TestScheduleController::class, 'index'])
            ->name('test-schedules.index');

        // Create jadwal ujian
        Route::post('/', [TestScheduleController::class, 'store'])
            ->name('test-schedules.store');

        // Detail jadwal ujian
        Route::get('/{testSchedule}', [TestScheduleController::class, 'show'])
            ->name('test-schedules.show');

        // Update jadwal ujian
        Route::put('/{testSchedule}', [TestScheduleController::class, 'update'])
            ->name('test-schedules.update');

        // Delete jadwal ujian
        Route::delete('/{testSchedule}', [TestScheduleController::class, 'destroy'])
            ->name('test-schedules.destroy');
    });

    // ========== WHATSAPP CONFIG ROUTES (ADMIN ONLY) ==========
    Route::prefix('whatsapp-configs')->middleware('can:manage-whatsapp-config')->group(function () {
        // List konfigurasi
        Route::get('/', [WhatsappConfigController::class, 'index'])
            ->name('whatsapp-configs.index');

        // Create konfigurasi
        Route::post('/', [WhatsappConfigController::class, 'store'])
            ->name('whatsapp-configs.store');

        // Detail konfigurasi
        Route::get('/{whatsappConfig}', [WhatsappConfigController::class, 'show'])
            ->name('whatsapp-configs.show');

        // Update konfigurasi
        Route::put('/{whatsappConfig}', [WhatsappConfigController::class, 'update'])
            ->name('whatsapp-configs.update');

        // Delete konfigurasi
        Route::delete('/{whatsappConfig}', [WhatsappConfigController::class, 'destroy'])
            ->name('whatsapp-configs.destroy');

        // Test koneksi WhatsApp
        Route::post('/{whatsappConfig}/test', [WhatsappConfigController::class, 'testConnection'])
            ->name('whatsapp-configs.test');
    });

    // ========== USERS ROUTES (ADMIN ONLY) ==========
    Route::prefix('users')->middleware('can:manage-users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
    });

});

// ============================================================
// HEALTH CHECK ROUTE
// ============================================================

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
    ]);
});