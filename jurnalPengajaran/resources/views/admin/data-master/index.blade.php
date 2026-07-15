@extends('layouts.app')

@section('title', 'Data Master - E-Jurnal')

@section('content')
<div class="max-w-6xl mx-auto w-full flex flex-col gap-6 py-6">
    <section>
        <h2 class="font-display-lg text-3xl font-bold text-slate-800 mb-1">Data Master</h2>
        <p class="text-body-base text-slate-500">Kelola data guru, kelas, dan mata pelajaran.</p>
    </section>

    <!-- Tombol Tambah Data -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('data-master.guru.create') }}" 
           class="bg-primary text-on-primary px-4 py-2 rounded-lg font-label-caps text-[11px] flex items-center gap-2 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">add</span> TAMBAH GURU
        </a>
        <a href="{{ route('data-master.kelas.create') }}" 
           class="bg-green-600 text-white px-4 py-2 rounded-lg font-label-caps text-[11px] flex items-center gap-2 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">add</span> TAMBAH KELAS
        </a>
        <a href="{{ route('data-master.mapel.create') }}" 
           class="bg-purple-600 text-white px-4 py-2 rounded-lg font-label-caps text-[11px] flex items-center gap-2 hover:opacity-90 transition-opacity">
            <span class="material-symbols-outlined text-sm">add</span> TAMBAH MAPEL
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Guru Card -->
        <a href="{{ route('data-master.guru') }}" 
           class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-teal-300 transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition-all">
                    <span class="material-symbols-outlined text-2xl">school</span>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">Guru</p>
                    <p class="text-sm text-slate-500">{{ $totalGuru ?? 0 }} data</p>
                </div>
                <span class="material-symbols-outlined ml-auto text-slate-400 group-hover:text-blue-600 transition-all">arrow_forward</span>
            </div>
        </a>

        <!-- Kelas Card -->
        <a href="{{ route('data-master.kelas') }}" 
           class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-green-300 transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-green-50 text-green-600 rounded-xl flex items-center justify-center group-hover:bg-green-100 transition-all">
                    <span class="material-symbols-outlined text-2xl">class</span>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">Kelas</p>
                    <p class="text-sm text-slate-500">{{ $totalKelas ?? 0 }} data</p>
                </div>
                <span class="material-symbols-outlined ml-auto text-slate-400 group-hover:text-green-600 transition-all">arrow_forward</span>
            </div>
        </a>

        <!-- Mapel Card -->
        <a href="{{ route('data-master.mapel') }}" 
           class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-purple-300 transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center group-hover:bg-purple-100 transition-all">
                    <span class="material-symbols-outlined text-2xl">menu_book</span>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">Mata Pelajaran</p>
                    <p class="text-sm text-slate-500">{{ $totalMapel ?? 0 }} data</p>
                </div>
                <span class="material-symbols-outlined ml-auto text-slate-400 group-hover:text-purple-600 transition-all">arrow_forward</span>
            </div>
        </a>
    </div>
</div>
@endsection