@extends('layouts.app')

@section('title', 'Edit Materi Jurnal - SIMJAR')

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
<div class="max-w-4xl mx-auto w-full flex flex-col gap-4 md:gap-6 pt-2 pb-6 px-3 md:px-6">
    <!-- Header Page -->
    <section class="flex flex-col md:flex-row md:items-end justify-between gap-3">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-slate-800 mb-1">Edit Bahasan Materi & Target Pembelajaran</h2>
            <p class="text-xs md:text-sm text-slate-500">Perbarui pokok bahasan atau target capaian pembelajaran kelas ini.</p>
        </div>
    </section>

    <!-- Info Kelas & Tanggal (Read Only) -->
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="glass-card p-4 rounded-xl flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-lg">meeting_room</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] uppercase font-bold text-slate-400">Kelas</p>
                <p class="text-sm font-bold text-slate-800 truncate">{{ $jadwal->nama_kelas ?? 'Kelas -' }}</p>
            </div>
        </div>

        <div class="glass-card p-4 rounded-xl flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-lg">menu_book</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] uppercase font-bold text-slate-400">Mata Pelajaran</p>
                <p class="text-sm font-bold text-teal-700 truncate">{{ $jadwal->nama_mapel ?? 'Mapel -' }}</p>
            </div>
        </div>

        <div class="glass-card p-4 rounded-xl flex items-center gap-3">
            <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-lg">calendar_today</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] uppercase font-bold text-slate-400">Tanggal KBM</p>
                <p class="text-sm font-bold text-slate-800 truncate">{{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d F Y') }}</p>
            </div>
        </div>
    </section>
    
    <!-- Form Utama Edit -->
    <form class="flex flex-col gap-4 md:gap-6" method="POST" action="{{ route('guru.jurnal.update', $jurnal->id) }}" id="jurnalForm">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-xl border border-outline-variant overflow-hidden shadow-sm">
            <div class="p-4 md:p-5 border-b border-outline-variant bg-surface-container-low/50 flex items-center gap-2">
                <span class="material-symbols-outlined text-teal-600">edit_note</span>
                <h3 class="font-bold text-slate-800 text-sm md:text-base">Perbarui Ringkasan Pengajaran</h3>
            </div>
            
            <div class="p-4 md:p-6 space-y-5">
                <!-- Bahasan Hari Ini -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600" for="bahasan">
                        Bahasan Hari Ini <span class="text-red-500">*</span>
                    </label>
                    <textarea class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 md:p-4 text-xs md:text-sm font-medium text-slate-800 input-focus transition-all resize-none @error('topic') border-red-500 @enderror" 
                              id="bahasan" 
                              name="topic" 
                              placeholder="Tuliskan pokok bahasan materi..." 
                              rows="4" required>{{ old('topic', $jurnal->materi) }}</textarea>
                    @error('topic')
                        <p class="text-red-500 text-xs font-medium mt-1">Bahasan hari ini wajib diisi.</p>
                    @enderror
                </div>
                
                <!-- Target Pertemuan Berikutnya -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600" for="target">
                        Target Pembelajaran Hari Ini
                    </label>
                    <textarea class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 md:p-4 text-xs md:text-sm font-medium text-slate-800 input-focus transition-all resize-none" 
                              id="target" 
                              name="next_target" 
                              placeholder="Apa yang ingin dicapai pada sesi selanjutnya?" 
                              rows="3">{{ old('next_target', $jurnal->target_next) }}</textarea>
                </div>
            </div>
            
            <!-- Footer Form Buttons -->
            <div class="p-4 md:p-5 bg-slate-50 border-t border-slate-100 flex justify-end items-center gap-3">
                <a href="{{ route('guru.dashboard') }}" 
                   class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs uppercase hover:bg-white transition-all">
                    Batal
                </a>
                <button type="submit" 
                        class="flex items-center gap-2 px-6 py-2.5 bg-teal-600 text-white font-bold text-xs uppercase rounded-xl hover:bg-teal-700 transition-all shadow-md shadow-teal-600/15">
                    <span>Simpan Perubahan</span>
                    <span class="material-symbols-outlined text-sm">save</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection