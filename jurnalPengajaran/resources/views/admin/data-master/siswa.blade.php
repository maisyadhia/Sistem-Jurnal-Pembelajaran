@extends('layouts.app')

@section('title', 'Data Siswa - E-Jurnal')

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
                                    <button onclick="openDeleteModal('{{ $item->id }}', '{{ $item->name }}', 'SISWA')" 
                                            class="p-1.5 text-on-surface-variant hover:text-error transition-colors">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
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

<!-- ============ MODAL POPUP DELETE ============ -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden modal-overlay flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl modal-content">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-red-600 text-2xl">delete_forever</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Hapus Data?</h3>
                <p class="text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
        </div>
        
        <div class="bg-red-50 rounded-xl p-4 mb-6">
            <p class="text-sm text-red-700">
                <span class="font-semibold">Data yang akan dihapus:</span><br>
                <span id="deleteItemName" class="font-medium">-</span>
                <span id="deleteItemCategory" class="text-xs text-red-500 ml-2">-</span>
            </p>
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

function openDeleteModal(id, name, category) {
    deleteData = { id: id, name: name, category: category };
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteItemCategory').textContent = '(' + category + ')';
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deleteData = null;
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!deleteData) return;
    const { id, name, category } = deleteData;
    
    this.disabled = true;
    this.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Menghapus...';
    
    let route = '/admin/data-master/siswa/' + id;
    
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