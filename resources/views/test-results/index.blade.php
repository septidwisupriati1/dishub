@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-800" id="pageTitle">Daftar Hasil Ujian Kendaraan</h1>
        <p class="text-gray-500 text-sm mt-1" id="pageSubtitle">Pantau seluruh riwayat pengujian kendaraan yang telah dilakukan.</p>
    </div>
    @if(auth()->user()->role === 'penguji' || auth()->user()->role === 'admin')
    <a href="{{ route('test-results.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md transition-colors flex items-center">
        <i class="fas fa-plus mr-2"></i> Input Hasil Baru
    </a>
    @endif
</div>

<div id="tableContainer" class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 text-sm">
                    <th class="py-4 px-6 font-semibold">Tanggal Uji</th>
                    <th class="py-4 px-6 font-semibold">Kendaraan</th>
                    <th class="py-4 px-6 font-semibold">Penguji</th>
                    <th class="py-4 px-6 font-semibold">Status Akhir</th>
                    <th class="py-4 px-6 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="resultsTableBody" class="divide-y divide-gray-100 text-gray-700">
                <tr>
                    <td colspan="5" class="py-12 text-center text-gray-500">
                        <i class="fas fa-spinner fa-spin text-2xl mb-3 text-blue-500"></i>
                        <p>Memuat data hasil ujian...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Container Sertifikat (Khusus Peserta) -->
<div id="certificateContainer" class="grid grid-cols-1 lg:grid-cols-2 gap-6 hidden">
    <!-- Sertifikat di-inject via JS -->
</div>

