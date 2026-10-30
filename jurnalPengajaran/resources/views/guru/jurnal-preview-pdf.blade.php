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

                <!-- PILIHAN KELAS DARI JADWAL & FASE OTOMATIS -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Kelas & Fase Otomatis</label>
                    <div class="flex gap-2">
                        <select id="selectKelas" name="kelas_pilihan" class="w-2/3 bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none">
                            <option value="semua">Semua Kelas</option>
                            @foreach($daftarKelasGuru as $kelas)
                                <option value="{{ $kelas }}">{{ $kelas }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="infoFase" readonly placeholder="Fase" 
                               class="w-1/3 bg-slate-100 border border-slate-200 text-center rounded-xl p-3 text-xs md:text-sm font-bold text-teal-700 outline-none cursor-not-allowed" />
                    </div>
                </div>

                <!-- MATA PELAJARAN (SELECT DINAMIS SESUAI KELAS & JADWAL) -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Mata Pelajaran Header</label>
                    <select id="selectMapel" name="mata_pelajaran" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none" required>
                        <option value="semua">-- Semua Mata Pelajaran --</option>
                    </select>
                </div>

                <!-- TAHUN AJARAN -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Tahun Ajaran</label>
                    <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaran) }}" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none" required />
                </div>

                <!-- SEMESTER -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Semester</label>
                    <select name="semester" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none">
                        <option value="1 (GANJIL)" {{ $semester === '1 (GANJIL)' ? 'selected' : '' }}>1 (GANJIL)</option>
                        <option value="2 (GENAP)" {{ $semester === '2 (GENAP)' ? 'selected' : '' }}>2 (GENAP)</option>
                    </select>
                </div>

                <!-- OPSI PILIHAN FILTER BULAN (1 BULAN ATAU RENTANG BULAN) -->
                <div class="space-y-1.5 sm:col-span-2 border-t border-slate-100 pt-3 mt-2">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Periode Bulan Cetak PDF</label>
                        <div class="flex items-center gap-4 text-xs font-medium text-slate-600">
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="tipe_periode" value="satu" id="radioSatuBulan" checked class="text-teal-600 focus:ring-teal-500">
                                <span> 1 Bulan</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="tipe_periode" value="rentang" id="radioRentangBulan" class="text-teal-600 focus:ring-teal-500">
                                <span>Rentang Bulan</span>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- BULAN AWAL / BULAN PILIHAN -->
                        <div>
                            <span id="labelBulanAwal" class="block text-[11px] font-medium text-slate-500 mb-1">Pilih Bulan:</span>
                            <select id="selectBulanAwal" name="bulan_awal" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none">
                                <option value="">-- Semua Bulan --</option>
                                @foreach([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $num => $name)
                                    <option value="{{ sprintf('%02d', $num) }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- BULAN AKHIR (OTOMATIS DISABLED JIKA HANYA 1 BULAN) -->
                        <div id="wrapperBulanAkhir" class="opacity-50 pointer-events-none">
                            <span class="block text-[11px] font-medium text-slate-500 mb-1">Sampai Bulan:</span>
                            <select id="selectBulanAkhir" name="bulan_akhir" disabled class="w-full bg-slate-100 border border-slate-200 rounded-xl p-3 text-xs md:text-sm font-semibold text-slate-800 focus:border-teal-500 outline-none">
                                <option value="">-- Sama Dengan Bulan Pilihan --</option>
                                @foreach([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $num => $name)
                                    <option value="{{ sprintf('%02d', $num) }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
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
    const mapelData = @json($mapelPerKelas ?? []);
    const defaultAllMapel = @json($allMapel ?? '');

    const selectKelas = document.getElementById('selectKelas');
    const selectMapel = document.getElementById('selectMapel');
    const infoFase = document.getElementById('infoFase');

    const radioSatuBulan = document.getElementById('radioSatuBulan');
    const radioRentangBulan = document.getElementById('radioRentangBulan');
    const labelBulanAwal = document.getElementById('labelBulanAwal');
    const selectBulanAwal = document.getElementById('selectBulanAwal');
    const wrapperBulanAkhir = document.getElementById('wrapperBulanAkhir');
    const selectBulanAkhir = document.getElementById('selectBulanAkhir');

    // Fungsi Deteksi Fase Otomatis
    function deteksiFase(namaKelas) {
        if (!namaKelas || namaKelas === 'semua') return 'Semua Fase';
        const match = namaKelas.match(/\d+/);
        if (!match) return '-';
        
        const tingkat = parseInt(match[0]);
        if ([1, 2].includes(tingkat)) return 'Fase A';
        if ([3, 4].includes(tingkat)) return 'Fase B';
        if ([5, 6].includes(tingkat)) return 'Fase C';
        return '-';
    }

    function updateFaseDisplay() {
        infoFase.value = deteksiFase(selectKelas.value);
    }

    function togglePeriodeBulan() {
        if (radioSatuBulan.checked) {
            labelBulanAwal.textContent = "Pilih Bulan:";
            wrapperBulanAkhir.classList.add('opacity-50', 'pointer-events-none');
            selectBulanAkhir.disabled = true;
            selectBulanAkhir.value = "";
            selectBulanAkhir.classList.replace('bg-slate-50', 'bg-slate-100');
        } else {
            labelBulanAwal.textContent = "Dari Bulan:";
            wrapperBulanAkhir.classList.remove('opacity-50', 'pointer-events-none');
            selectBulanAkhir.disabled = false;
            selectBulanAkhir.classList.replace('bg-slate-100', 'bg-slate-50');
        }
    }

    radioSatuBulan.addEventListener('change', togglePeriodeBulan);
    radioRentangBulan.addEventListener('change', togglePeriodeBulan);

    function updateMapelOptions() {
        const selectedKelas = selectKelas.value;
        let mapelList = [];

        let rawData = null;
        if (selectedKelas === 'semua') {
            rawData = defaultAllMapel;
        } else if (mapelData[selectedKelas]) {
            rawData = mapelData[selectedKelas];
        }

        if (typeof rawData === 'string') {
            let splitData = rawData.split(/[\/,]/);
            splitData.forEach(m => {
                let cleaned = m.trim();
                if (cleaned.length > 0) mapelList.push(cleaned);
            });
        } else if (Array.isArray(rawData)) {
            rawData.forEach(item => {
                if (typeof item === 'string') {
                    item.split(/[\/,]/).forEach(m => {
                        let cleaned = m.trim();
                        if (cleaned.length > 0) mapelList.push(cleaned);
                    });
                }
            });
        }

        mapelList = [...new Set(mapelList)];
        selectMapel.innerHTML = '';

        const defaultOption = document.createElement('option');
        defaultOption.value = 'semua';
        defaultOption.textContent = '-- Semua Mata Pelajaran --';
        selectMapel.appendChild(defaultOption);

        mapelList.forEach(mapel => {
            const opt = document.createElement('option');
            opt.value = mapel;
            opt.textContent = mapel;
            selectMapel.appendChild(opt);
        });
    }

    selectKelas.addEventListener('change', () => {
        updateMapelOptions();
        updateFaseDisplay();
    });

    document.addEventListener('DOMContentLoaded', () => {
        updateMapelOptions();
        updateFaseDisplay();
        togglePeriodeBulan();
    });
</script>
@endsection