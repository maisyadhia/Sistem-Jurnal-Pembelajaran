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
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto w-full flex flex-col gap-4 md:gap-8 pt-2 pb-6 px-3 md:px-6">
    <!-- Header Page -->
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-3">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-slate-800 mb-1">Input Jurnal Harian</h2>
            <p class="text-xs md:text-sm text-slate-500">Dokumentasikan progress belajar mengajar anda hari ini.</p>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 bg-secondary-container/30 border border-secondary text-secondary rounded-full w-fit">
            <span class="material-symbols-outlined text-sm animate-pulse" style="font-variation-settings: 'FILL' 1;">cloud_done</span>
            <span class="font-label-caps text-[10px] md:text-[11px] uppercase">Draft Tersimpan Otomatis</span>
        </div>
    </section>
    
    <!-- 3 Cards Info Sesi (Responsif di HP) -->
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-gutter">
        <div class="glass-card p-4 md:p-6 rounded-xl flex items-center gap-4">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-primary-fixed rounded-lg flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">meeting_room</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-label-caps font-label-caps text-on-surface-variant/70 uppercase">Kelas Terdeteksi</p>
                <p class="text-base md:text-headline-md font-headline-md text-primary truncate">{{ $jadwal->nama_kelas ?? 'Kelas -' }}</p>
            </div>
        </div>

        <div class="glass-card p-4 md:p-6 rounded-xl flex items-center gap-4 border-l-4 border-l-secondary">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-secondary-fixed rounded-lg flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">calculate</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-label-caps font-label-caps text-on-surface-variant/70 uppercase">Mata Pelajaran</p>
                <p class="text-base md:text-headline-md font-headline-md text-secondary truncate">{{ $jadwal->nama_mapel ?? 'Mata Pelajaran -' }}</p>
            </div>
        </div>

        <!-- Card Waktu Sesi (Otomatis menyesuaikan jadwal berurutan / terputus) -->
        <div class="glass-card p-4 md:p-6 rounded-xl flex items-center gap-4">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-surface-container-high rounded-lg flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-variation-settings: 'FILL' 1;">schedule</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-label-caps font-label-caps text-on-surface-variant/70 uppercase">Waktu Sesi</p>
                
                @if(isset($jadwal->waktu_list) && count($jadwal->waktu_list) > 1 && !($jadwal->is_sequential ?? true))
                    <div class="flex flex-col gap-0.5 mt-0.5">
                        @foreach($jadwal->waktu_list as $waktuItem)
                            <p class="text-xs md:text-sm font-bold text-slate-800 leading-tight">
                                {{ $waktuItem }}
                            </p>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs md:text-base font-bold text-slate-800 truncate">
                        {{ $jadwal->waktu_text ?? 'Sesi Aktif' }}
                    </p>
                @endif

                @if(!empty($jadwal->jam_ke_text))
                    <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">{{ $jadwal->jam_ke_text }}</span>
                @endif
            </div>
        </div>
    </section>
    
    <!-- Form Utama -->
    <form class="flex flex-col gap-4 md:gap-6" method="POST" action="{{ route('guru.jurnal.store') }}" id="jurnalForm">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ request()->route('kelas_id') }}">
        <input type="hidden" name="mapel_id" value="{{ request()->route('mapel_id') }}">
        
        <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
            <div class="p-4 md:p-6 border-b border-outline-variant bg-surface-container-low/50">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">history_edu</span>
                    <h3 class="font-bold text-slate-800 text-sm md:text-base">Rangkuman Pengajaran</h3>
                </div>
            </div>
            
            <div class="p-4 md:p-8 space-y-6 md:space-y-8">
                <!-- Bahasan Hari Ini -->
                <div class="space-y-2 md:space-y-3">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-1">
                        <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider" for="bahasan">
                            Bahasan Hari Ini <span class="text-error">*</span>
                        </label>
                        <span class="text-body-sm text-outline italic text-xs">Terakhir diubah: {{ $lastUpdated ?? 'Baru saja' }}</span>
                    </div>
                    <textarea class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg p-3 md:p-4 font-body-base text-xs md:text-body-base input-focus transition-all resize-none @error('topic') border-error @enderror" 
                              id="bahasan" 
                              name="topic" 
                              placeholder="Tuliskan pokok bahasan, materi yang disampaikan, dan dinamika kelas..." 
                              rows="4">{{ old('topic', $jurnal->topic ?? '') }}</textarea>
                    @error('topic')
                        <p class="text-error text-xs md:text-sm font-medium mt-1">Bahasan hari ini wajib diisi.</p>
                    @enderror
                </div>
                
                <!-- Target Pertemuan Berikutnya -->
                <div class="space-y-2 md:space-y-3">
                    <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider" for="target">
                        Target Pertemuan Berikutnya
                    </label>
                    <textarea class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg p-3 md:p-4 font-body-base text-xs md:text-body-base input-focus transition-all resize-none @error('next_target') border-error @enderror" 
                              id="target" 
                              name="next_target" 
                              placeholder="Apa yang ingin dicapai pada sesi selanjutnya?" 
                              rows="3">{{ old('next_target', $jurnal->next_target ?? '') }}</textarea>
                    @error('next_target')
                        <p class="text-error text-xs md:text-sm font-medium mt-1">Format target pertemuan berikutnya tidak valid.</p>
                    @enderror
                </div>

                <!-- Daftar Siswa -->
                <div class="space-y-3 md:space-y-4 pt-3 md:pt-4 border-t border-outline-variant/30">
                    <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider block">
                        Pilih Siswa & Atur Catatan / Kehadiran Spesifik
                    </label>
                    
                    <div class="flex flex-col gap-3 md:gap-4 bg-surface-container-lowest border border-outline-variant rounded-xl p-3.5 md:p-6">
                        @forelse($daftar_siswa as $siswa)
                            <div class="p-3 md:p-4 bg-surface-container-low/60 rounded-xl border border-outline-variant/50 transition-all student-card">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input class="w-4 h-4 md:w-5 md:h-5 rounded text-primary focus:ring-primary border-outline-variant cursor-pointer student-checkbox" 
                                           type="checkbox" 
                                           name="student_ids[]" 
                                           value="{{ $siswa->id }}"
                                           checked
                                           onchange="toggleStudentDetail(this, 'detail-{{ $siswa->id }}')"/>
                                    <span class="font-body-base text-xs md:text-body-base text-on-surface font-semibold">{{ $siswa->name }}</span>
                                </label>

                                <div id="detail-{{ $siswa->id }}" class="mt-3 md:mt-4 pl-3 md:pl-8 space-y-3 border-l-2 border-primary/30 animate-fade-in">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-6">
                                        <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Status:</span>
                                        <div class="flex items-center gap-3 md:gap-4 flex-wrap">
                                            <label class="flex items-center gap-1.5 text-xs md:text-sm cursor-pointer text-slate-700">
                                                <input type="radio" name="status[{{ $siswa->id }}]" value="Hadir" checked class="text-primary focus:ring-primary"> Hadir
                                            </label>
                                            <label class="flex items-center gap-1.5 text-xs md:text-sm cursor-pointer text-amber-600">
                                                <input type="radio" name="status[{{ $siswa->id }}]" value="Sakit" class="text-amber-500 focus:ring-amber-500"> Sakit
                                            </label>
                                            <label class="flex items-center gap-1.5 text-xs md:text-sm cursor-pointer text-blue-600">
                                                <input type="radio" name="status[{{ $siswa->id }}]" value="Izin" class="text-blue-500 focus:ring-blue-500"> Izin
                                            </label>
                                            <label class="flex items-center gap-1.5 text-xs md:text-sm cursor-pointer text-error">
                                                <input type="radio" name="status[{{ $siswa->id }}]" value="Alpha" class="text-error focus:ring-error"> Alpha
                                            </label>
                                        </div>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider" for="note-{{ $siswa->id }}">
                                            Catatan Khusus Wali Murid (Opsional)
                                        </label>
                                        <textarea class="w-full bg-white border border-outline-variant rounded-lg p-2.5 md:p-3 text-xs md:text-sm font-body-base text-body-base input-focus transition-all resize-none" 
                                                  id="note-{{ $siswa->id }}" 
                                                  name="notes[{{ $siswa->id }}]" 
                                                  placeholder="Contoh: Terlambat masuk kelas / Aktif menjawab pertanyaan..." 
                                                  rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic p-2 col-span-full">Tidak ada data siswa terdeteksi di kelas ini.</p>
                        @endforelse
                    </div>
                </div>
                
                <!-- Checkbox RPP -->
                <div class="flex flex-wrap gap-4 pt-3 md:pt-4 border-t border-outline-variant/30">
                    <label class="flex items-center gap-3 px-3.5 py-2 bg-surface-container-low rounded-lg cursor-pointer hover:bg-surface-container-high transition-all">
                        <input class="w-4 h-4 md:w-5 md:h-5 rounded text-primary focus:ring-primary border-outline-variant" 
                               type="checkbox" 
                               name="rpp_completed" 
                               value="1" 
                               {{ old('rpp_completed', $jurnal->rpp_completed ?? true) ? 'checked' : '' }}/>
                        <span class="font-body-sm text-xs md:text-body-sm text-on-surface">Materi Selesai Sesuai RPP</span>
                    </label>
                </div>
            </div>
            
            <!-- Footer Form Buttons -->
            <div class="p-4 md:p-6 bg-surface-container-low border-t border-outline-variant flex justify-end items-center gap-3 md:gap-4">
                <a href="{{ route('guru.dashboard') }}" 
                    class="px-4 md:px-6 py-2.5 rounded-lg border border-outline text-on-surface-variant font-label-caps text-label-caps text-xs md:text-sm uppercase hover:bg-white transition-all text-center justify-center flex items-center">
                    Batal
                </a>
                <button class="flex items-center gap-2 px-6 md:px-10 py-2.5 bg-primary text-white font-label-caps text-label-caps text-xs md:text-sm uppercase rounded-lg hover:bg-on-primary-fixed-variant transition-all transform active:scale-95" id="submitBtn" type="submit">
                    <span id="btnText">Kirim Jurnal</span>
                    <span class="material-symbols-outlined text-base md:text-lg" id="btnIcon">send</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Toast Berhasil -->
