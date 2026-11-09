@extends('layouts.app')

@section('title', 'Kenaikan Kelas - E-Jurnal')

@push('styles')
<style>
    .stat-card { transition: all 0.2s ease; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }
    .tab-btn { transition: all 0.2s ease; }
    .tab-btn.active { background: #00236f; color: white; }
    .modal-overlay { background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
    .modal-content { animation: modalFadeIn 0.3s ease-out forwards; }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #c5c5d3; border-radius: 10px; }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto space-y-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Kenaikan Kelas & Manajemen Siswa</h2>
            <p class="text-sm text-slate-500">Kelola kenaikan kelas, kelulusan, dan kepindahan siswa.</p>
        </div>
        <a href="{{ route('data-master') }}" 
           class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Kembali ke Data Master
        </a>
    </div>

    <!-- Info Tahun Ajaran -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">Tahun Ajaran Aktif</p>
                <p class="text-xl font-bold text-blue-800">{{ $tahunAjaranAktif }}</p>
            </div>
            <div>
                <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">Tahun Ajaran Baru</p>
                <p class="text-xl font-bold text-blue-800">{{ $tahunAjaranBaru }}</p>
            </div>
            <div>
                <p class="text-xs text-blue-600 font-semibold uppercase tracking-wider">Total Siswa Aktif</p>
                <p class="text-xl font-bold text-blue-800">{{ $totalSiswa }} siswa</p>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="flex flex-wrap border-b border-slate-200 bg-slate-50">
            <button onclick="switchTab('aktif')" id="tab-btn-aktif" 
                    class="tab-btn active px-4 py-3 text-sm font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">groups</span>
                Siswa Aktif ({{ $siswaAktif->count() }})
            </button>
            <button onclick="switchTab('lulus')" id="tab-btn-lulus"
                    class="tab-btn px-4 py-3 text-sm font-semibold flex items-center gap-2 text-slate-600 hover:bg-slate-100">
                <span class="material-symbols-outlined text-lg">check_circle</span>
                Lulus ({{ $siswaLulus->count() }})
            </button>
            <button onclick="switchTab('pindah')" id="tab-btn-pindah"
                    class="tab-btn px-4 py-3 text-sm font-semibold flex items-center gap-2 text-slate-600 hover:bg-slate-100">
                <span class="material-symbols-outlined text-lg">logout</span>
                Pindah ({{ $siswaPindah->count() }})
            </button>
            <button onclick="switchTab('tinggal')" id="tab-btn-tinggal"
                    class="tab-btn px-4 py-3 text-sm font-semibold flex items-center gap-2 text-slate-600 hover:bg-slate-100">
                <span class="material-symbols-outlined text-lg">schedule</span>
                Tinggal Kelas ({{ $siswaTinggal->count() }})
            </button>
            <button onclick="switchTab('riwayat')" id="tab-btn-riwayat"
                    class="tab-btn px-4 py-3 text-sm font-semibold flex items-center gap-2 text-slate-600 hover:bg-slate-100">
                <span class="material-symbols-outlined text-lg">history</span>
                Riwayat Kenaikan
            </button>
        </div>

        <!-- TAB: SISWA AKTIF -->
        <div id="tab-content-aktif" class="tab-content">
            <div class="p-4 border-b border-slate-200 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-800">Daftar Siswa Aktif</h3>
                    <p class="text-xs text-slate-500">Pilih siswa yang akan naik kelas, lalu tentukan kelas barunya.</p>
                </div>
            </div>
            
            <form method="POST" action="{{ route('kenaikan-kelas.proses') }}" id="form-naik-kelas">
                @csrf
                <input type="hidden" name="tahun_ajaran_baru" value="{{ $tahunAjaranBaru }}">
                
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase w-12">
                                    <input type="checkbox" id="check-all" onchange="toggleAll(this)" 
                                           class="w-4 h-4 rounded border-slate-300 text-primary cursor-pointer">
                                </th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Nama</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">NISN</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Kelas Lama</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Kelas Baru</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($siswaAktif as $siswa)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3">
                                        <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}" 
                                               class="siswa-checkbox w-4 h-4 rounded border-slate-300 text-primary cursor-pointer">
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-[10px]">
                                                {{ strtoupper(substr($siswa->name, 0, 2)) }}
                                            </div>
                                            <span class="text-sm font-medium text-slate-800">{{ $siswa->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-slate-600">{{ $siswa->nisn }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs font-medium">{{ $siswa->class }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <select name="kelas_baru[{{ $siswa->id }}]" 
                                                class="border border-slate-200 rounded-lg px-3 py-1.5 text-sm w-full max-w-[150px]">
                                            <option value="">Pilih Kelas</option>
                                            @foreach($daftarKelas as $kelas)
                                                <option value="{{ $kelas->nama_kelas }}">{{ $kelas->nama_kelas }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex gap-1 justify-end flex-wrap">
                                            <button type="button" onclick="tinggalKelas({{ $siswa->id }}, '{{ addslashes($siswa->name) }}')"
                                                    class="px-2 py-1 text-[10px] font-semibold bg-amber-100 text-amber-700 rounded hover:bg-amber-200">
                                                Tinggal
                                            </button>
                                            <button type="button" onclick="luluskan({{ $siswa->id }}, '{{ addslashes($siswa->name) }}')"
                                                    class="px-2 py-1 text-[10px] font-semibold bg-green-100 text-green-700 rounded hover:bg-green-200">
                                                Lulus
                                            </button>
                                            <button type="button" onclick="pindah({{ $siswa->id }}, '{{ addslashes($siswa->name) }}')"
                                                    class="px-2 py-1 text-[10px] font-semibold bg-red-100 text-red-700 rounded hover:bg-red-200">
                                                Pindah
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-slate-400 italic">
                                        Tidak ada siswa aktif.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($siswaAktif->count() > 0)
                <div class="p-4 border-t border-slate-200 bg-slate-50 flex flex-wrap justify-between items-center gap-2">
                    <p class="text-xs text-slate-500">
                        <span class="font-semibold" id="count-selected">0</span> siswa dipilih
                    </p>
                    <div class="flex gap-2">
                        <button type="button" onclick="openBagiNilaiModal()"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">grade</span>
                            Bagi Berdasarkan Nilai
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 flex items-center gap-2"
                                onclick="return confirm('Proses kenaikan kelas untuk siswa yang dipilih?')">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            Proses Naik Kelas
                        </button>
                    </div>
                </div>
                @endif
            </form>
        </div>

        <!-- TAB: SISWA LULUS -->
        <div id="tab-content-lulus" class="tab-content hidden">
            <div class="p-4 border-b border-slate-200">
                <h3 class="font-bold text-slate-800">Siswa Lulus</h3>
                <p class="text-xs text-slate-500">Daftar siswa yang sudah diluluskan.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">No</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Nama</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">NISN</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Kelas Terakhir</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Tanggal Lulus</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($siswaLulus as $index => $siswa)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium">{{ $siswa->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $siswa->nisn }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">{{ $siswa->class }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $siswa->tanggal_keluar ? \Carbon\Carbon::parse($siswa->tanggal_keluar)->format('d M Y') : '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <button onclick="batalkanStatus({{ $siswa->id }}, '{{ addslashes($siswa->name) }}')"
                                            class="px-2 py-1 text-[10px] font-semibold bg-slate-100 text-slate-700 rounded hover:bg-slate-200">
                                        Batalkan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-400 italic text-sm">
                                    Belum ada siswa yang lulus.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB: SISWA PINDAH -->
        <div id="tab-content-pindah" class="tab-content hidden">
            <div class="p-4 border-b border-slate-200">
                <h3 class="font-bold text-slate-800">Siswa Pindah</h3>
                <p class="text-xs text-slate-500">Daftar siswa yang pindah ke sekolah lain.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">No</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Nama</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">NISN</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Kelas Terakhir</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Sekolah Tujuan</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Tanggal Pindah</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($siswaPindah as $index => $siswa)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium">{{ $siswa->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $siswa->nisn }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">{{ $siswa->class }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $siswa->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $siswa->tanggal_keluar ? \Carbon\Carbon::parse($siswa->tanggal_keluar)->format('d M Y') : '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <button onclick="batalkanStatus({{ $siswa->id }}, '{{ addslashes($siswa->name) }}')"
                                            class="px-2 py-1 text-[10px] font-semibold bg-slate-100 text-slate-700 rounded hover:bg-slate-200">
                                        Batalkan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-slate-400 italic text-sm">
                                    Belum ada siswa yang pindah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB: SISWA TINGGAL KELAS -->
        <div id="tab-content-tinggal" class="tab-content hidden">
            <div class="p-4 border-b border-slate-200">
                <h3 class="font-bold text-slate-800">Siswa Tinggal Kelas</h3>
                <p class="text-xs text-slate-500">Daftar siswa yang tinggal kelas.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">No</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Nama</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">NISN</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Kelas</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Tahun Ajaran</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($siswaTinggal as $index => $siswa)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium">{{ $siswa->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $siswa->nisn }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 rounded text-xs font-medium">{{ $siswa->class }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $siswa->tahun_ajaran }}</td>
                                <td class="px-4 py-3 text-right">
                                    <button onclick="batalkanStatus({{ $siswa->id }}, '{{ addslashes($siswa->name) }}')"
                                            class="px-2 py-1 text-[10px] font-semibold bg-slate-100 text-slate-700 rounded hover:bg-slate-200">
                                        Batalkan
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-400 italic text-sm">
                                    Belum ada siswa yang tinggal kelas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB: RIWAYAT -->
        <div id="tab-content-riwayat" class="tab-content hidden">
            <div class="p-4 border-b border-slate-200">
                <h3 class="font-bold text-slate-800">Riwayat Kenaikan Kelas</h3>
                <p class="text-xs text-slate-500">Semua riwayat kenaikan, tinggal, lulus, dan pindah.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">No</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Nama Siswa</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Tahun Ajaran</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Kelas</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Keterangan</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($riwayatKenaikan as $index => $riwayat)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3 text-sm">{{ $riwayatKenaikan->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-sm font-medium">{{ $riwayat->student->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $riwayat->tahun_ajaran }}</td>
                                <td class="px-4 py-3 text-sm">{{ $riwayat->kelas }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @php
                                        $badge = [
                                            'naik' => 'bg-blue-100 text-blue-700',
                                            'tinggal' => 'bg-amber-100 text-amber-700',
                                            'lulus' => 'bg-green-100 text-green-700',
                                            'pindah' => 'bg-red-100 text-red-700',
                                        ][$riwayat->status] ?? 'bg-slate-100 text-slate-700';
                                    @endphp
                                    <span class="px-2 py-1 rounded text-xs font-medium {{ $badge }}">
                                        {{ ucfirst($riwayat->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $riwayat->keterangan ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $riwayat->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400 italic text-sm">
                                    Belum ada riwayat kenaikan kelas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($riwayatKenaikan->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $riwayatKenaikan->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Info Tambahan -->
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
        <div class="flex gap-3">
            <span class="material-symbols-outlined text-amber-600 shrink-0">info</span>
            <div class="text-sm text-amber-800">
                <p class="font-semibold mb-1">Panduan:</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    <li><strong>Naik Kelas:</strong> Centang siswa → pilih kelas baru → klik "Proses Kenaikan Kelas"</li>
                    <li><strong>Tinggal Kelas:</strong> Klik tombol "Tinggal" untuk menandai siswa tinggal kelas</li>
                    <li><strong>Lulus:</strong> Klik tombol "Lulus" untuk menandai siswa lulus</li>
                    <li><strong>Pindah:</strong> Klik tombol "Pindah" untuk menandai siswa pindah + sekolah tujuan</li>
                    <li><strong>Bagi Berdasarkan Nilai:</strong> Siswa dibagi berdasarkan nilai ASAT</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tombol Kembali -->
    <div>
        <a href="{{ route('data-master') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm font-medium text-slate-700">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Kembali ke Data Master
        </a>
    </div>
</div>

<!-- ============ MODAL BAGI KELAS BERDASARKAN NILAI ============ -->
<div id="modalBagiNilai" class="fixed inset-0 z-50 hidden modal-overlay flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl modal-content">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-indigo-600 text-2xl">grade</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Bagi Berdasarkan Nilai</h3>
                <p class="text-sm text-slate-500">Siswa dibagi berdasarkan nilai ASAT.</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('kenaikan-kelas.bagi-nilai') }}">
            @csrf
            <input type="hidden" name="tahun_ajaran_baru" value="{{ $tahunAjaranBaru }}">
            
            <div class="bg-blue-50 rounded-xl p-4 mb-4">
                <p class="text-xs text-blue-700 font-semibold mb-2">📋 Aturan:</p>
                <ul class="text-xs text-blue-600 space-y-1">
                    <li>• Rangking 1-30 → <strong>6A</strong> (15L + 15P)</li>
                    <li>• Rangking 31-60 → <strong>6B</strong> (15L + 15P)</li>
                    <li>• Rangking 61-90 → <strong>6C</strong> (15L + 15P)</li>
                    <li>• Sisa → <strong>6D-6G</strong> (acak, L/P seimbang)</li>
                    <li>• <strong>Gender wajib diisi</strong></li>
                </ul>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Kelas</label>
                    <input type="number" name="jumlah_kelas" value="7" min="1" max="10" required
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm">
                    <p class="text-xs text-slate-500 mt-1">7 = 6A, 6B, 6C, 6D, 6E, 6F, 6G</p>
                </div>
            </div>
            
            <div class="flex gap-2 justify-end mt-6">
                <button type="button" onclick="closeBagiNilaiModal()"
                        class="px-4 py-2 border border-slate-200 rounded-lg text-sm">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                    Proses
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ============ TAB SWITCH ============
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.add('text-slate-600', 'hover:bg-slate-100');
    });
    document.getElementById('tab-content-' + tab).classList.remove('hidden');
    const btn = document.getElementById('tab-btn-' + tab);
    btn.classList.add('active');
    btn.classList.remove('text-slate-600', 'hover:bg-slate-100');
}

// ============ CHECKBOX ============
function toggleAll(checkbox) {
    document.querySelectorAll('.siswa-checkbox').forEach(cb => cb.checked = checkbox.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const count = document.querySelectorAll('.siswa-checkbox:checked').length;
    const countEl = document.getElementById('count-selected');
    if (countEl) countEl.textContent = count;
}

document.querySelectorAll('.siswa-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

// ============ AKSI SISWA ============
function tinggalKelas(id, name) {
    if (confirm(`Tandai "${name}" sebagai TINGGAL KELAS?`)) {
        submitForm(`/admin/data-master/kenaikan-kelas/${id}/tinggal-kelas`);
    }
}

function luluskan(id, name) {
    if (confirm(`Luluskan "${name}"?`)) {
        submitForm(`/admin/data-master/kenaikan-kelas/${id}/lulus`);
    }
}

function pindah(id, name) {
    const tujuan = prompt(`Pindah "${name}" ke sekolah mana?`);
    if (tujuan && tujuan.trim()) {
        submitForm(`/admin/data-master/kenaikan-kelas/${id}/pindah`, {
            sekolah_tujuan: tujuan.trim()
        });
    }
}

function submitForm(action, extraData = {}) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;
    
    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);
    
    for (const [key, value] of Object.entries(extraData)) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    }
    
    document.body.appendChild(form);
    form.submit();
}

// ============ MODAL BAGI NILAI (TAMBAHKAN INI) ============
function openBagiNilaiModal() {
    document.getElementById('modalBagiNilai').classList.remove('hidden');
}

function closeBagiNilaiModal() {
    document.getElementById('modalBagiNilai').classList.add('hidden');
}

// ============ CLOSE MODAL ON OVERLAY CLICK ============

document.getElementById('modalBagiNilai').addEventListener('click', function(e) {
    if (e.target === this) closeBagiNilaiModal();
});

// ============ CLOSE MODAL ON ESC ============
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeBagiAcakModal();
        closeBagiNilaiModal();
    }
});
</script>
@endpush