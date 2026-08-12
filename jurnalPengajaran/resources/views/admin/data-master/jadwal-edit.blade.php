@extends('layouts.app')

@section('title', 'Edit Jadwal - E-Jurnal')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
        <div class="mb-6">
            <h2 class="font-headline-md text-headline-md text-on-background">Edit Jadwal</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Perbarui data jadwal.</p>
        </div>
        
        <form method="POST" action="{{ route('data-master.jadwal.update', $jadwal->id) }}" class="space-y-5" id="jadwalForm">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="guru_id">Guru</label>
                    <select class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('guru_id') border-error @enderror" 
                            id="guru_id" name="guru_id" required>
                        <option value="">Pilih Guru</option>
                        @foreach($guru as $g)
                            <option value="{{ $g->id }}" {{ old('guru_id', $jadwal->guru_id) == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_guru }}
                            </option>
                        @endforeach
                    </select>
                    @error('guru_id')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="kelas_id">Kelas</label>
                    <select class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('kelas_id') border-error @enderror" 
                            id="kelas_id" name="kelas_id" required>
                        <option value="">Pilih Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ old('kelas_id', $jadwal->kelas_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('kelas_id')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="mapel_id">Mata Pelajaran</label>
                    <select class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('mapel_id') border-error @enderror" 
                            id="mapel_id" name="mapel_id" required>
                        <option value="">Pilih Mata Pelajaran</option>
                        @foreach($mapel as $m)
                            <option value="{{ $m->id }}" {{ old('mapel_id', $jadwal->mapel_id) == $m->id ? 'selected' : '' }}>
                                {{ $m->nama_mapel }}
                            </option>
                        @endforeach
                    </select>
                    @error('mapel_id')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="hari">Hari</label>
                    <select class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('hari') border-error @enderror" 
                            id="hari" name="hari" required>
                        <option value="">Pilih Hari</option>
                        @foreach($hari as $h)
                            <option value="{{ $h }}" {{ old('hari', $jadwal->hari) == $h ? 'selected' : '' }}>
                                {{ $h }}
                            </option>
                        @endforeach
                    </select>
                    @error('hari')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- JAM KE DENGAN WAKTU OTOMATIS TAPI BISA DIEDIT -->
            <div class="space-y-3">
                <label class="block font-label-caps text-label-caps text-on-surface-variant">Pilih Jam Mengajar</label>
                <p class="text-xs text-slate-400 mb-2">💡 Centang jam yang akan digunakan (waktu otomatis terisi, bisa diubah)</p>
                
                <div class="space-y-2">
                    @for($i = 0; $i <= 10; $i++)
                        @php
                            $isChecked = in_array($i, $jamKeList);
                            $waktu = $jamMapping[$i] ?? ['mulai' => '00:00', 'selesai' => '00:00'];
                            $label = $jamLabel[$i] ?? 'Jam ' . $i;
                        @endphp
                        <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-lg border border-outline-variant hover:bg-slate-100 transition-colors">
                            <label class="flex items-center gap-2 text-sm cursor-pointer min-w-[140px]">
                                <input type="checkbox" name="jam_ke[]" value="{{ $i }}" 
                                       class="jam-checkbox w-4 h-4 rounded border-slate-300 text-primary focus:ring-primary cursor-pointer"
                                       {{ $isChecked ? 'checked' : '' }}
                                       onchange="toggleWaktu(this, {{ $i }})">
                                <span class="font-medium">{{ $label }}</span>
                            </label>
                            
                            <div class="flex-1 grid grid-cols-2 gap-3">
                                <div class="waktu-group" id="waktu-{{ $i }}" style="{{ $isChecked ? '' : 'display: none;' }}">
                                    <input type="time" name="jam_mulai[{{ $i }}]" 
                                           class="w-full h-[36px] bg-white border border-outline-variant rounded-lg px-3 text-sm focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all"
                                           value="{{ old('jam_mulai.' . $i, $waktu['mulai']) }}">
                                </div>
                                <div class="waktu-group" id="waktu-selesai-{{ $i }}" style="{{ $isChecked ? '' : 'display: none;' }}">
                                    <input type="time" name="jam_selesai[{{ $i }}]" 
                                           class="w-full h-[36px] bg-white border border-outline-variant rounded-lg px-3 text-sm focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all"
                                           value="{{ old('jam_selesai.' . $i, $waktu['selesai']) }}">
                                </div>
                            </div>
                            <span class="text-xs text-slate-400 w-32 hidden md:block">
                                {{ $waktu['mulai'] }} - {{ $waktu['selesai'] }}
                            </span>
                        </div>
                    @endfor
                </div>
                
                @error('jam_ke')
                    <p class="text-error text-sm">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                <a href="{{ route('data-master.jadwal') }}" 
                   class="px-6 py-2.5 rounded-lg border border-outline text-on-surface-variant font-label-caps text-label-caps uppercase hover:bg-white transition-all">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-primary text-white font-label-caps text-label-caps uppercase rounded-lg hover:bg-on-primary-fixed-variant transition-all">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function toggleWaktu(checkbox, jam) {
    const waktuMulai = document.getElementById('waktu-' + jam);
    const waktuSelesai = document.getElementById('waktu-selesai-' + jam);
    
    if (checkbox.checked) {
        waktuMulai.style.display = 'block';
        waktuSelesai.style.display = 'block';
        // Set default waktu jika kosong
        const mapping = {
            0: ['06:30', '07:00'],
            1: ['07:00', '07:35'],
            2: ['07:35', '08:10'],
            3: ['08:10', '08:45'],
            4: ['08:45', '09:20'],
            5: ['09:35', '10:10'],
            6: ['10:10', '10:45'],
            7: ['10:45', '11:20'],
            8: ['11:35', '12:10'],
            9: ['12:45', '13:20'],
            10: ['13:20', '13:55']
        };
        if (mapping[jam]) {
            const inputMulai = waktuMulai.querySelector('input');
            const inputSelesai = waktuSelesai.querySelector('input');
            if (!inputMulai.value) inputMulai.value = mapping[jam][0];
            if (!inputSelesai.value) inputSelesai.value = mapping[jam][1];
        }
    } else {
        waktuMulai.style.display = 'none';
        waktuSelesai.style.display = 'none';
    }
}

document.getElementById('jadwalForm').addEventListener('submit', function(e) {
    const checkboxes = document.querySelectorAll('.jam-checkbox:checked');
    
    if (checkboxes.length === 0) {
        e.preventDefault();
        alert('⚠️ Pilih minimal 1 jam!');
        return false;
    }
});
</script>
@endpush
@endsection