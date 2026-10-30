<header
    class="flex justify-between items-center w-full px-3 md:px-margin-desktop h-16 bg-surface border-b border-outline-variant sticky top-0 z-30">
    <!-- KIRI: Hamburger Menu & Tanggal -->
    <div class="flex items-center gap-2 md:gap-4 min-w-0">
        <button class="md:hidden p-2 hover:bg-surface-container-low rounded-full shrink-0" onclick="toggleSidebar()">
            <span class="material-symbols-outlined block">menu</span>
        </button>

        <!-- Tanggal: Ringkas di HP, Lengkap di Laptop -->
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-1.5 hover:opacity-80 transition-opacity bg-surface-container-low/60 md:bg-transparent px-2.5 py-1 md:p-0 rounded-lg shrink-0">
            <span class="material-symbols-outlined text-primary text-lg md:text-xl">calendar_today</span>
            <span class="font-data-tabular text-xs md:text-data-tabular text-on-surface-variant font-medium">
                <span class="hidden sm:inline">{{ now()->locale('id')->isoFormat('dddd, D MMM YYYY') }}</span>
                <span class="inline sm:hidden">{{ now()->locale('id')->isoFormat('D MMM YYYY') }}</span>
            </span>
        </a>
    </div>

    <!-- KANAN: Notifikasi & Profil Avatar -->
    <div class="flex items-center gap-2 sm:gap-6 shrink-0">
        <div class="flex items-center gap-2 sm:gap-3">
            @php
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

            <!-- NOTIFICATION DROPDOWN MENU (UNTUK GURU) -->
            @if(session('user_role') === 'guru' && !request()->routeIs('dashboard.timeline'))
                <div class="relative" id="notifDropdownContainer">
                    <button type="button" onclick="toggleNotificationDropdown()"
                        class="p-2 text-on-surface-variant hover:bg-surface-container-low transition-colors rounded-full relative focus:outline-none flex items-center justify-center">
                        <span class="material-symbols-outlined text-2xl block">notifications</span>

                        @if($unreadNotifications->count() > 0)
                            <span class="absolute top-1 right-1 flex h-4 w-4 shrink-0 pointer-events-none">
                                <span class="animate-ping absolute inset-0 rounded-full bg-red-400 opacity-75"></span>
                                <span
                                    class="relative w-full h-full rounded-full bg-red-600 text-white text-[10px] font-extrabold flex items-center justify-center leading-none shadow-sm pb-[1px]">
                                    {{ $unreadNotifications->count() > 9 ? '9+' : $unreadNotifications->count() }}
                                </span>
                            </span>
                        @endif
                    </button>

                    <!-- Dropdown Pop-up Notifikasi -->
                    <div id="notifDropdownMenu"
                        class="hidden absolute right-0 mt-2 w-72 sm:w-80 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden animate-fade-in">
                        <div class="p-3.5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-500 text-lg">notifications_active</span>
                                <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Notifikasi Peringatan
                                </h4>
                            </div>
                            <span id="notifCountBadge"
                                class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                {{ $unreadNotifications->count() }} Baru
                            </span>
                        </div>

                        <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto custom-scrollbar"
                            id="notifListContainer">
                            @forelse($unreadNotifications as $notif)
                                @php
                                    $targetKelasId = $notif->kelas_id ?? null;
                                    $targetMapelId = $notif->mapel_id ?? null;

                                    if (!$targetKelasId || !$targetMapelId) {
                                        \Carbon\Carbon::setLocale('id');
                                        $hariIndo = \Carbon\Carbon::now()->translatedFormat('l');

                                        preg_match('/\((.*?)\)/', $notif->message, $matches);
                                        $mapelInNotif = isset($matches[1]) ? trim($matches[1]) : '';

                                        $allJadwalHariIni = DB::table('jadwals')
                                            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
                                            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
                                            ->where('jadwals.guru_id', $userId)
                                            ->where('jadwals.hari', $hariIndo)
                                            ->select('jadwals.kelas_id', 'jadwals.mapel_id', 'kelas_master.nama_kelas', 'mapel_master.nama_mapel')
                                            ->get();

                                        foreach ($allJadwalHariIni as $j) {
                                            $kelasMatch = str_contains($notif->message, $j->nama_kelas);

                                            $mapelMatch = false;
                                            if (!empty($mapelInNotif)) {
                                                $mapelMatch = (strcasecmp($j->nama_mapel, $mapelInNotif) === 0) || str_contains($mapelInNotif, $j->nama_mapel) || str_contains($j->nama_mapel, $mapelInNotif);
                                            } else {
                                                $mapelMatch = str_contains($notif->message, $j->nama_mapel);
                                            }

                                            if ($kelasMatch && $mapelMatch) {
                                                $targetKelasId = $j->kelas_id;
                                                $targetMapelId = $j->mapel_id;
                                                break;
                                            }
                                        }
                                    }

                                    $urlTujuan = ($targetKelasId && $targetMapelId)
                                        ? route('guru.jurnal.form', ['kelas_id' => $targetKelasId, 'mapel_id' => $targetMapelId])
                                        : route('guru.pilih.sesi');
                                @endphp

                                <a href="{{ $urlTujuan }}"
                                    class="p-3 hover:bg-amber-50/60 transition-colors flex items-start gap-3 relative group block">
                                    <span
                                        class="material-symbols-outlined text-amber-500 text-base mt-0.5 shrink-0">warning</span>
                                    <div class="flex-1 text-xs">
                                        <p class="text-slate-800 font-medium leading-snug">{{ $notif->message }}</p>
                                        <div class="flex items-center justify-between mt-1.5">
                                            <span class="text-[10px] text-slate-400">
                                                {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
                                            </span>
                                            <span
                                                class="text-[10px] font-bold text-teal-600 group-hover:underline flex items-center gap-0.5">
                                                Isi Jurnal
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
            @endif

            <!-- Garis Pemisah -->
            @if(session('user_role') === 'guru' && !request()->routeIs('dashboard.timeline'))
                <div class="h-6 w-px bg-outline-variant/60"></div>
            @endif

            <!-- User Profile Avatar -->
            <div class="relative" id="profileDropdownContainer">
                @if(session('user_role') === 'guru')
                    <!-- JIKA GURU: Gunakan Dropdown Lengkap (Profil & Keluar) -->
                    <button type="button" onclick="toggleProfileDropdown()"
                        class="flex items-center gap-2.5 pl-1 focus:outline-none hover:opacity-80 transition-opacity rounded-xl p-1 cursor-pointer">
                        <div class="text-right hidden sm:block">
                            <p class="font-label-caps text-label-caps text-primary leading-none mb-1 font-bold">
                                {{ session('user_name', 'Pengguna') }}</p>
                            <p class="text-[10px] text-on-surface-variant/70 leading-none font-medium">Guru</p>
                        </div>

                        @php
                            $guruDb = DB::table('guru')->where('id', $userId)->orWhere('nama_guru', session('user_name'))->first();
                            $avatarUrl = ($guruDb && !empty($guruDb->foto))
                                ? asset($guruDb->foto)
                                : "https://ui-avatars.com/api/?name=" . urlencode(session('user_name', 'User')) . "&background=00236f&color=ffffff&size=40";
                        @endphp

                        <img class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border-2 border-primary-fixed object-cover shrink-0 shadow-sm"
                            src="{{ $avatarUrl }}" alt="{{ session('user_name', 'User') }}" />

                        <span class="material-symbols-outlined text-slate-400 text-sm hidden sm:inline">expand_more</span>
                    </button>

                    <!-- Dropdown Menu Khusus Guru -->
                    <div id="profileDropdownMenu"
                        class="hidden absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-2xl border border-slate-100 z-50 overflow-hidden animate-fade-in py-1">
                        <div class="px-4 py-2.5 border-b border-slate-100 sm:hidden bg-slate-50/50">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ session('user_name', 'Pengguna') }}</p>
                            <p class="text-[10px] text-slate-500 capitalize">Guru</p>
                        </div>

                        <a href="{{ route('guru.profile.edit') }}"
                            class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-slate-700 hover:bg-teal-50 hover:text-teal-700 font-medium transition-colors">
                            <span class="material-symbols-outlined text-base text-teal-600">manage_accounts</span>
                            Pengaturan Profil
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs text-red-600 hover:bg-red-50 font-medium text-left transition-colors">
                                <span class="material-symbols-outlined text-base">logout</span>
                                Keluar
                            </button>
                        </form>
                    </div>

                @else
                    <!-- JIKA ADMIN / WALI MURID: Tampilan Profil Bersih Tanpa Ikon Logout Ekstra -->
                    <div class="flex items-center gap-2.5 pl-1 rounded-xl p-1">
                        <div class="text-right hidden sm:block">
                            <p class="font-label-caps text-label-caps text-primary leading-none mb-1 font-bold">
                                {{ session('user_name', 'Pengguna') }}</p>
                            <p class="text-[10px] text-on-surface-variant/70 leading-none font-medium">
                                @if(session('user_role') == 'parent')
                                    Wali Murid
                                @elseif(session('user_role') == 'admin')
                                    Admin
                                @else
                                    Pengguna
                                @endif
                            </p>
                        </div>

                        @php
                            $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode(session('user_name', 'User')) . "&background=00236f&color=ffffff&size=40";
                        @endphp

                        <img class="w-9 h-9 sm:w-10 sm:h-10 rounded-full border-2 border-primary-fixed object-cover shrink-0 shadow-sm"
                            src="{{ $avatarUrl }}" alt="{{ session('user_name', 'User') }}" />
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>

