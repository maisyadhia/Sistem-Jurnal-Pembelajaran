@extends('layouts.app')

@section('title', 'Pengaturan Profil - SIMJAR')

@section('content')
    <div class="max-w-2xl mx-auto w-full flex flex-col gap-6 pt-4 pb-8 px-3 md:px-6">
        <div>
            <h2 class="text-xl md:text-2xl font-bold text-slate-800">Pengaturan Profil</h2>
            <p class="text-xs text-slate-500">Kelola foto profil, nomor WhatsApp, dan keamanan sandi akun Anda.</p>
        </div>

        <!-- 
          Blok alert sukses lokal dihapus dari sini karena layout utama (app.blade.php) 
          sudah otomatis menampilkannya secara global agar tidak kembar.
        -->

        <!-- FORM UTAMA TERPADU -->
        <form action="{{ route('guru.profile.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
            @csrf
            @method('PUT')

            <!-- 1. Ganti Foto Profil -->
            <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-slate-200/80">
                <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-teal-600">photo_camera</span>
                    Ganti Foto Profil
                </h3>

                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-slate-900 text-white rounded-full flex items-center justify-center font-bold text-lg overflow-hidden shrink-0 shadow-inner">
                        @if(isset($guru->foto) && !empty($guru->foto))
                            <img src="{{ asset(ltrim($guru->foto, '/')) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($guru->nama_guru ?? session('user_name', 'G'), 0, 2)) }}
                        @endif
                    </div>
                    <div class="flex-1">
                        <!-- Bersifat opsional (tidak required) -->
                        <input type="file" name="photo" accept="image/png, image/jpeg, image/jpg"
                            class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1">Format: JPG, JPEG, PNG (Maks. 2MB)</p>
                    </div>
                </div>
            </div>

            <!-- 2. Informasi Akun & Nomor WhatsApp -->
            <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-slate-200/80 flex flex-col gap-4">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-teal-600">badge</span>
                    Informasi Kontak
                </h3>

                <!-- Nama Guru (Read-only) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Guru</label>
                    <p class="text-xs font-bold text-slate-800 bg-slate-100 px-3 py-2 rounded-xl border border-slate-200">
                        {{ $guru->nama_guru ?? ($user->name ?? 'Guru') }}
                    </p>
                </div>

                <!-- Input Nomor WhatsApp / HP (Data lama otomatis muncul & bisa diedit) -->
                <div>
                    <label for="no_hp" class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp</label>
                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $guru->no_hp ?? '') }}"
                        placeholder="Contoh: 081234567890"
                        class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-500">
                    <p class="text-[10px] text-slate-400 mt-1">*Gunakan nomor yang aktif.</p>
                </div>
            </div>

            <!-- 3. Ubah Password (Opsional) -->
            <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-slate-200/80 flex flex-col gap-4">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <span class="material-symbols-outlined text-teal-600">lock</span>
                    Ubah Password <span class="text-[10px] font-normal text-slate-400">(Kosongkan jika tidak ingin mengubah password)</span>
                </h3>

                <!-- Password Saat Ini -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Password Saat Ini</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="current_password"
                            class="w-full px-3 py-2 pr-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500">
                        <button type="button" onclick="togglePassword('current_password', 'icon_current')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <span class="material-symbols-outlined text-base" id="icon_current">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Password Baru -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="new_password" id="new_password"
                            class="w-full px-3 py-2 pr-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500">
                        <button type="button" onclick="togglePassword('new_password', 'icon_new')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <span class="material-symbols-outlined text-base" id="icon_new">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Password Baru -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Konfirmasi Password Baru</label>
                    <div class="relative">
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                            class="w-full px-3 py-2 pr-10 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-teal-500">
                        <button type="button" onclick="togglePassword('new_password_confirmation', 'icon_confirm')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none">
                            <span class="material-symbols-outlined text-base" id="icon_confirm">visibility</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit Tunggal -->
            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition-all shadow-md">
                    Update Data Profil
                </button>
            </div>
        </form>
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