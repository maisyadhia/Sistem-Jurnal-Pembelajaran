@extends('layouts.app')

@section('title', 'Dashboard Guru - E-Jurnal')

@push('styles')
<style>
    .stat-card {
        transition: all 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto w-full flex flex-col gap-6 py-6">
    <!-- Header -->
    <section>
        <h2 class="font-display-lg text-3xl font-bold text-slate-800 mb-1">
            Selamat Datang, {{ session('user_name') }}
        </h2>
        <p class="text-body-base text-slate-500">Ringkasan aktivitas mengajar Anda</p>
    </section>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">class</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Kelas</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalKelas ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">menu_book</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Total Mapel</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $totalMapel ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-2xl">today</span>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Jurnal Hari Ini</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $jurnalHariIni ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('guru.pilih.sesi') }}" 
           class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-teal-300 transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center group-hover:bg-teal-100 transition-all">
                    <span class="material-symbols-outlined text-2xl">edit_note</span>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">Buat Jurnal Baru</p>
                    <p class="text-sm text-slate-500">Isi jurnal mengajar hari ini</p>
                </div>
                <span class="material-symbols-outlined ml-auto text-slate-400 group-hover:text-teal-600 transition-all">arrow_forward</span>
            </div>
        </a>
        
        <a href="#" 
           class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-blue-300 transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-all">
                    <span class="material-symbols-outlined text-2xl">history</span>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">Riwayat Jurnal</p>
                    <p class="text-sm text-slate-500">Lihat jurnal yang sudah diisi</p>
                </div>
                <span class="material-symbols-outlined ml-auto text-slate-400 group-hover:text-blue-600 transition-all">arrow_forward</span>
            </div>
        </a>
    </div>

    <!-- Recent Journals -->
    @if(isset($jurnalTerbaru) && $jurnalTerbaru->count() > 0)
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Jurnal Terbaru</h3>
        <div class="divide-y divide-slate-100">
            @foreach($jurnalTerbaru as $jurnal)
            <div class="py-3 flex items-center justify-between">
                <div>
                    <p class="font-medium text-slate-800">{{ $jurnal->subject ?? 'Mata Pelajaran' }}</p>
                    <p class="text-sm text-slate-500">{{ $jurnal->topic ?? 'Topik' }}</p>
                </div>
                <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($jurnal->date_time)->format('d M Y H:i') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection