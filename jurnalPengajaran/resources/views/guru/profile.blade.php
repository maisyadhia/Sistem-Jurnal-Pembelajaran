@extends('layouts.app')

@section('title', 'Pengaturan Profil - SIMJAR')

@section('content')
<div class="max-w-2xl mx-auto w-full flex flex-col gap-6 pt-4 pb-8 px-3 md:px-6">
    <div>
        <h2 class="text-xl md:text-2xl font-bold text-slate-800">Pengaturan Profil</h2>
        <p class="text-xs text-slate-500">Kelola foto profil dan keamanan sandi akun Anda.</p>
    </div>

    <!-- Alert Sukses / Error -->

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-700 text-xs rounded-2xl flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">error</span>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <!-- Form Ganti Foto Profil -->
    <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-teal-600">photo_camera</span>
            Ganti Foto Profil
        </h3>

        <form action="{{ route('guru.profile.update-photo') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
            @csrf
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-slate-900 text-white rounded-full flex items-center justify-center font-bold text-lg overflow-hidden shrink-0 shadow-inner">
                    @if(isset($guru->foto) && !empty($guru->foto))
                        <img src="{{ asset(ltrim($guru->foto, '/')) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($guru->nama_guru ?? session('user_name', 'G'), 0, 2)) }}
                    @endif
                </div>
                <div class="flex-1">
                    <input type="file" name="photo" accept="image/png, image/jpeg, image/jpg" required
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                    <p class="text-[10px] text-slate-400 mt-1">Format: JPG, JPEG, PNG (Maks. 2MB)</p>
                </div>
            </div>
            <div class="flex justify-end pt-2">
                <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                    Unggah Foto Baru
                </button>
            </div>
        </form>
    </div>

    <!-- Form Ganti Password -->
    <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-teal-600">lock</span>
            Ubah Password
        </h3>

        <form action="{{ route('guru.profile.update-password') }}" method="POST" class="flex flex-col gap-4">
            @csrf
            
            <!-- Password Saat Ini -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Password Saat Ini</label>
                <div class="relative">
                    <input type="password" name="current_password" id="current_password" required 
                           class="w-full px-3 py-2 pr-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500">
                    <button type="button" onclick="togglePassword('current_password', 'icon_current')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                        <span class="material-symbols-outlined text-base" id="icon_current">visibility</span>
                    </button>
                </div>
            </div>

            <!-- Password Baru -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Password Baru</label>
                <div class="relative">
                    <input type="password" name="new_password" id="new_password" required 
                           class="w-full px-3 py-2 pr-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500">
                    <button type="button" onclick="togglePassword('new_password', 'icon_new')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                        <span class="material-symbols-outlined text-base" id="icon_new">visibility</span>
                    </button>
                </div>
            </div>

            <!-- Konfirmasi Password Baru -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required 
                           class="w-full px-3 py-2 pr-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500">
                    <button type="button" onclick="togglePassword('new_password_confirmation', 'icon_confirm')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                        <span class="material-symbols-outlined text-base" id="icon_confirm">visibility</span>
                    </button>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-all shadow-sm">
                    Perbarui Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId, iconId) {
    const input = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        icon.textContent = 'visibility';
    }
}
</script>
@endpush