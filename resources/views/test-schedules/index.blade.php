@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-[#0F2854]">Manajemen Jadwal Ujian</h1>
            <p class="text-gray-500 mt-1">Atur kuota dan jadwal operasional uji emisi kendaraan.</p>
        </div>
        <button onclick="openModal()" class="bg-[#1C4D8D] hover:bg-[#0F2854] text-white px-5 py-2.5 rounded-lg shadow-md font-medium transition flex items-center">
            <i class="fas fa-plus mr-2"></i> Tambah Jadwal
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="py-4 px-6 font-medium">Tanggal</th>
                        <th class="py-4 px-6 font-medium">Hari</th>
                        <th class="py-4 px-6 font-medium">Jam Operasional</th>
                        <th class="py-4 px-6 font-medium">Kuota</th>
                        <th class="py-4 px-6 font-medium">Status</th>
                        <th class="py-4 px-6 font-medium text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="schedulesTableBody" class="divide-y divide-gray-100 text-gray-700">
                    <tr><td colspan="6" class="py-8 text-center text-gray-400">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div id="scheduleModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden opacity-0 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 transition-transform duration-300">
        <div class="bg-[#0F2854] px-6 py-4 flex justify-between items-center text-white">
            <h3 class="text-lg font-bold" id="modalTitle">Tambah Jadwal</h3>
            <button onclick="closeModal()" class="text-gray-300 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
        </div>
        <form id="scheduleForm" onsubmit="saveSchedule(event)" class="p-6 space-y-4">
            <input type="hidden" id="scheduleId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Ujian</label>
                <input type="date" id="test_date" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Buka</label>
                    <input type="time" id="start_time" value="08:00" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Tutup</label>
                    <input type="time" id="end_time" value="15:00" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kuota Maksimal</label>
                <input type="number" id="max_queue" min="1" value="50" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-[#1C4D8D] focus:border-[#1C4D8D]">
                    <option value="open">Buka</option>
                    <option value="closed">Tutup</option>
                </select>
            </div>
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-[#1C4D8D] text-white font-medium rounded-lg hover:bg-[#0F2854] transition shadow">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
let schedulesList = [];
const api = axios.create({
    headers: { 
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    withCredentials: true
});

document.addEventListener('DOMContentLoaded', fetchSchedules);

async function fetchSchedules() {
    const tableBody = document.getElementById('schedulesTableBody');
    tableBody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-gray-400"><i class="fas fa-spinner fa-spin mr-2"></i>Memuat...</td></tr>';
    
    try {
        const response = await api.get('/api/test-schedules');
        schedulesList = response.data.data.data || response.data.data;
        renderTable();
    } catch (error) {
        tableBody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-red-500">Gagal memuat data jadwal.</td></tr>';
    }
}

function renderTable() {
    const tableBody = document.getElementById('schedulesTableBody');
    if (!schedulesList || schedulesList.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-gray-500">Belum ada jadwal.</td></tr>';
        return;
    }

    tableBody.innerHTML = schedulesList.map(schedule => {
        const isOpen = schedule.status === 'open';
        const statusBadge = isOpen
            ? '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Buka</span>'
            : '<span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Tutup</span>';

        const date = new Date(schedule.test_date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
        const percentage = Math.min(100, (schedule.current_queue / schedule.max_queue) * 100);

        return `
            <tr class="hover:bg-blue-50 transition-colors">
                <td class="py-4 px-6 font-medium text-gray-800">${date}</td>
                <td class="py-4 px-6">${schedule.day_of_week}</td>
                <td class="py-4 px-6">${schedule.start_time.substring(0,5)} - ${schedule.end_time.substring(0,5)}</td>
                <td class="py-4 px-6">
                    <div class="flex items-center">
                        <div class="w-24 bg-gray-200 rounded-full h-2.5 mr-2">
                            <div class="bg-[#1C4D8D] h-2.5 rounded-full" style="width: ${percentage}%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-600">${schedule.current_queue}/${schedule.max_queue}</span>
                    </div>
                </td>
                <td class="py-4 px-6">${statusBadge}</td>
                <td class="py-4 px-6 text-right">
                    <button onclick="editSchedule(${schedule.id})" class="text-[#1C4D8D] hover:text-[#0F2854] bg-blue-50 hover:bg-blue-100 p-2 rounded transition mr-1" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteSchedule(${schedule.id})" class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 p-2 rounded transition" title="Hapus">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function openModal(schedule = null) {
    const modal = document.getElementById('scheduleModal');
    const form = document.getElementById('scheduleForm');
    
    if (schedule) {
        document.getElementById('modalTitle').innerText = 'Edit Jadwal';
        document.getElementById('scheduleId').value = schedule.id;
        document.getElementById('test_date').value = schedule.test_date.split('T')[0];
        document.getElementById('start_time').value = schedule.start_time.substring(0,5);
        document.getElementById('end_time').value = schedule.end_time.substring(0,5);
        document.getElementById('max_queue').value = schedule.max_queue;
        document.getElementById('status').value = schedule.status;
    } else {
        document.getElementById('modalTitle').innerText = 'Tambah Jadwal';
        form.reset();
        document.getElementById('scheduleId').value = '';
    }

    modal.classList.remove('hidden');
    // small delay for transition
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
    }, 10);
}

function closeModal() {
    const modal = document.getElementById('scheduleModal');
    modal.classList.add('opacity-0');
    modal.querySelector('div').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function editSchedule(id) {
    const schedule = schedulesList.find(s => s.id === id);
    if(schedule) openModal(schedule);
}

async function saveSchedule(e) {
    e.preventDefault();
    const id = document.getElementById('scheduleId').value;
    const data = {
        test_date: document.getElementById('test_date').value,
        start_time: document.getElementById('start_time').value,
        end_time: document.getElementById('end_time').value,
        max_queue: document.getElementById('max_queue').value,
        status: document.getElementById('status').value
    };

    Notiflix.Block.standard('#scheduleModal > div', 'Menyimpan...');
    try {
        if (id) {
            await api.put(`/api/test-schedules/${id}`, data);
            Notiflix.Notify.success('Jadwal berhasil diperbarui');
        } else {
            await api.post('/api/test-schedules', data);
            Notiflix.Notify.success('Jadwal berhasil ditambahkan');
        }
        closeModal();
        fetchSchedules();
    } catch (error) {
        Notiflix.Notify.failure(error.response?.data?.message || 'Gagal menyimpan jadwal');
    } finally {
        Notiflix.Block.remove('#scheduleModal > div');
    }
}

function deleteSchedule(id) {
    Notiflix.Confirm.show(
        'Konfirmasi Hapus',
        'Yakin ingin menghapus jadwal ini?',
        'Hapus', 'Batal',
        async function() {
            Notiflix.Block.standard('body', 'Menghapus...');
            try {
                await api.delete(`/api/test-schedules/${id}`);
                Notiflix.Notify.success('Jadwal berhasil dihapus');
                fetchSchedules();
            } catch (error) {
                Notiflix.Notify.failure(error.response?.data?.message || 'Gagal menghapus jadwal');
            } finally {
                Notiflix.Block.remove('body');
            }
        }
    );
}
</script>
@endsection