<!-- Modal Detail -->
<div id="detailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden m-4 transform transition-transform scale-100">
        <div class="bg-blue-600 px-6 py-4 flex justify-between items-center border-b border-blue-700">
            <h3 class="text-lg font-bold text-white"><i class="fas fa-file-alt mr-2"></i> Detail Hasil Ujian</h3>
            <button onclick="closeModal()" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 md:p-8" id="modalContent">
            <!-- Modal content injected via JS -->
        </div>
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end">
            <button onclick="closeModal()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-6 rounded-lg transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.getElementById('tableContainer');
    const tableBody = document.getElementById('resultsTableBody');
    const certificateContainer = document.getElementById('certificateContainer');
    const detailModal = document.getElementById('detailModal');
    const modalContent = document.getElementById('modalContent');
    const pageTitle = document.getElementById('pageTitle');
    const pageSubtitle = document.getElementById('pageSubtitle');
    
    const userRole = '{{ auth()->user()->role }}';
    const isPeserta = userRole === 'peserta';
    
    let testResultsData = [];

    const api = axios.create({
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        withCredentials: true
    });

    if (isPeserta) {
        // Remove certificate-specific page title/subtitle as requested
        pageTitle.innerText = "";
        pageSubtitle.innerText = "";
        tableContainer.classList.add('hidden');
        certificateContainer.classList.remove('hidden');
    }

    async function loadResults() {
        try {
            if (isPeserta) {
                certificateContainer.innerHTML = '<div class="col-span-full py-12 text-center text-gray-500"><i class="fas fa-spinner fa-spin text-4xl mb-4 text-blue-500"></i><p>Memuat sertifikat...</p></div>';
            }

            const response = await api.get('/api/test-results');
            const dataObj = response.data.data || [];
            const results = Array.isArray(dataObj) ? dataObj : (dataObj.data || []);
            testResultsData = results;

            if (!results || results.length === 0) {
                if (isPeserta) {
                    certificateContainer.innerHTML = `
                        <div class="col-span-full py-16 px-4 text-center bg-gray-50 rounded-2xl border-2 border-dashed border-gray-300">
                            <i class="fas fa-certificate text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Sertifikat</h3>
                            <p class="text-gray-500">Kendaraan Anda belum memiliki riwayat uji atau belum selesai diuji.</p>
                        </div>
                    `;
                } else {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-500 flex flex-col items-center justify-center">
                                <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                <p>Belum ada riwayat hasil ujian.</p>
                            </td>
                        </tr>
                    `;
                }
                return;
            }

            if (isPeserta) {
                certificateContainer.innerHTML = results.map(result => {
                    const dateObj = new Date(result.tested_at);
                    const dateStr = dateObj.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
                    const vehiclePlate = result.vehicle ? result.vehicle.vehicle_number : '-';
                    const vehicleModel = result.vehicle ? `${result.vehicle.brand} ${result.vehicle.model}` : '';
                    const pengujiName = result.penguji ? result.penguji.name : '-';
                    const testNumber = result.test_number || '-';
                    const validUntilStr = result.valid_until 
                        ? new Date(result.valid_until).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) 
                        : '-';
                    
                    const isPass = result.overall_status === 'pass';
                    
                    return `
                        <div class="bg-white border-8 ${isPass ? 'border-blue-900' : 'border-red-900'} p-6 sm:p-8 rounded-xl shadow-xl relative overflow-hidden flex flex-col h-full group hover:shadow-2xl transition-shadow">
                            <!-- Watermark -->
                            <div class="absolute -right-12 -top-12 opacity-5 pointer-events-none transition-transform group-hover:scale-110 duration-500">
                                <i class="fas fa-certificate" style="font-size: 16rem; color: ${isPass ? '#1e3a8a' : '#7f1d1d'};"></i>
                            </div>
                            
                            <!-- Header removed as requested -->

                            <div class="space-y-4 relative z-10 flex-grow">
                                <div class="flex flex-col sm:flex-row justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-500 font-medium text-sm sm:text-base">Nomor Uji</span>
                                    <span class="font-black text-gray-800 text-lg">${testNumber}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-500 font-medium text-sm sm:text-base">Nomor Registrasi</span>
                                    <span class="font-bold text-gray-700 text-lg">${vehiclePlate}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-500 font-medium text-sm sm:text-base">Merk / Tipe</span>
                                    <span class="font-bold text-gray-700 text-right">${vehicleModel}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-500 font-medium text-sm sm:text-base">Tanggal Uji</span>
                                    <span class="font-bold text-gray-700">${dateStr}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-500 font-medium text-sm sm:text-base">Penguji</span>
                                    <span class="font-bold text-gray-700">${pengujiName}</span>
                                </div>
                                <div class="flex flex-col sm:flex-row justify-between border-b border-gray-100 pb-2">
                                    <span class="text-gray-500 font-medium text-sm sm:text-base">Berlaku Sampai</span>
                                    <span class="font-bold ${isPass ? 'text-green-600' : 'text-red-600'}">${validUntilStr}</span>
                                </div>
                            </div>

                            <div class="mt-8 text-center relative z-10">
                                <p class="text-gray-600 mb-4 text-sm px-4">Menyatakan bahwa kendaraan tersebut di atas telah melalui serangkaian uji teknis dan dinyatakan:</p>
                                
                                ${isPass ? 
                                    `<div class="inline-block border-4 border-green-600 text-green-600 bg-white px-6 sm:px-8 py-3 rounded-xl font-black text-xl sm:text-2xl tracking-widest transform -rotate-3 shadow-sm">
                                        LULUS UJI
                                    </div>` : 
                                    `<div class="inline-block border-4 border-red-600 text-red-600 bg-white px-6 sm:px-8 py-3 rounded-xl font-black text-xl sm:text-2xl tracking-widest transform -rotate-3 shadow-sm">
                                        TIDAK LULUS
                                    </div>`
                                }
                            </div>
                            
                            <div class="mt-8 text-center z-10 relative pt-4 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-3">
                                <button onclick="viewDetail(${result.id})" class="text-blue-600 hover:text-blue-800 text-sm font-semibold hover:underline flex items-center justify-center w-full bg-blue-50 hover:bg-blue-100 py-2.5 rounded-lg transition-colors">
                                    <i class="fas fa-list-ul mr-2"></i> Rincian Penilaian
                                </button>
                                <a href="/test-results/${result.id}/print" target="_blank" class="text-white bg-indigo-600 hover:bg-indigo-700 text-sm font-semibold flex items-center justify-center w-full py-2.5 rounded-lg transition-colors shadow-sm">
                                    <i class="fas fa-download mr-2"></i> Unduh PDF Sertifikat
                                </a>
                            </div>
                        </div>
                    `;
                }).join('');
            } else {
                tableBody.innerHTML = results.map(result => {
                const date = new Date(result.tested_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
                
                const vehiclePlate = result.vehicle ? result.vehicle.vehicle_number : '-';
                const vehicleModel = result.vehicle ? `${result.vehicle.brand} ${result.vehicle.model}` : '';
                const pengujiName = result.penguji ? result.penguji.name : '-';
                
                const statusBadge = result.overall_status === 'pass' 
                    ? '<span class="bg-green-100 text-green-800 border border-green-200 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide flex items-center inline-flex"><i class="fas fa-check-circle mr-1"></i> Lulus</span>'
                    : '<span class="bg-red-100 text-red-800 border border-red-200 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide flex items-center inline-flex"><i class="fas fa-times-circle mr-1"></i> Gagal</span>';

                return `
                    <tr class="hover:bg-blue-50 transition-colors group">
                        <td class="py-4 px-6 text-sm">
                            <div class="font-medium text-gray-900">${date.split(' ')[0]}</div>
                            <div class="text-xs text-gray-500">${date.split(' ')[1] || ''}</div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-gray-800">${vehiclePlate}</div>
                            <div class="text-xs text-gray-500">${vehicleModel}</div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center text-sm font-medium text-gray-700">
                                <i class="fas fa-user-shield text-gray-400 mr-2"></i> ${pengujiName}
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            ${statusBadge}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="viewDetail(${result.id})" title="Lihat Rincian" class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition-colors font-medium text-sm flex items-center">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="/test-results/${result.id}/print" target="_blank" title="Unduh Sertifikat PDF" class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-lg transition-colors font-medium text-sm flex items-center">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
            } // Close else block

        } catch (error) {
            console.error('Error fetching results:', error);
            if (isPeserta) {
                certificateContainer.innerHTML = '<div class="col-span-full py-12 text-center text-red-500 font-medium">Gagal memuat data sertifikat.</div>';
            } else {
                tableBody.innerHTML = '<tr><td colspan="5" class="py-12 text-center text-red-500 font-medium">Gagal memuat data hasil ujian.</td></tr>';
            }
        }
    }

    window.viewDetail = function(id) {
        const result = testResultsData.find(r => r.id === id);
        if (!result) return;

        const getBadge = (status) => status === 'pass' 
            ? '<span class="text-green-600 font-bold"><i class="fas fa-check"></i> Lulus</span>' 
            : '<span class="text-red-600 font-bold"><i class="fas fa-times"></i> Gagal</span>';

        const vehicleStr = result.vehicle ? `${result.vehicle.vehicle_number} - ${result.vehicle.brand} ${result.vehicle.model}` : '-';
        const testNumber = result.test_number || '-';
        const validUntilStr = result.valid_until 
            ? new Date(result.valid_until).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) 
            : '-';

        modalContent.innerHTML = `
            <div class="mb-6 pb-6 border-b border-gray-100 flex justify-between items-end">
                <div>
                    <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold mb-1">KENDARAAN</p>
                    <p class="text-xl font-bold text-gray-800">${vehicleStr}</p>
                    <p class="text-sm text-gray-500 mt-2">Nomor Uji: <span class="font-bold text-gray-700">${testNumber}</span></p>
                    <p class="text-sm text-gray-500">Masa Berlaku: <span class="font-bold ${result.overall_status === 'pass' ? 'text-green-600' : 'text-red-600'}">${validUntilStr}</span></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold mb-1">HASIL AKHIR</p>
                    <div class="text-2xl">${result.overall_status === 'pass' ? '<span class="text-green-600 font-black uppercase tracking-wide">LULUS UJI</span>' : '<span class="text-red-600 font-black uppercase tracking-wide">TIDAK LULUS</span>'}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-600 font-medium">Emisi Gas Buang</span>
                    ${getBadge(result.emission_status)}
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-600 font-medium">Sistem Rem</span>
                    ${getBadge(result.brake_status)}
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-600 font-medium">Lampu Utama</span>
                    ${getBadge(result.light_status)}
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-600 font-medium">Suara Klakson</span>
                    ${getBadge(result.horn_status)}
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-600 font-medium">Suspensi</span>
                    ${getBadge(result.suspension_status)}
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-50">
                    <span class="text-gray-600 font-medium">Alur Ban</span>
                    ${getBadge(result.tire_status)}
                </div>
            </div>

            ${result.overall_notes ? `
            <div class="mt-6 bg-gray-50 rounded-lg p-4 border border-gray-200">
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-2">Catatan Penguji</p>
                <p class="text-gray-800 text-sm italic">"${result.overall_notes}"</p>
            </div>
            ` : ''}
        `;

        detailModal.classList.remove('hidden');
    }

    window.closeModal = function() {
        detailModal.classList.add('hidden');
    }

    // Load data initially
    loadResults();
});
</script>
@endsection
