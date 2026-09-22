@extends('layouts.app')

@section('styles')
<style>
    .kpi-card {
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-left: 4px solid #1C4D8D;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .kpi-value {
        font-size: 2.25rem;
        font-weight: 800;
        color: #0F2854;
        line-height: 1.2;
    }
    .kpi-title {
        font-size: 0.875rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    .kpi-trend-up {
        color: #10B981;
        background-color: #D1FAE5;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }
    .kpi-trend-neutral {
        color: #6B7280;
        background-color: #F3F4F6;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }
    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #0F2854;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }
    .section-title::before {
        content: '';
        display: inline-block;
        width: 8px;
        height: 24px;
        background-color: #1C4D8D;
        margin-right: 0.75rem;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')
<!-- Header Area -->
<div class="mb-8 bg-gradient-to-r from-[#0F2854] to-[#1C4D8D] rounded-xl p-8 text-white shadow-xl relative overflow-hidden">
    <div class="absolute top-1/2 right-0 transform -translate-y-1/2 translate-x-[20%] pointer-events-none">
        <img src="{{ asset('images/logo-dishub.png') }}" class="w-56 h-56 sm:w-64 sm:h-64 object-contain filter brightness-0 invert opacity-20" alt="Watermark">
    </div>
    <div class="relative z-10">
        <div class="inline-block px-3 py-1 bg-white/20 rounded-full text-xs font-semibold tracking-widest uppercase mb-3 backdrop-blur-sm border border-white/30">
            KPI Dashboard
        </div>
        <h1 class="text-3xl font-bold mb-2">Sistem Informasi Antrean KIR</h1>
        <p class="text-blue-100 font-medium">
            @if(auth()->user()->isPeserta())
                Kendaraan — Pengujian Berkala — Portal Peserta
            @elseif(auth()->user()->isPenguji())
                Kendaraan — Pengujian Berkala — Portal Penguji
            @else
                Kendaraan — Pengujian Berkala — Portal Admin
            @endif
        </p>
    </div>
</div>

<!-- KPI Section -->
<div class="mb-10">
    <h2 class="section-title">Ringkasan KPI Utama</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        @if(auth()->user()->isPeserta())
            <!-- Total Vehicles -->
            <div class="kpi-card border-l-blue-500">
                <div class="flex justify-between items-start mb-2">
                    <p class="kpi-title"><i class="fas fa-car mr-2 text-blue-500"></i>Total Kendaraan</p>
                </div>
                <p class="kpi-value">{{ auth()->user()->vehicles()->count() }}</p>
                <div class="mt-4">
                    <span class="text-xs text-gray-500 block mb-1">Target: ≥ 1 Kendaraan</span>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: 100%"></div>
                    </div>
                    <span class="kpi-trend-up"><i class="fas fa-check mr-1"></i> Tercapai</span>
                </div>
            </div>

            <!-- Active Queues -->
            <div class="kpi-card border-l-yellow-500">
                <div class="flex justify-between items-start mb-2">
                    <p class="kpi-title"><i class="fas fa-stopwatch mr-2 text-yellow-500"></i>Antrean Aktif</p>
                </div>
                <p class="kpi-value">{{ auth()->user()->queues()->where('status', '!=', 'completed')->where('status', '!=', 'cancelled')->count() }}</p>
                <div class="mt-4">
                    <span class="text-xs text-gray-500 block mb-1">Status saat ini</span>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                        <div class="bg-yellow-500 h-1.5 rounded-full" style="width: 50%"></div>
                    </div>
                    <a href="{{ route('queues.index') }}" class="kpi-trend-neutral hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-right mr-1"></i> Lihat Antrean
                    </a>
                </div>
            </div>

            <!-- Test Passed -->
            <div class="kpi-card border-l-green-500">
                <div class="flex justify-between items-start mb-2">
                    <p class="kpi-title"><i class="fas fa-clipboard-check mr-2 text-green-500"></i>Ujian Lulus</p>
                </div>
                <p class="kpi-value">{{ auth()->user()->vehicles()->sum('test_count') }}</p>
                <div class="mt-4">
                    <span class="text-xs text-gray-500 block mb-1">Total riwayat kelulusan</span>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                        <div class="bg-green-500 h-1.5 rounded-full" style="width: 80%"></div>
                    </div>
                    <span class="kpi-trend-up"><i class="fas fa-check mr-1"></i> Tercapai</span>
                </div>
            </div>

            <!-- Last Test -->
            <div class="kpi-card border-l-purple-500">
                <div class="flex justify-between items-start mb-2">
                    <p class="kpi-title"><i class="fas fa-calendar-alt mr-2 text-purple-500"></i>Ujian Terakhir</p>
                </div>
                @php
                    $lastTest = auth()->user()->vehicles()
                        ->with('testResults')
                        ->get()
                        ->pluck('testResults')
                        ->flatten()
                        ->sortByDesc('tested_at')
                        ->first();
                @endphp
                <p class="text-2xl font-bold text-[#0F2854] mt-1 mb-2">{{ $lastTest ? $lastTest->tested_at->format('d/m/Y') : '-' }}</p>
                <div class="mt-4">
                    <span class="text-xs text-gray-500 block mb-1">Pembaruan data otomatis</span>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                        <div class="bg-purple-500 h-1.5 rounded-full" style="width: 100%"></div>
                    </div>
                    <span class="kpi-trend-neutral"><i class="fas fa-clock mr-1"></i> Up to date</span>
                </div>
            </div>
        @endif

        @if(auth()->user()->isPenguji())
            <!-- Total Tested -->
            <div class="kpi-card border-l-blue-500">
                <div class="flex justify-between items-start mb-2">
                    <p class="kpi-title"><i class="fas fa-clipboard-check mr-2 text-blue-500"></i>Pengujian Selesai</p>
                </div>
                <p class="kpi-value" id="pengujiTotalTested">...</p>
                <div class="mt-4">
                    <span class="text-xs text-gray-500 block mb-1">Kinerja hari ini</span>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: 70%"></div>
                    </div>
                    <span class="kpi-trend-up"><i class="fas fa-check mr-1"></i> Sesuai Target</span>
                </div>
            </div>

            <!-- Pending Queues -->
            <div class="kpi-card border-l-yellow-500">
                <div class="flex justify-between items-start mb-2">
                    <p class="kpi-title"><i class="fas fa-users mr-2 text-yellow-500"></i>Antrean Menunggu</p>
                </div>
                <p class="kpi-value" id="pengujiAntrianMenunggu">...</p>
                <div class="mt-4">
                    <span class="text-xs text-gray-500 block mb-1">Sisa pekerjaan</span>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                        <div class="bg-yellow-500 h-1.5 rounded-full" style="width: 40%"></div>
                    </div>
                    <span class="kpi-trend-neutral bg-yellow-100 text-yellow-700"><i class="fas fa-exclamation-triangle mr-1"></i> Perlu Perhatian</span>
                </div>
            </div>
        @endif

        @if(auth()->user()->isAdmin())
            <!-- Total Users -->
            <div class="kpi-card border-l-purple-500">
                <div class="flex justify-between items-start mb-2">
                    <p class="kpi-title"><i class="fas fa-users mr-2 text-purple-500"></i>Total Pengguna</p>
                </div>
                <p class="kpi-value">{{ \App\Models\User::count() }}</p>
                <div class="mt-4">
                    <span class="text-xs text-gray-500 block mb-1">Adopsi Digital</span>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                        <div class="bg-purple-500 h-1.5 rounded-full" style="width: 85%"></div>
                    </div>
                    <span class="kpi-trend-up"><i class="fas fa-check mr-1"></i> Tercapai</span>
                </div>
            </div>

            <!-- Today's Queues -->
            <div class="kpi-card border-l-blue-500">
                <div class="flex justify-between items-start mb-2">
                    <p class="kpi-title"><i class="fas fa-list-ol mr-2 text-blue-500"></i>Antrean Hari Ini</p>
                </div>
                <p class="kpi-value" id="adminAntrianHariIni">...</p>
                <div class="mt-4">
                    <span class="text-xs text-gray-500 block mb-1">Volume kendaraan harian</span>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                        <div class="bg-blue-500 h-1.5 rounded-full" style="width: 65%"></div>
                    </div>
                    <span class="kpi-trend-neutral"><i class="fas fa-chart-line mr-1"></i> Normal</span>
                </div>
            </div>
        @endif
    </div>
</div>

@if(auth()->user()->isPeserta())
<!-- NOMOR ANTREAN AKTIF PESERTA -->
@php $activeQueues = $chartData['activeQueues'] ?? collect(); @endphp
<div class="mb-10">
    <h2 class="section-title">Antrean Aktif Anda</h2>
    
    @if($activeQueues->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($activeQueues as $queue)
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden flex flex-col relative group">
                <!-- Status Header -->
                <div class="py-3 px-5 border-b border-gray-100 flex justify-between items-center {{ $queue->status === 'in_progress' ? 'bg-blue-50' : 'bg-yellow-50' }}">
                    <span class="text-xs font-bold uppercase tracking-widest {{ $queue->status === 'in_progress' ? 'text-blue-700' : 'text-yellow-700' }}">
                        <i class="fas {{ $queue->status === 'in_progress' ? 'fa-cog fa-spin' : 'fa-hourglass-half' }} mr-1"></i>
                        {{ $queue->status === 'in_progress' ? 'Sedang Diproses' : 'Menunggu Dipanggil' }}
                    </span>
                    
                    <!-- Delete/Cancel Button -->
                    @if($queue->status === 'waiting')
                        <button onclick="cancelQueue({{ $queue->id }})" class="text-red-500 hover:text-red-700 transition-colors p-1" title="Batalkan Antrean">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    @endif
                </div>

                <!-- Number Display -->
                <div class="p-8 text-center flex-grow flex flex-col justify-center items-center">
                    <div class="text-7xl font-black text-[#0F2854] mb-2 leading-none">
                        {{ $queue->queue_number }}
                    </div>
                    <div class="inline-block mt-2 px-4 py-1.5 bg-gray-100 rounded-full text-sm font-bold text-gray-700 border border-gray-200">
                        <i class="fas fa-car text-gray-500 mr-2"></i>{{ $queue->vehicle?->vehicle_number ?? '-' }}
                    </div>
                </div>

                <!-- Footer info -->
                <div class="bg-gray-50 py-3 px-5 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500 font-medium">
                    <span>
                        <i class="far fa-calendar-alt mr-1"></i>
                        {{ \Carbon\Carbon::parse($queue->queue_date)->translatedFormat('d M Y') }}
                    </span>
                    <a href="{{ route('queues.index') }}" class="text-[#1C4D8D] hover:underline font-bold">
                        Detail <i class="fas fa-chevron-right ml-1 text-[10px]"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="bg-white border border-dashed border-gray-300 rounded-xl p-10 flex flex-col items-center text-center shadow-sm">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                <i class="fas fa-ticket-alt text-2xl text-gray-400"></i>
            </div>
            <p class="text-gray-800 font-bold text-lg mb-1">Belum Ada Antrean Aktif</p>
            <p class="text-gray-500 text-sm mb-6 max-w-sm">Anda belum mengambil nomor antrean saat ini. Silakan daftar antrean untuk melakukan pengujian kendaraan.</p>
            <a href="{{ route('queues.index') }}"
                class="bg-[#1C4D8D] hover:bg-[#0F2854] text-white text-sm font-semibold px-6 py-2.5 rounded-lg shadow-md transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i> Ambil Antrean Sekarang
            </a>
        </div>
    @endif
</div>
@endif

<!-- VISUALIZATION SECTION -->
<div class="mb-10">
    <h2 class="section-title">Visualisasi Data</h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        @if(auth()->user()->isPeserta())
            <!-- CHARTS FOR PESERTA -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-6">Statistik Antrean Anda</h3>
                <div class="relative h-64">
                    <canvas id="chartPesertaQueue"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-6">Riwayat Hasil Ujian</h3>
                <div class="relative h-64">
                    <canvas id="chartPesertaHistory"></canvas>
                </div>
            </div>
        @endif

        @if(auth()->user()->isPenguji() || auth()->user()->isAdmin())
            <!-- CHARTS FOR ADMIN/PENGUJI -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-6">Distribusi Status Antrean Hari Ini</h3>
                <div class="relative h-64 flex items-center justify-center">
                    <canvas id="chartQueueStatus"></canvas>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider mb-6">Keseluruhan Hasil Ujian Hari Ini</h3>
                <div class="relative h-64 flex items-center justify-center">
                    <canvas id="chartTestResults"></canvas>
                </div>
            </div>
        @endif

    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const isPeserta = {{ auth()->user()->isPeserta() ? 'true' : 'false' }};
    const isPenguji = {{ auth()->user()->isPenguji() ? 'true' : 'false' }};
    const isAdmin = {{ auth()->user()->isAdmin() ? 'true' : 'false' }};
    const chartData = {!! json_encode($chartData) !!};

    const api = axios.create({
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        withCredentials: true
    });

    // Handle Cancel Queue
    window.cancelQueue = function(queueId) {
        Notiflix.Confirm.show(
            'Konfirmasi Pembatalan',
            'Apakah Anda yakin ingin membatalkan antrean ini? Aksi ini tidak dapat dikembalikan.',
            'Ya, Batalkan',
            'Tidak',
            async function okCb() {
                Notiflix.Block.standard('body', 'Memproses...');
                try {
                    await api.post(`/api/queues/${queueId}/cancel`);
                    Notiflix.Block.remove('body');
                    Notiflix.Report.success(
                        'Berhasil',
                        'Antrean berhasil dibatalkan.',
                        'OK',
                        () => location.reload()
                    );
                } catch (e) {
                    Notiflix.Block.remove('body');
                    console.error(e);
                    Notiflix.Report.failure(
                        'Gagal',
                        e.response?.data?.message || 'Gagal membatalkan antrean.',
                        'Tutup'
                    );
                }
            },
            function cancelCb() {},
            {
                width: '320px',
                borderRadius: '8px',
                titleColor: '#0F2854',
                okButtonBackground: '#EF4444',
                cancelButtonBackground: '#9CA3AF'
            }
        );
    };

    // Fetch and populate top cards data for Penguji and Admin
    try {
        if (isPenguji) {
            const resStats = await api.get('/api/penguji/daily-stats');
            if (resStats.data && resStats.data.data) {
                document.getElementById('pengujiTotalTested').innerText = resStats.data.data.total_tested;
            }
            
            const resQueues = await api.get('/api/queues/stats');
            if (resQueues.data && resQueues.data.data) {
                document.getElementById('pengujiAntrianMenunggu').innerText = resQueues.data.data.waiting;
            }
        }

        if (isAdmin) {
            const resReport = await api.get('/api/admin/daily-report');
            if (resReport.data && resReport.data.data) {
                document.getElementById('adminAntrianHariIni').innerText = resReport.data.data.total_queue;
            }
        }
    } catch (e) {
        console.error('Error fetching dashboard stats:', e);
    }

    // Chart.js Configuration
    Chart.defaults.font.family = "'Roboto Slab', serif";
    Chart.defaults.color = '#6b7280';
    
    const chartOptionsDoughnut = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'right', labels: { usePointStyle: true, padding: 20 } }
        },
        cutout: '75%',
        borderWidth: 0
    };

    // RENDER CHARTS
    if (isAdmin || isPenguji) {
        // Queue Status Doughnut Chart
        if (document.getElementById('chartQueueStatus')) {
            new Chart(document.getElementById('chartQueueStatus'), {
                type: 'doughnut',
                data: {
                    labels: ['Menunggu', 'Diproses', 'Selesai', 'Batal'],
                    datasets: [{
                        data: [
                            chartData.queueStatus?.waiting || 0,
                            chartData.queueStatus?.in_progress || 0,
                            chartData.queueStatus?.completed || 0,
                            chartData.queueStatus?.cancelled || 0
                        ],
                        backgroundColor: ['#F59E0B', '#3B82F6', '#10B981', '#EF4444'],
                        hoverOffset: 4
                    }]
                },
                options: chartOptionsDoughnut
            });
        }

        // Test Results Pie Chart
        if (document.getElementById('chartTestResults')) {
            new Chart(document.getElementById('chartTestResults'), {
                type: 'doughnut',
                data: {
                    labels: ['Lulus Ujian', 'Tidak Lulus'],
                    datasets: [{
                        data: [
                            chartData.testResults?.pass || 0,
                            chartData.testResults?.fail || 0
                        ],
                        backgroundColor: ['#10B981', '#EF4444'],
                        hoverOffset: 4
                    }]
                },
                options: chartOptionsDoughnut
            });
        }
    }

    if (isPeserta) {
        // Peserta Queue Status Bar Chart
        if (document.getElementById('chartPesertaQueue')) {
            new Chart(document.getElementById('chartPesertaQueue'), {
                type: 'bar',
                data: {
                    labels: ['Menunggu', 'Diproses', 'Selesai', 'Batal'],
                    datasets: [{
                        label: 'Jumlah Kendaraan',
                        data: [
                            chartData.todayQueues?.waiting || 0,
                            chartData.todayQueues?.in_progress || 0,
                            chartData.todayQueues?.completed || 0,
                            chartData.todayQueues?.cancelled || 0
                        ],
                        backgroundColor: ['#F59E0B', '#3B82F6', '#10B981', '#EF4444'],
                        borderRadius: 6,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { borderDash: [4, 4] } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // Peserta History Doughnut Chart
        if (document.getElementById('chartPesertaHistory')) {
            new Chart(document.getElementById('chartPesertaHistory'), {
                type: 'doughnut',
                data: {
                    labels: ['Lulus Ujian', 'Gagal Ujian'],
                    datasets: [{
                        data: [
                            chartData.testHistory?.pass || 0,
                            chartData.testHistory?.fail || 0
                        ],
                        backgroundColor: ['#10B981', '#EF4444'],
                        hoverOffset: 4
                    }]
                },
                options: chartOptionsDoughnut
            });
        }
    }
});
</script>
@endsection