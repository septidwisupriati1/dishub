<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Uji - {{ $result->vehicle->vehicle_number ?? 'Kendaraan' }}</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <style>
        body {
            background-color: #f3f4f6;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #sertifikat-content {
            width: 210mm;
            min-height: 297mm;
            background: white;
            position: relative;
            padding: 10mm;
            box-sizing: border-box;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .header-border {
            border-bottom: 3px solid #1E3A8A; /* Dark blue */
        }
        .section-header {
            background-color: #1E3A8A;
            color: white;
            font-weight: bold;
            padding: 4px 10px;
            font-size: 11px;
            text-transform: uppercase;
        }
        .info-table td {
            font-size: 11px;
            padding: 4px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 130px;
        }
        .info-table td:nth-child(2) {
            width: 15px;
            text-align: center;
        }
        .result-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        .result-table th {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 6px;
            text-align: center;
            font-weight: bold;
        }
        .result-table td {
            border: 1px solid #d1d5db;
            padding: 5px 8px;
        }
        .status-pass {
            color: #16a34a;
            font-weight: bold;
        }
        .status-fail {
            color: #dc2626;
            font-weight: bold;
        }
        #loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.9);
            z-index: 50;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

    <div id="loading-overlay">
        <i class="fas fa-spinner fa-spin text-5xl text-blue-800 mb-4"></i>
        <h2 class="text-2xl font-bold text-gray-800 font-sans">Sedang Membuat PDF...</h2>
        <p class="text-gray-500 mt-2 font-sans">Mohon tunggu, file hasil_uji.pdf akan otomatis terunduh.</p>
    </div>

    <!-- Konten Sertifikat -->
    <div id="sertifikat-content">
        <!-- Header -->
        <div class="flex justify-between items-center pb-2 header-border mb-4">
            <div class="flex items-center gap-4">
                <img src="/images/logo-dishub.png" alt="Logo Dishub" class="w-20 h-20 object-contain" onerror="this.src='/images/logo-dishub.png';">
                <div>
                    <h1 class="text-xl font-bold text-blue-900 leading-tight uppercase">DINAS PERHUBUNGAN<br>KOTA SURAKARTA</h1>
                    <p class="text-[10px] text-gray-800 leading-tight mt-1">
                        Jl. Menteri Supeno No.7, Manahan, Kec. Banjarsari,<br>
                        Kota Surakarta, Jawa Tengah 57139<br>
                        Telp. (0271) 714899
                    </p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-black text-blue-900 leading-tight tracking-wider">DISHUB<br>SURAKARTA</h2>
                <p class="text-[11px] text-blue-800 italic mt-1 font-serif">Selamat, Aman, Tertib, Nyaman</p>
            </div>
        </div>

        <!-- Title -->
        <div class="text-center mb-6">
            <h3 class="text-lg font-black text-blue-900 uppercase tracking-widest">Hasil Uji Kendaraan Bermotor</h3>
            <p class="text-sm font-bold mt-1">Nomor : {{ $result->test_number ?? 'KIR/'.date('Y').'/'.str_pad($result->id, 4, '0', STR_PAD_LEFT) }}</p>
            <p class="text-[10px] mt-2 text-gray-700">
                Berdasarkan Undang - Undang Nomor 22 Tahun 2009 tentang Lalu Lintas dan Angkutan Jalan<br>
                telah dilakukan uji kendaraan bermotor dengan hasil sebagai berikut
            </p>
        </div>

        <!-- Body 2 Columns -->
        <div class="flex gap-4 mb-4">
            
            <!-- Left Column: Identitas Kendaraan -->
            <div class="w-[55%]">
                <div class="section-header mb-2">Identitas Kendaraan</div>
                <table class="info-table w-full">
                    <tr>
                        <td>Nomor Polisi</td><td>:</td>
                        <td class="font-bold uppercase">{{ $result->vehicle->vehicle_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Nama Pemilik</td><td>:</td>
                        <td class="uppercase">{{ $result->vehicle->owner_name ?? ($result->vehicle->user->name ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td><td>:</td>
                        <td class="uppercase">{{ $result->vehicle->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Kendaraan</td><td>:</td>
                        <td class="uppercase">{{ $result->vehicle->vehicle_type ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Merk / Tipe</td><td>:</td>
                        <td class="uppercase">{{ $result->vehicle->brand ?? '-' }} / {{ $result->vehicle->model ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tahun Pembuatan</td><td>:</td>
                        <td class="uppercase">{{ $result->vehicle->year ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Nomor Rangka</td><td>:</td>
                        <td class="uppercase">{{ $result->vehicle->chassis_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Nomor Mesin</td><td>:</td>
                        <td class="uppercase">{{ $result->vehicle->engine_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Bahan Bakar</td><td>:</td>
                        <td class="uppercase">{{ $result->vehicle->fuel_type ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Warna</td><td>:</td>
                        <td class="uppercase">{{ $result->vehicle->color ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Penggunaan</td><td>:</td>
                        <td class="uppercase">{{ $result->vehicle->usage_type ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Berlaku Uji s/d</td><td>:</td>
                        <td class="font-bold uppercase">{{ $result->overall_status === 'pass' && $result->valid_until ? \Carbon\Carbon::parse($result->valid_until)->translatedFormat('d F Y') : '-' }}</td>
                    </tr>
                </table>
            </div>

            <!-- Right Column: Foto & Pelaksanaan Uji -->
            <div class="w-[45%] flex flex-col gap-4">
                <div>
                    <div class="section-header mb-2">Foto Kendaraan</div>
                    <div class="w-full h-[180px] border border-gray-300 bg-gray-100 flex items-center justify-center overflow-hidden">
                        @if($result->vehicle_photo && $result->getPhotoUrl())
                            <img src="{{ $result->getPhotoUrl() }}" class="w-full h-full object-cover" alt="Foto Kendaraan" crossorigin="anonymous">
                        @else
                            <span class="text-gray-400 text-xs italic">Tidak ada foto kendaraan</span>
                        @endif
                    </div>
                </div>

                <div>
                    <div class="section-header mb-2">Data Pelaksanaan Uji</div>
                    <table class="info-table w-full">
                        <tr>
                            <td style="width: 100px;">Tanggal Uji</td><td>:</td>
                            <td class="uppercase font-bold">{{ \Carbon\Carbon::parse($result->tested_at)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td>Tempat Uji</td><td>:</td>
                            <td class="uppercase">UPT PENGUJIAN KENDARAAN BERMOTOR DISHUB KOTA SURAKARTA</td>
                        </tr>
                        <tr>
                            <td>Petugas Uji</td><td>:</td>
                            <td class="uppercase">{{ $result->penguji->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>No Izin Petugas</td><td>:</td>
                            <td class="uppercase">{{ $result->penguji->nip ?? '19790815 200812 1 002' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        <!-- Hasil Pengujian Table -->
        <div class="mb-4">
            <div class="section-header mb-0">Hasil Pengujian</div>
            <table class="result-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">NO</th>
                        <th style="text-align: left;">KOMPONEN YANG DIUJI</th>
                        <th style="width: 100px;">HASIL UJI</th>
                        <th style="width: 120px;">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $getFormat = function($status) {
                            return $status === 'pass' 
                                ? '<span class="status-pass">BAIK</span>' 
                                : '<span class="status-fail">KURANG</span>';
                        };
                        $getIcon = function($status) {
                            return $status === 'pass' 
                                ? '<span class="status-pass">✓</span>' 
                                : '<span class="status-fail">✗</span>';
                        };
                    @endphp
                    <tr>
                        <td class="text-center">1</td>
                        <td>Sistem Rem</td>
                        <td class="text-center">{!! $getFormat($result->brake_status) !!}</td>
                        <td class="text-center">{!! $getIcon($result->brake_status) !!}</td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td>Lampu Utama</td>
                        <td class="text-center">{!! $getFormat($result->light_status) !!}</td>
                        <td class="text-center">{!! $getIcon($result->light_status) !!}</td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td>Suara Klakson</td>
                        <td class="text-center">{!! $getFormat($result->horn_status) !!}</td>
                        <td class="text-center">{!! $getIcon($result->horn_status) !!}</td>
                    </tr>
                    <tr>
                        <td class="text-center">4</td>
                        <td>Sistem Suspensi</td>
                        <td class="text-center">{!! $getFormat($result->suspension_status) !!}</td>
                        <td class="text-center">{!! $getIcon($result->suspension_status) !!}</td>
                    </tr>
                    <tr>
                        <td class="text-center">5</td>
                        <td>Alur Ban</td>
                        <td class="text-center">{!! $getFormat($result->tire_status) !!}</td>
                        <td class="text-center">{!! $getIcon($result->tire_status) !!}</td>
                    </tr>
                    <tr>
                        <td class="text-center">6</td>
                        <td>Emisi Gas Buang</td>
                        <td class="text-center">{!! $getFormat($result->emission_status) !!}</td>
                        <td class="text-center">{!! $getIcon($result->emission_status) !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Result Decision Boxes -->
        <div class="flex gap-4 mb-6">
            <div class="flex-1 border-2 border-gray-300 text-center py-2 flex flex-col justify-center items-center">
                <span class="text-[10px] font-bold text-gray-800 mb-1">NILAI KESELURUHAN</span>
                <span class="text-3xl font-black {{ $result->overall_status === 'pass' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $result->overall_status === 'pass' ? 'BAIK' : 'KURANG' }}
                </span>
            </div>
            
            <div class="flex-1 border-2 border-green-500 text-center py-2 flex flex-col justify-center items-center">
                <span class="text-[10px] font-bold text-gray-800 mb-1">HASIL KEPUTUSAN</span>
                @if($result->overall_status === 'pass')
                    <span class="text-2xl font-black text-green-600 tracking-wider">LULUS UJI</span>
                    <span class="text-[11px] font-bold text-green-700">KENDARAAN LAIK JALAN</span>
                @else
                    <span class="text-2xl font-black text-red-600 tracking-wider">TIDAK LULUS</span>
                    <span class="text-[11px] font-bold text-red-700">KENDARAAN TIDAK LAIK JALAN</span>
                @endif
            </div>
            
            <div class="w-[30%] border border-gray-300 p-2 text-[9px] flex flex-col justify-center">
                <div class="font-bold text-center mb-1 pb-1 border-b border-gray-200">KETERANGAN HASIL</div>
                <div class="flex mb-1"><span class="w-20 bg-green-100 text-center font-bold px-1 mr-2 text-green-800">LULUS</span> : Memenuhi Syarat Teknis</div>
                <div class="flex mb-1"><span class="w-20 bg-yellow-100 text-center font-bold px-1 mr-2 text-yellow-800">PERBAIKAN</span> : Syarat Tidak Lengkap</div>
                <div class="flex"><span class="w-20 bg-red-100 text-center font-bold px-1 mr-2 text-red-800">TIDAK LULUS</span> : Rusak / Gagal Teknis</div>
            </div>
        </div>

        <!-- Footer Notes and Signatures -->
        <div class="flex justify-between items-end border-t-2 border-blue-900 pt-3 relative">
            <div class="text-[10px] leading-relaxed">
                <strong>Catatan :</strong><br>
                1. Sertifikat ini merupakan bukti sah telah dilakukan uji kendaraan bermotor.<br>
                2. Berlaku 6 (enam) bulan sejak tanggal pengujian.<br>
                3. Lakukan uji berkala sebelum masa berlaku berakhir.<br>
                @if($result->overall_notes)
                4. <em>Penguji: {{ $result->overall_notes }}</em>
                @endif
            </div>
            
            <div class="text-center text-[11px] pr-12 relative z-10">
                Surakarta, {{ \Carbon\Carbon::parse($result->tested_at)->translatedFormat('d F Y') }}<br>
                KEPALA DINAS PERHUBUNGAN<br>
                KOTA SURAKARTA
                
                <div class="h-20 w-full relative my-2">
                    <!-- Signature placeholder -->
                    <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Signature_of_John_Hancock.svg" class="absolute inset-0 m-auto h-16 opacity-70" alt="TTD">
                    <!-- Stamp placeholder -->
                    <div class="absolute -left-12 top-2 w-20 h-20 border-2 border-purple-800 rounded-full flex items-center justify-center rotate-[-15deg] opacity-60">
                        <div class="w-16 h-16 border border-purple-800 rounded-full flex flex-col items-center justify-center text-[6px] text-purple-900 font-bold text-center leading-tight">
                            DINAS PERHUBUNGAN<br><br>KOTA SURAKARTA
                        </div>
                    </div>
                </div>
                
                <span class="font-bold underline">TAUFIK MUROHMAN, S.SiT., M.T.</span><br>
                Pembina Tk. I (IV/b)<br>
                NIP. 19700510 199703 1 002
            </div>
        </div>

        <div class="absolute bottom-0 left-0 w-full bg-[#1E3A8A] text-white py-1 px-8 flex justify-between text-[10px] font-bold">
            <span>DISHUB SURAKARTA</span>
            <span class="italic text-blue-200">#TerhubungUntukSelamat</span>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(() => {
                const element = document.getElementById('sertifikat-content');
                element.style.boxShadow = 'none';

                const opt = {
                    margin:       0,
                    filename:     'Hasil_Uji_{{ $result->vehicle->vehicle_number ?? 'Kendaraan' }}.pdf',
                    image:        { type: 'jpeg', quality: 1 },
                    html2canvas:  { scale: 2, useCORS: true, logging: true },
                    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };

                html2pdf().set(opt).from(element).save().then(() => {
                    document.getElementById('loading-overlay').style.display = 'none';
                    element.style.boxShadow = '0 10px 25px rgba(0,0,0,0.1)';
                    // Tutup otomatis setelah selesai
                    setTimeout(() => window.close(), 1000);
                });
            }, 800); 
        };
    </script>
</body>
</html>
