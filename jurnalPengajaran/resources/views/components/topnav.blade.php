<header class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop h-16 bg-surface border-b border-outline-variant sticky top-0 z-30">
    <div class="flex items-center gap-4">
        <button class="md:hidden p-2 hover:bg-surface-container-low rounded-full" onclick="toggleSidebar()">
            <span class="material-symbols-outlined">menu</span>
        </button>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
            <span class="material-symbols-outlined text-primary">calendar_today</span>
            <span class="font-data-tabular text-data-tabular text-on-surface-variant">
                {{ now()->locale('id')->isoFormat('dddd, D MMM YYYY') }}
            </span>
        </a>
    </div>
    
    <div class="flex items-center gap-6">
        <div class="flex items-center gap-3">
            @php
                // Ambil Notifikasi Belum Dibaca KHUSUS untuk GURU yang Sedang Login 
                $userId = session('guru_id') ?? session('admin_id') ?? session('user_id');
                $unreadNotifications = collect();

                if (session('user_role') === 'guru') {
                    $unreadNotifications = DB::table('notifications')
                        ->where('user_id', $userId)
                        ->where('is_read', 0)
                        ->orderBy('created_at', 'desc')
                        ->get();
                }
            @endphp

            <!-- 💡 NOTIFICATION DROPDOWN MENU (HANYA DITAMPILKAN JIKA USER ADALAH GURU) -->
            @if(session('user_role') === 'guru')
                <div class="relative" id="notifDropdownContainer">
                    <button type="button" onclick="toggleNotificationDropdown()" 
                            class="p-2 text-on-surface-variant hover:bg-surface-container-low transition-colors rounded-full relative focus:outline-none">
                        <span class="material-symbols-outlined">notifications</span>
                        
                        <!-- Red Badge Alert jika ada notifikasi belum dibaca -->
                        @if($unreadNotifications->count() > 0)
                            <span id="notifBadgeDot" class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-error rounded-full animate-ping"></span>
                            <span id="notifBadgeDotStatic" class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-error rounded-full"></span>
                        @endif
                    </button>

                    <!-- Dropdown Pop-up Notifikasi -->
                    <div id="notifDropdownMenu" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden animate-fade-in">
                        <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-500 text-lg">notifications_active</span>
                                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Notifikasi Peringatan</h4>
                            </div>
                            <span id="notifCountBadge" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                {{ $unreadNotifications->count() }} Baru
                            </span>
                        </div>

                        <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto custom-scrollbar" id="notifListContainer">
                            @forelse($unreadNotifications as $notif)
                                <!-- DIKLIK LANGSUNG MENUJU HALAMAN ISI JURNAL -->
                                <a href="{{ route('guru.pilih.sesi') }}" class="p-3 hover:bg-amber-50/60 transition-colors flex items-start gap-3 relative group block">
                                    <span class="material-symbols-outlined text-amber-500 text-base mt-0.5">warning</span>
                                    <div class="flex-1 text-xs">
                                        <p class="text-slate-800 font-medium leading-snug">{{ $notif->message }}</p>
                                        <div class="flex items-center justify-between mt-1.5">
                                            <span class="text-[10px] text-slate-400">
                                                {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                            </span>
                                            <span class="text-[10px] font-bold text-teal-600 group-hover:underline flex items-center gap-0.5">
                                                Isi Jurnal Sekarang
                                                <span class="material-symbols-outlined text-[10px]">arrow_forward</span>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-6 text-center text-slate-400 text-xs italic">
                                    Semua jurnal hari ini sudah lengkap diisi! 🎉
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @else
                <!-- Lonceng biasa/pasif untuk role non-guru -->
                <button class="p-2 text-on-surface-variant hover:bg-surface-container-low transition-colors rounded-full">
                    <span class="material-symbols-outlined">notifications</span>
                </button>
            @endif

            <div class="h-8 w-px bg-outline-variant"></div>
            
            <div class="flex items-center gap-3 pl-2">
                <div class="text-right hidden sm:block">
                    <p class="font-label-caps text-label-caps text-primary leading-none mb-1">{{ session('user_name', 'Pengguna') }}</p>
                    <p class="text-[10px] text-on-surface-variant/70 leading-none">
                        @if(session('user_role') == 'parent')
                            Wali Murid
                        @elseif(session('user_role') == 'admin')
                            Admin
                        @elseif(session('user_role') == 'humas')
                            Humas
                        @elseif(session('user_role') == 'guru')
                            Guru
                        @else
                            Pengguna
                        @endif
                    </p>
                </div>
                <img class="w-10 h-10 rounded-full border-2 border-primary-fixed object-cover" 
                     src="https://ui-avatars.com/api/?name={{ urlencode(session('user_name', 'User')) }}&background=00236f&color=ffffff&size=40" 
                     alt="{{ session('user_name', 'User') }}"/>
            </div>
        </div>
    </div>
</header>

<script>
function toggleSidebar() {
    const sidebar = document.querySelector('aside');
    if (sidebar) {
        sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('fixed');
        sidebar.classList.toggle('left-0');
        sidebar.classList.toggle('top-0');
        sidebar.classList.toggle('h-full');
        sidebar.classList.toggle('z-50');
        sidebar.classList.toggle('w-64');
    }
}

// Toggle Dropdown Notifikasi
function toggleNotificationDropdown() {
    const menu = document.getElementById('notifDropdownMenu');
    if (menu) {
        menu.classList.toggle('hidden');
    }
}

// Tutup dropdown jika klik di luar area
document.addEventListener('click', function(e) {
    const container = document.getElementById('notifDropdownContainer');
    const menu = document.getElementById('notifDropdownMenu');
    if (container && menu && !container.contains(e.target)) {
        menu.classList.add('hidden');
    }
});
</script>