@extends('layouts.app')

@section('title', 'Tambah Data Master - E-Jurnal')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
        <div class="mb-6">
            <h2 class="font-headline-md text-headline-md text-on-background">Tambah Data Master</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Tambahkan data guru, kelas, atau mata pelajaran baru.</p>
        </div>
        
        <form method="POST" action="{{ route('data-master.store') }}" class="space-y-5">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="name">Nama</label>
                    <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('name') border-error @enderror" 
                           id="name" name="name" type="text" required value="{{ old('name') }}"/>
                    @error('name')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="identifier">Identifier</label>
                    <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('identifier') border-error @enderror" 
                           id="identifier" name="identifier" type="text" required value="{{ old('identifier') }}"/>
                    @error('identifier')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="initials">Inisial (2-3 huruf)</label>
                    <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('initials') border-error @enderror" 
                           id="initials" name="initials" type="text" maxlength="3" required value="{{ old('initials') }}"/>
                    @error('initials')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="category">Kategori</label>
                    <select class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('category') border-error @enderror" 
                            id="category" name="category" required>
                        <option value="">Pilih Kategori</option>
                        <option value="GURU TETAP" {{ old('category') == 'GURU TETAP' ? 'selected' : '' }}>Guru Tetap</option>
                        <option value="INFRASTRUKTUR" {{ old('category') == 'INFRASTRUKTUR' ? 'selected' : '' }}>Infrastruktur</option>
                        <option value="MATA PELAJARAN" {{ old('category') == 'MATA PELAJARAN' ? 'selected' : '' }}>Mata Pelajaran</option>
                        <option value="KELAS" {{ old('category') == 'KELAS' ? 'selected' : '' }}>Kelas</option>
                    </select>
                    @error('category')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="status">Status</label>
                    <select class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('status') border-error @enderror" 
                            id="status" name="status" required>
                        <option value="">Pilih Status</option>
                        <option value="Aktif" {{ old('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Tidak Aktif" {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="Ditinjau" {{ old('status') == 'Ditinjau' ? 'selected' : '' }}>Ditinjau</option>
                    </select>
                    @error('status')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="color">Warna Avatar</label>
                    <select class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('color') border-error @enderror" 
                            id="color" name="color" required>
                        <option value="bg-secondary-container" {{ old('color') == 'bg-secondary-container' ? 'selected' : '' }}>Hijau</option>
                        <option value="bg-primary-container" {{ old('color') == 'bg-primary-container' ? 'selected' : '' }}>Biru</option>
                        <option value="bg-tertiary-container" {{ old('color') == 'bg-tertiary-container' ? 'selected' : '' }}>Oranye</option>
                        <option value="bg-error-container" {{ old('color') == 'bg-error-container' ? 'selected' : '' }}>Merah</option>
                        <option value="bg-primary" {{ old('color') == 'bg-primary' ? 'selected' : '' }}>Biru Tua</option>
                    </select>
                    @error('color')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <input type="hidden" name="statusColor" value="bg-secondary">
            
            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                <a href="{{ route('data-master') }}" 
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