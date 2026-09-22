@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Input Hasil Ujian Kendaraan</h1>
        <p class="text-gray-500 text-sm mt-1">Masukkan hasil pemeriksaan teknis untuk kendaraan yang sedang diuji.</p>
    </div>
    <a href="{{ route('test-results.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
    </a>
</div>

<div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
    <div class="bg-blue-600 px-6 py-4 border-b border-blue-700">
        <h2 class="text-lg font-semibold text-white flex items-center">
            <i class="fas fa-clipboard-check mr-2 text-blue-200"></i> Form Penilaian Kendaraan
        </h2>
    </div>
    
    <div class="p-6 md:p-8">
        <form id="createTestResultForm" class="space-y-8">
            <!-- Pilihan Kendaraan -->
            <div class="bg-gray-50 p-5 rounded-lg border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Kendaraan yang Diuji <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-500 mb-3">Hanya menampilkan kendaraan dengan status antrean "Diproses".</p>
                <select id="queueId" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4 bg-white">
                    <option value="">Memuat data antrean aktif...</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Emisi -->
                <div class="border rounded-lg p-4 relative hover:shadow-md transition-shadow">
                    <div class="absolute -top-3 left-4 bg-white px-2 text-sm font-bold text-gray-600">Emisi Gas Buang</div>
                    <div class="flex gap-4 mt-2 mb-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="emission_status" value="pass" required class="text-green-600 focus:ring-green-500 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-green-700 bg-green-100 px-2 py-1 rounded">Lulus</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="emission_status" value="fail" class="text-red-600 focus:ring-red-500 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-red-700 bg-red-100 px-2 py-1 rounded">Gagal</span>
                        </label>
                    </div>
                    <textarea id="emission_notes" rows="2" placeholder="Catatan Emisi (Opsional)" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-2"></textarea>
                </div>

                <!-- Rem -->
                <div class="border rounded-lg p-4 relative hover:shadow-md transition-shadow">
                    <div class="absolute -top-3 left-4 bg-white px-2 text-sm font-bold text-gray-600">Sistem Rem</div>
                    <div class="flex gap-4 mt-2 mb-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="brake_status" value="pass" required class="text-green-600 focus:ring-green-500 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-green-700 bg-green-100 px-2 py-1 rounded">Lulus</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="brake_status" value="fail" class="text-red-600 focus:ring-red-500 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-red-700 bg-red-100 px-2 py-1 rounded">Gagal</span>
                        </label>
                    </div>
                    <textarea id="brake_notes" rows="2" placeholder="Catatan Rem (Opsional)" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-2"></textarea>
                </div>

                <!-- Lampu -->
                <div class="border rounded-lg p-4 relative hover:shadow-md transition-shadow">
                    <div class="absolute -top-3 left-4 bg-white px-2 text-sm font-bold text-gray-600">Sistem Lampu Utama</div>
                    <div class="flex gap-4 mt-2 mb-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="light_status" value="pass" required class="text-green-600 focus:ring-green-500 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-green-700 bg-green-100 px-2 py-1 rounded">Lulus</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="light_status" value="fail" class="text-red-600 focus:ring-red-500 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-red-700 bg-red-100 px-2 py-1 rounded">Gagal</span>
                        </label>
                    </div>
                    <textarea id="light_notes" rows="2" placeholder="Catatan Lampu (Opsional)" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-2"></textarea>
                </div>

                <!-- Klakson -->
                <div class="border rounded-lg p-4 relative hover:shadow-md transition-shadow">
                    <div class="absolute -top-3 left-4 bg-white px-2 text-sm font-bold text-gray-600">Suara Klakson</div>
                    <div class="flex gap-4 mt-2 mb-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="horn_status" value="pass" required class="text-green-600 focus:ring-green-500 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-green-700 bg-green-100 px-2 py-1 rounded">Lulus</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="horn_status" value="fail" class="text-red-600 focus:ring-red-500 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-red-700 bg-red-100 px-2 py-1 rounded">Gagal</span>
                        </label>
                    </div>
                    <textarea id="horn_notes" rows="2" placeholder="Catatan Klakson (Opsional)" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-2"></textarea>
                </div>

                <!-- Suspensi -->
                <div class="border rounded-lg p-4 relative hover:shadow-md transition-shadow">
                    <div class="absolute -top-3 left-4 bg-white px-2 text-sm font-bold text-gray-600">Sistem Suspensi</div>
                    <div class="flex gap-4 mt-2 mb-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="suspension_status" value="pass" required class="text-green-600 focus:ring-green-500 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-green-700 bg-green-100 px-2 py-1 rounded">Lulus</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="suspension_status" value="fail" class="text-red-600 focus:ring-red-500 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-red-700 bg-red-100 px-2 py-1 rounded">Gagal</span>
                        </label>
                    </div>
                    <textarea id="suspension_notes" rows="2" placeholder="Catatan Suspensi (Opsional)" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-2"></textarea>
                </div>

                <!-- Ban -->
                <div class="border rounded-lg p-4 relative hover:shadow-md transition-shadow">
                    <div class="absolute -top-3 left-4 bg-white px-2 text-sm font-bold text-gray-600">Kedalaman Alur Ban</div>
                    <div class="flex gap-4 mt-2 mb-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="tire_status" value="pass" required class="text-green-600 focus:ring-green-500 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-green-700 bg-green-100 px-2 py-1 rounded">Lulus</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="tire_status" value="fail" class="text-red-600 focus:ring-red-500 w-4 h-4">
                            <span class="ml-2 text-sm font-medium text-red-700 bg-red-100 px-2 py-1 rounded">Gagal</span>
                        </label>
                    </div>
                    <textarea id="tire_notes" rows="2" placeholder="Catatan Ban (Opsional)" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 mt-2"></textarea>
                </div>
            </div>

            <!-- Catatan Keseluruhan -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Keseluruhan Penguji (Opsional)</label>
                <textarea id="overall_notes" rows="3" placeholder="Tambahkan catatan umum terkait kendaraan ini..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border"></textarea>
            </div>

            <!-- Upload Foto Bukti -->
            <div class="bg-blue-50 p-5 rounded-lg border border-blue-100">
                <label class="block text-sm font-bold text-blue-900 mb-2">Upload Foto Bukti Kendaraan <span class="text-red-500">*</span></label>
                <p class="text-xs text-blue-700 mb-3">Sertakan foto kendaraan yang sedang diuji (Tampak depan menyamping). Maksimal ukuran 2MB (Format: JPG, JPEG, PNG).</p>
                <input type="file" id="vehicle_photo" accept="image/jpeg, image/png, image/jpg" required class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 focus:outline-none bg-white border border-gray-300 rounded-lg shadow-sm">
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i> Simpan Hasil Ujian
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const api = axios.create({
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        withCredentials: true
    });

    const queueSelect = document.getElementById('queueId');
    const form = document.getElementById('createTestResultForm');

    let currentQueuesHash = '';

    // Load in_progress queues
    async function loadActiveQueues() {
        try {
            // API queues?status=in_progress
            const response = await api.get('/api/queues?status=in_progress');
            const queues = response.data.data.data || response.data.data;

            const newHash = queues.map(q => q.id).join(',');
            
            // Jika data tidak berubah, abaikan update DOM agar pilihan user tidak ter-reset
            if (newHash === currentQueuesHash && queueSelect.options.length > 0) {
                return;
            }
            currentQueuesHash = newHash;

            const selectedValue = queueSelect.value; // Simpan nilai yang sedang dipilih

            if (!queues || queues.length === 0) {
                queueSelect.innerHTML = '<option value="">-- Tidak ada kendaraan yang sedang diproses saat ini --</option>';
                return;
            }

            queueSelect.innerHTML = '<option value="">-- Pilih Kendaraan --</option>' + 
                queues.map(q => {
                    const vehicleStr = q.vehicle ? `${q.vehicle.vehicle_number} (${q.vehicle.brand} ${q.vehicle.model})` : 'Plat Tidak Tersedia';
                    const isSelected = (selectedValue == q.id) ? 'selected' : '';
                    return `<option value="${q.id}" ${isSelected}>Antrean #${q.queue_number} - ${vehicleStr}</option>`;
                }).join('');
        } catch (error) {
            console.error('Failed to load queues:', error);
        }
    }

    queueSelect.innerHTML = '<option value="">Memuat data...</option>';
    loadActiveQueues();
    
    // Polling setiap 5 detik agar realtime
    setInterval(loadActiveQueues, 5000);

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const btn = form.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('queue_id', queueSelect.value);
            formData.append('emission_status', document.querySelector('input[name="emission_status"]:checked').value);
            formData.append('emission_notes', document.getElementById('emission_notes').value);
            formData.append('brake_status', document.querySelector('input[name="brake_status"]:checked').value);
            formData.append('brake_notes', document.getElementById('brake_notes').value);
            formData.append('light_status', document.querySelector('input[name="light_status"]:checked').value);
            formData.append('light_notes', document.getElementById('light_notes').value);
            formData.append('horn_status', document.querySelector('input[name="horn_status"]:checked').value);
            formData.append('horn_notes', document.getElementById('horn_notes').value);
            formData.append('suspension_status', document.querySelector('input[name="suspension_status"]:checked').value);
            formData.append('suspension_notes', document.getElementById('suspension_notes').value);
            formData.append('tire_status', document.querySelector('input[name="tire_status"]:checked').value);
            formData.append('tire_notes', document.getElementById('tire_notes').value);
            formData.append('overall_notes', document.getElementById('overall_notes').value);

            const photoFile = document.getElementById('vehicle_photo').files[0];
            if (photoFile) {
                if (photoFile.size > 2048000) {
                    alert('Ukuran foto terlalu besar. Maksimal 2MB.');
                    btn.innerHTML = '<i class="fas fa-save mr-2"></i> Simpan Hasil Ujian';
                    btn.disabled = false;
                    return;
                }
                formData.append('vehicle_photo', photoFile);
            } else {
                alert('Foto bukti kendaraan wajib diunggah.');
                btn.innerHTML = '<i class="fas fa-save mr-2"></i> Simpan Hasil Ujian';
                btn.disabled = false;
                return;
            }

            await api.post('/api/test-results', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            
            alert('Berhasil! Hasil ujian telah disimpan dan antrean selesai.');
            window.location.href = "{{ route('test-results.index') }}";

        } catch (error) {
            console.error(error);
            alert(error.response?.data?.message || 'Terjadi kesalahan saat menyimpan hasil.');
        } finally {
            const btn = form.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-save mr-2"></i> Simpan Hasil Ujian';
            btn.disabled = false;
        }
    });
});
</script>
@endsection
