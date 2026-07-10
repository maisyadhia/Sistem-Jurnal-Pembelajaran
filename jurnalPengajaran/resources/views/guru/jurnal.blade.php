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
<div class="max-w-5xl mx-auto w-full flex flex-col gap-8">
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
    
    <section class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
        <div class="glass-card p-6 rounded-xl flex items-center gap-4">
            <div class="w-12 h-12 bg-primary-fixed rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">meeting_room</span>
            </div>
            <div>
                <p class="text-label-caps font-label-caps text-on-surface-variant/70 uppercase">Kelas Terdeteksi</p>
                <p class="text-headline-md font-headline-md text-primary">{{ $jadwal->nama_kelas ?? 'Kelas -' }}</p>
            </div>
        </div>

        <div class="glass-card p-6 rounded-xl flex items-center gap-4 border-l-4 border-l-secondary">
            <div class="w-12 h-12 bg-secondary-fixed rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">calculate</span>
            </div>
            <div>
                <p class="text-label-caps font-label-caps text-on-surface-variant/70 uppercase">Mata Pelajaran</p>
                <p class="text-headline-md font-headline-md text-secondary">{{ $jadwal->nama_mapel ?? 'Mata Pelajaran -' }}</p>
            </div>
        </div>

        <div class="glass-card p-6 rounded-xl flex items-center gap-4">
            <div class="w-12 h-12 bg-surface-container-high rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined text-on-surface-variant" style="font-variation-settings: 'FILL' 1;">schedule</span>
            </div>
            <div>
                <p class="text-label-caps font-label-caps text-on-surface-variant/70 uppercase">Waktu Sesi</p>
                <p class="text-headline-md font-headline-md text-on-surface">
                    @if(isset($jadwal->jam_mulai) && isset($jadwal->jam_selesai))
                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H.i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H.i') }}
                    @else
                        Sesi Aktif
                    @endif
                </p>
            </div>
        </div>
    </section>
    
    <form class="flex flex-col gap-6" method="POST" action="{{ route('guru.jurnal.store') }}" id="jurnalForm">
        @csrf
        <input type="hidden" name="kelas_id" value="{{ request()->route('kelas_id') }}">
        <input type="hidden" name="mapel_id" value="{{ request()->route('mapel_id') }}">
        
        <div class="bg-white rounded-xl border border-outline-variant overflow-hidden">
            <div class="p-6 border-b border-outline-variant bg-surface-container-low/50">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">history_edu</span>
                    <h3 class="font-headline-md text-headline-md">Rangkuman Pengajaran</h3>
                </div>
            </div>
            
            <div class="p-8 space-y-8">
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

                <div class="space-y-4 pt-4 border-t border-outline-variant/30">
                    <label class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-wider">
                        Pilih Siswa & Atur Catatan / Kehadiran Spesifik
                    </label>
                    
                    <div class="flex flex-col gap-4 bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
                        @forelse($daftar_siswa as $siswa)
                            <div class="p-4 bg-surface-container-low/60 rounded-xl border border-outline-variant/50 transition-all student-card">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input class="w-5 h-5 rounded text-primary focus:ring-primary border-outline-variant cursor-pointer student-checkbox" 
                                           type="checkbox" 
                                           name="student_ids[]" 
                                           value="{{ $siswa->id }}"
                                           onchange="toggleStudentDetail(this, 'detail-{{ $siswa->id }}')"/>
                                    <span class="font-body-base text-on-surface font-semibold">{{ $siswa->name }}</span>
                                </label>

                                <div id="detail-{{ $siswa->id }}" class="hidden mt-4 pl-8 space-y-3 border-l-2 border-primary/30 animate-fade-in">
                                    <div class="flex items-center gap-6">
                                        <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Status:</span>
                                        <div class="flex items-center gap-4">
                                            <label class="flex items-center gap-1.5 text-sm cursor-pointer text-slate-700">
                                                <input type="radio" name="status[{{ $siswa->id }}]" value="Hadir" checked class="text-primary focus:ring-primary"> Hadir
                                            </label>
                                            <label class="flex items-center gap-1.5 text-sm cursor-pointer text-amber-600">
                                                <input type="radio" name="status[{{ $siswa->id }}]" value="Sakit" class="text-amber-500 focus:ring-amber-500"> Sakit
                                            </label>
                                            <label class="flex items-center gap-1.5 text-sm cursor-pointer text-blue-600">
                                                <input type="radio" name="status[{{ $siswa->id }}]" value="Izin" class="text-blue-500 focus:ring-blue-500"> Izin
                                            </label>
                                            <label class="flex items-center gap-1.5 text-sm cursor-pointer text-error">
                                                <input type="radio" name="status[{{ $siswa->id }}]" value="Alpha" class="text-error focus:ring-error"> Alpha
                                            </label>
                                        </div>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label class="text-xs font-bold text-on-surface-variant uppercase tracking-wider" for="note-{{ $siswa->id }}">
                                            Catatan Khusus Wali Murid (Opsional)
                                        </label>
                                        <textarea class="w-full bg-white border border-outline-variant rounded-lg p-3 text-sm font-body-base text-body-base input-focus transition-all resize-none" 
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
                
                <div class="flex flex-wrap gap-4 pt-4 border-t border-outline-variant/30">
                    <label class="flex items-center gap-3 px-4 py-2 bg-surface-container-low rounded-lg cursor-pointer hover:bg-surface-container-high transition-all">
                        <input class="w-5 h-5 rounded text-primary focus:ring-primary border-outline-variant" 
                               type="checkbox" 
                               name="rpp_completed" 
                               value="1" 
                               {{ old('rpp_completed', $jurnal->rpp_completed ?? true) ? 'checked' : '' }}/>
                        <span class="font-body-sm text-on-surface">Materi Selesai Sesuai RPP</span>
                    </label>
                </div>
            </div>
            
            <div class="p-6 bg-surface-container-low border-t border-outline-variant flex justify-end items-center gap-4">
                <a href="{{ route('guru.dashboard') }}" 
                    class="px-6 py-2.5 rounded-lg border border-outline text-on-surface-variant font-label-caps text-label-caps uppercase hover:bg-white transition-all text-center justify-center flex items-center">
                    Batal
                </a>
                <button class="flex items-center gap-2 px-10 py-2.5 bg-primary text-white font-label-caps text-label-caps uppercase rounded-lg hover:bg-on-primary-fixed-variant transition-all transform active:scale-95" id="submitBtn" type="submit">
                    <span id="btnText">Kirim Jurnal</span>
                    <span class="material-symbols-outlined text-lg" id="btnIcon">send</span>
                </button>
            </div>
        </div>
    </form>
</div>

<div class="hidden fixed bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-3 bg-secondary px-6 py-4 rounded-xl text-white shadow-2xl z-50 animate-bounce" id="successToast">
    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">check_circle</span>
    <p class="font-label-caps uppercase font-bold">Jurnal Berhasil Dikirim &amp; Diarsipkan</p>
</div>
@endsection

@push('scripts')
<script>
    // Fungsi Toggle Input Detail Siswa Berdasarkan Checkbox
    function toggleStudentDetail(checkbox, detailId) {
        const detailPanel = document.getElementById(detailId);
        if (checkbox.checked) {
            detailPanel.classList.remove('hidden');
        } else {
            detailPanel.classList.add('hidden');
            detailPanel.querySelector('textarea').value = '';
            detailPanel.querySelector('input[value="Hadir"]').checked = true;
        }
    }

    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');
    const successToast = document.getElementById('successToast');
    const jurnalForm = document.getElementById('jurnalForm');
    
    // Handle form submission
    jurnalForm.addEventListener('submit', function(e) {
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