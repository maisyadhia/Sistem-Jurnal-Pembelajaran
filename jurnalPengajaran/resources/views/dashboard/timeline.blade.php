@extends('layouts.app')

@section('title', 'Timeline Pembelajaran - E-Jurnal')

@section('content')
<!-- Student Profile Header -->
<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-6">
    <div>
        <h2 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-on-background mb-2">
            Timeline Pembelajaran
        </h2>
        <div class="flex items-center gap-4">
            <div class="bg-primary-container text-on-primary-container px-3 py-1 rounded-lg flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">person</span>
                <span class="font-data-tabular text-data-tabular">{{ $student->name ?? 'Aditya Pratama' }}</span>
            </div>
            <div class="bg-surface-container-highest text-primary px-3 py-1 rounded-lg flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">class</span>
                <span class="font-data-tabular text-data-tabular">{{ $student->class ?? 'Kelas XI - IPA 2' }}</span>
            </div>
        </div>
    </div>
    
    <!-- Date Filter -->
    <div class="flex items-center gap-2 bg-surface-container-lowest p-1 rounded-xl border border-outline-variant">
        <a href="{{ route('dashboard.timeline', ['filter' => 'hari_ini']) }}" 
           class="px-4 py-2 font-label-caps text-label-caps rounded-lg transition-all text-center {{ $currentFilter === 'hari_ini' ? 'bg-primary text-on-primary shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
           Hari Ini
        </a>
        <a href="{{ route('dashboard.timeline', ['filter' => '1_minggu']) }}" 
           class="px-4 py-2 font-label-caps text-label-caps rounded-lg transition-all text-center {{ $currentFilter === '1_minggu' ? 'bg-primary text-on-primary shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
           1 Minggu
        </a>
        <a href="{{ route('dashboard.timeline', ['filter' => '1_bulan']) }}" 
           class="px-4 py-2 font-label-caps text-label-caps rounded-lg transition-all text-center {{ $currentFilter === '1_bulan' ? 'bg-primary text-on-primary shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
           1 Bulan
        </a>
        <div class="px-2">
            <span class="material-symbols-outlined text-outline cursor-pointer">calendar_today</span>
        </div>
    </div>
</div>

