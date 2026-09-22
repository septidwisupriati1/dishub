@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Pengaturan WhatsApp Gateway</h1>
            <p class="text-gray-500 mt-1">Kelola integrasi notifikasi otomatis via WhatsApp (Fonnte/Wablas/Twilio).</p>
        </div>
    </div>

    <div id="configLoading" class="py-12 text-center text-gray-500">
        Memuat konfigurasi...
    </div>

    <div id="configContent" class="hidden">
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-700">Konfigurasi Aktif</h2>
                <div id="statusBadgeContainer"></div>
            </div>
            <div class="p-6">
                <form id="whatsappConfigForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Provider Gateway</label>
                            <select id="provider" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border bg-gray-50">
                                <option value="fonnte">Fonnte</option>
                                <option value="wablas">Wablas</option>
                                <option value="twilio">Twilio</option>
                                <option value="ultramsg">Ultramsg</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp Pengirim</label>
                            <input type="text" id="phoneNumber" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border" placeholder="Contoh: 628123456789" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">API Key / Token</label>
                            <div class="relative">
                                <input type="password" id="apiKey" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border pr-10" placeholder="Masukkan API Key dari provider" required>
                                <button type="button" id="toggleApiKey" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg id="eyeIcon" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="eyeSlashIcon" class="h-5 w-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Batas Pesan Harian (Kuota)</label>
                            <input type="number" id="dailyLimit" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border" value="1000" min="1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Penggunaan Hari Ini</label>
                            <div class="flex items-center h-full">
                                <span class="text-2xl font-bold text-gray-800" id="currentUsage">0</span>
                                <span class="text-gray-500 ml-2">pesan</span>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Khusus</label>
                            <textarea id="notes" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-2.5 px-3 border" placeholder="Catatan internal..."></textarea>
                        </div>
                    </div>
                    
                    <div class="pt-4 flex gap-4 border-t border-gray-100">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors shadow-md">
                            Simpan Perubahan
                        </button>
                        <button type="button" id="btnTest" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2.5 px-6 rounded-lg transition-colors">
                            Test Koneksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', async function() {
    const configLoading = document.getElementById('configLoading');
    const configContent = document.getElementById('configContent');
    const form = document.getElementById('whatsappConfigForm');
    
    let currentConfigId = null;

    const api = axios.create({
        headers: { 
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        withCredentials: true
    });

    try {
        const response = await api.get('/api/whatsapp-configs');
        const dataObj = response.data.data || {};
        const configs = Array.isArray(dataObj) ? dataObj : (dataObj.data || []);
        
        if (configs && configs.length > 0) {
            const config = configs[0]; // Ambil konfigurasi pertama
            currentConfigId = config.id;
            
            document.getElementById('provider').value = config.gateway_provider;
            document.getElementById('phoneNumber').value = config.phone_number;
            document.getElementById('apiKey').value = config.api_key;
            document.getElementById('dailyLimit').value = config.daily_limit;
            document.getElementById('currentUsage').innerText = config.current_daily_count;
            document.getElementById('notes').value = config.notes || '';
            
            const badge = config.is_active 
                ? '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Aktif</span>'
                : '<span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Tidak Aktif</span>';
            document.getElementById('statusBadgeContainer').innerHTML = badge;
        } else {
            document.getElementById('statusBadgeContainer').innerHTML = '<span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Belum Dikonfigurasi</span>';
        }
        
        configLoading.classList.add('hidden');
        configContent.classList.remove('hidden');
    } catch (error) {
        console.error(error);
        configLoading.innerHTML = '<span class="text-red-500 font-medium">Gagal memuat konfigurasi. Anda mungkin bukan Admin atau terjadi error koneksi.</span>';
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const payload = {
            gateway_provider: document.getElementById('provider').value,
            phone_number: document.getElementById('phoneNumber').value,
            api_key: document.getElementById('apiKey').value,
            daily_limit: document.getElementById('dailyLimit').value,
            notes: document.getElementById('notes').value,
            is_active: true
        };

        try {
            if (currentConfigId) {
                await api.put(`/api/whatsapp-configs/${currentConfigId}`, payload);
            } else {
                await api.post('/api/whatsapp-configs', payload);
            }
            alert('Konfigurasi WhatsApp berhasil disimpan!');
            location.reload();
        } catch (error) {
            console.error(error);
            alert('Gagal menyimpan konfigurasi.');
        }
    });

    document.getElementById('btnTest').addEventListener('click', async function() {
        if (!currentConfigId) {
            alert('Silakan simpan konfigurasi terlebih dahulu sebelum melakukan test.');
            return;
        }
        
        const testPhone = prompt('Masukkan nomor WhatsApp tujuan test (Format: 628xxx):');
        if (!testPhone) return;

        try {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Testing...';
            btn.disabled = true;

            const res = await api.post(`/api/whatsapp-configs/${currentConfigId}/test`, {
                phone: testPhone
            });
            
            alert(res.data.message || 'Pesan test berhasil dikirim!');
        } catch (error) {
            console.error(error);
            alert(error.response?.data?.message || 'Gagal mengirim pesan test. Cek API Key atau format nomor.');
        } finally {
            this.innerHTML = 'Test Koneksi';
            this.disabled = false;
        }
    });

    const toggleApiKeyBtn = document.getElementById('toggleApiKey');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeSlashIcon = document.getElementById('eyeSlashIcon');
    const apiKeyInput = document.getElementById('apiKey');

    toggleApiKeyBtn.addEventListener('click', function() {
        if (apiKeyInput.type === 'password') {
            apiKeyInput.type = 'text';
            eyeIcon.classList.add('hidden');
            eyeSlashIcon.classList.remove('hidden');
        } else {
            apiKeyInput.type = 'password';
            eyeIcon.classList.remove('hidden');
            eyeSlashIcon.classList.add('hidden');
        }
    });
});
</script>
@endsection
