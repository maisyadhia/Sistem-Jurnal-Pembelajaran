@extends('layouts.app')

@section('title', 'Guru Input Portal - E-Jurnal')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border: 1px solid #E2E8F0;
    }
    .input-focus:focus {
        border-color: #0d9488;
        outline: none;
        box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.2);
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto w-full flex flex-col gap-8">
    <!-- Header Section -->
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="font-display-lg text-display-lg text-on-background mb-1">Input Jurnal Harian</h2>
            <p class="font-body-base text-body-base text-on-surface-variant">Dokumentasikan progress belajar mengajar anda hari ini.</p>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 bg-secondary-container/30 border border-secondary text-secondary rounded-full">
            <span class="material-symbols-outlined text-sm animate-pulse" style="font-variation-settings: 'FILL' 1;">cloud_done</span>
            <span class="font-label-caps text-[11px] uppercase">Draft Tersimpan Otomatis</span>
        </div>
    </section>
    
    <!-- Context Info Cards -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
        <div class="glass-card p-6 rounded-xl flex items-center gap-4">
            <div class="w-12 h-12 bg-primary-fixed rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">meeting_room</span>
            </div>
            <div>
                <p class="text-label-caps font-label-caps text-on-surface-variant/70 uppercase">Kelas Terdeteksi</p>
                <p class="text-headline-md font-headline-md text-primary">{{ $jurnal->class ?? 'Kelas 10-A' }}</p>
            </div>
        </div>
        <div class="glass-card p-6 rounded-xl flex items-center gap-4 border-l-4 border-l-secondary">
            <div class="w-12 h-12 bg-secondary-fixed rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">calculate</span>
            </div>
            <div>
                <p class="text-label-caps font-label-caps text-on-surface-variant/70 uppercase">Mata Pelajaran</p>
                <p class="text-headline-md font-headline-md text-secondary">{{ $jurnal->subject ?? 'Matematika' }}</p>
            </div>
        </div>
        <div class="glass-card p-6 rounded-xl flex items-center gap-4">
            <div class="w-12 h-12 bg-surface-container-high rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-variation-settings: 'FILL' 1;">schedule</span>
            </div>
            <div>
                <p class="text-label-caps font-label-caps text-on-surface-variant/70 uppercase">Waktu Sesi</p>
                <p class="text-headline-md font-headline-md text-on-surface">{{ $jurnal->time ?? '08:00 - 09:30' }}</p>
            </div>
        </div>
    </section>
    
    <!-- Main Input Form -->
    <form class="flex flex-col gap-6" method="POST" action="{{ route('guru.jurnal.store') }}" id="jurnalForm">
        @csrf
        
        <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
            <div class="p-6 border-b border-outline-variant bg-surface-container-low/50">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">history_edu</span>
                    <h3 class="font-headline-md text-headline-md">Rangkuman Pengajaran</h3>
                </div>
            </div>
            
            <div class="p-8 space-y-8">
                <!-- Today's Topic -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider" for="bahasan">
                            Bahasan Hari Ini
                        </label>
                        <span class="text-body-sm text-outline italic">Terakhir diubah: {{ $lastUpdated ?? '2 menit yang lalu' }}</span>
                    </div>
                    <textarea class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg p-4 font-body-base text-body-base input-focus transition-all resize-none @error('topic') border-error @enderror" 
                              id="bahasan" 
                              name="topic" 
                              placeholder="Tuliskan pokok bahasan, materi yang disampaikan, dan dinamika kelas..." 
                              rows="5">{{ old('topic', $jurnal->topic ?? 'Pengenalan Logaritma: Sifat-sifat dasar logaritma dan hubungannya dengan eksponen. Siswa mengerjakan latihan mandiri hal 42.') }}</textarea>
                    @error('topic')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Next Target -->
                <div class="space-y-3">
                    <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider" for="target">
                        Target Pertemuan Berikutnya
                    </label>
                    <textarea class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg p-4 font-body-base text-body-base input-focus transition-all resize-none @error('next_target') border-error @enderror" 
                              id="target" 
                              name="next_target" 
                              placeholder="Apa yang ingin dicapai pada sesi selanjutnya?" 
                              rows="3">{{ old('next_target', $jurnal->next_target ?? 'Penerapan logaritma dalam perhitungan bunga majemuk dan kuis kecil materi eksponen.') }}</textarea>
                    @error('next_target')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Status Checkboxes -->
                <div class="flex flex-wrap gap-4 pt-4 border-t border-outline-variant/30">
                    <label class="flex items-center gap-3 px-4 py-2 bg-surface-container-low rounded-lg cursor-pointer hover:bg-surface-container-high transition-all">
                        <input class="w-5 h-5 rounded text-primary focus:ring-primary border-outline-variant" 
                               type="checkbox" 
                               name="rpp_completed" 
                               value="1" 
                               {{ old('rpp_completed', $jurnal->rpp_completed ?? true) ? 'checked' : '' }}/>
                        <span class="font-body-sm text-on-surface">Materi Selesai Sesuai RPP</span>
                    </label>
                    <label class="flex items-center gap-3 px-4 py-2 bg-surface-container-low rounded-lg cursor-pointer hover:bg-surface-container-high transition-all">
                        <input class="w-5 h-5 rounded text-primary focus:ring-primary border-outline-variant" 
                               type="checkbox" 
                               name="absent_students" 
                               value="1" 
                               {{ old('absent_students', $jurnal->absent_students ?? false) ? 'checked' : '' }}/>
                        <span class="font-body-sm text-on-surface">Terdapat Siswa Tidak Hadir</span>
                    </label>
                </div>
            </div>
            
            <!-- Action Footer -->
            <div class="p-6 bg-surface-container-low border-t border-outline-variant flex justify-end items-center gap-4">
                <button class="px-6 py-2.5 rounded-lg border border-outline text-on-surface-variant font-label-caps text-label-caps uppercase hover:bg-white transition-all" type="button">
                    Batal
                </button>
                <button class="flex items-center gap-2 px-10 py-2.5 bg-primary text-white font-label-caps text-label-caps uppercase rounded-lg hover:bg-on-primary-fixed-variant transition-all transform active:scale-95" id="submitBtn" type="submit">
                    <span id="btnText">Kirim Jurnal</span>
                    <span class="material-symbols-outlined text-lg" id="btnIcon">send</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Success Toast -->
<div class="hidden fixed bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-3 bg-secondary px-6 py-4 rounded-xl text-white shadow-2xl z-50 animate-bounce" id="successToast">
    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
    <p class="font-label-caps uppercase font-bold">Jurnal Berhasil Dikirim &amp; Diarsipkan</p>
</div>
@endsection

@push('scripts')
<script>
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');
    const successToast = document.getElementById('successToast');
    const jurnalForm = document.getElementById('jurnalForm');
    
    // Handle form submission
    jurnalForm.addEventListener('submit', function(e) {
        // Show loading state
        btnText.textContent = 'Memproses...';
        btnIcon.textContent = 'sync';
        btnIcon.classList.add('animate-spin');
        submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
        submitBtn.disabled = true;
    });
    
    // If there's a success message from server
    @if(session('success'))
        setTimeout(() => {
            successToast.classList.remove('hidden');
            setTimeout(() => {
                successToast.classList.add('hidden');
                btnText.textContent = 'Kirim Jurnal';
                btnIcon.textContent = 'send';
                btnIcon.classList.remove('animate-spin');
                submitBtn.classList.remove('opacity-80', 'cursor-not-allowed');
                submitBtn.disabled = false;
            }, 3000);
        }, 500);
    @endif
    
    // Auto-save indicator flicker
    setInterval(() => {
        const indicator = document.querySelector('.animate-pulse');
        if (indicator) {
            indicator.style.opacity = '0.5';
            setTimeout(() => indicator.style.opacity = '1', 500);
        }
    }, 5000);
</script>
@endpush