@extends('layouts.app')

@section('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        #printableReport, #printableReport * { visibility: visible; }
        #printableReport { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
    }
</style>
@endsection

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-[#0F2854]">Laporan Harian Ujian</h1>
            <p class="text-gray-500 mt-1">Pantau statistik kelulusan dan kinerja pengujian kendaraan hari ini.</p>
        </div>
        <div class="flex items-center gap-3 bg-white p-3 rounded-lg shadow-sm border border-gray-100">
            <label class="text-sm font-medium text-gray-700">Pilih Tanggal:</label>
            <input type="date" id="reportDate" class="border-gray-300 rounded-md focus:ring-[#1C4D8D] focus:border-[#1C4D8D] py-1.5 px-3 text-sm" value="{{ date('Y-m-d') }}">
        </div>
    </div>

    <div id="reportLoading" class="py-12 text-center text-gray-500">
        <i class="fas fa-spinner fa-spin text-3xl mb-3 text-[#1C4D8D]"></i>
        <p>Memuat laporan...</p>
    </div>

    <div id="reportError" class="hidden bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-center shadow-sm">
        <i class="fas fa-exclamation-triangle mr-2"></i> Gagal memuat laporan. Pastikan Anda memiliki akses Admin.
    </div>

    <div id="reportContent" class="hidden">
        
        <div class="flex justify-end gap-3 mb-6 no-print">
            <button onclick="exportExcel()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow font-medium transition flex items-center text-sm">
                <i class="fas fa-file-excel mr-2"></i> Download Excel
            </button>
            <button onclick="exportPDF()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow font-medium transition flex items-center text-sm">
                <i class="fas fa-file-pdf mr-2"></i> Download PDF
            </button>
        </div>

        <div id="printableReport" class="bg-white p-8 rounded-xl shadow-lg border border-gray-100">
            <!-- Report Header for PDF -->
            <div class="text-center mb-8 pb-6 border-b-2 border-gray-200">
                <h2 class="text-2xl font-bold text-[#0F2854] uppercase tracking-wider">Laporan Pengujian Kendaraan (KIR)</h2>
                <p class="text-gray-600 font-medium mt-1">Tanggal: <span id="displayDate" class="font-bold"></span></p>
            </div>

            <!-- Main Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-10">
                <div class="bg-blue-50 rounded-xl p-6 border-l-4 border-blue-500">
                    <p class="text-sm text-blue-800 font-bold uppercase tracking-wider mb-2">Total Antrean</p>
                    <p class="text-4xl font-black text-blue-900" id="statTotalQueue">0</p>
                </div>
                <div class="bg-green-50 rounded-xl p-6 border-l-4 border-green-500">
                    <p class="text-sm text-green-800 font-bold uppercase tracking-wider mb-2">Lulus Ujian</p>
                    <p class="text-4xl font-black text-green-900" id="statTotalPassed">0</p>
                </div>
                <div class="bg-red-50 rounded-xl p-6 border-l-4 border-red-500">
                    <p class="text-sm text-red-800 font-bold uppercase tracking-wider mb-2">Gagal Ujian</p>
                    <p class="text-4xl font-black text-red-900" id="statTotalFailed">0</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-6 border-l-4 border-purple-500">
                    <p class="text-sm text-purple-800 font-bold uppercase tracking-wider mb-2">Tingkat Lulus</p>
                    <div class="flex items-end">
                        <p class="text-4xl font-black text-purple-900 leading-none" id="statPassPercentage">0</p>
                        <p class="text-xl font-bold text-purple-900 leading-none ml-1">%</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                <!-- Test Status Detail -->
                <div>
                    <h3 class="text-lg font-bold text-[#0F2854] mb-4 border-b pb-2">Rincian Status Operasional</h3>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr><td class="py-3 text-gray-600 font-medium">Menunggu Diuji</td><td class="py-3 text-right font-bold text-gray-800" id="statWaiting">0</td></tr>
                            <tr><td class="py-3 text-gray-600 font-medium">Sedang Diuji</td><td class="py-3 text-right font-bold text-[#1C4D8D]" id="statInProgress">0</td></tr>
                            <tr><td class="py-3 text-gray-600 font-medium">Selesai Diuji</td><td class="py-3 text-right font-bold text-green-600" id="statCompleted">0</td></tr>
                            <tr><td class="py-3 text-gray-600 font-medium">Dibatalkan</td><td class="py-3 text-right font-bold text-red-600" id="statCancelled">0</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Other Info -->
                <div>
                    <h3 class="text-lg font-bold text-[#0F2854] mb-4 border-b pb-2">Informasi SDM</h3>
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr><td class="py-3 text-gray-600 font-medium">Penguji Bertugas</td><td class="py-3 text-right font-bold text-gray-800" id="statActivePenguji">0 Orang</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detailed Table -->
            <div>
                <h3 class="text-lg font-bold text-[#0F2854] mb-4 border-b pb-2">Daftar Kendaraan Terdaftar</h3>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-left text-sm" id="reportTable">
                        <thead class="bg-[#0F2854] text-white">
                            <tr>
                                <th class="py-3 px-4 font-semibold">No. Antrean</th>
                                <th class="py-3 px-4 font-semibold">No. Kendaraan</th>
                                <th class="py-3 px-4 font-semibold">Nama Pemilik</th>
                                <th class="py-3 px-4 font-semibold">Status Operasional</th>
                                <th class="py-3 px-4 font-semibold">Hasil Kelulusan</th>
                                <th class="py-3 px-4 font-semibold">Nama Penguji</th>
                            </tr>
                        </thead>
                        <tbody id="reportTableBody" class="divide-y divide-gray-200 text-gray-700 bg-white">
                            <!-- JS Injected Rows -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div> <!-- End Printable Area -->
    </div>
</div>
@endsection

@section('scripts')
<!-- SheetJS for Excel Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<!-- html2pdf for PDF Export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
let currentReportData = [];
const api = axios.create({
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    withCredentials: true
});

document.addEventListener('DOMContentLoaded', function() {
    const reportDate = document.getElementById('reportDate');
    
    loadReport();
    reportDate.addEventListener('change', loadReport);
});

async function loadReport() {
    const reportDate = document.getElementById('reportDate').value;
    const reportLoading = document.getElementById('reportLoading');
    const reportError = document.getElementById('reportError');
    const reportContent = document.getElementById('reportContent');
    const tableBody = document.getElementById('reportTableBody');

    reportLoading.classList.remove('hidden');
    reportContent.classList.add('hidden');
    reportError.classList.add('hidden');

    try {
        const response = await api.get(`/api/admin/daily-report?date=${reportDate}`);
        const data = response.data.data;
        
        // Populate stats
        document.getElementById('displayDate').innerText = new Date(data.date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        
        document.getElementById('statTotalQueue').innerText = data.total_queue;
        document.getElementById('statTotalPassed').innerText = data.total_passed;
        document.getElementById('statTotalFailed').innerText = data.total_failed;
        document.getElementById('statPassPercentage').innerText = data.pass_percentage;
        
        document.getElementById('statWaiting').innerText = data.waiting;
        document.getElementById('statInProgress').innerText = data.in_progress;
        document.getElementById('statCompleted').innerText = data.total_completed;
        document.getElementById('statCancelled').innerText = data.cancelled;
        
        document.getElementById('statActivePenguji').innerText = data.active_penguji + ' Orang';

        // Populate Table
        currentReportData = data.details || [];
        if (currentReportData.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-gray-500 italic">Tidak ada data antrean untuk tanggal ini.</td></tr>';
        } else {
            tableBody.innerHTML = currentReportData.map(row => {
                let statusOp = '';
                if(row.status === 'waiting') statusOp = 'Menunggu';
                else if(row.status === 'in_progress') statusOp = 'Sedang Diuji';
                else if(row.status === 'completed') statusOp = 'Selesai';
                else statusOp = 'Batal';

                let hasil = row.test_result === 'pass' ? '<span class="text-green-600 font-bold">Lulus</span>' : 
                            (row.test_result === 'fail' ? '<span class="text-red-600 font-bold">Gagal</span>' : '-');

                return `
                    <tr class="hover:bg-gray-50">
                        <td class="py-3 px-4 font-bold text-[#0F2854]">${row.queue_number}</td>
                        <td class="py-3 px-4">${row.vehicle_number}</td>
                        <td class="py-3 px-4">${row.owner_name}</td>
                        <td class="py-3 px-4">${statusOp}</td>
                        <td class="py-3 px-4">${hasil}</td>
                        <td class="py-3 px-4">${row.penguji}</td>
                    </tr>
                `;
            }).join('');
        }

        reportContent.classList.remove('hidden');
    } catch (error) {
        console.error(error);
        reportError.classList.remove('hidden');
    } finally {
        reportLoading.classList.add('hidden');
    }
}

function exportExcel() {
    Notiflix.Block.standard('body', 'Menyiapkan Excel...');
    setTimeout(() => {
        const table = document.getElementById("reportTable");
        const wb = XLSX.utils.table_to_book(table, {sheet: "Laporan"});
        const dateStr = document.getElementById('reportDate').value;
        XLSX.writeFile(wb, `Laporan_KIR_${dateStr}.xlsx`);
        Notiflix.Block.remove('body');
        Notiflix.Notify.success('Excel berhasil diunduh');
    }, 500);
}

function exportPDF() {
    Notiflix.Block.standard('body', 'Mengekspor PDF...');
    const element = document.getElementById('printableReport');
    const dateStr = document.getElementById('reportDate').value;
    
    const opt = {
        margin:       0.5,
        filename:     `Laporan_KIR_${dateStr}.pdf`,
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2 },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save().then(() => {
        Notiflix.Block.remove('body');
        Notiflix.Notify.success('PDF berhasil diunduh');
    });
}
</script>
@endsection
