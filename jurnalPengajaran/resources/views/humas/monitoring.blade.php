@extends('layouts.app')

@section('title', 'Monitoring Console - E-Jurnal')

@push('styles')
<style>
    .sync-pulse {
        animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse-ring {
        0%, 100% { opacity: 1; }
        50% { opacity: .3; }
    }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #c5c5d3; border-radius: 10px; }
    .stat-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<div class="space-y-gutter">
    <!-- Welcome Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="font-display-lg-mobile md:font-display-lg text-display-lg-mobile md:text-display-lg text-on-background mb-1">
                Monitoring Console
            </h2>
            <p class="font-body-base text-body-base text-on-surface-variant">
                Pantau kepatuhan pengisian jurnal dan kelola data master.
            </p>
        </div>
        <div class="flex items-center gap-2 px-3 py-1.5 bg-secondary-container/30 border border-secondary text-secondary rounded-full">
            <span class="w-2 h-2 bg-secondary rounded-full sync-pulse"></span>
            <span class="font-label-caps text-[11px] uppercase">REAL-TIME SYNC ACTIVE</span>
        </div>
    </div>

    <!-- Bento Stats Grid -->
    <div class="grid grid-cols-12 gap-gutter">
        <!-- Compliance Main Card -->
        <div class="col-span-12 lg:col-span-8 bg-surface-container-lowest border border-outline-variant rounded-xl p-6 flex flex-col justify-between relative overflow-hidden group stat-card">
            <div class="absolute top-0 left-0 w-full h-1 bg-secondary"></div>
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-headline-md text-headline-md text-primary">Persentase Kepatuhan Guru</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">Akumulasi pengisian jurnal harian seluruh departemen.</p>
                </div>
                <div class="flex gap-2">
                    <span class="bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full font-label-caps text-[10px]">+{{ $complianceIncrease ?? '2.4' }}% vs Kemarin</span>
                </div>
            </div>
            <div class="flex items-end gap-8 mt-4">
                <div class="flex-1">
                    <div class="flex items-end gap-2 mb-2">
                        <span class="font-display-lg text-display-lg text-primary">{{ $complianceRate ?? '94.2' }}%</span>
                        <span class="font-label-caps text-label-caps text-on-surface-variant mb-2">TARGET: 98%</span>
                    </div>
                    <div class="w-full h-4 bg-surface-container-high rounded-full overflow-hidden">
                        <div class="h-full bg-secondary transition-all duration-1000 ease-out" 
                             style="width: {{ $complianceRate ?? 94.2 }}%"></div>
                    </div>
                </div>
                <div class="hidden md:flex gap-4">
                    <div class="text-center">
                        <p class="font-label-caps text-[10px] text-on-surface-variant">TEPAT WAKTU</p>
                        <p class="font-headline-md text-headline-md text-secondary">{{ $onTimeCount ?? 112 }}</p>
                    </div>
                    <div class="text-center">
                        <p class="font-label-caps text-[10px] text-on-surface-variant">TERLAMBAT</p>
                        <p class="font-headline-md text-headline-md text-on-tertiary-container">{{ $lateCount ?? 8 }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Action Card -->
        <div class="col-span-12 lg:col-span-4 bg-primary text-on-primary rounded-xl p-6 flex flex-col justify-between group cursor-pointer hover:bg-primary-container transition-all stat-card" onclick="window.location.href='{{ route('report.export') }}'">
            <div class="flex justify-between">
                <span class="material-symbols-outlined text-4xl">rocket_launch</span>
                <span class="material-symbols-outlined opacity-50 group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </div>
            <div>
                <h4 class="font-headline-md text-headline-md mb-1">Generate Report</h4>
                <p class="font-body-sm text-body-sm opacity-80">Export data kepatuhan mingguan dalam format PDF/Excel.</p>
            </div>
        </div>
    </div>
    
    <!-- Alert Section & Management -->
    <div class="grid grid-cols-12 gap-gutter">
        <!-- Kelas Tanpa Catatan -->
        <div class="col-span-12 lg:col-span-5 bg-surface-container-lowest border border-outline-variant rounded-xl flex flex-col">
            <div class="p-6 border-b border-outline-variant flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-error" style="font-variation-settings: 'FILL' 1;">warning</span>
                    <h3 class="font-headline-md text-headline-md">Kelas Tanpa Catatan</h3>
                </div>
                <span class="bg-error-container text-on-error-container px-2 py-0.5 rounded font-label-caps text-[10px]">{{ $unreportedClasses->count() }} KELAS</span>
            </div>
            <div class="p-0 flex-grow overflow-y-auto max-h-[400px] custom-scrollbar">
                <div class="divide-y divide-outline-variant">
                    @forelse($unreportedClasses as $class)
                        <div class="p-4 hover:bg-error-container/5 transition-colors flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-error/10 flex items-center justify-center text-error">
                                    <span class="font-bold text-body-base">{{ $class->code }}</span>
                                </div>
                                <div>
                                    <p class="font-label-caps text-label-caps font-bold">{{ $class->subject }}</p>
                                    <p class="font-body-sm text-[12px] text-on-surface-variant">{{ $class->teacher }} • {{ $class->schedule }}</p>
                                </div>
                            </div>
                            <button class="text-error hover:bg-error-container px-3 py-1 rounded font-label-caps text-[10px] border border-error remind-teacher" data-teacher="{{ $class->teacher }}" data-class="{{ $class->code }}">
                                INGATKAN
                            </button>
                        </div>
                    @empty
                        <div class="p-6 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl text-secondary">check_circle</span>
                            <p class="mt-2">Semua kelas sudah melaporkan jurnal hari ini!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Data Master -->
        <div class="col-span-12 lg:col-span-7 bg-surface-container-lowest border border-outline-variant rounded-xl flex flex-col">
            <div class="p-6 border-b border-outline-variant flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">database</span>
                    <h3 class="font-headline-md text-headline-md">Data Master Terkini</h3>
                </div>
                <div class="flex gap-2">
                    <button onclick="window.location.href='{{ route('data-master.create') }}'" 
                            class="bg-primary text-on-primary px-4 py-1.5 rounded-lg font-label-caps text-[11px] flex items-center gap-2 hover:opacity-90 transition-opacity">
                        <span class="material-symbols-outlined text-sm">add</span> TAMBAH DATA
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-surface-container-low border-b border-outline-variant">
                        <tr>
                            <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant">IDENTITAS</th>
                            <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant">KATEGORI</th>
                            <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant">STATUS</th>
                            <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant text-right">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant">
                        @forelse($dataMaster as $item)
                            <tr class="hover:bg-surface-container-low transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full {{ $item->color }} flex items-center justify-center text-white font-bold text-[10px]">
                                            {{ $item->initials }}
                                        </div>
                                        <div>
                                            <p class="font-data-tabular text-data-tabular">{{ $item->name }}</p>
                                            <p class="text-[11px] text-on-surface-variant">{{ $item->identifier }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-label-caps text-[10px] text-on-surface-variant px-2 py-1 bg-surface-container-high rounded">{{ $item->category }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full {{ $item->statusColor }}"></span>
                                        <span class="font-body-sm text-body-sm">{{ $item->status }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick="window.location.href='{{ route('data-master.edit', $item->id) }}'" 
                                                class="p-1.5 text-on-surface-variant hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </button>
                                        <button onclick="confirmDelete({{ $item->id }})" 
                                                class="p-1.5 text-on-surface-variant hover:text-error transition-colors">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-on-surface-variant">
                                    <span class="material-symbols-outlined text-3xl block mx-auto mb-2">inbox</span>
                                    Belum ada data master.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-surface-container-low border-t border-outline-variant flex justify-between items-center mt-auto">
                <p class="font-body-sm text-[12px] text-on-surface-variant">Menampilkan {{ $dataMaster->count() }} dari {{ $totalDataMaster }} entitas</p>
                <div class="flex gap-2">
                    <button class="p-1 border border-outline-variant rounded hover:bg-surface-container-high transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_left</span>
                    </button>
                    <button class="p-1 border border-outline-variant rounded hover:bg-surface-container-high transition-colors">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FAB -->
<button class="fixed bottom-8 right-8 w-14 h-14 bg-secondary text-on-secondary rounded-full shadow-lg flex items-center justify-center hover:scale-105 active:scale-95 transition-all z-50 group" onclick="window.location.href='{{ route('jurnal') }}'">
    <span class="material-symbols-outlined text-2xl group-hover:rotate-90 transition-transform duration-300">add</span>
    <span class="absolute right-16 bg-on-background text-white px-3 py-1.5 rounded-lg text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Input Jurnal Cepat</span>
</button>
@endsection

@push('scripts')
<script>
    // Sync indicator animation
    setInterval(() => {
        const syncIcon = document.querySelector('.sync-pulse');
        if (syncIcon) {
            syncIcon.classList.toggle('bg-secondary');
            syncIcon.classList.toggle('bg-secondary-fixed');
        }
    }, 3000);
    
    // Remind teacher functionality
    document.querySelectorAll('.remind-teacher').forEach(btn => {
        btn.addEventListener('click', function() {
            const teacher = this.dataset.teacher;
            const classCode = this.dataset.class;
            if (confirm(`Kirim pengingat ke ${teacher} untuk kelas ${classCode}?`)) {
                const originalText = this.textContent;
                this.textContent = 'TERKIRIM';
                this.classList.remove('text-error', 'border-error');
                this.classList.add('text-secondary', 'border-secondary');
                this.disabled = true;
                
                // Simulate API call
                fetch('{{ route('remind-teacher') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        teacher: teacher,
                        class: classCode
                    })
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          setTimeout(() => {
                              this.textContent = originalText;
                              this.classList.remove('text-secondary', 'border-secondary');
                              this.classList.add('text-error', 'border-error');
                              this.disabled = false;
                          }, 3000);
                      }
                  }).catch(() => {
                      this.textContent = originalText;
                      this.classList.remove('text-secondary', 'border-secondary');
                      this.classList.add('text-error', 'border-error');
                      this.disabled = false;
                  });
            }
        });
    });

    // Delete confirmation
    <!-- Bagian Delete Button di tabel -->
    <td class="px-6 py-4 text-right">
        <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <a href="{{ route('data-master.edit', $item->id) }}" 
            class="p-1.5 text-on-surface-variant hover:text-primary transition-colors">
                <span class="material-symbols-outlined text-lg">edit</span>
            </a>
            <form method="POST" action="{{ route('data-master.destroy', $item->id) }}" 
                class="inline-block" 
                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-1.5 text-on-surface-variant hover:text-error transition-colors">
                    <span class="material-symbols-outlined text-lg">delete</span>
                </button>
            </form>
        </div>
    </td>
</script>
@endpush