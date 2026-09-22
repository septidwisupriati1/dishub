<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root ke dashboard atau login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Guest routes (hanya untuk user yang belum login)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    
    // Register
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

// Authenticated routes (hanya untuk user yang sudah login)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Logout
    Route::match(['get', 'post'], 'logout', [AuthController::class, 'logout'])->name('logout');
    
    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        // Edit profile
        Route::get('edit', [AuthController::class, 'showProfile'])->name('edit');
        Route::put('update', [AuthController::class, 'updateProfile'])->name('update');
        
        // Change password
        Route::get('change-password', [AuthController::class, 'showChangePassword'])->name('change-password');
        Route::put('change-password', [AuthController::class, 'changePassword'])->name('update-password');
    });
    
    // Web routes that redirect to API or placeholder views
    Route::get('vehicles', function(\Illuminate\Http\Request $request) {
        $user = auth()->user();
        $query = \App\Models\Vehicle::with(['user', 'testResults']);
        
        if ($user->isPeserta()) {
            $query->where('user_id', $user->id);
        }

        if ($request->search) {
            $query->where('vehicle_number', 'like', '%' . $request->search . '%');
        }
        if ($request->vehicle_type) {
            $query->where('vehicle_type', $request->vehicle_type);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $vehicles = $query->paginate(10);
        return view('vehicles.index', compact('vehicles'));
    })->name('vehicles.index');

    Route::view('vehicles/create', 'vehicles.create')->name('vehicles.create');
    
    Route::get('vehicles/{vehicle}/edit', function(\App\Models\Vehicle $vehicle) {
        return view('vehicles.edit', compact('vehicle'));
    })->name('vehicles.edit');

    Route::put('vehicles/{vehicle}', function(\Illuminate\Http\Request $request, \App\Models\Vehicle $vehicle) {
        // Validasi dan update sederhana untuk web
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,under_maintenance',
            'brand' => 'required|string',
            'model' => 'required|string',
            'year' => 'required|integer',
            'color' => 'required|string',
            'engine_number' => 'required|string',
            'chassis_number' => 'required|string',
        ]);
        
        $vehicle->update($validated);
        
        return redirect()->route('vehicles.index')->with('success', 'Status dan data kendaraan berhasil diperbarui');
    })->name('web.vehicles.update');
    
    Route::get('vehicles/{vehicle}', function(\App\Models\Vehicle $vehicle) {
        // Jika view show belum ada, arahkan kembali
        if (view()->exists('vehicles.show')) {
            return view('vehicles.show', compact('vehicle'));
        }
        return back()->with('info', 'Detail kendaraan dalam pengembangan');
    })->name('vehicles.show');

    Route::delete('vehicles/{vehicle}', function(\App\Models\Vehicle $vehicle) {
        $vehicle->delete();
        return back()->with('success', 'Kendaraan berhasil dihapus');
    })->name('vehicles.destroy');
    Route::view('queues', 'queues.index')->name('queues.index');
    Route::get('test-results/{id}/print', function($id) {
        $result = \App\Models\TestResult::with(['vehicle.user', 'penguji'])->findOrFail($id);
        return view('test-results.print', compact('result'));
    })->name('test-results.print');

    Route::view('test-results', 'test-results.index')->name('test-results.index');
    Route::view('test-results/create', 'test-results.create')->name('test-results.create');
    Route::view('test-schedules', 'test-schedules.index')->name('test-schedules.index');
    Route::view('users', 'users.index')->name('users.index');
    Route::view('reports/daily', 'reports.daily')->name('reports.daily');
    Route::view('settings/whatsapp', 'settings.whatsapp')->name('settings.whatsapp');
    Route::view('help', 'help.index')->name('help');
});