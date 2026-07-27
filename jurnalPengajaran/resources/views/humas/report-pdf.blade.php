<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            padding: 40px;
            color: #0b1c30;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #00236f;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #00236f;
            font-size: 24px;
            margin: 0;
        }
        .header p {
            color: #444651;
            margin: 5px 0 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f8f9ff;
            border: 1px solid #c5c5d3;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }
        .stat-card .number {
            font-size: 32px;
            font-weight: 700;
            color: #00236f;
        }
        .stat-card .label {
            font-size: 12px;
            text-transform: uppercase;
            color: #444651;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background: #e5eeff;
            text-align: left;
            padding: 10px 12px;
            font-size: 12px;
            text-transform: uppercase;
            color: #444651;
        }
        td {
            padding: 10px 12px;
            border-bottom: 1px solid #c5c5d3;
            font-size: 14px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-badge.success {
            background: #86f2e4;
            color: #006a61;
        }
        .status-badge.warning {
            background: #ffddb8;
            color: #3e2400;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #757682;
            border-top: 1px solid #c5c5d3;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p>Periode: {{ ucfirst($period) }} - {{ $date }}</p>
        <p>Generated: {{ now()->locale('id')->isoFormat('dddd, D MMM YYYY HH:mm') }}</p>
        
        <!-- TOMBOL BACK (untuk PDF ini tidak akan muncul di PDF, hanya di browser) -->
        <div style="margin-top: 10px;">
            <a href="{{ route('monitoring') }}" 
               style="display: inline-block; padding: 8px 20px; background: #00236f; color: white; text-decoration: none; border-radius: 8px; font-size: 14px;">
                ← Kembali ke Monitoring
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="number">{{ $complianceRate }}%</div>
            <div class="label">Tingkat Kepatuhan</div>
        </div>
        <div class="stat-card">
            <div class="number">{{ $onTimeCount }}</div>
            <div class="label">Tepat Waktu</div>
        </div>
        <div class="stat-card">
            <div class="number">{{ $lateCount }}</div>
            <div class="label">Terlambat</div>
        </div>
    </div>

    <!-- Daftar Guru -->
    <h3 style="margin-top: 30px;">Daftar Guru</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Guru</th>
                <th>NIK</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($teachers as $teacher)
                @php
                    $isReported = isset($teacherStatus[$teacher->id]) ? $teacherStatus[$teacher->id] : false;
                    $status = $isReported ? 'Sudah Mengisi' : 'Belum Mengisi';
                    $statusClass = $isReported ? 'success' : 'warning';
                    $teacherName = isset($teacher->nama_guru) ? $teacher->nama_guru : (isset($teacher->name) ? $teacher->name : '-');
                    $teacherNik = isset($teacher->nik) ? $teacher->nik : '-';
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $teacherName }}</td>
                    <td>{{ $teacherNik }}</td>
                    <td>
                        <span class="status-badge {{ $statusClass }}">
                            {{ $status }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Unreported Classes -->
    @if(isset($unreported) && $unreported->count() > 0)
        <h3 style="margin-top: 30px;">Kelas Tanpa Catatan</h3>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelas</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($unreported as $class)
                    <tr>
                        <td>{{ $no++ }}</td>
                        <td>{{ isset($class->code) ? $class->code : (isset($class->nama_kelas) ? $class->nama_kelas : '-') }}</td>
                        <td>{{ isset($class->subject) ? $class->subject : (isset($class->nama_mapel) ? $class->nama_mapel : '-') }}</td>
                        <td>{{ isset($class->teacher) ? $class->teacher : (isset($class->nama_guru) ? $class->nama_guru : '-') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Tombol Back di Footer -->
    <div class="footer">
        <p>Laporan ini dihasilkan secara otomatis oleh Sistem E-Jurnal</p>
        <p>MIN 2 Kota Malang</p>
        <p style="margin-top: 15px;">
            <a href="{{ route('monitoring') }}" 
               style="display: inline-block; padding: 8px 20px; background: #00236f; color: white; text-decoration: none; border-radius: 8px; font-size: 14px;">
                ← Kembali ke Monitoring
            </a>
        </p>
    </div>
</body>
</html>