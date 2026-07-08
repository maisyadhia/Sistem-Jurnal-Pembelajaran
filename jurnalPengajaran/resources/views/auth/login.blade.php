@extends('layouts.guest')

@section('title', 'Login Wali Murid - E-Jurnal')

@section('content')
<div class="grid md:grid-cols-2">
    <!-- Brand & Info Column -->
    <section class="hidden md:flex flex-col justify-between p-margin-desktop bg-primary text-on-primary">
        <div>
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 bg-on-primary rounded-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">school</span>
                </div>
                <div>
                    <h1 class="font-headline-md text-headline-md leading-tight">E-Jurnal</h1>
                    <p class="font-label-caps text-label-caps opacity-80">Administrasi Terpadu</p>
                </div>
            </div>
            <h2 class="font-display-lg text-display-lg mb-4">Akses Terpusat Wali Murid</h2>
            <p class="font-body-base text-body-base opacity-90 leading-relaxed mb-6">
                Pantau perkembangan belajar, kehadiran, dan aktivitas jurnal mengajar putra-putri Anda secara langsung dan real-time.
            </p>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined mt-1 text-secondary-container">verified_user</span>
                    <div>
                        <p class="font-body-base font-semibold">Keamanan Terjamin</p>
                        <p class="font-body-sm opacity-80">Data siswa dilindungi dengan enkripsi standar institusi.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined mt-1 text-secondary-container">no_accounts</span>
                    <div>
                        <p class="font-body-base font-semibold">Tanpa Registrasi</p>
                        <p class="font-body-sm opacity-80">Gunakan kredensial resmi sekolah untuk akses instan.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 pt-8 border-t border-white/10">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white/20">
                    <img class="w-full h-full object-cover" 
                         src="{{ asset('images/school-logo.png') }}" 
                         alt="School Logo"/>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps">Institusi Pendidikan</p>
                    <p class="font-body-sm font-medium">{{ config('app.school_name', 'SMK Negeri Unggul Nusantara') }}</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Login Form Column -->
    <section class="p-margin-mobile md:p-margin-desktop bg-surface-container-lowest">
        <div class="md:hidden flex flex-col items-center mb-8">
            <div class="w-16 h-16 bg-primary rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-on-primary text-4xl" style="font-variation-settings: 'FILL' 1;">school</span>
            </div>
            <h1 class="font-display-lg-mobile text-display-lg-mobile text-primary text-center">E-Jurnal SMKN</h1>
            <p class="font-label-caps text-label-caps text-on-surface-variant text-center">Wali Murid Secure Access</p>
        </div>
        
        <div class="max-w-sm mx-auto">
            <header class="mb-8">
                <h3 class="font-headline-md text-headline-md text-on-background mb-2">Masuk ke Sistem</h3>
                <p class="font-body-sm text-on-surface-variant">
                    Silakan masukkan identitas siswa untuk melihat laporan jurnal dan kehadiran.
                </p>
            </header>
            
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                
                <!-- NISN Input -->
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="nisn">
                        NISN (NOMOR INDUK SISWA NASIONAL)
                    </label>
                    <div class="relative group">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline group-focus-within:text-primary transition-colors">fingerprint</span>
                        <input class="w-full h-[40px] pl-11 bg-surface border border-outline-variant rounded-lg text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('nisn') border-error @enderror" 
                               id="nisn" 
                               name="nisn" 
                               type="text" 
                               pattern="[0-9]{10}" 
                               placeholder="10 digit NISN" 
                               required 
                               value="{{ old('nisn') }}"/>
                    </div>
                    @error('nisn')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-[11px] text-on-surface-variant opacity-70">Lihat pada kartu pelajar atau raport siswa.</p>
                </div>
                
                <!-- DOB Input -->
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="dob">
                        TANGGAL LAHIR SISWA
                    </label>
                    <div class="relative group">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline group-focus-within:text-primary transition-colors">calendar_month</span>
                        <input class="w-full h-[40px] pl-11 bg-surface border border-outline-variant rounded-lg text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('dob') border-error @enderror" 
                               id="dob" 
                               name="dob" 
                               type="date" 
                               required 
                               value="{{ old('dob') }}"/>
                    </div>
                    @error('dob')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Info Alert -->
                <div class="bg-surface-container-high/50 p-4 rounded-lg flex gap-3 border border-outline-variant">
                    <span class="material-symbols-outlined text-secondary text-xl">info</span>
                    <p class="font-body-sm text-on-surface-variant leading-tight">
                        <strong>Penting:</strong> Wali murid tidak perlu melakukan registrasi. Akses dibuka otomatis menggunakan data sekolah.
                    </p>
                </div>
                
                <!-- Submit Button -->
                <button class="w-full bg-primary hover:bg-on-primary-fixed-variant text-on-primary font-semibold py-3 rounded-lg shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2 active:scale-[0.98]" type="submit">
                    <span>Akses Jurnal Siswa</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </form>
            
            <footer class="mt-12 text-center">
                <p class="font-body-sm text-on-surface-variant mb-4">Butuh bantuan akses?</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <a class="px-4 py-2 bg-surface-container-low border border-outline-variant rounded-full text-body-sm font-medium hover:bg-surface-container-high transition-colors flex items-center gap-2" href="#">
                        <span class="material-symbols-outlined text-lg">support_agent</span>
                        Hubungi Humas
                    </a>
                    <a class="px-4 py-2 bg-surface-container-low border border-outline-variant rounded-full text-body-sm font-medium hover:bg-surface-container-high transition-colors flex items-center gap-2" href="#">
                        <span class="material-symbols-outlined text-lg">help_center</span>
                        Panduan
                    </a>
                </div>
            </footer>
        </div>
    </section>
</div>
@endsection