<aside class="hidden md:flex flex-col h-full py-6 px-4 w-64 bg-surface-container-low border-r border-outline-variant">
    <div class="mb-10 px-2 flex items-center gap-3">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1;">school</span>
        </div>
        <div>
            <h1 class="font-headline-md text-headline-md text-primary leading-tight">E-Jurnal</h1>
            <p class="text-[10px] font-label-caps uppercase tracking-widest text-on-surface-variant opacity-70">
                @if(session('user_role') === 'admin' || session('user_role') === 'humas')
                    {{ session('admin_role') ?? 'Administrator' }}
                @elseif(session('user_role') === 'guru')
                    Guru
                @elseif(session('user_role') === 'parent')
                    Wali Murid
                @else
                    {{ session('user_role') ?? 'Administrasi Terpadu' }}
                @endif
            </p>
        </div>
    </div>
    
    <nav class="flex-1 space-y-1">
        @php
            $role = session('user_role');
            $menuItems = [];
            
            if ($role === 'admin' || $role === 'humas') {
                $menuItems = [
                    ['route' => 'monitoring', 'icon' => 'analytics', 'label' => 'Monitoring', 'params' => []],
                    ['route' => 'data-master', 'icon' => 'database', 'label' => 'Data Master', 'params' => []],
                    ['route' => 'admin.logs', 'icon' => 'history', 'label' => 'Log Aktivitas', 'params' => []],
                    ['route' => 'report.export', 'icon' => 'description', 'label' => 'Laporan', 'params' => ['format' => 'pdf']],
                ];
            } elseif ($role === 'guru') {
                $menuItems = [
                    ['route' => 'guru.dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard', 'params' => []],
                    ['route' => 'guru.pilih.sesi', 'icon' => 'edit_note', 'label' => 'Jurnal Mengajar', 'params' => []],
                ];
            } elseif ($role === 'parent') {
                $menuItems = [
                    ['route' => 'dashboard.timeline', 'icon' => 'dashboard', 'label' => 'Timeline', 'params' => []],
                ];
            }
        @endphp
        
        @foreach($menuItems as $item)
            @php
                $isActive = request()->routeIs($item['route']);
            @endphp

            <a href="{{ route($item['route'], $item['params'] ?? []) }}" 
               class="flex items-center gap-3 px-3 py-2.5 transition-all group rounded-lg
                      {{ $isActive ? 'bg-secondary-container text-on-secondary-container font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined group-hover:translate-x-1 duration-200 
                             {{ $isActive ? 'text-on-secondary-container' : '' }}">
                    {{ $item['icon'] }}
                </span>
                <span class="font-label-caps text-label-caps uppercase">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
    
    <div class="mt-auto pt-6 border-t border-outline-variant/30">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-3 py-2 text-error hover:bg-error-container/20 rounded-lg transition-all group w-full">
                <span class="material-symbols-outlined group-hover:translate-x-1 duration-200">logout</span>
                <span class="font-label-caps text-label-caps uppercase">Keluar</span>
            </button>
        </form>
    </div>
</aside>