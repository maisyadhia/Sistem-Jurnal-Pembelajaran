@extends('layouts.app')

@section('title', 'Konfirmasi Parameter Cetak PDF Jurnal - SIJAMPANG')

@section('content')
<div class="max-w-4xl mx-auto w-full flex flex-col gap-4 md:gap-6 pt-2 pb-6 px-3 md:px-6">
    <section class="text-center md:text-left pt-1 md:pt-0">
        <h2 class="text-xl md:text-2xl font-bold text-slate-800 mb-1">Konfirmasi Data Header & TTD Cetak PDF</h2>
        <p class="text-xs md:text-sm text-slate-500">Periksa atau sesuaikan informasi dokumen sebelum mengunduh / mencetak laporan jurnal mengajar.</p>
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
                <!-- NAMA PENYUSUN -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Nama Penyusun (Guru)</label>
                    <input type="text" name="nama_penyusun" value="{{ old('nama_penyusun', $guruName) }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none" required />
                </div>

                <!-- NIP GURU -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">NIP Guru</label>
                    <input type="text" name="nip_penyusun" value="{{ old('nip_penyusun', $guruNip) }}" readonly
                           class="w-full bg-slate-100 border border-slate-200 text-slate-500 cursor-not-allowed rounded-xl p-3 text-xs md:text-sm font-semibold outline-none" />
                </div>

                <!-- SATUAN PENDIDIKAN -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Satuan Pendidikan</label>
                    <input type="text" name="satuan_pendidikan" value="MIN 2 Kota Malang" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none" required />
                </div>

                <!-- MATA PELAJARAN (OTOMATIS MENYESUAIKAN JADWAL KELAS) -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Mata Pelajaran Header</label>
                    <input type="text" id="inputMapel" name="mata_pelajaran" value="{{ old('mata_pelajaran', $allMapel ?: 'Pilih Kelas Terlebih Dahulu') }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none" required />
                </div>

                <!-- PILIHAN KELAS DARI JADWAL -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Kelas</label>
                    <select id="selectKelas" name="kelas_pilihan" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none">
                        <option value="semua">Pilih Kelas</option>
                        @foreach($daftarKelasGuru as $kelas)
                            <option value="{{ $kelas }}">{{ $kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- TAHUN AJARAN -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaran) }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none" required />
                </div>

                <!-- SEMESTER -->
                <div class="space-y-1.5 sm:col-span-2">
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

<script>
    // Data mapel per kelas yang diambil langsung dari tabel jadwals
    const mapelData = @json($mapelPerKelas);
    const defaultAllMapel = @json($allMapel);

    const selectKelas = document.getElementById('selectKelas');
    const inputMapel = document.getElementById('inputMapel');

    selectKelas.addEventListener('change', function () {
        const selected = this.value;
        if (selected === 'semua') {
            inputMapel.value = defaultAllMapel || '';
        } else if (mapelData[selected]) {
            inputMapel.value = mapelData[selected];
        } else {
            inputMapel.value = '';
        }
    });
</script>
@endsection