@extends('layouts.app')

@section('title', 'Data Jadwal - E-Jurnal')

@section('content')
<div class="mb-4">
    <a href="{{ route('data-master') }}" 
       class="inline-flex items-center gap-2 text-sm text-slate-600 hover:text-primary transition-colors">
        <span class="material-symbols-outlined text-sm">arrow_back</span>
        Kembali ke Data Master
    </a>
</div>

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl">
    <div class="p-6 border-b border-outline-variant flex justify-between items-center">
        <div>
            <h2 class="font-headline-md text-headline-md text-on-background">Data Jadwal Pelajaran</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Kelola jadwal pelajaran guru.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('data-master.jadwal.create') }}" 
               class="bg-primary text-on-primary px-4 py-2 rounded-lg font-label-caps text-[11px] flex items-center gap-2 hover:opacity-90 transition-opacity">
                <span class="material-symbols-outlined text-sm">add</span> TAMBAH JADWAL
            </a>
        </div>
    </div>
    
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-surface-container-low border-b border-outline-variant">
                    <tr>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">HARI</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">JAM KE</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">GURU</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">KELAS</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">MAPEL</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant">WAKTU</th>
                        <th class="px-4 py-3 font-label-caps text-label-caps text-on-surface-variant text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    @forelse($groupedJadwal as $key => $group)
                        @php
                            $first = $group->first();
                            $jamList = $group->pluck('jam_ke')->sort()->values();
                            $waktuDisplay = '';
                            $waktuDetail = '';
                            $jamDisplay = '';
                            foreach($group as $g) {
                                $waktuDisplay .= 'Jam ' . $g->jam_ke . ': ' . \Carbon\Carbon::parse($g->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($g->jam_selesai)->format('H:i') . '<br>';
                                $waktuDetail .= 'Jam ' . $g->jam_ke . ': ' . \Carbon\Carbon::parse($g->jam_mulai)->format('H:i') . ' - ' . \Carbon\Carbon::parse($g->jam_selesai)->format('H:i') . "\n";
                            }
                            $jamDisplay = $jamList->implode(', ');
                            $groupId = $first->id;
                            
                            // Buat data JSON yang aman
                            $jsonData = json_encode([
                                'id' => $groupId,
                                'hari' => $first->hari,
                                'jam_ke' => $jamDisplay,
                                'guru' => $first->nama_guru,
                                'kelas' => $first->nama_kelas,
                                'mapel' => $first->nama_mapel,
                                'waktu' => trim($waktuDetail)
                            ]);
                        @endphp
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-4 py-4 font-medium">{{ $first->hari }}</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($jamList as $jam)
                                        <span class="px-2 py-0.5 bg-primary/10 text-primary rounded text-xs font-medium">{{ $jam }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-4 py-4">{{ $first->nama_guru }}</td>
                            <td class="px-4 py-4">{{ $first->nama_kelas }}</td>
                            <td class="px-4 py-4">{{ $first->nama_mapel }}</td>
                            <td class="px-4 py-4 text-sm">{!! $waktuDisplay !!}</td>
                            <td class="px-4 py-4 text-right">
                                <div class="flex justify-end gap-1">
                                    <a href="{{ route('data-master.jadwal.edit', $groupId) }}" 
                                       class="p-1.5 text-on-surface-variant hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-lg">edit</span>
                                    </a>
                                    <button onclick="openDeleteModal({{ $jsonData }})" 
                                            class="p-1.5 text-on-surface-variant hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-3xl block mx-auto mb-2">inbox</span>
                                Belum ada data jadwal.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============ MODAL POPUP DELETE ============ -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden modal-overlay flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl modal-content">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-red-600 text-2xl">delete_forever</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Hapus Jadwal?</h3>
                <p class="text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        
        <div class="bg-red-50 rounded-xl p-4 mb-6 space-y-2">
            <p class="text-sm text-red-700 font-semibold mb-2">📋 Detail Jadwal yang akan dihapus:</p>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div><span class="text-slate-600">Hari:</span> <span id="deleteHari" class="font-medium text-slate-800">-</span></div>
                <div><span class="text-slate-600">Jam Ke:</span> <span id="deleteJamKe" class="font-medium text-slate-800">-</span></div>
                <div><span class="text-slate-600">Guru:</span> <span id="deleteGuru" class="font-medium text-slate-800">-</span></div>
                <div><span class="text-slate-600">Kelas:</span> <span id="deleteKelas" class="font-medium text-slate-800">-</span></div>
                <div class="col-span-2"><span class="text-slate-600">Mata Pelajaran:</span> <span id="deleteMapel" class="font-medium text-slate-800">-</span></div>
                <div class="col-span-2">
                    <span class="text-slate-600">Waktu:</span> 
                    <span id="deleteWaktu" class="font-medium text-slate-800 whitespace-pre-line">-</span>
                </div>
            </div>
        </div>
        
        <div class="flex gap-3 justify-end">
            <button onclick="closeDeleteModal()" 
                    class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all text-sm font-medium">
                Batal
            </button>
            <button id="confirmDeleteBtn" 
                    class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white transition-all text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">delete</span>
                Hapus
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deleteData = null;

function openDeleteModal(data) {
    deleteData = data;
    
    document.getElementById('deleteHari').textContent = data.hari || '-';
    document.getElementById('deleteJamKe').textContent = data.jam_ke || '-';
    document.getElementById('deleteGuru').textContent = data.guru || '-';
    document.getElementById('deleteKelas').textContent = data.kelas || '-';
    document.getElementById('deleteMapel').textContent = data.mapel || '-';
    document.getElementById('deleteWaktu').textContent = data.waktu || '-';
    
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deleteData = null;
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!deleteData) return;
    
    this.disabled = true;
    this.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Menghapus...';
    
    let route = '/admin/data-master/jadwal/' + deleteData.id;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = route;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);
    
    document.body.appendChild(form);
    form.submit();
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') closeDeleteModal();
});

document.getElementById('deleteModal').addEventListener('click', function(event) {
    if (event.target === this) closeDeleteModal();
});
</script>
@endpush