<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kepatuhan Guru</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 5px 0 0 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; font-weight: bold; }
        .text-center { text-align: center; }
        .text-red { color: red; }
        .text-green { color: green; }
        .footer { margin-top: 30px; text-align: right; }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <div class="header">
        <h1>LAPORAN KEPATUHAN GURU</h1>
        <p>Sistem Informasi Jurnal Pengajaran (SIJAMPANG)</p>
        <p>Periode: {{ $currentFilter ? ucfirst(str_replace('_', ' ', $currentFilter)) : 'Semua Data' }}</p>
    </div>

    <!-- RINGKASAN -->
    <div style="margin-bottom: 15px;">
        <strong>Total Jurnal:</strong> {{ $totalJurnal }} | 
        <strong>Terlambat:</strong> <span class="text-red">{{ $totalTerlambat }}</span> | 
        <strong>Tepat Waktu:</strong> <span class="text-green">{{ $totalJurnal - $totalTerlambat }}</span>
    </div>

    <!-- TABEL JURNAL -->
    <table>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th>Tanggal KBM</th>
                <th>Guru</th>
                <th>Kelas</th>
                <th>Mapel</th>
                <th>Materi</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jurnals as $index => $jurnal)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($jurnal->tanggal_kbm)->format('d/m/Y') }}</td>
                <td>{{ $jurnal->nama_guru }}</td>
                <td>{{ $jurnal->nama_kelas }}</td>
                <td>{{ $jurnal->nama_mapel }}</td>
                <td>{{ \Illuminate\Support\Str::limit($jurnal->materi, 40) }}</td>
                <td class="text-center">
                    @if($jurnal->is_terlambat)
                        <span class="text-red">Terlambat</span>
                    @else
                        <span class="text-green">Tepat Waktu</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="footer">
        <p>Malang, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
        <br><br><br>
        <p><strong>Kepala Sekolah</strong></p>
    </div>

</body>
</html>