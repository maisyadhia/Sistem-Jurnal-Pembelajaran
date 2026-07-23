@extends('layouts.app')

@section('title', 'Dashboard Guru - E-Jurnal')

@push('styles')
<style>
    .stat-card {
        transition: all 0.2s ease;
    }
    .stat-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto w-full flex flex-col gap-4 pt-2 pb-6 px-6">
    <!-- Header -->
    <section class="text-center md:text-left">
        <h2 class="font-display-lg text-3xl font-bold text-slate-800 mb-0.5">
            Selamat Datang, {{ session('user_name') }}
        </h2>
        <p class="text-body-base text-slate-500">Ringkasan aktivitas mengajar Anda</p>
    </section>

    <!-- Banner Alerts -->
    @if(session('warning'))
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex gap-3 items-center text-amber-800 animate-fade-in">
            <span class="material-symbols-outlined text-amber-600">warning</span>
            <div class="flex-1 font-body-sm font-medium">
                {{ session('warning') }}
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex gap-3 items-center text-emerald-800 animate-fade-in">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <div class="flex-1 font-body-sm font-medium">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">class</span>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Total Kelas</p>
                    <p class="text-xl font-bold text-slate-800">{{ $totalKelas ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-green-50 text-green-600 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">menu_book</span>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Total Mapel</p>
                    <p class="text-xl font-bold text-slate-800">{{ $totalMapel ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">today</span>
                </div>
                <div>
                    <p class="text-xs text-slate-500">Jurnal Hari Ini</p>
                    <p class="text-xl font-bold text-slate-800">{{ $jurnalHariIni ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="w-full">
        <a href="{{ route('guru.pilih.sesi') }}" 
           class="block bg-white py-4 px-6 rounded-2xl shadow-sm border border-slate-200 hover:border-teal-300 transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center group-hover:bg-teal-100 transition-all">
                    <span class="material-symbols-outlined text-xl">edit_note</span>
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm sm:text-base">Buat Jurnal Baru</p>
                    <p class="text-xs text-slate-500">Isi lembar dokumen jurnal mengajar aktif hari ini</p>
                </div>
                <span class="material-symbols-outlined ml-auto text-slate-400 group-hover:text-teal-600 transition-all">arrow_forward</span>
            </div>
        </a>
    </div>

    <!-- 📊 TABEL RIWAYAT JURNAL -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col gap-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800">Riwayat Jurnal Mengajar</h3>
                <p class="text-[11px] text-slate-400">Daftar rekaman administrasi kelas yang telah diinput.</p>
            </div>
            
            <!-- QUICK FILTERS, CALENDAR & TOMBOL EXCEL  -->
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Tombol Download Excel -->
                <a href="{{ route('guru.jurnal.export', request()->all()) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-bold shadow-sm transition-all">
                    <span class="material-symbols-outlined text-sm">download</span>
                    Excel
                </a>

                <!-- Quick Filter Buttons -->
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200">
                    <a href="{{ route('guru.dashboard', ['filter' => 'hari_ini']) }}" 
                       class="px-3 py-1.5 text-[11px] font-semibold rounded-lg transition-all text-center {{ $currentFilter === 'hari_ini' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                       Hari Ini
                    </a>
                    <a href="{{ route('guru.dashboard', ['filter' => '1_minggu']) }}" 
                       class="px-3 py-1.5 text-[11px] font-semibold rounded-lg transition-all text-center {{ $currentFilter === '1_minggu' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                       1 Minggu
                    </a>
                    <a href="{{ route('guru.dashboard', ['filter' => '1_bulan']) }}" 
                       class="px-3 py-1.5 text-[11px] font-semibold rounded-lg transition-all text-center {{ $currentFilter === '1_bulan' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                       1 Bulan
                    </a>
                </div>

                <!-- Custom Date Picker Form -->
                <form method="GET" action="{{ route('guru.dashboard') }}" id="form-custom-date" class="flex items-center gap-1.5 relative">
                    <input type="date" id="custom-date-picker" name="tanggal" value="{{ request('tanggal') }}" 
                           class="absolute inset-0 opacity-0 w-8 cursor-pointer z-20" onchange="document.getElementById('form-custom-date').submit();">
                    
                    <div class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center bg-white hover:bg-slate-50 transition-colors cursor-pointer relative z-10 {{ $currentFilter === 'custom' ? 'border-slate-800 text-slate-800 bg-slate-50' : 'text-slate-400' }}">
                        <span class="material-symbols-outlined text-lg">calendar_today</span>
                    </div>

                    @if($currentFilter)
                        <a href="{{ route('guru.dashboard') }}" class="bg-slate-100 text-slate-600 px-2.5 py-1.5 text-[11px] font-semibold rounded-lg hover:bg-slate-200 transition-colors">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 text-[10px] font-bold uppercase tracking-wider bg-slate-50/70">
                        <th class="py-2.5 px-3">Waktu Input</th>
                        <th class="py-3 px-3">Kelas</th>
                        <th class="py-3 px-3">Mata Pelajaran</th>
                        <th class="py-3 px-3">Waktu Sesi</th>
                        <th class="py-3 px-3">Bahasan Materi</th>
                        <th class="py-3 px-3">Detail Absensi &amp; Catatan Siswa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs sm:text-sm text-slate-700">
                    @forelse($jurnalTerbaru as $jurnal)
                        <tr class="hover:bg-slate-50/50 transition-colors align-top">
                            <td class="py-2.5 px-3 whitespace-nowrap text-xs text-slate-500">
                                {{ \Carbon\Carbon::parse($jurnal->created_at)->format('d M Y H:i') }}
                            </td>
                            <td class="py-2.5 px-3 font-semibold text-slate-800">
                                {{ $jurnal->nama_kelas }}
                            </td>
                            <td class="py-2.5 px-3 text-teal-700 font-medium">
                                {{ $jurnal->nama_mapel }}
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap text-xs text-slate-600 font-medium">
                                @if(!empty($jurnal->jam_mulai) && !empty($jurnal->jam_selesai))
                                    {{ \Carbon\Carbon::parse($jurnal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jurnal->jam_selesai)->format('H:i') }}
                                @else
                                    Jam Sesi {{ $jurnal->jam_ke }}
                                @endif
                            </td>
                            <td class="py-2.5 px-3 max-w-xs break-words text-xs text-slate-600 leading-relaxed">
                                {{ $jurnal->materi }}
                            </td>
                            
                            <!-- DETAIL RINGKAS + INTIPAN SEKILAS + POP-UP LIHAT LEBIH LANJUT -->
                            <td class="py-2.5 px-3 text-xs">
                                @php
                                    $students = json_decode($jurnal->student_ids, true);
                                    $absenList = [];
                                    $noteList = [];

                                    if (is_array($students)) {
                                        foreach ($students as $s) {
                                            $siswaDb = DB::table('students')->where('id', $s['student_id'])->first();
                                            $namaSiswa = $siswaDb ? $siswaDb->name : 'Siswa ID: ' . $s['student_id'];

                                            if (isset($s['status']) && strtolower($s['status']) !== 'hadir') {
                                                $absenList[] = [
                                                    'nama' => $namaSiswa,
                                                    'status' => $s['status']
                                                ];
                                            }

                                            if (!empty($s['catatan'])) {
                                                $noteList[] = [
                                                    'nama' => $namaSiswa,
                                                    'text' => $s['catatan']
                                                ];
                                            }
                                        }
                                    }

                                    $totalKet = count($absenList) + count($noteList);
                                @endphp

                                <div class="flex flex-col gap-1">
                                    @if($totalKet > 0)
                                        @if(count($absenList) > 0)
                                            <div class="flex items-center gap-1 flex-wrap">
                                                <span class="font-bold text-red-600 text-[10px] uppercase">❌ Absen:</span>
                                                <span class="inline-flex items-center gap-1">
                                                    @php $a1 = $absenList[0]; @endphp
                                                    <span class="text-slate-800 font-semibold text-[11px]">{{ $a1['nama'] }}</span>
                                                    <span class="px-1 py-0.2 rounded text-[9px] font-bold {{ $a1['status'] === 'Alpha' ? 'bg-red-100 text-red-700' : ($a1['status'] === 'Sakit' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                                        {{ $a1['status'] }}
                                                    </span>
                                                </span>
                                                @if(count($absenList) > 1)
                                                    <span class="text-[10px] text-slate-400 font-bold">+{{ count($absenList) - 1 }} siswa</span>
                                                @endif
                                            </div>
                                        @endif

                                        @if(count($noteList) > 0)
                                            <div class="text-[11px] text-slate-600 truncate max-w-[200px]">
                                                <span class="font-bold text-teal-700 text-[10px] uppercase">📝 Catatan:</span>
                                                <span class="italic">"{{ \Illuminate\Support\Str::limit($noteList[0]['text'], 25) }}"</span>
                                            </div>
                                        @endif

                                        <button type="button" 
                                                onclick="openDetailModal({{ json_encode($absenList) }}, {{ json_encode($noteList) }}, '{{ $jurnal->nama_kelas }} - {{ $jurnal->nama_mapel }}')"
                                                class="mt-1 text-teal-700 hover:text-teal-900 font-bold text-[10px] uppercase tracking-wider flex items-center gap-0.5 w-fit">
                                            Lihat Lebih Lanjut
                                            <span class="material-symbols-outlined text-xs">arrow_forward</span>
                                        </button>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-full text-[10px] font-medium border border-emerald-200 w-fit">
                                            <span class="w-1 h-1 rounded-full bg-emerald-500"></span> Hadir Semua
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400 italic text-xs">
                                Tidak ada data jurnal ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notifications -->
    @if(isset($notifications) && $notifications->count() > 0)
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500">notifications_active</span>
            Notifikasi
            <span class="ml-auto text-xs bg-red-500 text-white px-2 py-0.5 rounded-full">{{ $notifications->count() }}</span>
        </h3>
        <div class="divide-y divide-slate-100">
            @foreach($notifications as $notif)
            <div class="py-3 flex items-start gap-3">
                <span class="material-symbols-outlined text-amber-500 text-sm">info</span>
                <div class="flex-1">
                    <p class="text-sm text-slate-700">{{ $notif->message }}</p>
                    <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</p>
                </div>
                <a href="{{ $notif->link ?? '#' }}" class="text-xs text-blue-600 hover:underline">Lihat</a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- MODAL POP-UP DETAIL ABSENSI & CATATAN SISWA -->
<div id="modalDetailJurnal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 flex flex-col gap-4 animate-fade-in">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-teal-600">assignment_ind</span>
                <h4 class="font-bold text-slate-800 text-base" id="modalTitle">Detail Siswa</h4>
            </div>
            <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">
                &times;
            </button>
        </div>

        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
            <!-- Container Absen -->
            <div id="containerAbsenModal" class="space-y-2">
                <p class="font-bold text-red-600 text-xs uppercase tracking-wider flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">person_off</span> Siswa Tidak Hadir:
                </p>
                <div id="listAbsenModal" class="flex flex-col gap-1.5 pl-2"></div>
            </div>

            <!-- Container Catatan -->
            <div id="containerCatatanModal" class="space-y-2 pt-2 border-t border-slate-100">
                <p class="font-bold text-teal-600 text-xs uppercase tracking-wider flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">chat</span> Catatan Khusus Siswa:
                </p>
                <div id="listCatatanModal" class="flex flex-col gap-1.5 pl-2"></div>
            </div>
        </div>

        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closeDetailModal()" class="px-4 py-2 bg-slate-100 text-slate-700 font-semibold rounded-lg text-xs hover:bg-slate-200 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openDetailModal(absenList, noteList, title) {
        document.getElementById('modalTitle').textContent = 'Detail Siswa: ' + title;
        
        const containerAbsen = document.getElementById('containerAbsenModal');
        const listAbsen = document.getElementById('listAbsenModal');
        listAbsen.innerHTML = '';

        if (absenList.length > 0) {
            containerAbsen.classList.remove('hidden');
            absenList.forEach(item => {
                let badgeBg = item.status === 'Alpha' ? 'bg-red-100 text-red-700 border-red-200' : (item.status === 'Sakit' ? 'bg-amber-100 text-amber-700 border-amber-200' : 'bg-blue-100 text-blue-700 border-blue-200');
                
                listAbsen.innerHTML += `
                    <div class="flex items-center justify-between bg-slate-50 p-2 rounded-lg border border-slate-100 text-xs">
                        <span class="font-semibold text-slate-800">${item.nama}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border ${badgeBg}">${item.status}</span>
                    </div>
                `;
            });
        } else {
            containerAbsen.classList.add('hidden');
        }

        const containerCatatan = document.getElementById('containerCatatanModal');
        const listCatatan = document.getElementById('listCatatanModal');
        listCatatan.innerHTML = '';

        if (noteList.length > 0) {
            containerCatatan.classList.remove('hidden');
            noteList.forEach(item => {
                listCatatan.innerHTML += `
                    <div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100 text-xs">
                        <p class="font-bold text-slate-800 mb-0.5">${item.nama}</p>
                        <p class="text-slate-600 italic">"${item.text}"</p>
                    </div>
                `;
            });
        } else {
            containerCatatan.classList.add('hidden');
        }

        document.getElementById('modalDetailJurnal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('modalDetailJurnal').classList.add('hidden');
    }
</script>
@endpush