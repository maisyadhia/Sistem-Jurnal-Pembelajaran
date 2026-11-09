<?php

namespace App\Http\Controllers;

use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use App\Models\UnreportedClass;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HumasMonitoringController extends Controller
{
    use LogsAdminActivity;

    public function index()
    {
        $complianceRate = $this->calculateComplianceRate();
        $onTimeCount = $this->getOnTimeCount();
        $lateCount = $this->getLateCount();
        $complianceIncrease = '2.4';

        Carbon::setLocale('id');
        $today = Carbon::today()->toDateString();
        $namaHariIndo = Carbon::now()->translatedFormat('l');

        // ========== AMBIL DATA MASTER TERKINI ==========
        // Ambil data guru terbaru
        $guru = DB::table('guru')->select(
            'id',
            'nama_guru as name',
            'nik as identifier',
            DB::raw("'GURU' as category"),
            DB::raw("'Aktif' as status"),
            DB::raw("'bg-secondary' as statusColor"),
            DB::raw("'bg-blue-500' as color"),
            DB::raw("UPPER(LEFT(nama_guru, 2)) as initials"),
            'created_at'
        )->orderBy('created_at', 'desc')->limit(5)->get();

        // Ambil data siswa terbaru
        $siswa = DB::table('students')->select(
            'id',
            'name',
            'nisn as identifier',
            DB::raw("'SISWA' as category"),
            DB::raw("'Aktif' as status"),
            DB::raw("'bg-secondary' as statusColor"),
            DB::raw("'bg-green-500' as color"),
            DB::raw("UPPER(LEFT(name, 2)) as initials"),
            'created_at'
        )->orderBy('created_at', 'desc')->limit(5)->get();

        // Ambil data kelas terbaru
        $kelas = DB::table('kelas_master')
            ->leftJoin('guru', 'kelas_master.wali_kelas_id', '=', 'guru.id')
            ->select(
                'kelas_master.id',
                'kelas_master.nama_kelas as name',
                'guru.nama_guru as identifier',
                DB::raw("'KELAS' as category"),
                DB::raw("'Tersedia' as status"),
                DB::raw("'bg-secondary' as statusColor"),
                DB::raw("'bg-purple-500' as color"),
                DB::raw("UPPER(LEFT(kelas_master.nama_kelas, 2)) as initials"),
                'kelas_master.created_at'
            )
            ->orderBy('kelas_master.created_at', 'desc')
            ->limit(5)
            ->get();

        // Ambil data mapel terbaru
        $mapel = DB::table('mapel_master')->select(
            'id',
            'nama_mapel as name',
            'kode_mapel as identifier',
            DB::raw("'MATA PELAJARAN' as category"),
            DB::raw("'Aktif' as status"),
            DB::raw("'bg-secondary' as statusColor"),
            DB::raw("'bg-orange-500' as color"),
            DB::raw("UPPER(LEFT(nama_mapel, 2)) as initials"),
            'created_at'
        )->orderBy('created_at', 'desc')->limit(5)->get();

        // Ambil data jadwal terbaru
        $jadwal = DB::table('jadwals')
            ->join('guru', 'jadwals.guru_id', '=', 'guru.id')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->select(
                'jadwals.id',
                DB::raw("CONCAT(guru.nama_guru, ' - ', mapel_master.nama_mapel, ' (', kelas_master.nama_kelas, ')') as name"),
                DB::raw("CONCAT(jadwals.hari, ', Jam ', jadwals.jam_ke) as identifier"),
                DB::raw("'JADWAL' as category"),
                DB::raw("'Aktif' as status"),
                DB::raw("'bg-secondary' as statusColor"),
                DB::raw("'bg-red-500' as color"),
                DB::raw("UPPER(LEFT(guru.nama_guru, 2)) as initials"),
                'jadwals.created_at'
            )
            ->orderBy('jadwals.created_at', 'desc')
            ->limit(5)
            ->get();

        // Gabungkan semua data dan urutkan berdasarkan created_at terbaru
        $dataMaster = $guru->merge($siswa)
            ->merge($kelas)
            ->merge($mapel)
            ->merge($jadwal)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        // Hitung total semua entitas
        $totalDataMaster = DB::table('guru')->count()
            + DB::table('students')->count()
            + DB::table('kelas_master')->count()
            + DB::table('mapel_master')->count()
            + DB::table('jadwals')->count();

        // ========== UNREPORTED CLASSES ==========
        $allTeachers = DB::table('guru')->get();

        $teacherIdsWithJurnal = DB::table('jurnals')
            ->whereDate('tanggal', $today)
            ->pluck('guru_id')
            ->toArray();

        $unreportedTeachers = collect();
        foreach ($allTeachers as $teacher) {
            if (!in_array($teacher->id, $teacherIdsWithJurnal)) {
                $alreadyReminded = DB::table('notifications')
                    ->where('user_id', $teacher->id)
                    ->whereDate('created_at', $today)
                    ->where('message', 'like', '%Pengingat%')
                    ->exists();

                if (!$alreadyReminded) {
                    $jadwalGuru = DB::table('jadwals')
                        ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
                        ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
                        ->where('jadwals.guru_id', $teacher->id)
                        ->where('jadwals.hari', $namaHariIndo)
                        ->select(
                            'kelas_master.nama_kelas as code',
                            'mapel_master.nama_mapel as subject',
                            DB::raw("CONCAT(jadwals.hari, ', ', TIME_FORMAT(jadwals.jam_mulai, '%H:%i'), ' - ', TIME_FORMAT(jadwals.jam_selesai, '%H:%i')) as schedule")
                        )
                        ->first();

                    if ($jadwalGuru) {
                        $unreportedTeachers->push((object) [
                            'id' => $teacher->id,
                            'teacher' => $teacher->nama_guru,
                            'code' => $jadwalGuru->code,
                            'subject' => $jadwalGuru->subject,
                            'schedule' => $jadwalGuru->schedule,
                            'date' => $today,
                            'reported' => false,
                        ]);
                    }
                }
            }
        }

        $unreportedFromDb = DB::table('unreported_classes')
            ->whereDate('date', $today)
            ->where('reported', false)
            ->get();

        $unreportedClasses = $unreportedTeachers->merge($unreportedFromDb);

        return view('humas.monitoring', compact(
            'complianceRate',
            'onTimeCount',
            'lateCount',
            'complianceIncrease',
            'unreportedClasses',
            'dataMaster',
            'totalDataMaster'
        ));
    }

    private function calculateComplianceRate()
    {
        $totalTeachers = DB::table('guru')->count();
        if ($totalTeachers == 0) return 0;

        $todayJurnals = DB::table('jurnals')
            ->whereDate('tanggal', today())
            ->count();

        return round(($todayJurnals / $totalTeachers) * 100, 1);
    }

    private function getOnTimeCount()
    {
        return DB::table('jurnals')
            ->whereDate('tanggal', today())
            ->whereTime('created_at', '<=', '12:00:00')
            ->count();
    }

    private function getLateCount()
    {
        return DB::table('jurnals')
            ->whereDate('tanggal', today())
            ->whereTime('created_at', '>', '12:00:00')
            ->count();
    }

    public function remindTeacher(Request $request)
    {
        try {
            $request->validate([
                'teacher' => 'required|string',
                'class' => 'required|string',
                'subject' => 'nullable|string',
            ]);

            $guru = DB::table('guru')
                ->where('nama_guru', 'like', '%' . $request->teacher . '%')
                ->first();

            if (!$guru) {
                $guru = DB::table('admins')
                    ->where('name', 'like', '%' . $request->teacher . '%')
                    ->first();
            }

            if ($guru) {
                $message = "Pengingat: Kelas {$request->class}";
                if ($request->subject) {
                    $message .= " ({$request->subject})";
                }
                $message .= " belum mengisi jurnal hari ini. Segera isi jurnal mengajar Anda!";

                DB::table('notifications')->insert([
                    'user_id' => $guru->id,
                    'user_type' => 'guru',
                    'message' => $message,
                    'link' => route('guru.pilih.sesi'),
                    'is_read' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->logActivity(
                    'remind',
                    'monitoring',
                    "Mengirim pengingat ke guru {$request->teacher} untuk kelas {$request->class}",
                    null,
                    ['teacher' => $request->teacher, 'class' => $request->class]
                );

                return response()->json([
                    'success' => true,
                    'message' => "Pengingat berhasil dikirim ke {$request->teacher}"
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => "Guru tidak ditemukan"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportReport(Request $request)
    {
        $teachers = DB::table('guru')->get();
        
        // Ambil SEMUA jurnal (dengan filter jika ada)
        $queryJurnal = DB::table('jurnals')
            ->join('guru', 'jurnals.guru_id', '=', 'guru.id')
            ->join('kelas_master', 'jurnals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jurnals.mapel_id', '=', 'mapel_master.id')
            ->select(
                'jurnals.id',
                'jurnals.tanggal as tanggal_kbm',
                'jurnals.created_at as tanggal_input',
                'guru.nama_guru',
                'guru.nik',
                'kelas_master.nama_kelas',
                'mapel_master.nama_mapel',
                'jurnals.materi',
                'jurnals.target_next',
                DB::raw('DATEDIFF(jurnals.created_at, jurnals.tanggal) as selisih_hari'),
                DB::raw('CASE WHEN DATE(jurnals.created_at) > jurnals.tanggal THEN 1 ELSE 0 END as is_terlambat')
            );

        // Filter
        if ($request->filled('filter')) {
            $filter = $request->filter;
            if ($filter === 'hari_ini') {
                $queryJurnal->whereDate('jurnals.tanggal', today());
            } elseif ($filter === '1_minggu') {
                $queryJurnal->whereDate('jurnals.tanggal', '>=', now()->subWeek());
            } elseif ($filter === '1_bulan') {
                $queryJurnal->whereDate('jurnals.tanggal', '>=', now()->subMonth());
            } elseif ($filter === 'terlambat') {
                $queryJurnal->whereRaw('DATE(jurnals.created_at) > jurnals.tanggal');
            }
        }

        $jurnals = $queryJurnal->orderBy('jurnals.created_at', 'desc')->get();

        $this->logActivity('export', 'report', 'Mengekspor laporan kepatuhan', null, ['total' => $jurnals->count()]);

        return $this->exportExcel([
            'jurnals' => $jurnals,
            'teachers' => $teachers,
        ]);
    }

    private function exportExcel($data)
    {
        $filename = "laporan_kepatuhan_" . date('Y-m-d_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function() use ($data) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['LAPORAN KEPATUHAN GURU']);
            fputcsv($handle, ['Generated', now()->format('d/m/Y H:i')]);
            fputcsv($handle, ['']);

            fputcsv($handle, ['DAFTAR JURNAL']);
            fputcsv($handle, ['No', 'Tanggal KBM', 'Tanggal Input', 'Guru', 'NIK', 'Kelas', 'Mapel', 'Materi', 'Status', 'Selisih Hari']);

            $no = 1;
            foreach ($data['jurnals'] as $jurnal) {
                $status = $jurnal->is_terlambat ? 'Terlambat' : 'Tepat Waktu';
                fputcsv($handle, [
                    $no++,
                    \Carbon\Carbon::parse($jurnal->tanggal_kbm)->format('d/m/Y'),
                    \Carbon\Carbon::parse($jurnal->tanggal_input)->format('d/m/Y H:i'),
                    $jurnal->nama_guru,
                    $jurnal->nik ?? '-',
                    $jurnal->nama_kelas,
                    $jurnal->nama_mapel,
                    $jurnal->materi ?? '-',
                    $status,
                    $jurnal->selisih_hari . ' hari'
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function laporanIndex(Request $request)
    {
        // ========== FILTER ==========
        $currentFilter = $request->query('filter');
        $search = $request->query('search');
        
        // ========== QUERY JURNAL (SEMUA RIWAYAT) ==========
        $queryJurnal = DB::table('jurnals')
            ->join('guru', 'jurnals.guru_id', '=', 'guru.id')
            ->join('kelas_master', 'jurnals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jurnals.mapel_id', '=', 'mapel_master.id')
            ->select(
                'jurnals.id',
                'jurnals.tanggal as tanggal_kbm',
                'jurnals.created_at as tanggal_input',
                'guru.nama_guru',
                'guru.nik',
                'kelas_master.nama_kelas',
                'mapel_master.nama_mapel',
                'jurnals.materi',
                'jurnals.target_next',
                DB::raw('DATEDIFF(jurnals.created_at, jurnals.tanggal) as selisih_hari'),
                DB::raw('CASE WHEN DATE(jurnals.created_at) > jurnals.tanggal THEN 1 ELSE 0 END as is_terlambat')
            );

        // Search
        if ($search) {
            $queryJurnal->where(function($q) use ($search) {
                $q->where('guru.nama_guru', 'like', "%{$search}%")
                ->orWhere('kelas_master.nama_kelas', 'like', "%{$search}%")
                ->orWhere('mapel_master.nama_mapel', 'like', "%{$search}%")
                ->orWhere('jurnals.materi', 'like', "%{$search}%");
            });
        }

        // Filter periode
        if ($currentFilter === 'hari_ini') {
            $queryJurnal->whereDate('jurnals.tanggal', today());
        } elseif ($currentFilter === '1_minggu') {
            $queryJurnal->whereDate('jurnals.tanggal', '>=', now()->subWeek());
        } elseif ($currentFilter === '1_bulan') {
            $queryJurnal->whereDate('jurnals.tanggal', '>=', now()->subMonth());
        } elseif ($currentFilter === 'terlambat') {
            $queryJurnal->whereRaw('DATE(jurnals.created_at) > jurnals.tanggal');
        }

        // 🔥 PAGINATION - seperti dashboard guru
        $jurnals = $queryJurnal->orderBy('jurnals.created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // ========== STATISTIK ==========
        $totalJurnal = DB::table('jurnals')->count();
        $totalTerlambat = DB::table('jurnals')
            ->whereRaw('DATE(created_at) > tanggal')
            ->count();
        $totalTepatWaktu = $totalJurnal - $totalTerlambat;
        
        // Persentase
        $complianceRate = $totalJurnal > 0 
            ? round(($totalTepatWaktu / $totalJurnal) * 100, 1) 
            : 0;

        // ========== DAFTAR GURU (untuk tabel status) ==========
        $teachers = DB::table('guru')->get();
        
        // Hitung status per guru dari SEMUA jurnal
        $teacherStatus = [];
        $teacherLateStatus = [];
        $teacherLateCount = [];
        $teacherTotalCount = [];

        foreach ($teachers as $teacher) {
            $totalJurnalGuru = DB::table('jurnals')
                ->where('guru_id', $teacher->id)
                ->count();
            
            $totalTerlambatGuru = DB::table('jurnals')
                ->where('guru_id', $teacher->id)
                ->whereRaw('DATE(created_at) > tanggal')
                ->count();

            $teacherStatus[$teacher->id] = $totalJurnalGuru > 0;
            $teacherLateStatus[$teacher->id] = $totalTerlambatGuru > 0;
            $teacherLateCount[$teacher->id] = $totalTerlambatGuru;
            $teacherTotalCount[$teacher->id] = $totalJurnalGuru;
        }

        // ========== UNREPORTED CLASSES ==========
        $unreported = DB::table('unreported_classes')
            ->where('reported', false)
            ->get();

        return view('humas.laporan', compact(
            'jurnals',
            'totalJurnal',
            'totalTerlambat',
            'totalTepatWaktu',
            'complianceRate',
            'teachers',
            'teacherStatus',
            'teacherLateStatus',
            'teacherLateCount',
            'teacherTotalCount',
            'unreported',
            'currentFilter',
            'search'
        ));
    }
}