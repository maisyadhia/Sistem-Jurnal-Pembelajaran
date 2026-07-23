@extends('layouts.app')

@section('title', 'Data Siswa - E-Jurnal')

@section('content')
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl">
    <div class="p-6 border-b border-outline-variant flex justify-between items-center">
        <div>
            <h2 class="font-headline-md text-headline-md text-on-background">Data Siswa</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Kelola data siswa.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('data-master.siswa.create') }}" 
               class="bg-primary text-on-primary px-4 py-2 rounded-lg font-label-caps text-[11px] flex items-center gap-2 hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-sm">add</span> TAMBAH SISWA
            </a>
        </div>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low border-b border-outline-variant">
                    <tr>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">NAMA</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">NISN</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">KELAS</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">ORANG TUA</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($siswa as $item)
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold text-[10px]">
                                        {{ strtoupper(substr($item->name, 0, 2)) }}
                                    </div>
                                    <span class="font-data-tabular text-data-tabular">{{ $item->name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">{{ $item->nisn }}</td>
                            <td class="px-4 py-4">{{ $item->class }}</td>
                            <td class="px-4 py-4">{{ $item->parent_name ?? '-' }}</td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('data-master.siswa.edit', $item->id) }}" 
                                       class="p-1.5 text-on-surface-variant hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('data-master.siswa.destroy', $item->id) }}" 
                                          class="inline-block" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-on-surface-variant hover:text-error transition-colors">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-3xl block mx-auto mb-2">inbox</span>
                                Belum ada data siswa.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection