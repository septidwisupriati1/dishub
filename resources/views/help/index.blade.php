@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Pusat Bantuan</h2>
        <p class="text-gray-600 mt-1">Panduan penggunaan halaman-halaman yang tersedia untuk akun Anda.</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Halaman Anda sebagai {{ ucfirst(auth()->user()->role) }}</h3>
            
            <div class="space-y-6">
                @if(auth()->user()->isPeserta())
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                            <i class="fas fa-chart-line text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-md font-bold text-gray-800">Dashboard</h4>
                            <p class="text-gray-600 mt-1">Halaman utama yang menampilkan ringkasan informasi, notifikasi terbaru, dan status secara umum saat Anda pertama kali masuk.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                            <i class="fas fa-car text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-md font-bold text-gray-800">Kendaraan Saya</h4>
                            <p class="text-gray-600 mt-1">Halaman untuk mendaftarkan kendaraan baru dan mengelola daftar kendaraan yang Anda miliki. Anda dapat memperbarui informasi kendaraan yang akan diuji KIR.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                            <i class="fas fa-list-ol text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-md font-bold text-gray-800">Antrian Ujian</h4>
                            <p class="text-gray-600 mt-1">Halaman untuk mendaftarkan kendaraan Anda ke antrean uji KIR pada tanggal tertentu, serta memantau status panggilan antrean saat berada di lokasi.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                            <i class="fas fa-check-square text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-md font-bold text-gray-800">Hasil Ujian</h4>
                            <p class="text-gray-600 mt-1">Halaman untuk melihat riwayat lengkap dan detail hasil akhir uji KIR kendaraan Anda, apakah dinyatakan lulus atau tidak lulus uji.</p>
                        </div>
                    </div>
                @endif

                @if(auth()->user()->isPenguji())
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                            <i class="fas fa-chart-line text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-md font-bold text-gray-800">Dashboard</h4>
                            <p class="text-gray-600 mt-1">Halaman utama yang menampilkan ringkasan informasi antrean hari ini dan tugas-tugas pengujian Anda.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                            <i class="fas fa-list-ol text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-md font-bold text-gray-800">Antrian</h4>
                            <p class="text-gray-600 mt-1">Halaman untuk melihat daftar antrean kendaraan yang sedang menunggu untuk diuji pada hari ini, serta melakukan pemanggilan peserta.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                            <i class="fas fa-clipboard-check text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-md font-bold text-gray-800">Input Hasil Ujian</h4>
                            <p class="text-gray-600 mt-1">Halaman form untuk memasukkan parameter dan hasil pemeriksaan teknis kendaraan secara detail yang digunakan untuk menentukan kelulusan.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                            <i class="fas fa-chart-bar text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-md font-bold text-gray-800">Statistik Saya</h4>
                            <p class="text-gray-600 mt-1">Halaman untuk melihat statistik kinerja Anda, termasuk jumlah kendaraan yang telah Anda uji dalam periode tertentu.</p>
                        </div>
                    </div>
                @endif

                @if(auth()->user()->isAdmin())
                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                            <i class="fas fa-chart-line text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-md font-bold text-gray-800">Dashboard</h4>
                            <p class="text-gray-600 mt-1">Halaman utama yang menampilkan statistik global sistem, performa pendaftaran, dan data-data kunci yang membutuhkan perhatian Admin.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                            <i class="fas fa-calendar text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-md font-bold text-gray-800">Jadwal Ujian</h4>
                            <p class="text-gray-600 mt-1">Halaman untuk mengatur kuota antrean per hari, membuka atau menutup tanggal layanan, serta menentukan hari libur nasional.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-md font-bold text-gray-800">User</h4>
                            <p class="text-gray-600 mt-1">Halaman manajemen pengguna untuk menambah, mengubah, menonaktifkan, atau mereset password akun Admin, Penguji, dan Peserta.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                            <i class="fas fa-file-alt text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-md font-bold text-gray-800">Laporan Harian</h4>
                            <p class="text-gray-600 mt-1">Halaman rekapitulasi data pendaftaran, kelulusan, dan status operasional sistem secara harian yang dapat diunduh (export) untuk keperluan pelaporan.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg text-blue-600">
                            <i class="fas fa-cog text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-md font-bold text-gray-800">Pengaturan</h4>
                            <p class="text-gray-600 mt-1">Halaman konfigurasi sistem tingkat lanjut, termasuk pengaturan koneksi ke layanan WhatsApp Gateway untuk notifikasi otomatis.</p>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-100 bg-gray-50 -mx-6 -mb-6 p-6">
                <h4 class="text-md font-semibold text-gray-800 mb-2">Butuh Bantuan Lain?</h4>
                <p class="text-gray-600 text-sm">Jika Anda mengalami kendala teknis atau memiliki pertanyaan lain terkait penggunaan sistem, silakan hubungi administrator sistem atau petugas pelayanan KIR.</p>
            </div>
        </div>
    </div>
</div>
@endsection
