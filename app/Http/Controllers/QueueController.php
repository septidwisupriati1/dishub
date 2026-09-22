<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\TestSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\WhatsappService;

class QueueController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;

        // Auto-migrate column if not exists (Hack for environment without terminal)
        if (!\Illuminate\Support\Facades\Schema::hasColumn('queues', 'whatsapp_status')) {
            \Illuminate\Support\Facades\Schema::table('queues', function (\Illuminate\Database\Schema\Blueprint $table) {
                $table->string('whatsapp_status')->default('pending')->after('status');
            });
        }
    }

    public function index(Request $request)
    {
        try {
            $user = auth()->user();

            $query = Queue::with(['vehicle', 'testSchedule', 'user']);

            if ($request->date) {
                $query->whereDate('queue_date', $request->date);
            }

            if ($request->status) {
                $query->where('status', $request->status);
            }

            if ($request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('queue_number', 'like', "%{$search}%")
                      ->orWhereHas('vehicle', function($vq) use ($search) {
                          $vq->where('vehicle_number', 'like', "%{$search}%")
                             ->orWhere('owner_name', 'like', "%{$search}%")
                             ->orWhere('brand', 'like', "%{$search}%");
                      })
                      ->orWhereHas('user', function($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Aman dari error method tidak ada
            if ($user && method_exists($user, 'isPeserta') && $user->isPeserta()) {
                $query->where('user_id', $user->id);
            }

            $queues = $query->orderBy('queue_date')
                ->orderBy('queue_number')
                ->paginate(10);

            return response()->json([
                'message' => 'Daftar antrian berhasil diambil',
                'data' => $queues,
            ], 200);

        } catch (\Throwable $e) { // gunakan Throwable agar semua error tertangkap
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            DB::beginTransaction();

            $date = \Carbon\Carbon::parse($request->queue_date);
            $dayOfWeek = $date->format('l'); // Returns Monday, Tuesday, etc.

            if (in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
                DB::rollBack();
                return response()->json(['message' => 'Layanan pengujian tutup pada hari Sabtu dan Minggu. Silakan pilih hari kerja.'], 422);
            }

            $schedule = TestSchedule::firstOrCreate(
                ['test_date' => $request->queue_date],
                [
                    'day_of_week' => $dayOfWeek,
                    'max_queue' => 50,
                    'current_queue' => 0,
                    'status' => 'open'
                ]
            );

            // Validasi jadwal penuh (penting untuk test)
            if ($schedule->current_queue >= $schedule->max_queue) {
                DB::rollBack();
                return response()->json(['message' => 'Jadwal penuh'], 422);
            }

            if (!$request->vehicle_number) {
                DB::rollBack();
                return response()->json(['message' => 'Nomor plat kendaraan wajib diisi'], 422);
            }

            // Validasi: Cek apakah kendaraan ini sedang dalam antrean aktif
            $vehicleNumber = strtoupper(trim($request->vehicle_number));
            $existingVehicle = \App\Models\Vehicle::where('vehicle_number', $vehicleNumber)->first();
            
            if ($existingVehicle) {
                $activeQueue = Queue::where('vehicle_id', $existingVehicle->id)
                    ->whereIn('status', ['waiting', 'in_progress'])
                    ->first();
                    
                if ($activeQueue) {
                    DB::rollBack();
                    return response()->json(['message' => 'Kendaraan ini sedang dalam antrean aktif dan belum selesai diuji.'], 422);
                }
            }

            // Validasi Nomor Mesin dan Nomor Rangka agar tidak duplikat
            if ($request->engine_number) {
                $checkEngine = \App\Models\Vehicle::where('engine_number', $request->engine_number)
                    ->where('vehicle_number', '!=', $vehicleNumber)
                    ->first();
                if ($checkEngine) {
                    DB::rollBack();
                    return response()->json(['message' => 'Nomor Mesin ini sudah terdaftar pada kendaraan lain (' . $checkEngine->vehicle_number . '). Silakan periksa kembali.'], 422);
                }
            }

            if ($request->chassis_number) {
                $checkChassis = \App\Models\Vehicle::where('chassis_number', $request->chassis_number)
                    ->where('vehicle_number', '!=', $vehicleNumber)
                    ->first();
                if ($checkChassis) {
                    DB::rollBack();
                    return response()->json(['message' => 'Nomor Rangka ini sudah terdaftar pada kendaraan lain (' . $checkChassis->vehicle_number . '). Silakan periksa kembali.'], 422);
                }
            }

            // Update or create vehicle based on typed vehicle_number with full details
            $vehicle = \App\Models\Vehicle::updateOrCreate(
                ['vehicle_number' => $vehicleNumber],
                [
                    'user_id' => $user->id,
                    'owner_name' => $request->owner_name ?? $user->name,
                    'address' => $request->address ?? '-',
                    'vehicle_type' => $request->vehicle_type ?? 'car',
                    'brand' => $request->brand ?? 'Belum Diisi',
                    'model' => $request->model ?? 'Belum Diisi',
                    'usage_type' => $request->usage_type ?? 'umum',
                    'fuel_type' => $request->fuel_type ?? 'bensin',
                    'year' => $request->year ?? date('Y'),
                    'color' => $request->color ?? 'Belum Diisi',
                    'engine_number' => $request->engine_number ?? ('ENG' . uniqid()),
                    'chassis_number' => $request->chassis_number ?? ('CHS' . uniqid()),
                    'status' => 'active'
                ]
            );

            $queueNumber = $schedule->current_queue + 1;

            $queue = Queue::create([
                'vehicle_id' => $vehicle->id,
                'user_id' => $user->id,
                'test_schedule_id' => $schedule->id,
                'queue_date' => $request->queue_date,
                'queue_number' => $queueNumber,
                'status' => 'waiting',
            ]);

            $schedule->increment('current_queue');

            DB::commit();

            // Send WhatsApp Notification using Fonnte (via WhatsappService)
            $queue->load(['user', 'vehicle']); // load relations needed for notification
            
            // Bungkus dalam try-catch agar kegagalan Fonnte tidak merusak apa pun
            try {
                $this->whatsappService->notifyQueueCreated($queue, $request->whatsapp_number);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Fonnte failed: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Antrian berhasil dibuat',
                'data' => [
                    'id' => $queue->id,
                    'queue_number' => $queue->formatted_queue_number,
                    'status' => $queue->status,
                ]
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function callQueue(Queue $queue)
    {
        try {
            $user = auth()->user();

            // Aman jika method tidak ada
            if (!$user || !method_exists($user, 'isPenguji') || !$user->isPenguji()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            if ($queue->status !== 'waiting') {
                return response()->json(['message' => 'Invalid status'], 422);
            }

            $queue->update([
                'status' => 'in_progress',
            ]);

            return response()->json([
                'message' => 'Antrian berhasil dipanggil'
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancelQueue(Queue $queue)
    {
        try {
            $user = auth()->user();

            if (!$user || $user->id !== $queue->user_id) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            if ($queue->status !== 'waiting') {
                return response()->json(['message' => 'Invalid status'], 422);
            }

            DB::beginTransaction();

            $queue->update([
                'status' => 'cancelled'
            ]);

            // Aman jika relasi null
            if ($queue->testSchedule) {
                $queue->testSchedule->decrement('current_queue');
            }

            DB::commit();

            return response()->json([
                'message' => 'Antrian berhasil dibatalkan'
            ], 200);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getQueueStats(Request $request)
    {
        try {
            $date = $request->date ?? now()->toDateString();

            $schedule = TestSchedule::where('test_date', $date)->first();

            $total = Queue::whereDate('queue_date', $date)->count();

            return response()->json([
                'message' => 'Statistik antrian berhasil diambil',
                'data' => [
                    'total_queue'          => $total,
                    'waiting'              => Queue::whereDate('queue_date', $date)->where('status', 'waiting')->count(),
                    'in_progress'          => Queue::whereDate('queue_date', $date)->where('status', 'in_progress')->count(),
                    'completed'            => Queue::whereDate('queue_date', $date)->where('status', 'completed')->count(),
                    'cancelled'            => Queue::whereDate('queue_date', $date)->where('status', 'cancelled')->count(),
                    'available_slots'      => $schedule ? ($schedule->max_queue - $schedule->current_queue) : 0,
                    'occupancy_percentage' => $schedule && $schedule->max_queue > 0
                        ? round(($schedule->current_queue / $schedule->max_queue) * 100, 2)
                        : 0,
                    'schedule' => $schedule,
                ]
            ], 200);

        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Penguji: ubah status antrean
     */
    public function updateStatus(Request $request, Queue $queue)
    {
        try {
            $user = auth()->user();

            if (!$user || !method_exists($user, 'isPenguji') || (!$user->isPenguji() && !$user->isAdmin())) {
                return response()->json(['message' => 'Forbidden: hanya penguji/admin'], 403);
            }

            $validStatuses = ['waiting', 'in_progress', 'completed', 'cancelled'];

            if (!in_array($request->status, $validStatuses)) {
                return response()->json(['message' => 'Status tidak valid'], 422);
            }

            $queue->update(['status' => $request->status]);

            return response()->json([
                'message' => 'Status antrian berhasil diperbarui',
                'data'    => ['id' => $queue->id, 'status' => $queue->status]
            ], 200);

        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}