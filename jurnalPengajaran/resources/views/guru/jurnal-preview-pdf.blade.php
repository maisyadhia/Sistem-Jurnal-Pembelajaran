@extends('layouts.app')

@section('title', 'Konfirmasi Parameter Cetak PDF Jurnal - SIMJAR')

@section('content')
<div class="max-w-4xl mx-auto w-full flex flex-col gap-4 md:gap-6 pt-2 pb-6 px-3 md:px-6">
    <section class="text-center md:text-left pt-1 md:pt-0">
        <h2 class="text-xl md:text-2xl font-bold text-slate-800 mb-1">Konfirmasi Data Header Cetak PDF</h2>
        <p class="text-xs md:text-sm text-slate-500">Periksa atau sesuaikan informasi header dokumen sebelum mengunduh / mencetak laporan jurnal mengajar.</p>
    </section>

    <div class="bg-white p-5 md:p-8 rounded-2xl shadow-sm border border-slate-200/80">
        <form method="GET" action="{{ route('guru.jurnal.export-pdf') }}" target="_blank" class="space-y-4 md:space-y-6">
            
            @if(request()->filled('filter'))
                <input type="hidden" name="filter" value="{{ request('filter') }}">
            @endif
            @if(request()->filled('tanggal'))
                <input type="hidden" name="tanggal" value="{{ request('tanggal') }}">
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Nama Penyusun (Guru)</label>
                    <input type="text" name="nama_penyusun" value="{{ old('nama_penyusun', $guruName) }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none" required />
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Satuan Pendidikan</label>
                    <input type="text" name="satuan_pendidikan" value="MIN 2 Kota Malang" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none" required />
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Mata Pelajaran Header</label>
                    <input type="text" name="mata_pelajaran" value="{{ old('mata_pelajaran', $detectedMapel ?: 'Coding') }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none" required />
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Fase / Kelas</label>
                    <input type="text" name="fase_kelas" value="{{ old('fase_kelas', 'C / V (Lima)') }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none" required />
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaran) }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none" required />
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Semester</label>
                    <select name="semester" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none">
                        <option value="1 (GANJIL)" {{ $semester === '1 (GANJIL)' ? 'selected' : '' }}>1 (GANJIL)</option>
                        <option value="2 (GENAP)" {{ $semester === '2 (GENAP)' ? 'selected' : '' }}>2 (GENAP)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end items-center gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('guru.dashboard') }}" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-md transition-all">
                    <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                    <span>Buka / Cetak PDF Laporan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection