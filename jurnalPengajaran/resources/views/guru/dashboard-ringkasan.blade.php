@extends('layouts.app')

@section('title', 'Dashboard Guru - E-Jurnal')

@push('styles')
<style>
    .stat-card {
        transition: all 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
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

    <!-- 📊 TABEL RIWAYAT JURNAL (💡 Diperkecil padding p-6 jadi p-4 ) -->
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 flex flex-col gap-3">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800">Riwayat Jurnal Mengajar</h3>
                <p class="text-[11px] text-slate-400">Daftar rekaman administrasi kelas yang telah diinput.</p>
            </div>
            
            <form method="GET" action="{{ route('guru.dashboard') }}" class="flex items-end gap-2">
                <div class="flex flex-col gap-0.5">
                    <label for="filter_date" class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Pilih Tanggal</label>
                    <input type="date" id="filter_date" name="tanggal" value="{{ request('tanggal') }}" 
                           class="bg-slate-50 border border-slate-200 rounded-lg p-1.5 text-[11px] text-slate-700 outline-none focus:border-teal-500 cursor-pointer">
                </div>
                <button type="submit" class="bg-slate-800 text-white px-2.5 py-1.5 text-[11px] font-semibold rounded-lg hover:bg-slate-900 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">filter_alt</span>
                    Filter
                </button>
                @if(request('tanggal'))
                    <a href="{{ route('guru.dashboard') }}" class="bg-slate-100 text-slate-600 px-2.5 py-1.5 text-[11px] font-semibold rounded-lg hover:bg-slate-200 transition-colors">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <!-- 💡 Ukuran font header diperkecil ke text-[10px]  -->
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
                        <!-- 💡 Baris tabel diperketat padding vertikalnya jadi py-2.5  -->
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
                            
                            <td class="py-2.5 px-3 text-xs">
                                @php
                                    $students = json_decode($jurnal->student_ids, true);
                                    $absenList = [];
                                    $noteList = [];

                                    if (is_array($students)) {
                                        foreach ($students as $s) {
                                            $siswaDb = DB::table('students')->where('id', $s['student_id'])->first();
                                            $namaSiswa = $siswaDb ? $siswaDb->name : 'Siswa ID: ' . $s['student_id'];

                                            if (isset($s['status']) && $s['status'] !== 'Hadir') {
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
                                @endphp

                                <div class="flex flex-col gap-1.5">
                                    @if(count($absenList) > 0)
                                        <div class="space-y-0.5">
                                            <p class="font-bold text-red-700 text-[9px] uppercase tracking-wider">❌ Tidak Hadir:</p>
                                            <div class="flex flex-col gap-0.5 pl-0.5">
                                                @foreach($absenList as $absen)
                                                    @php
                                                        $badgeBg = $absen['status'] === 'Alpha' ? 'bg-red-50 text-red-700 border-red-200' : ($absen['status'] === 'Sakit' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-blue-50 text-blue-700 border-blue-200');
                                                    @endphp
                                                    <span class="inline-flex items-center gap-1 text-[11px]">
                                                        <span class="px-1 py-0.5 rounded border text-[8px] font-bold {{ $badgeBg }}">{{ $absen['status'] }}</span>
                                                        <span class="text-slate-700 font-medium">{{ $absen['nama'] }}</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if(count($noteList) > 0)
                                        <div class="space-y-0.5 {{ count($absenList) > 0 ? 'pt-1 border-t border-slate-100' : '' }}">
                                            <p class="font-bold text-teal-700 text-[9px] uppercase tracking-wider">📝 Catatan Khusus:</p>
                                            <ul class="list-disc list-inside space-y-0.5 pl-0.5 text-[11px] text-slate-600">
                                                @foreach($noteList as $note)
                                                    <li class="leading-tight">
                                                        <strong class="text-slate-800">{{ $note['nama'] }}</strong>: <span class="italic">"{{ $note['text'] }}"</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(count($absenList) === 0 && count($noteList) === 0)
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
                                Tidak ada data jurnal ditemukan .
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
@endsection