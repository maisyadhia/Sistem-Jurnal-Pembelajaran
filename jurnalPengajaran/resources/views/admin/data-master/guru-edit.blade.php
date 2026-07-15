@extends('layouts.app')

@section('title', 'Edit Guru - E-Jurnal')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
        <div class="mb-6">
            <h2 class="font-headline-md text-headline-md text-on-background">Edit Guru</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Perbarui data guru.</p>
        </div>
        
        <form method="POST" action="{{ route('data-master.guru.update', $guru->id) }}" class="space-y-5">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="kode_guru">Kode Guru</label>
                    <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('kode_guru') border-error @enderror" 
                           id="kode_guru" name="kode_guru" type="text" required value="{{ old('kode_guru', $guru->kode_guru) }}"/>
                    @error('kode_guru')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="nik">NIK</label>
                    <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('nik') border-error @enderror" 
                           id="nik" name="nik" type="text" required value="{{ old('nik', $guru->nik) }}"/>
                    @error('nik')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="space-y-1.5">
                <label class="block font-label-caps text-label-caps text-on-surface-variant" for="nama_guru">Nama Guru</label>
                <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('nama_guru') border-error @enderror" 
                       id="nama_guru" name="nama_guru" type="text" required value="{{ old('nama_guru', $guru->nama_guru) }}"/>
                @error('nama_guru')
                    <p class="text-error text-sm">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="password">Password (kosongkan jika tidak diubah)</label>
                    <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('password') border-error @enderror" 
                           id="password" name="password" type="password" value=""/>
                    @error('password')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="role">Role</label>
                    <select class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('role') border-error @enderror" 
                            id="role" name="role" required>
                        <option value="guru" {{ old('role', $guru->role) == 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="admin" {{ old('role', $guru->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="humas" {{ old('role', $guru->role) == 'humas' ? 'selected' : '' }}>Humas</option>
                    </select>
                    @error('role')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                <a href="{{ route('data-master.guru') }}" 
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
@endsection