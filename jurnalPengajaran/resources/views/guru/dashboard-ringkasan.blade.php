@extends('layouts.app')

@section('title', 'Dashboard Guru - SIMJAR')

@push('styles')
<style>
    .stat-card {
        transition: all 0.2s ease;
    }
    .stat-card:hover {
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
        transform: translateY(-2px);
    }
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto w-full flex flex-col gap-4 pt-2 pb-6 px-3 md:px-6">
    <!-- Header -->
    <section class="text-center md:text-left pt-1 md:pt-0">
        <h2 class="text-xl md:text-3xl font-bold text-slate-800 mb-0.5">
            Selamat Datang, {{ session('user_name') }}
        </h2>
        <p class="text-xs md:text-body-base text-slate-500">Ringkasan aktivitas mengajar Anda</p>
    </section>

    <!-- Banner Alerts -->
    @if(session('warning'))
        <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl flex gap-3 items-center text-amber-800 animate-fade-in">
            <span class="material-symbols-outlined text-amber-600 shrink-0">warning</span>
            <div class="flex-1 text-xs md:text-sm font-medium">
                {{ session('warning') }}
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl flex gap-3 items-center text-emerald-800 animate-fade-in">
            <span class="material-symbols-outlined text-emerald-600 shrink-0">check_circle</span>
            <div class="flex-1 text-xs md:text-sm font-medium">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
        <div class="stat-card bg-white p-3.5 md:p-4 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 md:w-11 md:h-11 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-xl">class</span>
                </div>
                <div>
                    <p class="text-[11px] md:text-xs text-slate-500 font-medium">Total Kelas</p>
                    <p class="text-lg md:text-xl font-bold text-slate-800">{{ $totalKelas ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white p-3.5 md:p-4 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 md:w-11 md:h-11 bg-green-50 text-green-600 rounded-xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-xl">menu_book</span>
                </div>
                <div>
                    <p class="text-[11px] md:text-xs text-slate-500 font-medium">Total Mapel</p>
                    <p class="text-lg md:text-xl font-bold text-slate-800">{{ $totalMapel ?? 0 }}</p>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white p-3.5 md:p-4 rounded-2xl shadow-sm border border-slate-200/80">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 md:w-11 md:h-11 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-xl">today</span>
                </div>
                <div>
                    <p class="text-[11px] md:text-xs text-slate-500 font-medium">Jurnal Hari Ini</p>
                    <p class="text-lg md:text-xl font-bold text-slate-800">{{ $jurnalHariIni ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Card -->
    <div class="w-full">
        <a href="{{ route('guru.pilih.sesi') }}" 
           class="block bg-white p-4 md:px-6 md:py-4 rounded-2xl shadow-sm border border-slate-200/80 hover:border-teal-300 transition-all group">
            <div class="flex items-center gap-3 md:gap-4">
                <div class="w-11 h-11 md:w-12 md:h-12 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center group-hover:bg-teal-100 transition-all shrink-0">
                    <span class="material-symbols-outlined text-xl md:text-2xl">edit_note</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 text-sm md:text-base leading-snug">Buat Jurnal Baru</p>
                    <p class="text-[11px] md:text-xs text-slate-500 truncate">Isi lembar dokumen jurnal mengajar aktif hari ini</p>
                </div>
                <span class="material-symbols-outlined text-slate-400 group-hover:text-teal-600 transition-all shrink-0">arrow_forward</span>
            </div>
        </a>
    </div>

    <!-- TABEL RIWAYAT JURNAL -->
    <div class="bg-white p-4 md:p-5 rounded-2xl shadow-sm border border-slate-200/80 flex flex-col gap-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800">Riwayat Jurnal Mengajar</h3>
                <p class="text-[11px] text-slate-400">Daftar rekaman administrasi kelas yang telah diinput.</p>
            </div>
            
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 sm:pb-0">
                <a href="{{ route('guru.jurnal.export', request()->all()) }}" 
                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-bold shadow-sm transition-all shrink-0">
                    <span class="material-symbols-outlined text-sm">download</span>
                    Excel
                </a>

                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200 shrink-0">
                    <a href="{{ route('guru.dashboard', ['filter' => 'hari_ini']) }}" 
                       class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition-all text-center whitespace-nowrap {{ $currentFilter === 'hari_ini' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                       Hari Ini
                    </a>
                    <a href="{{ route('guru.dashboard', ['filter' => '1_minggu']) }}" 
                       class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition-all text-center whitespace-nowrap {{ $currentFilter === '1_minggu' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                       1 Minggu
                    </a>
                    <a href="{{ route('guru.dashboard', ['filter' => '1_bulan']) }}" 
                       class="px-2.5 py-1 text-[11px] font-semibold rounded-lg transition-all text-center whitespace-nowrap {{ $currentFilter === '1_bulan' ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                       1 Bulan
                    </a>
                </div>

                <form method="GET" action="{{ route('guru.dashboard') }}" id="form-custom-date" class="flex items-center gap-1 relative shrink-0">
                    <input type="date" id="custom-date-picker" name="tanggal" value="{{ request('tanggal') }}" 
                           class="absolute inset-0 opacity-0 w-8 cursor-pointer z-20" onchange="document.getElementById('form-custom-date').submit();">
                    
                    <div class="w-7 h-7 md:w-8 md:h-8 rounded-lg border border-slate-200 flex items-center justify-center bg-white hover:bg-slate-50 transition-colors cursor-pointer relative z-10 {{ $currentFilter === 'custom' ? 'border-slate-800 text-slate-800 bg-slate-50' : 'text-slate-400' }}">
                        <span class="material-symbols-outlined text-base md:text-lg">calendar_today</span>
                    </div>

                    @if($currentFilter)
                        <a href="{{ route('guru.dashboard') }}" class="bg-slate-100 text-slate-600 px-2 py-1 text-[10px] font-semibold rounded-lg hover:bg-slate-200 transition-colors">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="overflow-x-auto no-scrollbar -mx-4 px-4 sm:mx-0 sm:px-0">
            <table class="w-full text-left border-collapse min-w-[600px] md:min-w-full">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 text-[10px] font-bold uppercase tracking-wider bg-slate-50/70">
                        <th class="py-2.5 px-3">Waktu Input</th>
                        <th class="py-2.5 px-3">Kelas</th>
                        <th class="py-2.5 px-3">Mata Pelajaran</th>
                        <th class="py-2.5 px-3">Sesi</th>
                        <th class="py-2.5 px-3">Bahasan Materi & Target</th>
                        <th class="py-2.5 px-3">Absensi & Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs md:text-sm text-slate-700">
                    @forelse($jurnalTerbaru as $jurnal)
                        <tr class="hover:bg-slate-50/50 transition-colors align-top">
                            <td class="py-2.5 px-3 whitespace-nowrap text-[11px] text-slate-500">
                                {{ \Carbon\Carbon::parse($jurnal->created_at)->format('d M Y H:i') }}
                            </td>
                            <td class="py-2.5 px-3 font-semibold text-slate-800 whitespace-nowrap">
                                {{ $jurnal->nama_kelas }}
                            </td>
                            <td class="py-2.5 px-3 text-teal-700 font-medium whitespace-nowrap">
                                {{ $jurnal->nama_mapel }}
                            </td>
                            
                            <td class="py-2.5 px-3 whitespace-nowrap text-[11px] text-slate-700 font-semibold">
                                @if(!empty($jurnal->waktu_sesi_list) && count($jurnal->waktu_sesi_list) > 0)
                                    <div class="flex flex-col gap-0.5">
                                        @foreach($jurnal->waktu_sesi_list as $wSesi)
                                            <span>{{ $wSesi }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span>Jam ke-{{ $jurnal->jam_ke }}</span>
                                @endif
                                
                                @if(!empty($jurnal->jam_ke_label))
                                    <span class="text-[10px] text-slate-400 font-medium block mt-0.5">{{ $jurnal->jam_ke_label }}</span>
                                @endif
                            </td>

                            <td class="py-2.5 px-3 max-w-[200px] md:max-w-xs break-words text-xs leading-relaxed">
                                <div>
                                    <span class="font-bold text-slate-800">Materi:</span>
                                    <p class="text-slate-600 whitespace-pre-line">{{ $jurnal->materi ?? '-' }}</p>
                                </div>

                                @if(!empty($jurnal->target_next))
                                    <div class="mt-1.5 pt-1.5 border-t border-slate-100">
                                        <span class="font-bold text-teal-700 text-[10px] uppercase tracking-wider">Target Berikutnya:</span>
                                        <p class="text-slate-500 italic text-[11px] whitespace-pre-line">{{ $jurnal->target_next }}</p>
                                    </div>
                                @endif
                            </td>
                            
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
                                                <span class="font-bold text-red-600 text-[10px] uppercase">Absen:</span>
                                                <span class="inline-flex items-center gap-1">
                                                    @php $a1 = $absenList[0]; @endphp
                                                    <span class="text-slate-800 font-semibold text-[11px]">{{ $a1['nama'] }}</span>
                                                    <span class="px-1 py-0.2 rounded text-[9px] font-bold {{ $a1['status'] === 'Alpha' ? 'bg-red-100 text-red-700' : ($a1['status'] === 'Sakit' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                                        {{ $a1['status'] }}
                                                    </span>
                                                </span>
                                                @if(count($absenList) > 1)
                                                    <span class="text-[10px] text-slate-400 font-bold">+{{ count($absenList) - 1 }}</span>
                                                @endif
                                            </div>
                                        @endif

                                        @if(count($noteList) > 0)
                                            <div class="text-[11px] text-slate-600 truncate max-w-[150px]">
                                                <span class="font-bold text-teal-700 text-[10px] uppercase">Catatan:</span>
                                                <span class="italic">"{{ \Illuminate\Support\Str::limit($noteList[0]['text'], 20) }}"</span>
                                            </div>
                                        @endif

                                        <button type="button" 
                                                data-absen='@json($absenList)'
                                                data-catatan='@json($noteList)'
                                                data-title="{{ $jurnal->nama_kelas }} - {{ $jurnal->nama_mapel }}"
                                                onclick="openDetailModalSafe(this)"
                                                class="mt-0.5 text-teal-700 hover:text-teal-900 font-bold text-[10px] uppercase tracking-wider flex items-center gap-0.5 w-fit cursor-pointer">
                                            Detail
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

    <!-- Card Notifikasi Bawah -->
    @if(isset($notifications) && $notifications->count() > 0)
    <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-slate-200/80">
        <h3 class="text-base md:text-lg font-bold text-slate-800 mb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-amber-500">notifications_active</span>
            Notifikasi
            <span class="ml-auto text-xs bg-red-500 text-white px-2 py-0.5 rounded-full font-bold">{{ $notifications->count() }}</span>
        </h3>
        <div class="divide-y divide-slate-100">
            @foreach($notifications as $notif)
            @php
                // 💡 LOGIKA DUA PILAR UNTUK DASHBOARD BAWAH DENGAN EKSTRAKSI KURUNG ()
                $targetKelasId = $notif->kelas_id ?? null;
                $targetMapelId = $notif->mapel_id ?? null;

                if (!$targetKelasId || !$targetMapelId) {
                    $guruIdSession = session('guru_id') ?? session('admin_id');
                    Carbon\Carbon::setLocale('id');
                    $hariIndo = Carbon\Carbon::now()->translatedFormat('l');

                    preg_match('/\((.*?)\)/', $notif->message, $matches);
                    $mapelInNotif = isset($matches[1]) ? trim($matches[1]) : '';

                    $allJadwalHariIni = DB::table('jadwals')
                        ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
                        ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
                        ->where('jadwals.guru_id', $guruIdSession)
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

            <div class="py-3 flex items-start gap-2.5 md:gap-3">
                <span class="material-symbols-outlined text-amber-500 text-base mt-0.5 shrink-0">info</span>
                <div class="flex-1 min-w-0">
                    <p class="text-xs md:text-sm text-slate-700 leading-snug">{{ $notif->message }}</p>
                    <p class="text-[10px] md:text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</p>
                </div>

                <a href="{{ $urlTujuan }}" class="text-xs font-bold text-teal-600 hover:text-teal-800 hover:underline shrink-0 flex items-center gap-0.5">
                    Isi Jurnal
                    <span class="material-symbols-outlined text-xs">arrow_forward</span>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<!-- MODAL POP-UP DETAIL ABSENSI & CATATAN SISWA -->
<div id="modalDetailJurnal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-5 md:p-6 shadow-2xl border border-slate-100 flex flex-col gap-4 animate-fade-in">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-teal-600">assignment_ind</span>
                <h4 class="font-bold text-slate-800 text-sm md:text-base" id="modalTitle">Detail Siswa</h4>
            </div>
            <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold p-1">
                &times;
            </button>
        </div>

        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
            <div id="containerAbsenModal" class="space-y-2">
                <p class="font-bold text-red-600 text-xs uppercase tracking-wider flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">person_off</span> Siswa Tidak Hadir:
                </p>
                <div id="listAbsenModal" class="flex flex-col gap-1.5 pl-2"></div>
            </div>

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
    function openDetailModalSafe(btn) {
        const absenList = JSON.parse(btn.getAttribute('data-absen') || '[]');
        const noteList = JSON.parse(btn.getAttribute('data-catatan') || '[]');
        const title = btn.getAttribute('data-title') || 'Detail Siswa';

        document.getElementById('modalTitle').textContent = 'Detail: ' + title;
        
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