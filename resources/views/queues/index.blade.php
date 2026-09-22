@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Antrian Ujian Emisi</h1>
            <p class="text-gray-500 mt-1">Kelola dan pantau antrian kendaraan yang akan diuji.</p>
        </div>
        <button onclick="document.getElementById('createQueueModal').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg shadow-md transition-all duration-300 transform hover:scale-105 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Ambil Antrian
        </button>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8" id="statsContainer">
        <!-- Stats will be populated by JS -->
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-700">Daftar Antrian Hari Ini</h2>
            <input type="date" id="filterDate" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" value="{{ date('Y-m-d') }}">
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-4 px-6 font-medium">No. Antrian</th>
                        <th class="py-4 px-6 font-medium">Plat Motor / Kendaraan</th>
                        <th class="py-4 px-6 font-medium">Pemilik</th>
                        <th class="py-4 px-6 font-medium">Status Antrean</th>
                        <th class="py-4 px-6 font-medium">Status WA</th>
                        <th class="py-4 px-6 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="queuesTableBody" class="divide-y divide-gray-100 text-gray-700">
                    <!-- Data will be populated by JS -->
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400">
                            Memuat data antrian...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Create Queue -->
<div id="createQueueModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl transform transition-all p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-5 sticky top-0 bg-white pb-2 border-b border-gray-100 z-10">
            <h3 class="text-xl font-bold text-gray-800">Ambil Antrian Baru & Data Kendaraan</h3>
            <button onclick="document.getElementById('createQueueModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <form id="createQueueForm" class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Kendaraan (Plat Nomor) *</label>
                    <input type="text" id="vehicleNumber" required placeholder="Contoh: B 1234 XYZ" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border uppercase">
                    <p class="text-xs text-gray-500 mt-1">Ketik nomor plat kendaraan Anda (otomatis didaftarkan jika belum ada).</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik</label>
                    <input type="text" id="ownerName" placeholder="Nama Sesuai STNK" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Pemilik</label>
                    <input type="text" id="address" placeholder="Alamat Sesuai STNK" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Merek Kendaraan</label>
                    <input type="text" id="brand" placeholder="Contoh: Toyota, Honda" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kendaraan</label>
                    <select id="vehicleType" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border bg-white">
                        <option value="car">Mobil (Pribadi/Penumpang)</option>
                        <option value="truck">Truk / Pick Up / Mobil Barang</option>
                        <option value="bus">Bus / Minibus</option>
                        <option value="motorcycle">Sepeda Motor</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Pembuatan</label>
                    <input type="number" id="year" placeholder="Contoh: 2020" min="1950" max="{{ date('Y') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warna Kendaraan</label>
                    <input type="text" id="color" placeholder="Contoh: Hitam" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rangka (Chasis)</label>
                    <input type="text" id="chassisNumber" placeholder="Sesuai STNK" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Mesin</label>
                    <input type="text" id="engineNumber" placeholder="Sesuai STNK" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sifat Penggunaan</label>
                    <select id="usageType" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border bg-white">
                        <option value="umum">Umum</option>
                        <option value="tidak umum">Tidak Umum</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bahan Bakar</label>
                    <select id="fuelType" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border bg-white">
                        <option value="bensin">Bensin</option>
                        <option value="solar">Solar</option>
                        <option value="listrik">Listrik</option>
                    </select>
                </div>
                
                <div class="md:col-span-2 pt-2 border-t border-gray-100"></div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp Aktif *</label>
                    <input type="text" id="whatsappNumber" required placeholder="Contoh: 08123456789" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border">
                    <p class="text-xs text-gray-500 mt-1">Untuk notifikasi antrean.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Ujian *</label>
                    <input type="date" id="queueDate" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border">
                </div>
            </div>
            
            <div class="pt-5 mt-4 flex gap-3 sticky bottom-0 bg-white pb-2 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('createQueueModal').classList.add('hidden')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2.5 px-4 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-4 rounded-lg transition-colors shadow-md">
                    Daftar Antrian
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('queuesTableBody');
    const filterDate = document.getElementById('filterDate');
    const statsContainer = document.getElementById('statsContainer');
    const createForm = document.getElementById('createQueueForm');
    const userRole = '{{ auth()->user()->role ?? "peserta" }}';
    const isPenguji = userRole === 'penguji' || userRole === 'admin';
    
    // Setup Axios to use Sanctum CSRF and allow credentials
    // Note: This assumes axios is globally available as is typical in Laravel.
    // Alternatively, use fetch with similar headers.
    const api = axios.create({
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        withCredentials: true
    });

    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = urlParams.get('search');
    
    if (searchQuery) {
        filterDate.value = ''; // Clear date filter if searching globally
    }

    // Load data initially
    loadQueues();
    loadStats();

    filterDate.addEventListener('change', () => {
        if (searchQuery && filterDate.value) {
            // If they pick a date while searching, maybe clear the search from URL?
            // Actually, let's just let it be. They can clear the URL manually or we just keep search.
        }
        loadQueues();
        loadStats();
    });

    createForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const btn = createForm.querySelector('button[type="submit"]');
            btn.innerHTML = 'Menyimpan...';
            btn.disabled = true;

            const payload = {
                vehicle_number: document.getElementById('vehicleNumber').value,
                owner_name: document.getElementById('ownerName').value,
                address: document.getElementById('address').value,
                brand: document.getElementById('brand').value,
                vehicle_type: document.getElementById('vehicleType').value,
                year: document.getElementById('year').value,
                color: document.getElementById('color').value,
                chassis_number: document.getElementById('chassisNumber').value,
                engine_number: document.getElementById('engineNumber').value,
                usage_type: document.getElementById('usageType').value,
                fuel_type: document.getElementById('fuelType').value,
                whatsapp_number: document.getElementById('whatsappNumber').value,
                queue_date: document.getElementById('queueDate').value
            };

            const response = await api.post('/api/queues', payload);
            
            alert('Antrian berhasil diambil! Cek WhatsApp Anda untuk notifikasi (jika Fonnte aktif).');
            document.getElementById('createQueueModal').classList.add('hidden');
            
            // Set filterDate ke tanggal yang baru diambil agar langsung tampil
            filterDate.value = payload.queue_date;
            
            loadQueues();
            loadStats();
        } catch (error) {
            console.error(error);
            alert(error.response?.data?.message || 'Terjadi kesalahan saat mengambil antrian.');
        } finally {
            const btn = createForm.querySelector('button[type="submit"]');
            btn.innerHTML = 'Simpan Antrian';
            btn.disabled = false;
        }
    });

    async function loadQueues() {
        try {
            tableBody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-gray-500">Memuat data...</td></tr>';
            
            let apiUrl = '/api/queues?';
            if (filterDate.value) {
                apiUrl += `date=${filterDate.value}&`;
            }
            if (searchQuery) {
                apiUrl += `search=${encodeURIComponent(searchQuery)}&`;
            }
            
            const response = await api.get(apiUrl);
            const queues = response.data.data.data || response.data.data;
            
            if (!queues || queues.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 text-gray-300 mb-3 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            Belum ada antrian pada tanggal ini.
                        </td>
                    </tr>
                `;
                return;
            }

            tableBody.innerHTML = queues.map(queue => {
                let statusBadge = '';
                switch(queue.status) {
                    case 'waiting': statusBadge = '<span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Menunggu</span>'; break;
                    case 'in_progress': statusBadge = '<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">Diproses</span>'; break;
                    case 'completed': statusBadge = '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Selesai</span>'; break;
                    case 'cancelled': statusBadge = '<span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Batal</span>'; break;
                }

                let waBadge = '';
                switch(queue.whatsapp_status) {
                    case 'success': waBadge = '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Terkirim</span>'; break;
                    case 'failed': waBadge = '<span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Gagal</span>'; break;
                    default: waBadge = '<span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">Menunggu</span>'; break;
                }

                // Kolom Pemilik: ambil nama user peserta
                const ownerName = queue.user ? queue.user.name : '-';

                // Status editor: hanya tampil untuk penguji/admin
                const statusOptions = ['waiting','in_progress','completed','cancelled'];
                const statusLabels = {waiting:'Menunggu', in_progress:'Diproses', completed:'Selesai', cancelled:'Batal'};
                const statusCell = isPenguji
                    ? `<select onchange="updateQueueStatus(${queue.id}, this.value)" 
                            class="border border-gray-300 rounded-lg text-xs px-2 py-1 focus:ring-blue-500 focus:border-blue-500">
                          ${statusOptions.map(s => `<option value="${s}" ${queue.status===s?'selected':''}>${statusLabels[s]}</option>`).join('')}
                       </select>`
                    : statusBadge;

                return `
                    <tr class="hover:bg-blue-50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="text-lg font-bold text-blue-600 bg-blue-100 w-12 h-12 flex items-center justify-center rounded-full">
                                ${queue.queue_number}
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-gray-800">${queue.vehicle ? queue.vehicle.vehicle_number : '-'}</div>
                            <div class="text-xs text-gray-500">${queue.vehicle ? queue.vehicle.brand + ' ' + queue.vehicle.model : ''}</div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-medium text-gray-800">${ownerName}</div>
                        </td>
                        <td class="py-4 px-6">
                            ${statusCell}
                        </td>
                        <td class="py-4 px-6">
                            ${waBadge}
                        </td>
                        <td class="py-4 px-6 text-right">
                            ${queue.status === 'waiting' && !isPenguji ? 
                                `<button onclick="cancelQueue(${queue.id})" class="text-red-500 hover:text-red-700 text-sm font-medium">Batalkan</button>` : ''
                            }
                        </td>
                    </tr>
                `;
            }).join('');
        } catch (error) {
            console.error('Error fetching queues:', error);
            tableBody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-red-500">Gagal memuat data antrian: ' + (error.response?.data?.message || error.message) + '</td></tr>';
        }
    }

    async function loadStats() {
        try {
            const response = await api.get(`/api/queues/stats?date=${filterDate.value}`);
            const stats = response.data.data;
            
            statsContainer.innerHTML = `
                <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-blue-500">
                    <p class="text-sm text-gray-500 font-medium">Total Antrian</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">${stats.total_queue}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-yellow-500">
                    <p class="text-sm text-gray-500 font-medium">Menunggu</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">${stats.waiting}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-green-500">
                    <p class="text-sm text-gray-500 font-medium">Selesai</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">${stats.completed}</p>
                </div>
                <div class="bg-white rounded-xl shadow-md p-5 border-l-4 border-purple-500">
                    <p class="text-sm text-gray-500 font-medium">Sisa Slot Jadwal</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1">${stats.available_slots}</p>
                </div>
            `;
        } catch (error) {
            console.error('Error fetching stats:', error);
        }
    }

    window.updateQueueStatus = async function(id, status) {
        try {
            await api.put(`/api/queues/${id}/status`, { status });
            loadQueues();
            loadStats();
        } catch (error) {
            alert(error.response?.data?.message || 'Gagal memperbarui status');
            loadQueues(); // reload to reset dropdown
        }
    }

    window.cancelQueue = async function(id) {
        if(confirm('Apakah Anda yakin ingin membatalkan antrian ini?')) {
            try {
                await api.post(`/api/queues/${id}/cancel`);
                loadQueues();
                loadStats();
            } catch (error) {
                alert(error.response?.data?.message || 'Gagal membatalkan antrian');
            }
        }
    }

    window.callQueue = async function(id) {
        if(confirm('Panggil antrian ini sekarang?')) {
            try {
                await api.post(`/api/queues/${id}/call`);
                loadQueues();
                loadStats();
            } catch (error) {
                alert(error.response?.data?.message || 'Gagal memanggil antrian');
            }
        }
    }
});
</script>
@endsection
