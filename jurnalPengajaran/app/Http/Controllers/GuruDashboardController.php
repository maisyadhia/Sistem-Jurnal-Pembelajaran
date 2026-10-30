<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class GuruDashboardController extends Controller
{
    // Halaman Pilih Sesi (route: guru.pilih.sesi)
    public function index()
    {
        date_default_timezone_set('Asia/Jakarta');
        $guruId = session('guru_id');

        if (!$guruId) {
            if (session('admin_id')) {
                $guruId = session('admin_id');
            } else {
                return redirect()->route('login');
            }
        }

        Carbon::setLocale('id');
        $now = Carbon::now('Asia/Jakarta');
        $namaHariIndo = $now->translatedFormat('l');

        $jadwalHariIni = DB::table('jadwals')
            ->where('guru_id', $guruId)
            ->where('hari', $namaHariIndo)
            ->select('kelas_id', 'mapel_id')
            ->get();

        $daftar_kelas = DB::table('jadwals')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->where('jadwals.guru_id', $guruId)
            ->select('kelas_master.id as id', 'kelas_master.nama_kelas as nama_kelas')
            ->distinct()
            ->orderBy('kelas_master.nama_kelas')
            ->get();

        $daftar_mapel = DB::table('jadwals')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->where('jadwals.guru_id', $guruId)
            ->select('mapel_master.id as id', 'mapel_master.nama_mapel as nama_mapel')
            ->distinct()
            ->orderBy('mapel_master.nama_mapel')
            ->get();

        if ($daftar_kelas->isEmpty()) {
            return view('guru.pilih-sesi', compact('daftar_kelas', 'daftar_mapel', 'jadwalHariIni', 'namaHariIndo'))
                ->with('warning', 'Belum ada jadwal yang ditugaskan untuk Anda.');
        }

        return view('guru.pilih-sesi', compact('daftar_kelas', 'daftar_mapel', 'jadwalHariIni', 'namaHariIndo'));
    }

    // Menampilkan halaman edit profil
    public function editProfile()
    {
        $guruId = session('guru_id') ?? session('admin_id');
        if (!$guruId) {
            return redirect()->route('login');
        }

        $user = DB::table('users')->where('id', $guruId)->first();
        $guru = DB::table('guru')
            ->where('id', $guruId)
            ->orWhere('nama_guru', $user->name ?? '')
            ->first();

        return view('guru.profile', compact('user', 'guru'));
    }

    // Memproses ganti foto profil (VERSI STANDAR LARAVEL / PUBLIC_PATH)
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'photo.required' => 'Pilih file foto terlebih dahulu.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format foto harus berjenis: JPG, JPEG, PNG.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $userId = session('guru_id') ?? session('admin_id') ?? session('user_id');
        $guru = DB::table('guru')->where('id', $userId)->first();

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Mengarah ke folder public/uploads/profiles di dalam project Laravel
            $destinationPath = public_path('uploads/profiles');

            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true, true);
            }

            $file->move($destinationPath, $filename);

            if ($guru && !empty($guru->foto)) {
                $oldFilePath = public_path($guru->foto);
                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }

            $path = 'uploads/profiles/' . $filename;

            DB::table('guru')->where('id', $userId)->update([
                'foto' => $path,
                'updated_at' => now()
            ]);
        }

        return back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'current_password.required' => 'Password saat ini harus diisi.',
            'new_password.required' => 'Password baru harus diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $guruId = session('guru_id') ?? session('admin_id');
        $guru = DB::table('guru')->where('id', $guruId)->first();

        if (!$guru) {
            return back()->withErrors(['current_password' => 'Data guru tidak ditemukan.']);
        }

        if (!Hash::check($request->current_password, $guru->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah!']);
        }

        DB::table('guru')->where('id', $guruId)->update([
            'password' => Hash::make($request->new_password),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }

    // Dashboard Ringkasan
    public function dashboard(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $guruId = session('guru_id');

        if (!$guruId) {
            if (session('admin_id')) {
                $guruId = session('admin_id');
            } else {
                return redirect()->route('login');
            }
        }

        if (session('user_role') === 'guru') {
            Carbon::setLocale('id');
            $now = Carbon::now('Asia/Jakarta');
            $today = $now->toDateString();
            $namaHariIndo = $now->translatedFormat('l');

            DB::table('notifications')
                ->where('user_id', $guruId)
                ->whereDate('created_at', '<', $today)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);

            $jadwalHariIni = DB::table('jadwals')
                ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
                ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
                ->where('jadwals.guru_id', $guruId)
                ->where('jadwals.hari', $namaHariIndo)
                ->select('jadwals.*', 'kelas_master.nama_kelas', 'mapel_master.nama_mapel')
                ->get();

            foreach ($jadwalHariIni as $jadwal) {
                $jurnalAda = DB::table('jurnals')
                    ->where('guru_id', $guruId)
                    ->where('kelas_id', $jadwal->kelas_id)
                    ->where('mapel_id', $jadwal->mapel_id)
                    ->whereDate('tanggal', $today)
                    ->exists();

                $cleanMapel = trim(preg_replace('/\s+/', ' ', str_replace(['(', ')'], '', $jadwal->nama_mapel)));
                $firstMapelWord = explode(' ', $cleanMapel)[0] ?? $jadwal->nama_mapel;

                if ($jurnalAda) {
                    DB::table('notifications')
                        ->where('user_id', $guruId)
                        ->whereDate('created_at', $today)
                        ->where('is_read', 0)
                        ->where('message', 'like', "%{$jadwal->nama_kelas}%")
                        ->where(function ($query) use ($jadwal, $firstMapelWord) {
                            $query->where('message', 'like', "%{$jadwal->nama_mapel}%")
                                ->orWhere('message', 'like', "%{$firstMapelWord}%");
                        })
                        ->update(['is_read' => 1]);
                } else {
                    $notifPernahDibuat = DB::table('notifications')
                        ->where('user_id', $guruId)
                        ->whereDate('created_at', $today)
                        ->where('is_read', 0)
                        ->where('message', 'like', "%{$jadwal->nama_kelas}%")
                        ->where(function ($query) use ($jadwal, $firstMapelWord) {
                            $query->where('message', 'like', "%{$jadwal->nama_mapel}%")
                                ->orWhere('message', 'like', "%{$firstMapelWord}%");
                        })
                        ->exists();

                    if (!$notifPernahDibuat) {
                        DB::table('notifications')->insert([
                            'user_id' => $guruId,
                            'message' => "Pengingat Otomatis: Anda belum mengisi jurnal mengajar kelas {$jadwal->nama_kelas} ({$jadwal->nama_mapel}) hari ini!",
                            'is_read' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        $notifications = DB::table('notifications')
            ->where('user_id', $guruId)
            ->where('is_read', 0)
            ->whereDate('created_at', Carbon::now('Asia/Jakarta')->toDateString())
            ->orderBy('created_at', 'desc')
            ->get();

        $totalKelas = DB::table('jadwals')->where('guru_id', $guruId)->distinct('kelas_id')->count('kelas_id');
        $totalMapel = DB::table('jadwals')->where('guru_id', $guruId)->distinct('mapel_id')->count('mapel_id');
        $jurnalHariIni = DB::table('jurnals')->where('guru_id', $guruId)->whereDate('tanggal', Carbon::now('Asia/Jakarta')->toDateString())->count();

        $queryJurnal = DB::table('jurnals')
            ->join('kelas_master', 'jurnals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jurnals.mapel_id', '=', 'mapel_master.id')
            ->where('jurnals.guru_id', $guruId)
            ->select('jurnals.*', 'kelas_master.nama_kelas as nama_kelas', 'mapel_master.nama_mapel as nama_mapel');

        if ($request->filled('search')) {
            $search = $request->search;
            $queryJurnal->where(function ($q) use ($search) {
                $q->where('jurnals.materi', 'like', "%{$search}%")
                    ->orWhere('jurnals.target_next', 'like', "%{$search}%")
                    ->orWhere('kelas_master.nama_kelas', 'like', "%{$search}%")
                    ->orWhere('mapel_master.nama_mapel', 'like', "%{$search}%");
            });
        }

        $currentFilter = $request->query('filter');
        $startDate = null;

        if ($request->filled('tanggal')) {
            $queryJurnal->whereDate('jurnals.tanggal', $request->tanggal);
            $currentFilter = 'custom';
        } elseif ($currentFilter) {
            $now = Carbon::now('Asia/Jakarta');
            if ($currentFilter === 'hari_ini') {
                $startDate = $now->toDateString();
            } elseif ($currentFilter === '1_minggu') {
                $startDate = $now->copy()->subWeek()->toDateString();
            } elseif ($currentFilter === '1_bulan') {
                $startDate = $now->copy()->subMonth()->toDateString();
            }

            if ($startDate) {
                $queryJurnal->whereDate('jurnals.tanggal', '>=', $startDate);
            }
        }

        $jurnalTerbaru = $queryJurnal->orderBy('jurnals.created_at', 'desc')->paginate(10)->withQueryString();

        foreach ($jurnalTerbaru as $j) {
            Carbon::setLocale('id');
            $hariJurnal = Carbon::parse($j->tanggal)->translatedFormat('l');

            $jadwalMatching = DB::table('jadwals')
                ->where('guru_id', $guruId)
                ->where('kelas_id', $j->kelas_id)
                ->where('mapel_id', $j->mapel_id)
                ->where('hari', $hariJurnal)
                ->orderBy('jam_ke', 'asc')
                ->get();

            $waktuSesiArr = [];
            $jamKeList = [];

            if ($jadwalMatching->isNotEmpty()) {
                foreach ($jadwalMatching as $jm) {
                    $jamKeList[] = $jm->jam_ke;
                    if (!empty($jm->jam_mulai) && !empty($jm->jam_selesai)) {
                        $m = Carbon::parse($jm->jam_mulai)->format('H:i');
                        $s = Carbon::parse($jm->jam_selesai)->format('H:i');
                        $waktuSesiArr[] = "{$m} - {$s}";
                    }
                }
            }

            $j->waktu_sesi_list = $waktuSesiArr;
            $j->jam_ke_label = count($jamKeList) > 0 ? 'Jam ke ' . implode(' & ', array_unique($jamKeList)) : 'Jam ke-' . $j->jam_ke;
        }

        return view('guru.dashboard-ringkasan', compact('totalKelas', 'totalMapel', 'jurnalHariIni', 'jurnalTerbaru', 'notifications', 'currentFilter'));
    }

    public function previewPdf(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $guruId = session('guru_id') ?? session('admin_id');
        if (!$guruId) return redirect()->route('login');

        $guruUser = DB::table('users')->where('id', $guruId)->first();
        $guruName = $guruUser->name ?? (session('user_name') ?? 'Guru');

        $dataGuru = DB::table('guru')->where('id', $guruId)->orWhere('nama_guru', $guruName)->first();
        $rawIdentitas = $dataGuru->nik ?? ($dataGuru->nip ?? ($guruUser->nip ?? ''));
        $digitOnly = preg_replace('/[^0-9]/', '', (string) $rawIdentitas);
        $guruNip = (strlen($digitOnly) === 18) ? $digitOnly : '-';

        $jadwalGuruIds = array_unique(array_filter([$guruId, $dataGuru->id ?? null]));

        $jadwals = DB::table('jadwals')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->whereIn('jadwals.guru_id', $jadwalGuruIds)
            ->select('kelas_master.nama_kelas', 'mapel_master.nama_mapel')
            ->distinct()
            ->get();

        $daftarKelasGuru = $jadwals->pluck('nama_kelas')->unique()->values();

        $mapelPerKelas = [];
        foreach ($jadwals->groupBy('nama_kelas') as $kelas => $items) {
            $mapelPerKelas[$kelas] = $items->pluck('nama_mapel')->unique()->implode(' / ');
        }

        $allMapel = $jadwals->pluck('nama_mapel')->unique()->implode(' / ');

        $year = (int) date('Y');
        $month = (int) date('n');
        if ($month >= 7) {
            $tahunAjaran = $year . '/' . ($year + 1);
            $semester = '1 (GANJIL)';
        } else {
            $tahunAjaran = ($year - 1) . '/' . $year;
            $semester = '2 (GENAP)';
        }

        return view('guru.jurnal-preview-pdf', compact('guruName', 'guruNip', 'daftarKelasGuru', 'mapelPerKelas', 'allMapel', 'tahunAjaran', 'semester'));
    }

    public function exportPdf(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $guruId = session('guru_id') ?? session('admin_id');
        if (!$guruId) return redirect()->route('login');

        $guruUser = DB::table('users')->where('id', $guruId)->first();
        $guruName = $guruUser->name ?? (session('user_name') ?? 'Guru');

        $dataGuru = DB::table('guru')->where('id', $guruId)->orWhere('nama_guru', $guruName)->first();
        $rawIdentitas = $dataGuru->nik ?? ($dataGuru->nip ?? ($guruUser->nip ?? ''));
        $digitOnly = preg_replace('/[^0-9]/', '', (string) $rawIdentitas);
        $guruNip = (strlen($digitOnly) === 18) ? $digitOnly : '-';

        $queryJurnal = DB::table('jurnals')
            ->join('kelas_master', 'jurnals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jurnals.mapel_id', '=', 'mapel_master.id')
            ->where('jurnals.guru_id', $guruId)
            ->select('jurnals.*', 'kelas_master.nama_kelas', 'mapel_master.nama_mapel');

        if ($request->filled('kelas_pilihan') && $request->kelas_pilihan !== 'semua') {
            $queryJurnal->where('kelas_master.nama_kelas', $request->kelas_pilihan);
        }

        $currentFilter = $request->query('filter');
        if ($request->filled('tanggal')) {
            $queryJurnal->whereDate('jurnals.tanggal', $request->tanggal);
        } elseif ($currentFilter === 'hari_ini') {
            $queryJurnal->whereDate('jurnals.tanggal', Carbon::today('Asia/Jakarta'));
        } elseif ($currentFilter === '1_minggu') {
            $queryJurnal->whereDate('jurnals.tanggal', '>=', Carbon::now('Asia/Jakarta')->subWeek());
        } elseif ($currentFilter === '1_bulan') {
            $queryJurnal->whereDate('jurnals.tanggal', '>=', Carbon::now('Asia/Jakarta')->subMonth());
        }

        $jurnals = $queryJurnal->orderBy('jurnals.tanggal', 'asc')->get();
        $detectedMapel = $jurnals->pluck('nama_mapel')->unique()->implode(' / ');

        $namaPenyusun = $request->input('nama_penyusun', $guruName);
        $nipPenyusun = $request->input('nip_penyusun', $guruNip) ?: '-';
        $satuanPendidikan = $request->input('satuan_pendidikan', 'MIN 2 Kota Malang');
        $mataPelajaran = $request->filled('mata_pelajaran') ? $request->input('mata_pelajaran') : ($detectedMapel ?: 'Coding');
        $faseKelas = $request->input('kelas_pilihan') && $request->kelas_pilihan !== 'semua' ? $request->input('kelas_pilihan') : 'Semua Kelas';
        $tahunAjaran = $request->input('tahun_ajaran', '2026/2027');
        $semester = $request->input('semester', '1 (GANJIL)');
        $namaKepala = 'NANANG SUKMAWAN SETYABUDI, S.Pd, M.PdI';
        $nipKepala = '1978112720050111002';

        foreach ($jurnals as $index => $j) {
            Carbon::setLocale('id');
            $j->hari_format = Carbon::parse($j->tanggal)->translatedFormat('l');
            $j->tanggal_format = Carbon::parse($j->tanggal)->translatedFormat('d F Y');

            $jadwalMatching = DB::table('jadwals')
                ->where('guru_id', $guruId)
                ->where('kelas_id', $j->kelas_id)
                ->where('mapel_id', $j->mapel_id)
                ->where('hari', $j->hari_format)
                ->orderBy('jam_ke', 'asc')
                ->pluck('jam_ke')
                ->toArray();

            if (count($jadwalMatching) > 1) {
                $j->jam_sesi_display = min($jadwalMatching) . ' - ' . max($jadwalMatching);
            } elseif (count($jadwalMatching) == 1) {
                $j->jam_sesi_display = (string) $jadwalMatching[0];
            } else {
                $j->jam_sesi_display = (string) ($j->jam_ke ?? '1');
            }

            $students = json_decode($j->student_ids, true) ?? [];
            $absenList = [];
            if (is_array($students)) {
                foreach ($students as $s) {
                    if (isset($s['status']) && strtolower($s['status']) !== 'hadir') {
                        $absenList[] = $s['status'];
                    }
                }
            }

            $j->atp_code = "1." . ($index + 1);
            $j->penilaian_text = "Formatif (Observasi KBM)";
            $j->refleksi_text = count($absenList) > 0 ? "KBM berjalan lancar. Terdapat " . count($absenList) . " siswa tidak hadir (" . implode(', ', array_unique($absenList)) . ")." : "Peserta didik antusias, konsep dasar dan target materi tersampaikan dengan baik.";
        }

        $tglDownload = Carbon::now('Asia/Jakarta')->format('d-m-Y');
        $fileName = 'Jurnal_Mengajar_SIJAMPANG_' . $tglDownload . '.pdf';

        if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('guru.jurnal-pdf-template', compact('jurnals', 'namaPenyusun', 'nipPenyusun', 'namaKepala', 'nipKepala', 'satuanPendidikan', 'mataPelajaran', 'faseKelas', 'tahunAjaran', 'semester', 'tglDownload'))->setPaper('a4', 'landscape');
            return $pdf->stream($fileName);
        }

        return view('guru.jurnal-pdf-template', compact('jurnals', 'namaPenyusun', 'nipPenyusun', 'namaKepala', 'nipKepala', 'satuanPendidikan', 'mataPelajaran', 'faseKelas', 'tahunAjaran', 'semester', 'tglDownload'));
    }

    public function exportExcel(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $guruId = session('guru_id') ?? session('admin_id');
        if (!$guruId) return redirect()->route('login');

        $guruName = session('user_name') ?? 'Guru';

        $queryJurnal = DB::table('jurnals')
            ->join('kelas_master', 'jurnals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jurnals.mapel_id', '=', 'mapel_master.id')
            ->where('jurnals.guru_id', $guruId)
            ->select('jurnals.*', 'kelas_master.nama_kelas', 'mapel_master.nama_mapel');

        $currentFilter = $request->query('filter');
        $periodeLabel = 'Semua Periode';

        if ($request->filled('tanggal')) {
            $queryJurnal->whereDate('jurnals.tanggal', $request->tanggal);
            $periodeLabel = Carbon::parse($request->tanggal)->format('d/m/Y');
        } elseif ($currentFilter === 'hari_ini') {
            $queryJurnal->whereDate('jurnals.tanggal', Carbon::today('Asia/Jakarta'));
            $periodeLabel = 'Hari Ini (' . Carbon::today('Asia/Jakarta')->format('d/m/Y') . ')';
        } elseif ($currentFilter === '1_minggu') {
            $queryJurnal->whereDate('jurnals.tanggal', '>=', Carbon::now('Asia/Jakarta')->subWeek());
            $periodeLabel = '1 Minggu Terakhir';
        } elseif ($currentFilter === '1_bulan') {
            $queryJurnal->whereDate('jurnals.tanggal', '>=', Carbon::now('Asia/Jakarta')->subMonth());
            $periodeLabel = '1 Bulan Terakhir';
        }

        $jurnals = $queryJurnal->orderBy('jurnals.tanggal', 'desc')->get();
        $fileName = 'Riwayat_Jurnal_Mengajar_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($jurnals, $guruName, $periodeLabel, $guruId) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['LAPORAN REKAPITULASI JURNAL MENGAJAR GURU']);
            fputcsv($file, ['Nama Guru', $guruName]);
            fputcsv($file, ['Tanggal Export', Carbon::now('Asia/Jakarta')->translatedFormat('d F Y H:i')]);
            fputcsv($file, ['Periode Laporan', $periodeLabel]);
            fputcsv($file, []);

            fputcsv($file, ['No', 'Tanggal Input', 'Tanggal KBM', 'Kelas', 'Mata Pelajaran', 'Waktu Sesi', 'Bahasan Materi', 'Target Berikutnya', 'Ringkasan Kehadiran', 'Catatan Khusus Siswa']);

            $no = 1;
            foreach ($jurnals as $jurnal) {
                Carbon::setLocale('id');
                $hariJ = Carbon::parse($jurnal->tanggal)->translatedFormat('l');
                $jadwalMatch = DB::table('jadwals')
                    ->where('guru_id', $guruId)
                    ->where('kelas_id', $jurnal->kelas_id)
                    ->where('mapel_id', $jurnal->mapel_id)
                    ->where('hari', $hariJ)
                    ->get();

                $waktuArr = [];
                foreach ($jadwalMatch as $jm) {
                    if (!empty($jm->jam_mulai) && !empty($jm->jam_selesai)) {
                        $waktuArr[] = Carbon::parse($jm->jam_mulai)->format('H:i') . ' - ' . Carbon::parse($jm->jam_selesai)->format('H:i');
                    }
                }
                $waktuSesi = count($waktuArr) > 0 ? implode(' | ', $waktuArr) : ('Jam ke-' . ($jurnal->jam_ke ?? '-'));

                $students = json_decode($jurnal->student_ids, true);
                $hadirCount = 0; $sakitCount = 0; $izinCount = 0; $alphaCount = 0;
                $catatanArr = [];

                if (is_array($students)) {
                    foreach ($students as $s) {
                        $status = strtolower($s['status'] ?? 'hadir');
                        if ($status === 'hadir') $hadirCount++;
                        elseif ($status === 'sakit') $sakitCount++;
                        elseif ($status === 'izin') $izinCount++;
                        elseif ($status === 'alpha') $alphaCount++;

                        if (!empty($s['catatan'])) {
                            $siswaDb = DB::table('students')->where('id', $s['student_id'] ?? 0)->first();
                            $nama = $siswaDb ? $siswaDb->name : ('ID:' . ($s['student_id'] ?? '-'));
                            $catatanArr[] = $nama . ' (' . ucfirst($status) . '): "' . $s['catatan'] . '"';
                        }
                    }
                }

                $summaryAbsen = "Hadir: {$hadirCount}, Sakit: {$sakitCount}, Izin: {$izinCount}, Alpha: {$alphaCount}";
                $txtCatatan = count($catatanArr) > 0 ? implode(' | ', $catatanArr) : '-';

                fputcsv($file, [
                    $no++,
                    Carbon::parse($jurnal->created_at)->format('d/m/Y H:i'),
                    Carbon::parse($jurnal->tanggal)->format('d/m/Y'),
                    $jurnal->nama_kelas,
                    $jurnal->nama_mapel,
                    $waktuSesi,
                    $jurnal->materi ?? '-',
                    $jurnal->target_next ?? '-',
                    $summaryAbsen,
                    $txtCatatan
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}