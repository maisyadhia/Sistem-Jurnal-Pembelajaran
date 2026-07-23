@extends('layouts.app')

@section('title', 'Tambah Siswa - E-Jurnal')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-6">
        <div class="mb-6">
            <h2 class="font-headline-md text-headline-md text-on-background">Tambah Siswa</h2>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Tambahkan data siswa baru.</p>
        </div>
        
        <form method="POST" action="{{ route('data-master.siswa.store') }}" class="space-y-5">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="nisn">NISN</label>
                    <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('nisn') border-error @enderror" 
                           id="nisn" name="nisn" type="text" required value="{{ old('nisn') }}" maxlength="10"/>
                    @error('nisn')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="dob">Tanggal Lahir</label>
                    <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('dob') border-error @enderror" 
                           id="dob" name="dob" type="date" required value="{{ old('dob') }}"/>
                    @error('dob')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="space-y-1.5">
                <label class="block font-label-caps text-label-caps text-on-surface-variant" for="name">Nama Siswa</label>
                <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('name') border-error @enderror" 
                       id="name" name="name" type="text" required value="{{ old('name') }}"/>
                @error('name')
                    <p class="text-error text-sm">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="space-y-1.5">
                <label class="block font-label-caps text-label-caps text-on-surface-variant" for="class">Kelas</label>
                <select class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('class') border-error @enderror" 
                        id="class" name="class" required>
                    <option value="">Pilih Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->nama_kelas }}" {{ old('class') == $k->nama_kelas ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
                @error('class')
                    <p class="text-error text-sm">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="parent_name">Nama Orang Tua</label>
                    <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('parent_name') border-error @enderror" 
                           id="parent_name" name="parent_name" type="text" value="{{ old('parent_name') }}"/>
                    @error('parent_name')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="parent_phone">No. Telepon Orang Tua</label>
                    <input class="w-full h-[40px] bg-surface border border-outline-variant rounded-lg px-4 text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('parent_phone') border-error @enderror" 
                           id="parent_phone" name="parent_phone" type="text" value="{{ old('parent_phone') }}"/>
                    @error('parent_phone')
                        <p class="text-error text-sm">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-outline-variant">
                <a href="{{ route('data-master.siswa') }}" 
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