<!-- OVERLAY - TUTUP SIDEBAR SAAT KLIK DI LUAR -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden" onclick="closeSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (sidebar) {
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('fixed');
            sidebar.classList.toggle('left-0');
            sidebar.classList.toggle('top-0');
            sidebar.classList.toggle('h-full');
            sidebar.classList.toggle('z-50');
            sidebar.classList.toggle('w-64');
            sidebar.classList.toggle('shadow-2xl');
        }

        if (overlay) {
            overlay.classList.toggle('hidden');
        }
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (sidebar) {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('fixed', 'left-0', 'top-0', 'h-full', 'z-50', 'w-64', 'shadow-2xl');
        }

        if (overlay) {
            overlay.classList.add('hidden');
        }
    }

    function toggleNotificationDropdown() {
        const menu = document.getElementById('notifDropdownMenu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    function toggleProfileDropdown() {
        const menu = document.getElementById('profileDropdownMenu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    document.addEventListener('click', function (e) {
        // Tutup Notifikasi saat klik di luar
        const notifContainer = document.getElementById('notifDropdownContainer');
        const notifMenu = document.getElementById('notifDropdownMenu');
        if (notifContainer && notifMenu && !notifContainer.contains(e.target)) {
            notifMenu.classList.add('hidden');
        }

        // Tutup Profil saat klik di luar
        const profileContainer = document.getElementById('profileDropdownContainer');
        const profileMenu = document.getElementById('profileDropdownMenu');
        if (profileContainer && profileMenu && !profileContainer.contains(e.target)) {
            profileMenu.classList.add('hidden');
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 768) {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar) {
                sidebar.classList.remove('fixed', 'left-0', 'top-0', 'h-full', 'z-50', 'w-64', 'shadow-2xl');
                sidebar.classList.add('md:flex');
            }
            if (overlay) {
                overlay.classList.add('hidden');
            }
        }
    });
</script>