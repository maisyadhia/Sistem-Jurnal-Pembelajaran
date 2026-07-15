@extends('layouts.guest')

@section('title', 'Login - E-Jurnal')

@section('content')
<div class="grid md:grid-cols-2">
    <!-- Brand Column -->
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
            <h2 class="font-display-lg text-display-lg mb-4">Akses Terpusat</h2>
            <p class="font-body-base text-body-base opacity-90 leading-relaxed mb-6">
                Sistem informasi jurnal mengajar terintegrasi untuk guru, admin, dan wali murid.
            </p>
            <div class="space-y-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined mt-1 text-secondary-container">verified_user</span>
                    <div>
                        <p class="font-body-base font-semibold">Keamanan Terjamin</p>
                        <p class="font-body-sm opacity-80">Data terenkripsi dengan standar keamanan institusi.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined mt-1 text-secondary-container">layers</span>
                    <div>
                        <p class="font-body-base font-semibold">Multi-Role Access</p>
                        <p class="font-body-sm opacity-80">Satu sistem untuk guru, admin, dan wali murid.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 pt-8 border-t border-white/10">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full overflow-hidden border-2 border-white/20">
                    <img class="w-full h-full object-cover" src="{{ asset('images/school-logo.png') }}" alt="School Logo"/>
                </div>
                <div>
                    <p class="font-label-caps text-label-caps">Institusi Pendidikan</p>
                    <p class="font-body-sm font-medium">{{ config('app.school_name', 'MIN 2 Kota Malang') }}</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Login Form -->
    <section class="p-margin-mobile md:p-margin-desktop bg-surface-container-lowest">
        <div class="md:hidden flex flex-col items-center mb-8">
            <div class="w-16 h-16 bg-primary rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-on-primary text-4xl" style="font-variation-settings: 'FILL' 1;">school</span>
            </div>
            <h1 class="font-display-lg-mobile text-display-lg-mobile text-primary text-center">E-Jurnal</h1>
            <p class="font-label-caps text-label-caps text-on-surface-variant text-center">Akses Terpusat</p>
        </div>
        
        <div class="max-w-sm mx-auto">
            <header class="mb-8">
                <h3 class="font-headline-md text-headline-md text-on-background mb-2">Masuk ke Sistem</h3>
                <p class="font-body-sm text-on-surface-variant">
                    Pilih peran Anda untuk mengakses sistem.
                </p>
            </header>
            
            <form method="POST" action="{{ route('login') }}" class="space-y-5" id="loginForm">
                @csrf
                
                <!-- Role Selection -->
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant">Pilih Peran</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" class="role-btn px-4 py-2 rounded-lg border border-outline-variant text-center hover:bg-surface-container-low transition-all active" data-role="admin">
                            <span class="material-symbols-outlined text-lg block mx-auto">admin_panel_settings</span>
                            <span class="text-xs font-medium">Admin/Humas</span>
                        </button>
                        <button type="button" class="role-btn px-4 py-2 rounded-lg border border-outline-variant text-center hover:bg-surface-container-low transition-all" data-role="guru">
                            <span class="material-symbols-outlined text-lg block mx-auto">school</span>
                            <span class="text-xs font-medium">Guru</span>
                        </button>
                        <button type="button" class="role-btn px-4 py-2 rounded-lg border border-outline-variant text-center hover:bg-surface-container-low transition-all" data-role="parent">
                            <span class="material-symbols-outlined text-lg block mx-auto">family_history</span>
                            <span class="text-xs font-medium">Wali Murid</span>
                        </button>
                    </div>
                    <input type="hidden" name="role" id="selectedRole" value="admin">
                </div>
                
                <!-- NIK / NISN Input -->
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="nik" id="nikLabel">USERNAME</label>
                    <div class="relative group">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline group-focus-within:text-primary transition-colors">badge</span>
                        <input class="w-full h-[40px] pl-11 bg-surface border border-outline-variant rounded-lg text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('nik') border-error @enderror" 
                               id="nik" 
                               name="nik" 
                               type="text" 
                               placeholder="Masukkan username" 
                               required 
                               value="{{ old('nik') }}"/>
                    </div>
                    @error('nik')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p id="nikHelp" class="font-body-sm text-on-surface-variant" style="display:none;">
                        <span id="nikHelpText"></span>
                    </p>
                </div>
                
                <!-- Password / DOB Input -->
                <div class="space-y-1.5" id="passwordContainer">
                    <label class="block font-label-caps text-label-caps text-on-surface-variant" for="password" id="passwordLabel">PASSWORD</label>
                    <div class="relative group">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline group-focus-within:text-primary transition-colors" id="passwordIcon">lock</span>
                        <input class="w-full h-[40px] pl-11 bg-surface border border-outline-variant rounded-lg text-body-base focus:ring-2 focus:ring-secondary focus:border-secondary outline-none transition-all @error('password') border-error @enderror" 
                               id="password" 
                               name="password" 
                               type="password" 
                               placeholder="Masukkan password" 
                               required/>
                    </div>
                    <p class="font-body-sm text-on-surface-variant" id="passwordHint" style="display:none;">
                        Format: YYYY-MM-DD (Tahun-Bulan-Tanggal), contoh: 2015-08-17
                    </p>
                    @error('password')
                        <p class="text-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Info Alert -->
                <div class="bg-surface-container-high/50 p-4 rounded-lg flex gap-3 border border-outline-variant" id="infoAlert">
                    <span class="material-symbols-outlined text-secondary text-xl">info</span>
                    <p class="font-body-sm text-on-surface-variant leading-tight" id="infoText">
                        <strong>Admin/Humas:</strong> Masukkan username dan password Anda.
                    </p>
                </div>
                
                <!-- Submit Button -->
                <button class="w-full bg-primary hover:bg-on-primary-fixed-variant text-on-primary font-semibold py-3 rounded-lg shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2 active:scale-[0.98]" type="submit">
                    <span>Masuk</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </form>
            
            <footer class="mt-12 text-center">
                <p class="font-body-sm text-on-surface-variant mb-4">Butuh bantuan akses?</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <a class="px-4 py-2 bg-surface-container-low border border-outline-variant rounded-full text-body-sm font-medium hover:bg-surface-container-high transition-colors flex items-center gap-2" href="#">
                        <span class="material-symbols-outlined text-lg">support_agent</span>
                        Hubungi Admin
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ============ KONFIGURASI ROLE ============
    const roleConfig = {
        admin: {
            nikLabel: 'USERNAME',
            nikPlaceholder: 'Masukkan username admin (contoh: admin, humas)',
            nikHelp: 'Username adalah ID login untuk admin/humas',
            passwordLabel: 'PASSWORD',
            passwordPlaceholder: 'Masukkan password',
            inputType: 'password',
            icon: 'lock',
            showHint: false,
            info: '<strong>Admin/Humas:</strong> Masukkan username dan password Anda.',
            minLength: null // Tidak ada validasi panjang
        },
        guru: {
            nikLabel: 'NIK (NOMOR INDUK KEPENDUDUKAN)',
            nikPlaceholder: 'Masukkan NIK 16 digit',
            nikHelp: 'NIK harus terdiri dari 16 digit angka',
            passwordLabel: 'PASSWORD',
            passwordPlaceholder: 'Masukkan password',
            inputType: 'password',
            icon: 'lock',
            showHint: false,
            info: '<strong>Guru:</strong> Masukkan NIK 16 digit dan password Anda.',
            minLength: 16 // Validasi minimal 16 digit
        },
        parent: {
            nikLabel: 'NISN (NOMOR INDUK SISWA NASIONAL)',
            nikPlaceholder: 'Masukkan NISN siswa',
            nikHelp: 'NISN adalah nomor induk siswa nasional',
            passwordLabel: 'TANGGAL LAHIR SISWA',
            passwordPlaceholder: '',
            inputType: 'date',
            icon: 'calendar_month',
            showHint: true,
            info: '<strong>Wali Murid:</strong> Masukkan NISN dan Tanggal Lahir siswa. Tidak perlu registrasi.',
            minLength: null
        }
    };

    // ============ DOM ELEMENTS ============
    const roleButtons = document.querySelectorAll('.role-btn');
    const selectedRole = document.getElementById('selectedRole');
    const nikLabel = document.getElementById('nikLabel');
    const nikInput = document.getElementById('nik');
    const nikHelp = document.getElementById('nikHelp');
    const nikHelpText = document.getElementById('nikHelpText');
    const passwordContainer = document.getElementById('passwordContainer');
    const passwordLabel = document.getElementById('passwordLabel');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');
    const passwordHint = document.getElementById('passwordHint');
    const infoText = document.getElementById('infoText');
    const loginForm = document.getElementById('loginForm');

    // ============ FUNGSI UPDATE UI ============
    function updateUIBasedOnRole(role) {
        const config = roleConfig[role];
        if (!config) return;

        // Update label dan placeholder NIK
        nikLabel.textContent = config.nikLabel;
        nikInput.placeholder = config.nikPlaceholder;
        
        // Update help text
        if (config.nikHelp) {
            nikHelpText.textContent = config.nikHelp;
            nikHelp.style.display = 'block';
        } else {
            nikHelp.style.display = 'none';
        }

        // Update password field
        passwordLabel.textContent = config.passwordLabel;
        passwordInput.type = config.inputType;
        if (config.inputType === 'date') {
            passwordInput.removeAttribute('placeholder');
        } else {
            passwordInput.placeholder = config.passwordPlaceholder;
        }

        // Update icon
        passwordIcon.textContent = config.icon;

        // Tampilkan/sembunyikan hint
        passwordHint.style.display = config.showHint ? 'block' : 'none';
        
        // Update info
        infoText.innerHTML = config.info;

        // Update validasi min length
        if (config.minLength) {
            nikInput.setAttribute('minlength', config.minLength);
        } else {
            nikInput.removeAttribute('minlength');
        }
    }

    // ============ EVENT LISTENER: ROLE BUTTONS ============
    roleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active state
            roleButtons.forEach(b => b.classList.remove('active', 'bg-primary-container', 'border-primary'));
            this.classList.add('active', 'bg-primary-container', 'border-primary');
            
            const role = this.dataset.role;
            selectedRole.value = role;
            updateUIBasedOnRole(role);
            
            // Reset error state
            document.querySelectorAll('.border-error').forEach(el => {
                el.classList.remove('border-error');
            });
        });
    });

    // ============ VALIDASI CLIENT-SIDE ============
    loginForm.addEventListener('submit', function(e) {
        const role = selectedRole.value;
        const nik = nikInput.value.trim();
        const password = passwordInput.value.trim();
        const config = roleConfig[role];
        
        let errors = [];

        // Validasi berdasarkan role
        if (role === 'admin') {
            // Admin: username tidak boleh kosong
            if (nik === '') {
                errors.push('Username admin tidak boleh kosong!');
                nikInput.classList.add('border-error');
            } else {
                nikInput.classList.remove('border-error');
            }
            
            // Password tidak boleh kosong
            if (password === '') {
                errors.push('Password tidak boleh kosong!');
                passwordInput.classList.add('border-error');
            } else {
                passwordInput.classList.remove('border-error');
            }
        }
        
        if (role === 'guru') {
            // Guru: NIK harus 16 digit
            if (nik.length < 16) {
                errors.push('NIK Guru harus minimal 16 digit!');
                nikInput.classList.add('border-error');
            } else if (!/^\d+$/.test(nik)) {
                errors.push('NIK hanya boleh terdiri dari angka!');
                nikInput.classList.add('border-error');
            } else {
                nikInput.classList.remove('border-error');
            }
            
            // Password tidak boleh kosong
            if (password === '') {
                errors.push('Password tidak boleh kosong!');
                passwordInput.classList.add('border-error');
            } else {
                passwordInput.classList.remove('border-error');
            }
        }
        
        if (role === 'parent') {
            // Parent: NISN tidak boleh kosong
            if (nik === '') {
                errors.push('NISN tidak boleh kosong!');
                nikInput.classList.add('border-error');
            } else {
                nikInput.classList.remove('border-error');
            }
            
            // Tanggal lahir tidak boleh kosong
            if (password === '') {
                errors.push('Tanggal lahir harus diisi!');
                passwordInput.classList.add('border-error');
            } else {
                passwordInput.classList.remove('border-error');
            }
        }

        // Jika ada error, prevent submit
        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join('\n'));
            
            // Focus ke field pertama yang error
            if (nikInput.classList.contains('border-error')) {
                nikInput.focus();
            } else if (passwordInput.classList.contains('border-error')) {
                passwordInput.focus();
            }
            return false;
        }
    });

    // ============ REAL-TIME VALIDASI ============
    // Validasi NIK saat typing
    nikInput.addEventListener('input', function() {
        const role = selectedRole.value;
        const config = roleConfig[role];
        
        if (role === 'guru') {
            const value = this.value;
            // Hanya izinkan angka
            this.value = value.replace(/\D/g, '');
            
            if (this.value.length > 0 && this.value.length < 16) {
                this.classList.add('border-warning');
                this.classList.remove('border-error');
                nikHelpText.textContent = `NIK: ${this.value.length}/16 digit`;
                nikHelp.style.display = 'block';
                nikHelpText.style.color = '#f59e0b';
            } else if (this.value.length >= 16) {
                this.classList.remove('border-warning', 'border-error');
                nikHelpText.textContent = '✓ NIK lengkap 16 digit';
                nikHelp.style.display = 'block';
                nikHelpText.style.color = '#10b981';
            } else {
                this.classList.remove('border-warning', 'border-error');
                nikHelp.style.display = 'none';
            }
        }
    });

    // ============ INIT ============
    // Set default role (admin)
    updateUIBasedOnRole('admin');
    
    // Style untuk border warning
    const style = document.createElement('style');
    style.textContent = `
        .border-warning {
            border-color: #f59e0b !important;
        }
        .border-warning:focus {
            ring-color: #f59e0b !important;
        }
    `;
    document.head.appendChild(style);
});
</script>

<style>
    .role-btn.active {
        background-color: #dce1ff;
        border-color: #4059aa;
    }
    .role-btn.active .material-symbols-outlined {
        color: #00236f;
    }

    /* Styling native date picker icon */
    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: invert(38%) sepia(15%) saturate(1224%) hue-rotate(202deg) brightness(94%) contrast(88%);
    }
    
    /* Animasi untuk validasi */
    .border-error {
        border-color: #ef4444 !important;
        animation: shake 0.5s;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
</style>
@endpush