<?php

namespace App\Http\Controllers;

use App\Models\TestSchedule;
use App\Http\Requests\StoreTestScheduleRequest;
use App\Http\Requests\UpdateTestScheduleRequest;
use Illuminate\Http\Request;

class TestScheduleController extends Controller
{
    /**
     * Middleware untuk hanya admin
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'message' => 'Hanya admin yang dapat mengakses jadwal ujian',
                ], 403);
            }
            return $next($request);
        });
    }

    /**
     * Tampilkan semua jadwal ujian
     */
    public function index(Request $request)
    {
        try {
            $query = TestSchedule::query();

            // Filter by date range
            if ($request->start_date && $request->end_date) {
                $query->whereBetween('test_date', [$request->start_date, $request->end_date]);
            }

            // Filter by status
            if ($request->status) {
                $query->where('status', $request->status);
            }

            $schedules = $query->orderBy('test_date')
                              ->paginate($request->per_page ?? 10);

            return response()->json([
                'message' => 'Daftar jadwal ujian berhasil diambil',
                'data' => $schedules,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buat jadwal ujian baru
     */
    public function store(StoreTestScheduleRequest $request)
    {
        try {
            $schedule = TestSchedule::create([
                'test_date' => $request->test_date,
                'day_of_week' => \Carbon\Carbon::createFromFormat('Y-m-d', $request->test_date)->englishDayOfWeek,
                'max_queue' => $request->max_queue,
                'status' => 'open',
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'notes' => $request->notes ?? null,
            ]);

            return response()->json([
                'message' => 'Jadwal ujian berhasil ditambahkan',
                'data' => $schedule,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Tambah jadwal ujian gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Tampilkan detail jadwal ujian
     */
    public function show(TestSchedule $testSchedule)
    {
        try {
            $testSchedule->load('queues');

            return response()->json([
                'message' => 'Detail jadwal ujian berhasil diambil',
                'data' => $testSchedule,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update jadwal ujian
     */
    public function update(UpdateTestScheduleRequest $request, TestSchedule $testSchedule)
    {
        try {
            $testSchedule->update($request->validated());

            return response()->json([
                'message' => 'Jadwal ujian berhasil diperbarui',
                'data' => $testSchedule,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Update jadwal ujian gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus jadwal ujian
     */
    public function destroy(TestSchedule $testSchedule)
    {
        try {
            // Cek apakah sudah ada antrian
            if ($testSchedule->queues()->exists()) {
                return response()->json([
                    'message' => 'Tidak bisa menghapus jadwal yang sudah memiliki antrian',
                ], 422);
            }

            $testSchedule->delete();

            return response()->json([
                'message' => 'Jadwal ujian berhasil dihapus',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hapus jadwal ujian gagal: ' . $e->getMessage(),
            ], 500);
        }
    }
}