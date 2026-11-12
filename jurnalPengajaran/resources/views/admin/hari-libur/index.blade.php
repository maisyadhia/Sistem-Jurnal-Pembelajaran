@extends('layouts.app')

@section('title', 'Hari Libur - E-Jurnal')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Hari Libur Nasional</h2>
            <p class="text-sm text-slate-500">Kelola tanggal merah agar sistem tidak mengirim notifikasi saat libur.</p>
        </div>
        <a href="{{ route('data-master') }}" class="text-sm text-slate-600 hover:text-primary">
            ← Kembali ke Data Master
        </a>
    </div>

    <!-- Form Sync -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center">
                <span class="material-symbols-outlined">cloud_sync</span>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">Sinkronisasi Otomatis</h3>
                <p class="text-xs text-slate-500">Ambil data hari libur dari API resmi.</p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('hari-libur.sync') }}" class="flex flex-wrap items-end gap-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tahun</label>
                <input type="number" name="tahun" value="{{ date('Y') }}" min="2020" max="2030"
                       class="border border-slate-200 rounded-lg px-3 py-2 text-sm w-32">
            </div>
            <button type="submit" 
                    class="bg-primary text-white px-4 py-2 rounded-lg text-sm hover:bg-primary/90 flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">sync</span>
                Sync Sekarang
            </button>
        </form>

        @if(session('success'))
            <div class="mt-4 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-2 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mt-4 bg-red-50 border border-red-200 text-red-800 px-4 py-2 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Tabel Hari Libur -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex justify-between items-center">
            <h3 class="font-bold text-slate-800">Daftar Hari Libur</h3>
            <span class="text-sm text-slate-500">{{ $hariLibur->count() }} data</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">No</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Keterangan</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($hariLibur as $index => $item)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-sm">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-medium">
                                {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('l, d F Y') }}
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $item->keterangan }}</td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('hari-libur.destroy', $item->id) }}" 
                                      class="inline" onsubmit="return confirm('Hapus hari libur ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-slate-400 italic">
                                Belum ada data hari libur. Klik "Sync Sekarang" untuk mengambil dari API.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection