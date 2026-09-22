<?php

namespace App\Http\Controllers;

use App\Models\TestResult;
use App\Models\Queue;
use App\Http\Requests\StoreTestResultRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TestResultController extends Controller
{
    /**
     * Get list of test results
     */
    public function index()
    {
        try {
            $query = TestResult::with(['queue', 'vehicle', 'penguji']);

            if (auth()->check() && auth()->user()->role === 'peserta') {
                $query->whereHas('queue', function($q) {
                    $q->where('user_id', auth()->id());
                });
            }

            $testResults = $query->paginate(10);

            return response()->json([
                'message' => 'Daftar hasil ujian berhasil diambil',
                'data' => $testResults
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create test result
     */
    public function store(StoreTestResultRequest $request)
    {
        try {
            if (!auth()->user() || !in_array(auth()->user()->role, ['penguji', 'admin'])) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            DB::beginTransaction();

            $queue = Queue::findOrFail($request->queue_id);

            $statuses = [
                $request->emission_status,
                $request->brake_status,
                $request->light_status,
                $request->horn_status,
                $request->suspension_status,
                $request->tire_status,
            ];

            $overallStatus = collect($statuses)->every(fn($s) => $s === 'pass') ? 'pass' : 'fail';

            // Generate SLO number (SLO-XXXXXX)
            $testNumber = 'SLO-' . mt_rand(100000, 999999);
            
            // Generate valid_until (6 months from now if passed)
            $validUntil = $overallStatus === 'pass' ? now()->addMonths(6) : null;

            $photoPath = null;
            if ($request->hasFile('vehicle_photo')) {
                $file = $request->file('vehicle_photo');
                // Validate size < 2MB manually if not in Request
                if ($file->getSize() > 2048000) {
                    return response()->json(['message' => 'Ukuran foto maksimal 2MB'], 422);
                }
                $photoPath = $file->store('vehicle_photos', 'public');
            }

            $testResult = TestResult::create([
                'test_number' => $testNumber,
                'queue_id' => $queue->id,
                'vehicle_id' => $queue->vehicle_id,
                'vehicle_photo' => $photoPath,
                'penguji_id' => auth()->id(),
                'emission_status' => $request->emission_status,
                'emission_notes' => $request->emission_notes ?? null,
                'brake_status' => $request->brake_status,
                'brake_notes' => $request->brake_notes ?? null,
                'light_status' => $request->light_status,
                'light_notes' => $request->light_notes ?? null,
                'horn_status' => $request->horn_status,
                'horn_notes' => $request->horn_notes ?? null,
                'suspension_status' => $request->suspension_status,
                'suspension_notes' => $request->suspension_notes ?? null,
                'tire_status' => $request->tire_status,
                'tire_notes' => $request->tire_notes ?? null,
                'overall_status' => $overallStatus,
                'overall_notes' => $request->overall_notes ?? null,
                'tested_at' => now(),
                'valid_until' => $validUntil,
            ]);

            // Update status antrean menjadi selesai
            $queue->update([
                'status' => 'completed'
            ]);

            DB::commit();

            // Kirim notifikasi WhatsApp (lakukan setelah commit agar data aman)
            try {
                $whatsappService = app(\App\Services\WhatsappService::class);
                $whatsappService->notifyTestResult($testResult);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mengirim pesan WA: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Hasil ujian berhasil ditambahkan',
                'data' => $testResult
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get test result detail
     */
    public function show(TestResult $testResult)
    {
        try {
            return response()->json([
                'message' => 'Detail hasil ujian berhasil diambil',
                'data' => $testResult->load(['queue', 'vehicle', 'penguji'])
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get penguji daily stats
     */
    public function getPengujiDailyStats()
    {
        try {
            if (!auth()->user() || auth()->user()->role !== 'penguji') {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            $today = now()->format('Y-m-d');
            $stats = collect(TestResult::where('penguji_id', auth()->id())
                ->whereDate('tested_at', $today)
                ->select('overall_status')
                ->get()
                ->groupBy('overall_status')
                ->map->count());

            return response()->json([
                'message' => 'Statistik harian penguji berhasil diambil',
                'data' => [
                    'date' => $today,
                    'total' => $stats->sum(),
                    'pass' => $stats->get('pass', 0),
                    'fail' => $stats->get('fail', 0),
                ]
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get admin daily report
     */
    public function getAdminDailyReport(Request $request)
    {
        try {
            if (!auth()->user() || auth()->user()->role !== 'admin') {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            $date = $request->input('date', now()->format('Y-m-d'));
            
            $queues = \App\Models\Queue::with(['vehicle.user', 'testResult.penguji'])
                        ->whereDate('queue_date', $date)
                        ->get();

            $total_queue = $queues->count();
            $waiting = $queues->where('status', 'waiting')->count();
            $in_progress = $queues->where('status', 'in_progress')->count();
            $completed = $queues->where('status', 'completed')->count();
            $cancelled = $queues->where('status', 'cancelled')->count();

            $testResults = $queues->whereNotNull('testResult');
            $pass = $testResults->where('testResult.overall_status', 'pass')->count();
            $fail = $testResults->where('testResult.overall_status', 'fail')->count();
            
            $pass_percentage = $completed > 0 ? round(($pass / $completed) * 100, 1) : 0;
            
            $active_penguji = $testResults->pluck('testResult.tested_by')->unique()->count();

            $details = $queues->map(function($q) {
                return [
                    'queue_number' => $q->queue_number,
                    'vehicle_number' => $q->vehicle->vehicle_number ?? '-',
                    'owner_name' => $q->vehicle->owner_name ?? ($q->vehicle->user->name ?? '-'),
                    'status' => $q->status,
                    'test_result' => $q->testResult->overall_status ?? '-',
                    'penguji' => $q->testResult->penguji->name ?? '-'
                ];
            });

            return response()->json([
                'message' => 'Laporan harian admin berhasil diambil',
                'data' => [
                    'date' => $date,
                    'total_queue' => $total_queue,
                    'waiting' => $waiting,
                    'in_progress' => $in_progress,
                    'total_completed' => $completed,
                    'cancelled' => $cancelled,
                    'total_passed' => $pass,
                    'total_failed' => $fail,
                    'pass_percentage' => $pass_percentage,
                    'active_penguji' => $active_penguji,
                    'details' => $details->values()
                ]
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
