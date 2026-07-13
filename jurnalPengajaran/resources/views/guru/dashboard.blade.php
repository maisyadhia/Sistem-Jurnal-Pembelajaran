@extends('layouts.app')

@section('title', 'Pilih Sesi Mengajar - E-Jurnal')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .custom-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-size: 1.25rem;
        background-position: right 1rem center;
        background-repeat: no-repeat;
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
<div class="max-w-4xl mx-auto w-full flex flex-col gap-6 py-6">
    
    <section class="text-center md:text-left">
        <h2 class="font-display-lg text-3xl font-bold text-slate-800 mb-1">Pilih Sesi Mengajar</h2>
        <p class="text-body-base text-slate-500">Silakan tentukan ruang kelas dan mata pelajaran sebelum mengisi lembar jurnal harian.</p>
    </section>

    <div class="glass-card p-8 rounded-2xl shadow-xl shadow-slate-100/50 max-w-2xl mx-auto w-full">
        @if(session('warning'))
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl flex gap-3 items-center text-amber-800 animate-fade-in">
                <span class="material-symbols-outlined text-amber-600">warning</span>
                <div class="flex-1 font-body-sm font-medium">
                    {{ session('warning') }}
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex gap-3 items-center text-emerald-800 animate-fade-in">
                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                <div class="flex-1 font-body-sm font-medium">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="flex items-center gap-3 mb-8 pb-4 border-b border-slate-200/60">
            <div class="w-10 h-10 bg-teal-50 text-teal-600 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined font-semibold">grid_view</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Konfirmasi Kelas & Mapel</h3>
                <p class="text-xs text-slate-400">Pastikan sesi yang dipilih sesuai dengan jadwal aktif hari ini.</p>
            </div>
        </div>

        <form id="selectSesiForm" class="space-y-6">
            <div class="space-y-2">
                <label for="kelas" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Kelas Terdeteksi</label>
                <select id="kelas" required class="custom-select w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-slate-700 font-medium focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none cursor-pointer">
                    <option value="" disabled selected>-- Pilih Ruang Kelas --</option>
                    @foreach($daftar_kelas as $kelas)
                        <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label for="mapel" class="block text-xs font-bold uppercase tracking-wider text-slate-500">Mata Pelajaran</label>
                <select id="mapel" required class="custom-select w-full bg-slate-50 border border-slate-200 rounded-xl p-4 text-slate-700 font-medium focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none cursor-pointer">
                    <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
                    @foreach($daftar_mapel as $mapel)
                        <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-500 font-medium text-sm hover:bg-slate-50 transition-all">
                    Kembali
                </a>
                <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-teal-600 hover:bg-teal-700 text-white font-medium text-sm rounded-xl transition-all shadow-lg shadow-teal-600/15 active:scale-[0.98]">
                    <span>Buka Jurnal</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('selectSesiForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const kelasId = document.getElementById('kelas').value;
        const mapelId = document.getElementById('mapel').value;

        // Redirect dinamis ke Form Jurnal dengan parameter ID yang dipilih
        window.location.href = `/guru/jurnal/${kelasId}/${mapelId}`;
    });
</script>
@endpush