<div class="hidden fixed bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-3 bg-secondary px-6 py-4 rounded-xl text-white shadow-2xl z-50 animate-bounce max-w-[90vw]" id="successToast">
    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
    <p class="font-label-caps uppercase font-bold text-xs md:text-sm">Jurnal Berhasil Dikirim &amp; Diarsipkan</p>
</div>
@endsection

@push('scripts')
<script>
    function toggleStudentDetail(checkbox, detailId) {
        const detailPanel = document.getElementById(detailId);
        if (checkbox.checked) {
            detailPanel.classList.remove('hidden');
        } else {
            detailPanel.classList.add('hidden');
            const noteArea = detailPanel.querySelector('textarea');
            if(noteArea) noteArea.value = '';
            const hadirRadio = detailPanel.querySelector('input[value="Hadir"]');
            if(hadirRadio) hadirRadio.checked = true;
        }
    }

    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');
    const successToast = document.getElementById('successToast');
    const jurnalForm = document.getElementById('jurnalForm');
    
    jurnalForm.addEventListener('submit', function(e) {
        btnText.textContent = 'Memproses...';
        btnIcon.textContent = 'sync';
        btnIcon.classList.add('animate-spin');
        submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
        submitBtn.disabled = true;
    });
    
    @if(session('success'))
        setTimeout(() => {
            if(successToast) successToast.classList.remove('hidden');
            setTimeout(() => {
                if(successToast) successToast.classList.add('hidden');
                btnText.textContent = 'Kirim Jurnal';
                btnIcon.textContent = 'send';
                btnIcon.classList.remove('animate-spin');
                submitBtn.classList.remove('opacity-80', 'cursor-not-allowed');
                submitBtn.disabled = false;
            }, 3000);
        }, 500);
    @endif

    setInterval(() => {
        const indicator = document.querySelector('.animate-pulse');
        if (indicator) {
            indicator.style.opacity = '0.5';
            setTimeout(() => indicator.style.opacity = '1', 500);
        }
    }, 5000);
</script>
@endpush