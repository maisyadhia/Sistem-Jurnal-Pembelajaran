@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran - E-Jurnal')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
        <div class="mb-6">
            <h2 class="font-headline-md text-headline-md text-on-background">Edit Mata Pelajaran</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Perbarui data mata pelajaran.</p>
        </div>
        
        <form method="POST" action="{{ route('data-master.mapel.update', $mapel->id) }}" class="space-y-5">
            @csrf
            @method('PUT')
            
            <div class="space-y-1.5">
                <label class="block font-label-caps text-label-caps text-on-surface-variant" for="kode_mapel">Kode Mata Pelajaran</label>
                <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('kode_mapel') border-error @enderror" 
                       id="kode_mapel" name="kode_mapel" type="text" required value="{{ old('kode_mapel', $mapel->kode_mapel) }}"/>
                @error('kode_mapel')
                    <p class="text-error text-sm">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="space-y-1.5">
                <label class="block font-label-caps text-label-caps text-on-surface-variant" for="nama_mapel">Nama Mata Pelajaran</label>
                <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('nama_mapel') border-error @enderror" 
                       id="nama_mapel" name="nama_mapel" type="text" required value="{{ old('nama_mapel', $mapel->nama_mapel) }}"/>
                @error('nama_mapel')
                    <p class="text-error text-sm">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                <a href="{{ route('data-master.mapel') }}" 
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