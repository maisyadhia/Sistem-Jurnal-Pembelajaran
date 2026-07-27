@extends('layouts.app')

@section('title', 'Laporan Kepatuhan Guru - E-Jurnal')

@push('styles')
<style>
    .stat-card {
        transition: all 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
    .status-badge {
        display: inline-block;
        padding: 2px 12px;
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
                Laporan kepatuhan pengisian jurnal harian.
            </p>
        </div>
        <div class="flex gap-2">
            <!-- Filter Tanggal -->
            <form method="GET" action="{{ route('laporan.index') }}" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $date }}" 
                       class="px-3 py-2 border border-outline-variant rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm hover:opacity-90 transition-opacity">
                    Filter
                </button>
            </form>
            <!-- Tombol Download Excel -->
            <a href="{{ route('report.export', ['date' => $date]) }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 hover:bg-green-700 transition-colors">
                <span class="material-symbols-outlined text-sm">download</span>
                Download Excel
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">analytics</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Tingkat Kepatuhan</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $complianceRate }}%</p>
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
                    <p class="text-2xl font-bold text-slate-800">{{ $onTimeCount }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">warning</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Terlambat</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $lateCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Guru -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex justify-between items-center">
            <h3 class="font-bold text-slate-800">Daftar Guru</h3>
            <span class="text-sm text-slate-500">Total: {{ $teachers->count() }} guru</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Guru</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">NIK</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $no = 1; @endphp
                    @foreach($teachers as $teacher)
                        @php
                            $isReported = isset($teacherStatus[$teacher->id]) ? $teacherStatus[$teacher->id] : false;
                            $status = $isReported ? 'Sudah Mengisi' : 'Belum Mengisi';
                            $statusClass = $isReported ? 'success' : 'warning';
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-sm">{{ $no++ }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $teacher->nama_guru }}</td>
                            <td class="px-4 py-3 text-sm">{{ $teacher->nik }}</td>
                            <td class="px-4 py-3">
                                <span class="status-badge {{ $statusClass }}">
                                    {{ $status }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kelas Tanpa Catatan -->
    @if($unreported->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex justify-between items-center bg-red-50">
            <h3 class="font-bold text-red-700">Kelas Tanpa Catatan</h3>
            <span class="text-sm text-red-600">{{ $unreported->count() }} kelas</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Guru</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php $no = 1; @endphp
                    @foreach($unreported as $class)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-sm">{{ $no++ }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $class->code ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $class->subject ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm">{{ $class->teacher ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Tombol Kembali -->
    <div class="flex justify-start">
        <a href="{{ route('monitoring') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition-all text-sm font-medium text-slate-700">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Kembali ke Monitoring
        </a>
    </div>
</div>
@endsection