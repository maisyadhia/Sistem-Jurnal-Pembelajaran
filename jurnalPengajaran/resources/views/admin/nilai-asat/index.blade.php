@extends('layouts.app')

@section('title', 'Nilai ASAT - E-Jurnal')

@push('styles')
<style>
    .stat-card { transition: all 0.2s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto space-y-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Nilai ASAT</h2>
            <p class="text-sm text-slate-500">Import nilai Asesmen Sumatif Akhir Tahun dari RDM (Raport Digital Madrasah).</p>
        </div>
        <a href="{{ route('data-master') }}" 
           class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Kembali ke Data Master
        </a>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="stat-card bg-white p-4 rounded-xl border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined">groups</span>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Total Siswa Aktif</p>
                    <p class="text-xl font-bold text-slate-800">{{ $totalSiswa }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white p-4 rounded-xl border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-50 text-green-600 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined">check_circle</span>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Sudah Ada Nilai</p>
                    <p class="text-xl font-bold text-green-600">{{ $totalNilai }}</p>
                </div>
            </div>
        </div>
        <div class="stat-card bg-white p-4 rounded-xl border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined">warning</span>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Belum Ada Nilai</p>
                    <p class="text-xl font-bold text-amber-600">{{ $belumInput }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Import -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined">upload_file</span>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">Import Excel RDM</h3>
                <p class="text-xs text-slate-500">Upload file nilai ASAT dari Raport Digital Madrasah.</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('nilai-asat.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                        @foreach(config('tahun_ajaran.opsi') as $ta)
                            <option value="{{ $ta }}" {{ $ta == $tahunAjaran ? 'selected' : '' }}>{{ $ta }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">File Excel</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none
                                  file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:bg-primary file:text-white file:text-xs">
                </div>
            </div>

            @error('file')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
            @error('tahun_ajaran')
                <p class="text-red-500 text-sm">{{ $message }}</p>
            @enderror
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-xs text-blue-700 font-semibold mb-1">📋 Format File:</p>
                <ul class="text-xs text-blue-600 space-y-0.5">
                    <li>• Kolom <strong>nisn</strong> (wajib)</li>
                    <li>• Kolom <strong>rata_rata</strong> atau <strong>nilai_total</strong> (wajib)</li>
                    <li>• Kolom <strong>nama</strong>, <strong>kelas</strong> (opsional)</li>
                    <li>• NISN harus sesuai dengan data siswa</li>
                </ul>
            </div>
            
            <div class="flex flex-wrap justify-end gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('nilai-asat.template') }}" 
                   class="px-4 py-2 border border-slate-200 rounded-lg text-sm hover:bg-slate-50 flex items-center gap-2 text-slate-700">
                    <span class="material-symbols-outlined text-sm">download</span>
                    Download Template
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-primary text-white rounded-lg text-sm hover:bg-primary/90 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">upload</span>
                    Import
                </button>
            </div>
        </form>
    </div>

    <!-- Filter & Tabel -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-slate-800">Data Nilai ASAT</h3>
                <p class="text-xs text-slate-500">Total {{ $nilaiAsat->total() }} data</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" action="{{ route('nilai-asat.index') }}" class="flex items-center gap-2">
                    <select name="tahun_ajaran" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm">
                        @foreach(config('tahun_ajaran.opsi') as $ta)
                            <option value="{{ $ta }}" {{ $ta == $tahunAjaran ? 'selected' : '' }}>{{ $ta }}</option>
                        @endforeach
                    </select>
                    <select name="kelas" class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($daftarKelas as $k)
                            <option value="{{ $k }}" {{ $kelas == $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-primary text-white px-3 py-1.5 rounded-lg text-sm">
                        Filter
                    </button>
                    @if($kelas || $tahunAjaran != config('tahun_ajaran.aktif'))
                        <a href="{{ route('nilai-asat.index') }}" class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg text-sm">
                            Reset
                        </a>
                    @endif
                </form>
                
                <a href="{{ route('nilai-asat.export', ['tahun_ajaran' => $tahunAjaran]) }}" 
                   class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-sm hover:bg-green-700 flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">download</span>
                    Export
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">No</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Nama</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">NISN</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Kelas</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Gender</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-center">Nilai ASAT</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($nilaiAsat as $index => $n)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-sm">{{ $nilaiAsat->firstItem() + $index }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px]">
                                        {{ strtoupper(substr($n->student->name ?? '??', 0, 2)) }}
                                    </div>
                                    <span class="text-sm font-medium text-slate-800">{{ $n->student->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $n->student->nisn ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs font-medium">{{ $n->kelas }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(($n->student->gender ?? '') === 'L')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">L</span>
                                @elseif(($n->student->gender ?? '') === 'P')
                                    <span class="px-2 py-1 bg-pink-100 text-pink-700 rounded text-xs font-medium">P</span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold text-slate-800">{{ number_format((float) $n->nilai_total, 2) }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('nilai-asat.destroy', $n->id) }}" 
                                      class="inline" 
                                      onsubmit="return confirm('Hapus nilai {{ $n->student->name ?? '' }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 transition-colors">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center">
                                <span class="material-symbols-outlined text-4xl text-slate-300 block mx-auto mb-2">inbox</span>
                                <p class="text-slate-400 italic text-sm">Belum ada data nilai ASAT.</p>
                                <p class="text-xs text-slate-400 mt-1">Import dari Excel RDM untuk memulai.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($nilaiAsat->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $nilaiAsat->links() }}
            </div>
        @endif
    </div>
</div>
@endsection