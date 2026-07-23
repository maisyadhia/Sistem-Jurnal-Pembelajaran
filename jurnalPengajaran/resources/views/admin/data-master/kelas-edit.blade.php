@extends('layouts.app')

@section('title', 'Edit Kelas - E-Jurnal')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
        <div class="mb-6">
            <h2 class="font-headline-md text-headline-md text-on-background">Edit Kelas</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Perbarui data kelas.</p>
        </div>
        
        <form method="POST" action="{{ route('data-master.kelas.update', $kelas->id) }}" class="space-y-5">
            @csrf
            @method('PUT')
            
            <div class="space-y-1.5">
                <label class="block font-label-caps text-label-caps text-on-surface-variant" for="nama_kelas">Nama Kelas</label>
                <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('nama_kelas') border-error @enderror" 
                       id="nama_kelas" name="nama_kelas" type="text" required value="{{ old('nama_kelas', $kelas->nama_kelas) }}"/>
                @error('nama_kelas')
                    <p class="text-error text-sm">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="space-y-1.5">
                <label class="block font-label-caps text-label-caps text-on-surface-variant" for="wali_kelas">Wali Kelas</label>
                <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('wali_kelas') border-error @enderror" 
                       id="wali_kelas" name="wali_kelas" type="text" required value="{{ old('wali_kelas', $kelas->wali_kelas) }}"/>
                @error('wali_kelas')
                    <p class="text-error text-sm">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                <a href="{{ route('data-master.kelas') }}" 
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