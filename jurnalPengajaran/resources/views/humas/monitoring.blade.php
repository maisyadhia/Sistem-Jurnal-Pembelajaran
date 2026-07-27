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
    /* Modal animation */
    .modal-overlay {
        background: rgba(0,0,0,0.5);
        backdrop-filter: blur(4px);
    }
    .modal-content {
        animation: modalFadeIn 0.3s ease-out forwards;
    }
    @keyframes modalFadeIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }

    /* Notifikasi di atas card */
    .notification-item {
        animation: slideDown 0.3s ease-out forwards;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .notification-item.hide {
        animation: slideUp 0.3s ease-in forwards;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideUp {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-10px);
        }
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
        
        <!-- Quick Action Card - Generate Report Excel -->
        <div class="col-span-12 lg:col-span-4 bg-primary text-on-primary rounded-xl p-6 flex flex-col justify-between group cursor-pointer hover:bg-primary-container transition-all stat-card" 
            onclick="window.location.href='{{ route('report.export') }}'">
            <div class="flex justify-between">
                <span class="material-symbols-outlined text-4xl">description</span>
                <span class="material-symbols-outlined opacity-50 group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </div>
            <div>
                <h4 class="font-headline-md text-headline-md mb-1">Generate Report</h4>
                <p class="font-body-sm text-body-sm opacity-80">Download laporan kepatuhan guru dalam format Excel.</p>
            </div>
        </div>
    </div>
    
    <!-- Alert Section & Management -->
    <div class="grid grid-cols-12 gap-gutter">
        <!-- KOLOM KIRI: NOTIFIKASI + KELAS TANPA CATATAN -->
        <div class="col-span-12 lg:col-span-5 flex flex-col gap-4">
            <!-- TEMPAT NOTIFIKASI -->
            <div id="notificationWrapper"></div>
            
            <!-- Kelas Tanpa Catatan -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl flex flex-col">
                <div class="p-6 border-b border-outline-variant flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-error" style="font-variation-settings: 'FILL' 1;">warning</span>
                        <h3 class="font-headline-md text-headline-md">Kelas Tanpa Catatan</h3>
                    </div>
                    <span class="bg-error-container text-on-error-container px-2 py-0.5 rounded font-label-caps text-[10px]" id="unreportedCount">{{ $unreportedClasses->count() }} KELAS</span>
                </div>
                <div class="p-0 flex-grow overflow-y-auto max-h-[400px] custom-scrollbar" id="unreportedList">
                    <div class="divide-y divide-outline-variant">
                        @forelse($unreportedClasses as $class)
                            <div class="p-4 hover:bg-error-container/5 transition-colors flex items-center justify-between unreported-item" data-id="{{ $class->id ?? $loop->index }}">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-error/10 flex items-center justify-center text-error">
                                        <span class="font-bold text-body-base">{{ $class->code }}</span>
                                    </div>
                                    <div>
                                        <p class="font-label-caps text-label-caps font-bold">{{ $class->subject }}</p>
                                        <p class="font-body-sm text-[12px] text-on-surface-variant">{{ $class->teacher }} • {{ $class->schedule }}</p>
                                    </div>
                                </div>
                                <button class="remind-btn text-error hover:bg-error-container px-3 py-1 rounded font-label-caps text-[10px] border border-error transition-all" 
                                        data-teacher="{{ $class->teacher }}"
                                        data-class="{{ $class->code }}"
                                        data-subject="{{ $class->subject }}">
                                    INGATKAN
                                </button>
                            </div>
                        @empty
                            <div class="p-6 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-4xl text-secondary">check_circle</span>
                                <p class="mt-2">Semua guru sudah mengisi jurnal hari ini! 🎉</p>
                            </div>
                        @endforelse
                    </div>
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
                    <!-- Dropdown untuk pilih tambah data -->
                    <div class="relative" id="dropdownContainer">
                        <button onclick="toggleDropdown()" 
                                class="bg-primary text-on-primary px-4 py-1.5 rounded-lg font-label-caps text-[11px] flex items-center gap-2 hover:opacity-90 transition-opacity">
                            <span class="material-symbols-outlined text-sm">add</span> TAMBAH DATA
                            <span class="material-symbols-outlined text-sm" id="dropdownIcon">expand_more</span>
                        </button>
                        <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-outline-variant overflow-hidden z-20">
                            <a href="{{ route('data-master.guru.create') }}" 
                            class="block px-4 py-2 text-sm hover:bg-surface-container-low transition-colors">
                                <span class="material-symbols-outlined text-sm mr-2 align-middle">school</span> Tambah Guru
                            </a>
                            <a href="{{ route('data-master.siswa.create') }}" 
                            class="block px-4 py-2 text-sm hover:bg-surface-container-low transition-colors">
                                <span class="material-symbols-outlined text-sm mr-2 align-middle">groups</span> Tambah Siswa
                            </a>
                            <a href="{{ route('data-master.kelas.create') }}" 
                            class="block px-4 py-2 text-sm hover:bg-surface-container-low transition-colors">
                                <span class="material-symbols-outlined text-sm mr-2 align-middle">class</span> Tambah Kelas
                            </a>
                            <a href="{{ route('data-master.mapel.create') }}" 
                            class="block px-4 py-2 text-sm hover:bg-surface-container-low transition-colors">
                                <span class="material-symbols-outlined text-sm mr-2 align-middle">menu_book</span> Tambah Mapel
                            </a>
                            <a href="{{ route('data-master.jadwal.create') }}" 
                            class="block px-4 py-2 text-sm hover:bg-surface-container-low transition-colors">
                                <span class="material-symbols-outlined text-sm mr-2 align-middle">schedule</span> Tambah Jadwal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tabel Data Master -->
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
                            <tr class="hover:bg-surface-container-low transition-colors group" data-id="{{ $item->id }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full {{ $item->color ?? 'bg-secondary-container' }} flex items-center justify-center text-white font-bold text-[10px]">
                                            {{ $item->initials ?? '??' }}
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
                                        <span class="w-2 h-2 rounded-full {{ $item->statusColor ?? 'bg-secondary' }}"></span>
                                        <span class="font-body-sm text-body-sm">{{ $item->status }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="#" 
                                        class="p-1.5 text-on-surface-variant hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </a>
                                        <button onclick="confirmDelete('{{ $item->id }}', '{{ $item->name }}', '{{ $item->category }}')" 
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
            </div>
        </div>
    </div>
</div>

<!-- ============ MODAL POPUP KONFIRMASI REMIND ============ -->
<div id="remindModal" class="fixed inset-0 z-50 hidden modal-overlay flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl modal-content">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-amber-600 text-2xl">notifications_active</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800">Konfirmasi Pengingat</h3>
                <p class="text-sm text-slate-500">Kirim pengingat ke guru?</p>
            </div>
        </div>
        
        <div class="bg-slate-50 rounded-xl p-4 mb-6 space-y-2">
            <p class="text-sm text-slate-700">
                <span class="font-semibold">Guru:</span> 
                <span id="remindTeacherName" class="text-slate-900">-</span>
            </p>
            <p class="text-sm text-slate-700">
                <span class="font-semibold">Kelas:</span> 
                <span id="remindClassName" class="text-slate-900">-</span>
            </p>
            <p class="text-sm text-slate-700">
                <span class="font-semibold">Mata Pelajaran:</span> 
                <span id="remindSubjectName" class="text-slate-900">-</span>
            </p>
        </div>
        
        <div class="flex gap-3 justify-end">
            <button onclick="closeRemindModal()" 
                    class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-all text-sm font-medium">
                Batal
            </button>
            <button id="confirmRemindBtn" 
                    class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white transition-all text-sm font-medium flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">send</span>
                Kirim Pengingat
            </button>
        </div>
    </div>
</div>

<!-- FAB - Hanya untuk role guru -->
@if(session('user_role') === 'guru')
<button class="fixed bottom-8 right-8 w-14 h-14 bg-secondary text-on-secondary rounded-full shadow-lg flex items-center justify-center hover:scale-105 active:scale-95 transition-all z-50 group" 
        onclick="window.location.href='{{ route('guru.pilih.sesi') }}'">
    <span class="material-symbols-outlined text-2xl group-hover:rotate-90 transition-transform duration-300">add</span>
    <span class="absolute right-16 bg-on-background text-white px-3 py-1.5 rounded-lg text-xs font-bold opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">Input Jurnal Cepat</span>
</button>
@endif
@endsection

@push('scripts')
<script>
    // ============ VARIABLES ============
    let currentRemindData = null;
    let notificationTimeout = null;

    // ============ DROPDOWN TOGGLE ============
    function toggleDropdown() {
        const menu = document.getElementById('dropdownMenu');
        const icon = document.getElementById('dropdownIcon');
        
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            icon.textContent = 'expand_less';
        } else {
            menu.classList.add('hidden');
            icon.textContent = 'expand_more';
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const container = document.getElementById('dropdownContainer');
        if (container && !container.contains(event.target)) {
            const menu = document.getElementById('dropdownMenu');
            const icon = document.getElementById('dropdownIcon');
            if (menu) {
                menu.classList.add('hidden');
                if (icon) icon.textContent = 'expand_more';
            }
        }
    });

    // ============ NOTIFICATION ============
    function showNotification(message, type = 'success') {
        const wrapper = document.getElementById('notificationWrapper');
        
        // Hapus notifikasi lama
        const oldNotif = wrapper.querySelector('.notification-item');
        if (oldNotif) {
            oldNotif.remove();
        }
        
        // Clear timeout lama
        if (notificationTimeout) {
            clearTimeout(notificationTimeout);
            notificationTimeout = null;
        }
        
        // Buat elemen notifikasi
        const notif = document.createElement('div');
        notif.className = `notification-item p-4 rounded-xl border flex items-center gap-3`;
        
        if (type === 'success') {
            notif.className += ' bg-emerald-50 border-emerald-200';
            notif.innerHTML = `
                <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                <span class="flex-1 text-sm font-medium text-emerald-800">${message}</span>
                <button onclick="closeNotification(this)" class="text-emerald-400 hover:text-emerald-600 transition-colors">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            `;
        } else {
            notif.className += ' bg-red-50 border-red-200';
            notif.innerHTML = `
                <span class="material-symbols-outlined text-red-600">error</span>
                <span class="flex-1 text-sm font-medium text-red-800">${message}</span>
                <button onclick="closeNotification(this)" class="text-red-400 hover:text-red-600 transition-colors">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            `;
        }
        
        wrapper.appendChild(notif);
        
        // Auto hilang setelah 5 detik
        notificationTimeout = setTimeout(() => {
            closeNotification(notif);
        }, 5000);
    }

    function closeNotification(element) {
        // Jika element adalah button, cari parent notif
        if (element.tagName === 'BUTTON') {
            element = element.closest('.notification-item');
        }
        
        if (element && element.parentNode) {
            if (notificationTimeout) {
                clearTimeout(notificationTimeout);
                notificationTimeout = null;
            }
            
            element.classList.add('hide');
            setTimeout(() => {
                if (element.parentNode) {
                    element.remove();
                }
            }, 350);
        }
    }

    // ============ REMIND TEACHER WITH MODAL ============
    function openRemindModal(teacher, classCode, subject, btnElement) {
        currentRemindData = {
            teacher: teacher,
            class: classCode,
            subject: subject || 'Mata Pelajaran',
            btn: btnElement
        };
        
        document.getElementById('remindTeacherName').textContent = teacher;
        document.getElementById('remindClassName').textContent = classCode;
        document.getElementById('remindSubjectName').textContent = subject || 'Mata Pelajaran';
        document.getElementById('remindModal').classList.remove('hidden');
    }

    function closeRemindModal() {
        document.getElementById('remindModal').classList.add('hidden');
        currentRemindData = null;
    }

    // Confirm remind button
    document.getElementById('confirmRemindBtn').addEventListener('click', function() {
        if (!currentRemindData) return;
        
        const { teacher, class: classCode, subject, btn } = currentRemindData;
        const card = btn ? btn.closest('.unreported-item') : null;
        
        // Disable button
        this.disabled = true;
        this.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Mengirim...';
        
        fetch('{{ route('remind-teacher') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                teacher: teacher,
                class: classCode,
                subject: subject
            })
        })
        .then(response => response.json())
        .then(data => {
            closeRemindModal();
            
            if (data.success) {
                // HAPUS CARD dengan animasi
                if (card) {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '0';
                    card.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        card.remove();
                        updateUnreportedCounter();
                    }, 500);
                }
                
                showNotification('✅ Pengingat berhasil dikirim ke ' + teacher, 'success');
            } else {
                showNotification('❌ ' + (data.message || 'Gagal mengirim pengingat'), 'error');
            }
        })
        .catch(error => {
            closeRemindModal();
            showNotification('❌ Terjadi kesalahan. Silakan coba lagi.', 'error');
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<span class="material-symbols-outlined text-sm">send</span> Kirim Pengingat';
        });
    });

    // ============ REMIND BUTTONS ============
    document.querySelectorAll('.remind-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const teacher = this.dataset.teacher;
            const classCode = this.dataset.class;
            const subject = this.dataset.subject || 'Mata Pelajaran';
            openRemindModal(teacher, classCode, subject, this);
        });
    });

    // ============ UPDATE COUNTER ============
    function updateUnreportedCounter() {
        const remaining = document.querySelectorAll('.unreported-item').length;
        const counter = document.getElementById('unreportedCount');
        const list = document.getElementById('unreportedList');
        
        if (counter) {
            counter.textContent = remaining + ' KELAS';
            if (remaining === 0) {
                list.innerHTML = `
                    <div class="p-6 text-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-4xl text-secondary">check_circle</span>
                        <p class="mt-2">Semua guru sudah mengisi jurnal hari ini! 🎉</p>
                    </div>
                `;
            }
        }
    }

    // ============ CONFIRM DELETE ============
    function confirmDelete(id, name, category) {
        if (confirm(`Apakah Anda yakin ingin menghapus data "${name}" (${category})?`)) {
            let route = '';
            
            const categoryUpper = category.toUpperCase();
            if (categoryUpper.includes('GURU')) {
                route = '/admin/data-master/guru/' + id;
            } else if (categoryUpper.includes('KELAS') || categoryUpper.includes('INFRASTRUKTUR')) {
                route = '/admin/data-master/kelas/' + id;
            } else if (categoryUpper.includes('MATA PELAJARAN') || categoryUpper.includes('MAPEL')) {
                route = '/admin/data-master/mapel/' + id;
            } else {
                route = '/admin/data-master/guru/' + id;
            }
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = route;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    // ============ SYNC INDICATOR ============
    setInterval(() => {
        const syncIcon = document.querySelector('.sync-pulse');
        if (syncIcon) {
            syncIcon.classList.toggle('bg-secondary');
            syncIcon.classList.toggle('bg-secondary-fixed');
        }
    }, 3000);

    // ============ CLOSE MODAL ON ESC ============
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeRemindModal();
            // Tutup notifikasi juga
            const notif = document.querySelector('.notification-item');
            if (notif) closeNotification(notif);
        }
    });

    // ============ CLOSE MODAL ON OVERLAY CLICK ============
    document.getElementById('remindModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeRemindModal();
        }
    });
</script>
@endpush