<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use App\Models\TestResult;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show dashboard
     */
    public function index()
    {
        $user = auth()->user();
        $chartData = [];

        $today = Carbon::today();

        if ($user->isAdmin()) {
            // Admin Charts Data
            $chartData['queueStatus'] = [
                'waiting' => Queue::whereDate('queue_date', $today)->where('status', 'waiting')->count(),
                'in_progress' => Queue::whereDate('queue_date', $today)->where('status', 'in_progress')->count(),
                'completed' => Queue::whereDate('queue_date', $today)->where('status', 'completed')->count(),
                'cancelled' => Queue::whereDate('queue_date', $today)->where('status', 'cancelled')->count(),
            ];

            $chartData['testResults'] = [
                'pass' => TestResult::whereDate('tested_at', $today)->where('overall_status', 'pass')->count(),
                'fail' => TestResult::whereDate('tested_at', $today)->where('overall_status', 'fail')->count(),
            ];
        } elseif ($user->isPenguji()) {
            // Penguji Charts Data
            $chartData['queueStatus'] = [
                'waiting' => Queue::whereDate('queue_date', $today)->where('status', 'waiting')->count(),
                'in_progress' => Queue::whereDate('queue_date', $today)->where('status', 'in_progress')->count(),
                'completed' => Queue::whereDate('queue_date', $today)->where('status', 'completed')->count(),
            ];

            $chartData['testResults'] = [
                'pass' => TestResult::where('penguji_id', $user->id)->whereDate('tested_at', $today)->where('overall_status', 'pass')->count(),
                'fail' => TestResult::where('penguji_id', $user->id)->whereDate('tested_at', $today)->where('overall_status', 'fail')->count(),
            ];
        } else {
            // Peserta Charts Data
            $allResults = TestResult::whereHas('queue', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->get();

            $chartData['testHistory'] = [
                'pass' => $allResults->where('overall_status', 'pass')->count(),
                'fail' => $allResults->where('overall_status', 'fail')->count(),
            ];

            $chartData['todayQueues'] = [
                'waiting'     => Queue::where('user_id', $user->id)->where('status', 'waiting')->count(),
                'in_progress' => Queue::where('user_id', $user->id)->where('status', 'in_progress')->count(),
                'completed'   => Queue::where('user_id', $user->id)->where('status', 'completed')->count(),
                'cancelled'   => Queue::where('user_id', $user->id)->where('status', 'cancelled')->count(),
            ];

            // Ambil antrean aktif (waiting atau in_progress) milik peserta
            $activeQueues = Queue::with('vehicle')
                ->where('user_id', $user->id)
                ->whereIn('status', ['waiting', 'in_progress'])
                ->orderBy('queue_date', 'asc')
                ->orderBy('queue_number', 'asc')
                ->get();

            $chartData['activeQueues'] = $activeQueues;
        }

        return view('dashboard', compact('chartData'));
    }
}