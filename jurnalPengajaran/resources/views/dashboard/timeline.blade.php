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
        <button class="px-4 py-2 font-label-caps text-label-caps text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-all date-filter">Hari Ini</button>
        <button class="px-4 py-2 font-label-caps text-label-caps bg-primary text-on-primary rounded-lg shadow-sm date-filter active">1 Minggu</button>
        <button class="px-4 py-2 font-label-caps text-label-caps text-on-surface-variant hover:bg-surface-container-low rounded-lg transition-all date-filter">1 Bulan</button>
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
                <h3 class="font-label-caps text-label-caps text-primary">Kehadiran Minggu Ini</h3>
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
        
        <div class="bg-surface-container-lowest border border-outline-variant p-6 rounded-xl relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="font-label-caps text-label-caps text-primary mb-4">Catatan Wali Kelas</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant italic">
                    "{{ $note->content ?? 'Aditya menunjukkan peningkatan signifikan dalam partisipasi diskusi kelas Biologi. Pertahankan fokusnya.' }}"
                </p>
                <div class="mt-4 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-tertiary-fixed flex items-center justify-center">
                        <span class="material-symbols-outlined text-tertiary text-sm">person</span>
                    </div>
                    <span class="font-label-caps text-[10px] text-on-surface-variant">{{ $note->teacher ?? 'Drs. Bambang S.' }}</span>
                </div>
            </div>
            <div class="absolute -right-4 -bottom-4 opacity-5">
                <span class="material-symbols-outlined text-9xl">format_quote</span>
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
                @forelse($activities as $activity)
                    <x-timeline-item :activity="$activity" />
                @empty
                    <p class="text-center text-on-surface-variant">Belum ada aktivitas untuk periode ini.</p>
                @endforelse
            </div>
            
            <div class="mt-12 flex justify-center">
                <button class="flex items-center gap-2 px-6 py-2 border border-outline-variant rounded-full font-label-caps text-label-caps text-primary hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-sm">expand_more</span>
                    Tampilkan Aktivitas Sebelumnya
                </button>
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
</style>
@endpush

@push('scripts')
<script>
    // Date filter switching
    document.querySelectorAll('.date-filter').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.date-filter').forEach(b => {
                b.classList.remove('bg-primary', 'text-on-primary', 'shadow-sm');
                b.classList.add('text-on-surface-variant', 'hover:bg-surface-container-low');
            });
            this.classList.add('bg-primary', 'text-on-primary', 'shadow-sm');
            this.classList.remove('text-on-surface-variant', 'hover:bg-surface-container-low');
        });
    });
</script>
@endpush