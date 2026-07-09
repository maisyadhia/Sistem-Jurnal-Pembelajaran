<aside class="hidden md:flex flex-col h-full py-6 px-4 w-64 bg-surface-container-low border-r border-outline-variant">
    <div class="mb-10 px-2 flex items-center gap-3">
        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
            <span class="material-symbols-outlined text-white" style="font-variation-settings: 'FILL' 1;">school</span>
        </div>
        <div>
            <h1 class="font-headline-md text-headline-md text-primary leading-tight">E-Jurnal</h1>
            <p class="text-[10px] font-label-caps uppercase tracking-widest text-on-surface-variant opacity-70">
                {{ session('admin_role') ?? session('user_role') ?? 'Administrasi Terpadu' }}
            </p>
        </div>
    </div>
    
    <nav class="flex-1 space-y-1">
        @php
            $role = session('user_role');
            $menuItems = [];
            
            if ($role === 'admin' || $role === 'humas') {
                $menuItems = [
                    ['route' => 'monitoring', 'icon' => 'analytics', 'label' => 'Monitoring'],
                    ['route' => 'data-master', 'icon' => 'database', 'label' => 'Data Master'],
                    ['route' => 'report.export', 'icon' => 'description', 'label' => 'Laporan'],
                ];
            } elseif ($role === 'guru') {
                $menuItems = [
                    ['route' => 'jurnal', 'icon' => 'edit_note', 'label' => 'Jurnal Mengajar'],
                    ['route' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Dashboard'],
                ];
            } elseif ($role === 'parent') {
                $menuItems = [
                    ['route' => 'dashboard.timeline', 'icon' => 'dashboard', 'label' => 'Timeline'],
                ];
            }
        @endphp
        
        @foreach($menuItems as $item)
            <a href="{{ route($item['route']) }}" 
               class="flex items-center gap-3 px-3 py-2.5 transition-all group rounded-lg
                      {{ request()->routeIs($item['route']) ? 'bg-secondary-container text-on-secondary-container font-bold' : 'text-on-surface-variant hover:bg-surface-container-high' }}">
                <span class="material-symbols-outlined group-hover:translate-x-1 duration-200 
                             {{ request()->routeIs($item['route']) ? 'text-on-secondary-container' : '' }}">
                    {{ $item['icon'] }}
                </span>
                <span class="font-label-caps text-label-caps uppercase">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>
    
    <div class="mt-auto space-y-4 pt-6 border-t border-outline-variant/30">
        <button class="w-full bg-surface-container-highest text-primary font-bold py-2 px-4 rounded-lg text-sm border border-outline-variant/50 hover:bg-primary hover:text-white transition-colors">
            Bantuan Teknis
        </button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 px-3 py-2 text-error hover:bg-error-container/20 rounded-lg transition-all group w-full">
                <span class="material-symbols-outlined group-hover:translate-x-1 duration-200">logout</span>
                <span class="font-label-caps text-label-caps uppercase">Keluar</span>
            </button>
        </form>
    </div>
</aside>