<!-- Bento Grid Stats & Timeline -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
    <!-- Left Column: Summary Cards -->
    <div class="lg:col-span-4 space-y-gutter">
        <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-label-caps text-label-caps text-primary">Kehadiran Periode Ini</h3>
                <span class="material-symbols-outlined text-secondary">check_circle</span>
            </div>
            <div class="flex items-baseline gap-2">
                <span class="text-4xl font-bold text-on-background">98%</span>
                <span class="text-body-sm text-secondary font-medium">+2% dari bln lalu</span>
            </div>
            <div class="mt-4 w-full bg-surface-container h-2 rounded-full overflow-hidden">
                <div class="bg-secondary h-full" style="width: 98%"></div>
            </div>
        </div>
        
        <!-- CATATAN GURU -->
        <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl">
            <h3 class="font-label-caps text-label-caps text-primary mb-4">Semua Catatan Guru ({{ $notes->count() }})</h3>
            
            <div class="max-h-[300px] overflow-y-auto pr-1 space-y-4 scrollbar-thin">
                @forelse($notes as $note)
                    <div class="p-3 bg-surface-container-low rounded-lg border border-outline-variant relative">
                        <p class="font-body-sm text-body-sm text-on-surface-variant italic mb-2">
                            "{{ $note->content }}"
                        </p>
                        <div class="flex flex-col gap-1 text-[10px] text-on-surface-variant/80 border-t border-outline-variant/50 pt-2">
                            <span class="font-semibold text-primary">{{ $note->teacher }} ({{ $note->mapel }})</span>
                            <span>{{ \Carbon\Carbon::parse($note->tanggal)->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="font-body-sm text-body-sm text-on-surface-variant italic text-center py-4">
                        Belum ada catatan dari guru untuk periode ini.
                    </p>
                @endforelse
            </div>
        </div>
        
        <div class="bg-surface-container-low border border-dashed border-outline-variant p-4 rounded-xl flex gap-3">
            <span class="material-symbols-outlined text-primary">lock</span>
            <p class="text-[11px] leading-tight text-on-surface-variant">
                <strong>Privasi Terjamin:</strong> Dashboard ini dienkripsi dan hanya menampilkan data akademik spesifik untuk anak Anda sesuai regulasi sekolah.
            </p>
        </div>
    </div>
    
    <!-- Right Column: Vertical Timeline -->
    <div class="lg:col-span-8">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6 md:p-8">
            <h3 class="font-headline-md text-headline-md text-on-background mb-8">Aktivitas Belajar</h3>
            
            <div class="relative timeline-line space-y-12">
                @php
                    // Kelompokkan data collection $activities berdasarkan Bulan & Tahun secara dinamis
                    $groupedActivities = $activities->groupBy(function($activity) {
                        return \Carbon\Carbon::parse($activity->tanggal)->translatedFormat('F Y');
                    });
                @endphp

                @forelse($groupedActivities as $bulan => $items)
                    <!-- Pembatas Header Bulan -->
                    <div class="relative pl-4 mt-8 first:mt-0">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-teal-50 text-teal-800 text-xs font-bold rounded-full border border-teal-200 shadow-sm uppercase tracking-wider relative z-10">
                            <span class="material-symbols-outlined text-sm">calendar_month</span>
                            {{ $bulan }}
                        </div>
                    </div>

                    <!-- List Aktivitas Belajar Di Bulan Tersebut -->
                    @foreach($items as $activity)
                        <div class="relative pl-14">
                            <!-- Dot Marker -->
                            <div class="absolute left-0 top-0 w-10 h-10 rounded-full bg-primary-container flex items-center justify-center z-10">
                                <span class="material-symbols-outlined text-primary text-lg">
                                    {{ strtolower($activity->status ?? 'hadir') === 'hadir' ? 'check_circle' : 'cancel' }}
                                </span>
                            </div>

                            <div class="bg-surface-container-low border border-outline-variant rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2 flex-wrap gap-1">
                                    <span class="font-label-caps text-label-caps text-primary">{{ $activity->mapel }}</span>
                                    <span class="text-xs text-on-surface-variant">
                                        {{ \Carbon\Carbon::parse($activity->tanggal)->translatedFormat('d M Y') }} · Jam ke-{{ $activity->jam_ke }}
                                    </span>
                                </div>

                                <p class="font-body-base text-on-background mb-1">{{ $activity->materi }}</p>

                                @if(!empty($activity->catatan))
                                    <p class="text-body-sm text-on-surface-variant italic mt-2">"{{ $activity->catatan }}"</p>
                                @endif

                                <div class="flex items-center gap-2 mt-3">
                                    <span class="material-symbols-outlined text-outline text-sm">person</span>
                                    <span class="text-xs text-on-surface-variant">{{ $activity->guru }}</span>

                                    <!-- Badge Status Kehadiran (Hijau jika Hadir, Merah jika Tidak Hadir) -->
                                    @if(strtolower($activity->status ?? 'hadir') !== 'hadir')
                                        <span class="ml-auto text-xs font-medium text-error bg-error-container px-2 py-0.5 rounded-full">
                                            {{ ucfirst($activity->status ?? 'Tidak Hadir') }}
                                        </span>
                                    @else
                                        <span class="ml-auto text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                            Hadir
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @empty
                    <p class="text-center text-on-surface-variant py-8">Belum ada aktivitas untuk periode ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline-line::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #c5c5d3;
    }
    @media (max-width: 768px) {
        .timeline-line::before {
            left: 16px;
        }
    }
    .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
    }
</style>
@endpush