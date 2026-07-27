@extends('layouts.app')

@section('title', 'Timeline Pembelajaran - SIMJAR')

@push('styles')
<style>
    .timeline-line::before {
        content: '';
        position: absolute;
        left: 18px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #cbd5e1;
    }
    @media (min-width: 768px) {
        .timeline-line::before {
            left: 20px;
        }
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto w-full flex flex-col gap-4 pt-2 pb-6 px-3 md:px-6">
    <!-- Student Profile Header & Filter -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-xl md:text-3xl font-bold text-slate-800 mb-2">
                Timeline Pembelajaran
            </h2>
            <div class="flex items-center gap-2 md:gap-3 flex-wrap">
                <!-- ⚪ BADGE NAMA SISWA (TEKS PUTIH) -->
                <div class="bg-primary text-white px-3 py-1.5 rounded-xl flex items-center gap-1.5 text-xs font-semibold shadow-sm">
                    <span class="material-symbols-outlined text-base text-white">person</span>
                    <span>{{ $student->name ?? '-' }}</span>
                </div>
                
                <div class="bg-surface-container-highest text-primary px-3 py-1.5 rounded-xl flex items-center gap-1.5 text-xs font-semibold border border-outline-variant">
                    <span class="material-symbols-outlined text-base">class</span>
                    <span>{{ $student->class ?? '-' }}</span>
                </div>
            </div>
        </div>
        
        <!-- Date Filter -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar bg-slate-100 p-1.5 rounded-2xl border border-slate-200 shrink-0">
            <a href="{{ route('dashboard.timeline', ['filter' => 'hari_ini']) }}" 
               class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ $currentFilter === 'hari_ini' ? 'bg-primary text-on-primary shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
               Hari Ini
            </a>
            <a href="{{ route('dashboard.timeline', ['filter' => '1_minggu']) }}" 
               class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ $currentFilter === '1_minggu' ? 'bg-primary text-on-primary shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
               1 Minggu
            </a>
            <a href="{{ route('dashboard.timeline', ['filter' => '1_bulan']) }}" 
               class="px-3.5 py-1.5 text-xs font-bold rounded-xl transition-all whitespace-nowrap {{ $currentFilter === '1_bulan' ? 'bg-primary text-on-primary shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
               1 Bulan
            </a>
            <div class="px-2 text-slate-400 border-l border-slate-200 shrink-0">
                <span class="material-symbols-outlined text-base block">calendar_today</span>
            </div>
        </div>
    </div>

    <!-- Bento Grid Stats & Timeline -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 md:gap-6 mt-2">
        <!-- Left Column: Summary Cards -->
        <div class="lg:col-span-4 space-y-4">
            <!-- Card Kehadiran -->
            <div class="bg-white border border-slate-200/80 p-4 md:p-5 rounded-2xl shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kehadiran Periode Ini</h3>
                    <span class="material-symbols-outlined text-primary">check_circle</span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl md:text-4xl font-extrabold text-slate-800">98%</span>
                    <span class="text-xs text-primary font-semibold">+2% dari bln lalu</span>
                </div>
                <div class="mt-3 w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-primary h-full rounded-full" style="width: 98%"></div>
                </div>
            </div>
            
            <!-- Card Catatan Guru -->
            <div class="bg-white border border-slate-200/80 p-4 md:p-5 rounded-2xl shadow-sm">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">
                    Semua Catatan Guru ({{ $notes->count() }})
                </h3>
                
                <div class="max-h-[260px] overflow-y-auto pr-1 space-y-3 custom-scrollbar">
                    @forelse($notes as $note)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 relative">
                            <p class="text-xs text-slate-700 italic mb-2 leading-relaxed">
                                "{{ $note->content }}"
                            </p>
                            <div class="flex flex-col gap-0.5 text-[10px] text-slate-400 border-t border-slate-200/60 pt-2">
                                <span class="font-bold text-primary">{{ $note->teacher }} ({{ $note->mapel }})</span>
                                <span>{{ \Carbon\Carbon::parse($note->tanggal)->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic text-center py-4">
                            Belum ada catatan dari guru untuk periode ini.
                        </p>
                    @endforelse
                </div>
            </div>
            
            <!-- Card Enkripsi Privasi -->
            <div class="bg-slate-50 border border-dashed border-slate-300 p-3.5 rounded-2xl flex gap-3 items-start">
                <span class="material-symbols-outlined text-primary text-lg mt-0.5 shrink-0">lock</span>
                <p class="text-[11px] leading-relaxed text-slate-600">
                    <strong>Privasi Terjamin:</strong> Dashboard ini dienkripsi dan hanya menampilkan data akademik spesifik untuk anak Anda sesuai regulasi sekolah.
                </p>
            </div>
        </div>
        
        <!-- Right Column: Vertical Timeline -->
        <div class="lg:col-span-8">
            <div class="bg-white border border-slate-200/80 rounded-2xl p-4 md:p-6 shadow-sm">
                <h3 class="text-base md:text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">timeline</span>
                    Aktivitas Belajar
                </h3>
                
                <div class="relative timeline-line space-y-8 md:space-y-10">
                    @php
                        $groupedActivities = $activities->groupBy(function($activity) {
                            return \Carbon\Carbon::parse($activity->tanggal)->translatedFormat('F Y');
                        });
                    @endphp

                    @forelse($groupedActivities as $bulan => $items)
                        <!-- ⚪ PEMBATAS HEADER BULAN (TEKS PUTIH) -->
                        <div class="relative pl-2 mt-6 first:mt-0">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-primary text-white text-[11px] font-bold rounded-full shadow-sm uppercase tracking-wider relative z-10">
                                <span class="material-symbols-outlined text-sm text-white">calendar_month</span>
                                {{ $bulan }}
                            </div>
                        </div>

                        <!-- List Aktivitas Belajar -->
                        @foreach($items as $activity)
                            <div class="relative pl-10 md:pl-12">
                                @php
                                    $isHadir = strtolower($activity->status ?? 'hadir') === 'hadir';
                                @endphp

                                <!-- Dot Marker -->
                                <div class="absolute left-0 top-1 w-9 h-9 rounded-full {{ $isHadir ? 'bg-primary shadow-md shadow-primary/20' : 'bg-red-500 shadow-md shadow-red-200' }} flex items-center justify-center z-10">
                                    <span class="material-symbols-outlined text-white text-base md:text-lg font-bold">
                                        {{ $isHadir ? 'check' : 'close' }}
                                    </span>
                                </div>

                                <!-- Card Item Aktivitas -->
                                <div class="bg-slate-50/80 hover:bg-slate-50 border border-slate-200/80 rounded-2xl p-3.5 md:p-4 transition-all">
                                    <div class="flex items-center justify-between mb-1.5 flex-wrap gap-1">
                                        <span class="font-bold text-xs md:text-sm text-primary">{{ $activity->mapel }}</span>
                                        <span class="text-[11px] text-slate-500 font-medium">
                                            {{ \Carbon\Carbon::parse($activity->tanggal)->translatedFormat('d M Y') }} · Jam ke-{{ $activity->jam_ke }}
                                        </span>
                                    </div>

                                    <p class="text-xs md:text-sm text-slate-800 leading-relaxed font-normal mb-1">
                                        {{ $activity->materi }}
                                    </p>

                                    @if(!empty($activity->catatan))
                                        <p class="text-xs text-slate-500 italic mt-1.5 bg-white p-2 rounded-xl border border-slate-100">
                                            "{{ $activity->catatan }}"
                                        </p>
                                    @endif

                                    <div class="flex items-center gap-2 mt-3 pt-2 border-t border-slate-200/50">
                                        <span class="material-symbols-outlined text-slate-400 text-sm">person</span>
                                        <span class="text-xs text-slate-600 font-medium">{{ $activity->guru }}</span>

                                        <!-- Badge Kehadiran -->
                                        @if(!$isHadir)
                                            <span class="ml-auto text-[10px] font-bold text-red-700 bg-red-100 border border-red-200 px-2 py-0.5 rounded-full">
                                                {{ ucfirst($activity->status ?? 'Tidak Hadir') }}
                                            </span>
                                        @else
                                            <span class="ml-auto text-[10px] font-bold text-primary bg-primary-container/40 border border-primary/20 px-2 py-0.5 rounded-full">
                                                Hadir
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <p class="text-center text-slate-400 italic text-xs py-8">Belum ada aktivitas untuk periode ini.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection