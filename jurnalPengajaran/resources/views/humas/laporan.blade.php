@extends('layouts.app')

@section('title', 'Laporan Kepatuhan Guru - E-Jurnal')

@push('styles')
<style>
    .stat-card { transition: all 0.2s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
    .status-badge { display: inline-block; padding: 2px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    .status-badge.success { background: #86f2e4; color: #006a61; }
    .status-badge.warning { background: #ffddb8; color: #3e2400; }
    .status-badge.danger { background: #fecaca; color: #991b1b; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endpush

@section('content')
<div class="space-y-gutter">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-on-background mb-1">
                Laporan Kepatuhan Guru
            </h2>
            <p class="font-body-base text-body-base text-on-surface-variant">
                Riwayat lengkap jurnal mengajar semua guru.
            </p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('report.export') }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 hover:bg-green-700 transition-colors">
                <span class="material-symbols-outlined text-sm">download</span>
                Download Excel
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">description</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Jurnal</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalJurnal }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">check_circle</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Tepat Waktu</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalTepatWaktu }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">schedule</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Terlambat</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalTerlambat }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">analytics</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Kepatuhan</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $complianceRate }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <!-- Search -->
            <form method="GET" action="{{ route('laporan.index') }}" id="form-search" class="flex-1">
                @if(request('filter'))
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                @endif
                <div class="relative">
                    <input type="text" id="input-search" name="search" value="{{ $search }}"
                           placeholder="Cari guru, kelas, mapel, materi..."
                           class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-primary transition-all"
                           autocomplete="off">
                    <span class="material-symbols-outlined text-slate-400 text-lg absolute left-3 top-1/2 -translate-y-1/2">search</span>
                </div>
            </form>

            <!-- Filter Buttons -->
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200">
                <a href="{{ route('laporan.index', ['filter' => 'hari_ini', 'search' => $search]) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ $currentFilter === 'hari_ini' ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-200' }}">
                    Hari Ini
                </a>
                <a href="{{ route('laporan.index', ['filter' => '1_minggu', 'search' => $search]) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ $currentFilter === '1_minggu' ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-200' }}">
                    1 Minggu
                </a>
                <a href="{{ route('laporan.index', ['filter' => '1_bulan', 'search' => $search]) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ $currentFilter === '1_bulan' ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-200' }}">
                    1 Bulan
                </a>
                <a href="{{ route('laporan.index', ['filter' => 'terlambat', 'search' => $search]) }}"
                   class="px-3 py-1.5 text-xs font-semibold rounded-lg transition-all whitespace-nowrap {{ $currentFilter === 'terlambat' ? 'bg-red-600 text-white' : 'text-red-600 hover:bg-red-50' }}">
                    ⚠️ Terlambat
                </a>
                @if($currentFilter || $search)
                    <a href="{{ route('laporan.index') }}"
                       class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-200 text-slate-600 hover:bg-slate-300">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- 🔥 RIWAYAT JURNAL (PAGINATION) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex justify-between items-center">
            <h3 class="font-bold text-slate-800">Riwayat Jurnal Mengajar</h3>
            <span class="text-sm text-slate-500">{{ $jurnals->total() }} total jurnal</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal KBM</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal Input</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Guru</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Mapel</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Materi</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($jurnals as $index => $jurnal)
                        <tr class="transition-colors {{ $jurnal->is_terlambat ? 'bg-red-50/50 hover:bg-red-50' : 'hover:bg-slate-50' }}">
                            <td class="px-4 py-3 text-sm">{{ $jurnals->firstItem() + $index }}</td>
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ \Carbon\Carbon::parse($jurnal->tanggal_kbm)->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-500">
                                {{ \Carbon\Carbon::parse($jurnal->tanggal_input)->format('d M Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $jurnal->nama_guru }}</td>
                            <td class="px-4 py-3 text-sm">{{ $jurnal->nama_kelas }}</td>
                            <td class="px-4 py-3 text-sm">{{ $jurnal->nama_mapel }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600 max-w-xs truncate">
                                {{ Str::limit($jurnal->materi, 50) }}
                            </td>
                            <td class="px-4 py-3">
                                @if($jurnal->is_terlambat)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-700 border border-red-200 whitespace-nowrap">
                                        ⚠️ Terlambat {{ $jurnal->selisih_hari }}h
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200 whitespace-nowrap">
                                        ✅ Tepat Waktu
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400 italic">
                                Tidak ada jurnal ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- 🔥 PAGINATION -->
        @if($jurnals->hasPages())
            <div class="p-4 border-t border-slate-200 flex items-center justify-between flex-wrap gap-2">
                <p class="text-sm text-slate-500">
                    Menampilkan <span class="font-semibold">{{ $jurnals->firstItem() }}</span> - 
                    <span class="font-semibold">{{ $jurnals->lastItem() }}</span> dari 
                    <span class="font-semibold">{{ $jurnals->total() }}</span> jurnal
                </p>
                <div>
                    {{ $jurnals->links('pagination::tailwind') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Daftar Guru (Status Keseluruhan) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex justify-between items-center">
            <h3 class="font-bold text-slate-800">Status Kepatuhan Per Guru</h3>
            <span class="text-sm text-slate-500">{{ $teachers->count() }} guru</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Guru</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">NIK</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Total Jurnal</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $no = 1; @endphp
                    @foreach($teachers as $teacher)
                        @php
                        $total = $teacherTotalCount[$teacher->id] ?? 0;
                        $lateCount = $teacherLateCount[$teacher->id] ?? 0;
                        $isLate = $lateCount > 0;
                        $belumIsi = $teacherBelumIsi[$teacher->id] ?? false; // <-- CEK NOTIFIKASI

                        if ($belumIsi) {
                            // Prioritas 1: Jika ada notif belum dibaca, berarti belum mengisi
                            $status = 'Belum Mengisi';
                            $statusClass = 'warning';
                            $keterangan = 'Tidak mengisi jurnal hari ini';
                        } elseif ($total > 0) {
                            // Prioritas 2: Jika sudah isi, cek terlambat atau tepat waktu
                            $status = $isLate ? 'Terlambat' : 'Tepat Waktu';
                            $statusClass = $isLate ? 'danger' : 'success';
                            $keterangan = $isLate ? "{$lateCount} dari {$total} jurnal terlambat" : 'Tepat waktu';
                        } else {
                            // Prioritas 3: Jika tidak ada jurnal dan tidak ada notif (misal tidak ada jadwal)
                            $status = 'Belum Ada Jurnal';
                            $statusClass = 'warning';
                            $keterangan = 'Tidak ada jurnal';
                        }
                    @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-sm">{{ $no++ }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $teacher->nama_guru }}</td>
                            <td class="px-4 py-3 text-sm">{{ $teacher->nik }}</td>
                            <td class="px-4 py-3 text-sm">{{ $total }} jurnal</td>
                            <td class="px-4 py-3">
                                <span class="status-badge {{ $statusClass }}">{{ $status }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                @if($isLate)
                                    <span class="text-red-600 font-medium">⚠️ {{ $keterangan }}</span>
                                @elseif($total > 0)
                                    <span class="text-green-600">✅ {{ $keterangan }}</span>
                                @else
                                    <span class="text-amber-600">⏳ {{ $keterangan }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tombol Kembali -->
    <div class="flex justify-start">
        <a href="{{ route('monitoring') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition-all text-sm font-medium text-slate-700">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Kembali ke Monitoring
        </a>
    </div>
</div>

@push('scripts')
<script>
    let searchTimer;
    const inputSearch = document.getElementById('input-search');
    if (inputSearch) {
        inputSearch.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                document.getElementById('form-search').submit();
            }, 500);
        });
    }
</script>
@endpush
@endsection