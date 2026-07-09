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
        <div class="hidden lg:flex items-center bg-surface-container-low px-4 py-1.5 rounded-full border border-outline-variant/30">
            <span class="material-symbols-outlined text-sm mr-2 text-outline">search</span>
            <input class="bg-transparent border-none focus:ring-0 text-sm w-48" placeholder="Cari aktivitas..." type="text"/>
        </div>
        
        <div class="flex items-center gap-3">
            <button class="p-2 text-on-surface-variant hover:bg-surface-container-low transition-colors rounded-full relative">
                <span class="material-symbols-outlined">notifications</span>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full"></span>
            </button>
            <button class="p-2 text-on-surface-variant hover:bg-surface-container-low transition-colors rounded-full">
                <span class="material-symbols-outlined">help</span>
            </button>
            
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
</script>