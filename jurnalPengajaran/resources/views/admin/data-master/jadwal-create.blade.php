@extends('layouts.app')

@section('title', 'Tambah Jadwal - E-Jurnal')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
        <div class="mb-6">
            <h2 class="font-headline-md text-headline-md text-on-background">Tambah Jadwal</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Tambahkan jadwal pelajaran baru.</p>
        </div>
        
        <form method="POST" action="{{ route('data-master.jadwal.store') }}" class="space-y-5">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="guru_id">Guru</label>
                    <select class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('guru_id') border-error @enderror" 
                            id="guru_id" name="guru_id" required>
                        <option value="">Pilih Guru</option>
                        @foreach($guru as $g)
                            <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>
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
                            <option value="{{ $k->id }}" {{ old('kelas_id') == $k->id ? 'selected' : '' }}>
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
                            <option value="{{ $m->id }}" {{ old('mapel_id') == $m->id ? 'selected' : '' }}>
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
                            <option value="{{ $h }}" {{ old('hari') == $h ? 'selected' : '' }}>
                                {{ $h }}
                            </option>
                        @endforeach
                    </select>
                    @error('hari')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="jam_ke">Jam Ke</label>
                    <select class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('jam_ke') border-error @enderror" 
                            id="jam_ke" name="jam_ke" required>
                        <option value="">Pilih</option>
                        @foreach($jamKe as $j)
                            <option value="{{ $j }}" {{ old('jam_ke') == $j ? 'selected' : '' }}>
                                {{ $j }}
                            </option>
                        @endforeach
                    </select>
                    @error('jam_ke')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="jam_mulai">Jam Mulai</label>
                    <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('jam_mulai') border-error @enderror" 
                           id="jam_mulai" name="jam_mulai" type="time" required value="{{ old('jam_mulai') }}"/>
                    @error('jam_mulai')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="jam_selesai">Jam Selesai</label>
                    <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('jam_selesai') border-error @enderror" 
                           id="jam_selesai" name="jam_selesai" type="time" required value="{{ old('jam_selesai') }}"/>
                    @error('jam_selesai')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                <a href="{{ route('data-master.jadwal') }}" 
                   class="px-6 py-2.5 rounded-lg border border-outline text-on-surface-variant font-label-caps text-label-caps uppercase hover:bg-white transition-all">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 bg-primary text-white font-label-caps text-label-caps uppercase rounded-lg hover:bg-on-primary-fixed-variant transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection