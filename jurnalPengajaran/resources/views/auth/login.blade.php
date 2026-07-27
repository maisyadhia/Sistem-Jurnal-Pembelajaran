@extends('layouts.guest')

@section('title', 'Login - E-Jurnal')

@section('content')
<div class="grid md:grid-cols-2">
    <!-- Brand Column (Desktop Only) -->
    <section class="hidden md:flex flex-col justify-between p-margin-desktop bg-gradient-to-br from-primary via-primary to-primary-container text-on-primary relative overflow-hidden">
        <!-- Decorative Background Circle -->
        <div class="absolute -right-16 -bottom-16 w-64 h-64 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-8">
                <div class="w-12 h-12 flex items-center justify-center filter drop-shadow-md">
                    <img class="w-full h-full object-contain" src="{{ asset('images/logoJurnal.png') }}" alt="Logo SIMJAR"/>
                </div>
                <div>
                    <h1 class="font-headline-md text-headline-md font-bold tracking-tight leading-tight">SIMJAR</h1>
                    <p class="font-label-caps text-[11px] opacity-80 uppercase tracking-wider">Sistem Informasi Jurnal Ajar</p>
                </div>
            </div>
            
            <h2 class="font-display-lg text-display-lg mb-4 font-bold">Akses Terpusat</h2>
            <p class="font-body-base text-body-base opacity-90 leading-relaxed mb-8">
                Sistem informasi jurnal mengajar terintegrasi untuk guru, admin, dan wali murid.
            </p>
            
            <div class="space-y-5">
                <div class="flex items-start gap-3.5 bg-white/5 p-3.5 rounded-xl border border-white/10 backdrop-blur-sm">
                    <span class="material-symbols-outlined mt-0.5 text-secondary-container">verified_user</span>
                    <div>
                        <p class="font-body-base font-semibold">Keamanan Terjamin</p>
                        <p class="font-body-sm text-xs opacity-80">Data terenkripsi dengan standar keamanan institusi.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3.5 bg-white/5 p-3.5 rounded-xl border border-white/10 backdrop-blur-sm">
                    <span class="material-symbols-outlined mt-0.5 text-secondary-container">layers</span>
                    <div>
                        <p class="font-body-base font-semibold">Multi-Role Access</p>
                        <p class="font-body-sm text-xs opacity-80">Satu sistem untuk guru, admin, dan wali murid.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 pt-6 border-t border-white/10 relative z-10">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center filter drop-shadow">
                    <img class="w-full h-full object-contain" src="{{ asset('images/logoMIN2.png') }}" alt="School Logo"/>
                </div>
                <div>
                    <p class="font-label-caps text-[10px] uppercase tracking-wider opacity-70">Institusi Pendidikan</p>
                    <p class="font-body-sm font-semibold text-sm">{{ config('app.school_name', 'MIN 2 Kota Malang') }}</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Login Form Column -->
    <section class="p-6 md:p-margin-desktop bg-surface-container-lowest flex flex-col justify-center">
        <!-- 💡 HEADER ESTETIK KHUSUS HP (MOBILE ONLY) -->
        <div class="md:hidden flex flex-col items-center mb-6 pt-2">
            <div class="w-16 h-16 flex items-center justify-center mb-3 filter drop-shadow-md">
                <img class="w-full h-full object-contain" src="{{ asset('images/logoJurnal.png') }}" alt="Logo SIMJAR"/>
            </div>
            <h1 class="text-2xl font-black text-primary tracking-tight text-center">SIMJAR</h1>
            <p class="text-[11px] font-semibold text-on-surface-variant/80 uppercase tracking-widest text-center mt-0.5">Sistem Informasi Jurnal Ajar</p>
        </div>
        
        <div class="max-w-sm mx-auto w-full">
            <header class="mb-6 text-center md:text-left">
                <h3 class="font-headline-md text-xl md:text-headline-md font-bold text-on-background mb-1">Masuk ke Sistem</h3>
                <p class="font-body-sm text-xs md:text-sm text-on-surface-variant">
                    Pilih peran Anda untuk mengakses sistem.
                </p>
            </header>
            
            <form method="POST" action="{{ route('login') }}" class="space-y-4" id="loginForm">
                @csrf
                
                <!-- Role Selection Buttons -->
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-[11px] font-bold tracking-wider text-on-surface-variant uppercase">Pilih Peran</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" class="role-btn px-2 py-2.5 rounded-xl border border-outline-variant/60 text-center hover:bg-surface-container-low transition-all duration-200 active flex flex-col items-center justify-center gap-1" data-role="admin">
                            <span class="material-symbols-outlined text-xl">admin_panel_settings</span>
                            <span class="text-[11px] font-bold leading-none">Admin</span>
                        </button>
                        <button type="button" class="role-btn px-2 py-2.5 rounded-xl border border-outline-variant/60 text-center hover:bg-surface-container-low transition-all duration-200 flex flex-col items-center justify-center gap-1" data-role="guru">
                            <span class="material-symbols-outlined text-xl">school</span>
                            <span class="text-[11px] font-bold leading-none">Guru</span>
                        </button>
                        <button type="button" class="role-btn px-2 py-2.5 rounded-xl border border-outline-variant/60 text-center hover:bg-surface-container-low transition-all duration-200 flex flex-col items-center justify-center gap-1" data-role="parent">
                            <span class="material-symbols-outlined text-xl">family_history</span>
                            <span class="text-[11px] font-bold leading-none">Wali Murid</span>
                        </button>
                    </div>
                    <input type="hidden" name="role" id="selectedRole" value="admin">
                </div>
                
                <!-- Username / NIK / NISN Input -->
                <div class="space-y-1.5">
                    <label class="block font-label-caps text-[11px] font-bold tracking-wider text-on-surface-variant uppercase" for="nik" id="nikLabel">USERNAME</label>
                    <div class="relative group">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline group-focus-within:text-primary transition-colors text-xl">badge</span>
                        <input class="w-full h-11 pl-11 pr-4 bg-surface border border-outline-variant/70 rounded-xl text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all duration-200 @error('nik') border-error @enderror" 
                               id="nik" 
                               name="nik" 
                               type="text" 
                               placeholder="Masukkan username" 
                               required 
                               value="{{ old('nik') }}"/>
                    </div>
                    @error('nik')
                        <p class="text-error text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                    <p id="nikHelp" class="font-body-sm text-xs text-on-surface-variant" style="display:none;">
                        <span id="nikHelpText"></span>
                    </p>
                </div>
                
                <!-- Password Input -->
                <div class="space-y-1.5" id="passwordContainer">
                    <label class="block font-label-caps text-[11px] font-bold tracking-wider text-on-surface-variant uppercase" for="password" id="passwordLabel">PASSWORD</label>
                    <div class="relative group">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline group-focus-within:text-primary transition-colors text-xl" id="passwordIcon">lock</span>
                        <input class="w-full h-11 pl-11 pr-4 bg-surface border border-outline-variant/70 rounded-xl text-sm focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all duration-200 @error('password') border-error @enderror" 
                               id="password" 
                               name="password" 
                               type="password" 
                               placeholder="Masukkan password" 
                               required/>
                    </div>
                    <p class="font-body-sm text-[11px] text-on-surface-variant/80 mt-1" id="passwordHint" style="display:none;">
                        Format: YYYY-MM-DD (Tahun-Bulan-Tanggal), contoh: 2015-08-17
                    </p>
                    @error('password')
                        <p class="text-error text-xs mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Info Alert Card -->
                <div class="bg-primary-container/20 p-3.5 rounded-xl flex items-start gap-3 border border-primary/10" id="infoAlert">
                    <span class="material-symbols-outlined text-primary text-lg mt-0.5">info</span>
                    <p class="text-xs text-on-surface-variant leading-relaxed" id="infoText">
                        <strong>Admin:</strong> Masukkan username dan password Anda.
                    </p>
                </div>
                
                <!-- Submit Button -->
                <button class="w-full h-11 bg-primary hover:bg-primary-container hover:text-primary text-on-primary font-bold text-sm rounded-xl shadow-md shadow-primary/20 hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2 active:scale-[0.98] mt-2" type="submit">
                    <span>Masuk</span>
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </button>
            </form>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleConfig = {
        admin: {
            nikLabel: 'USERNAME',
            nikPlaceholder: 'Masukkan username admin',
            nikHelp: 'Username adalah ID login untuk admin',
            passwordLabel: 'PASSWORD',
            passwordPlaceholder: 'Masukkan password',
            inputType: 'password',
            icon: 'lock',
            showHint: false,
            info: '<strong>Admin:</strong> Masukkan username dan password Anda.',
            minLength: null
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
            minLength: 16
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

    const roleButtons = document.querySelectorAll('.role-btn');
    const selectedRole = document.getElementById('selectedRole');
    const nikLabel = document.getElementById('nikLabel');
    const nikInput = document.getElementById('nik');
    const nikHelp = document.getElementById('nikHelp');
    const nikHelpText = document.getElementById('nikHelpText');
    const passwordLabel = document.getElementById('passwordLabel');
    const passwordInput = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');
    const passwordHint = document.getElementById('passwordHint');
    const infoText = document.getElementById('infoText');
    const loginForm = document.getElementById('loginForm');

    function updateUIBasedOnRole(role) {
        const config = roleConfig[role];
        if (!config) return;

        nikLabel.textContent = config.nikLabel;
        nikInput.placeholder = config.nikPlaceholder;
        
        if (config.nikHelp) {
            nikHelpText.textContent = config.nikHelp;
            nikHelp.style.display = 'block';
        } else {
            nikHelp.style.display = 'none';
        }

        passwordLabel.textContent = config.passwordLabel;
        passwordInput.type = config.inputType;
        if (config.inputType === 'date') {
            passwordInput.removeAttribute('placeholder');
        } else {
            passwordInput.placeholder = config.passwordPlaceholder;
        }

        passwordIcon.textContent = config.icon;
        passwordHint.style.display = config.showHint ? 'block' : 'none';
        infoText.innerHTML = config.info;

        if (config.minLength) {
            nikInput.setAttribute('minlength', config.minLength);
        } else {
            nikInput.removeAttribute('minlength');
        }
    }

    roleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            roleButtons.forEach(b => b.classList.remove('active', 'bg-primary/10', 'border-primary', 'text-primary', 'shadow-sm'));
            this.classList.add('active', 'bg-primary/10', 'border-primary', 'text-primary', 'shadow-sm');
            
            const role = this.dataset.role;
            selectedRole.value = role;
            updateUIBasedOnRole(role);
            
            document.querySelectorAll('.border-error').forEach(el => {
                el.classList.remove('border-error');
            });
        });
    });

    loginForm.addEventListener('submit', function(e) {
        const role = selectedRole.value;
        const nik = nikInput.value.trim();
        const password = passwordInput.value.trim();
        
        let errors = [];

        if (role === 'admin') {
            if (nik === '') {
                errors.push('Username admin tidak boleh kosong!');
                nikInput.classList.add('border-error');
            } else {
                nikInput.classList.remove('border-error');
            }
            
            if (password === '') {
                errors.push('Password tidak boleh kosong!');
                passwordInput.classList.add('border-error');
            } else {
                passwordInput.classList.remove('border-error');
            }
        }
        
        if (role === 'guru') {
            if (nik.length < 16) {
                errors.push('NIK Guru harus minimal 16 digit!');
                nikInput.classList.add('border-error');
            } else if (!/^\d+$/.test(nik)) {
                errors.push('NIK hanya boleh terdiri dari angka!');
                nikInput.classList.add('border-error');
            } else {
                nikInput.classList.remove('border-error');
            }
            
            if (password === '') {
                errors.push('Password tidak boleh kosong!');
                passwordInput.classList.add('border-error');
            } else {
                passwordInput.classList.remove('border-error');
            }
        }
        
        if (role === 'parent') {
            if (nik === '') {
                errors.push('NISN tidak boleh kosong!');
                nikInput.classList.add('border-error');
            } else {
                nikInput.classList.remove('border-error');
            }
            
            if (password === '') {
                errors.push('Tanggal lahir harus diisi!');
                passwordInput.classList.add('border-error');
            } else {
                passwordInput.classList.remove('border-error');
            }
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join('\n'));
            
            if (nikInput.classList.contains('border-error')) {
                nikInput.focus();
            } else if (passwordInput.classList.contains('border-error')) {
                passwordInput.focus();
            }
            return false;
        }
    });

    nikInput.addEventListener('input', function() {
        const role = selectedRole.value;
        
        if (role === 'guru') {
            const value = this.value;
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

    updateUIBasedOnRole('admin');
    
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
        background-color: rgba(0, 35, 111, 0.08);
        border-color: #00236f;
        color: #00236f;
    }
    .role-btn.active .material-symbols-outlined {
        color: #00236f;
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: invert(38%) sepia(15%) saturate(1224%) hue-rotate(202deg) brightness(94%) contrast(88%);
    }
    
    .border-error {
        border-color: #ef4444 !important;
        animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both;
    }
    
    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }
</style>
@endpush