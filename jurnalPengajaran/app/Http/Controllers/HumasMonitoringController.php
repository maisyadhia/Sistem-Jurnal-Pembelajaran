<?php

namespace App\Http\Controllers;

use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use App\Models\UnreportedClass;
use App\Models\DataMaster;
use App\Models\Admin;
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

        // Ambil semua guru
        $allTeachers = DB::table('guru')->get();

        // Ambil data master dari semua tabel
        $dataMaster = collect();
        
        // Ambil data guru
        $guru = DB::table('guru')->select(
            'id',
            'nama_guru as name',
            'nik as identifier',
            DB::raw("'GURU' as category"),
            DB::raw("'Aktif' as status"),
            DB::raw("'bg-secondary' as statusColor"),
            DB::raw("'bg-secondary-container' as color"),
            DB::raw("UPPER(LEFT(nama_guru, 2)) as initials")
        )->get();
        
        // Ambil data kelas
        $kelas = DB::table('kelas_master')->select(
            'id',
            'nama_kelas as name',
            'wali_kelas as identifier',
            DB::raw("'KELAS' as category"),
            DB::raw("'Tersedia' as status"),
            DB::raw("'bg-secondary' as statusColor"),
            DB::raw("'bg-primary-container' as color"),
            DB::raw("UPPER(LEFT(nama_kelas, 2)) as initials")
        )->get();
        
        // Ambil data mapel
        $mapel = DB::table('mapel_master')->select(
            'id',
            'nama_mapel as name',
            'kode_mapel as identifier',
            DB::raw("'MATA PELAJARAN' as category"),
            DB::raw("'Aktif' as status"),
            DB::raw("'bg-secondary' as statusColor"),
            DB::raw("'bg-tertiary-container' as color"),
            DB::raw("UPPER(LEFT(nama_mapel, 2)) as initials")
        )->get();
        
        // Gabungkan semua data
        $dataMaster = $guru->merge($kelas)->merge($mapel)->take(5);
        $totalDataMaster = $guru->count() + $kelas->count() + $mapel->count();

        
        // Ambil ID guru yang sudah mengisi jurnal hari ini
        $teacherIdsWithJurnal = DB::table('jurnals')
            ->whereDate('tanggal', $today)
            ->pluck('guru_id')
            ->toArray();
        
        // Cari guru yang BELUM mengisi dan BELUM diingatkan hari ini
        $unreportedTeachers = collect();
        foreach ($allTeachers as $teacher) {
            if (!in_array($teacher->id, $teacherIdsWithJurnal)) {
                // Cek apakah sudah diingatkan hari ini
                $alreadyReminded = DB::table('notifications')
                    ->where('user_id', $teacher->id)
                    ->whereDate('created_at', $today)
                    ->where('message', 'like', '%Pengingat%')
                    ->exists();

                if (!$alreadyReminded) {
                    // Ambil jadwal guru hari ini
                    $jadwal = DB::table('jadwals')
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
                    
                    if ($jadwal) {
                        $unreportedTeachers->push((object) [
                            'id' => $teacher->id,
                            'teacher' => $teacher->nama_guru,
                            'code' => $jadwal->code,
                            'subject' => $jadwal->subject,
                            'schedule' => $jadwal->schedule,
                            'date' => $today,
                            'reported' => false,
                        ]);
                    }
                }
            }
        }

        // Ambil unreported classes dari tabel (fallback)
        $unreportedFromDb = DB::table('unreported_classes')
            ->whereDate('date', $today)
            ->where('reported', false)
            ->get();

        $unreportedClasses = $unreportedTeachers->merge($unreportedFromDb);

        $dataMaster = DataMaster::latest()->take(5)->get();
        $totalDataMaster = DataMaster::count();

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

            // Cari guru di database
            $guru = DB::table('guru')
                ->where('nama_guru', 'like', '%' . $request->teacher . '%')
                ->first();

            if (!$guru) {
                $guru = DB::table('admins')
                    ->where('name', 'like', '%' . $request->teacher . '%')
                    ->first();
            }

            if ($guru) {
                // Simpan notifikasi
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

                // Log aktivitas
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
        $date = $request->get('date', today()->format('Y-m-d'));
        $period = $request->get('period', 'weekly');

        // Ambil semua guru
        $teachers = DB::table('guru')->get();
        
        // Ambil jurnal berdasarkan tanggal
        $jurnals = DB::table('jurnals')
            ->whereDate('tanggal', $date)
            ->get();

        // Hitung status per guru
        $teacherStatus = [];
        foreach ($teachers as $teacher) {
            $isReported = false;
            foreach ($jurnals as $jurnal) {
                if ($jurnal->guru_id == $teacher->id) {
                    $isReported = true;
                    break;
                }
            }
            $teacherStatus[$teacher->id] = $isReported;
        }

        $data = [
            'title' => 'Laporan Kepatuhan Guru',
            'date' => $date,
            'period' => $period,
            'complianceRate' => $this->calculateComplianceRate(),
            'onTimeCount' => $this->getOnTimeCount(),
            'lateCount' => $this->getLateCount(),
            'teachers' => $teachers,
            'jurnals' => $jurnals,
            'teacherStatus' => $teacherStatus,
            'unreported' => DB::table('unreported_classes')
                ->whereDate('date', $date)
                ->where('reported', false)
                ->get(),
        ];

        // Log aktivitas export
        $this->logActivity(
            'export',
            'report',
            "Mengekspor laporan kepatuhan periode {$period} tanggal {$date}",
            null,
            ['date' => $date, 'period' => $period, 'format' => 'excel']
        );

        // Langsung export Excel
        return $this->exportExcel($data);
    }

    private function exportExcel($data)
    {
        $filename = "laporan_kepatuhan_{$data['date']}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($data) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fputs($handle, "\xEF\xBB\xBF");
            
            // Header Laporan
            fputcsv($handle, ['LAPORAN KEPATUHAN GURU']);
            fputcsv($handle, ['Tanggal', $data['date']]);
            fputcsv($handle, ['Periode', $data['period']]);
            fputcsv($handle, ['']);
            
            // Statistik
            fputcsv($handle, ['STATISTIK KEPATUHAN']);
            fputcsv($handle, ['Tingkat Kepatuhan', $data['complianceRate'] . '%']);
            fputcsv($handle, ['Tepat Waktu', $data['onTimeCount']]);
            fputcsv($handle, ['Terlambat', $data['lateCount']]);
            fputcsv($handle, ['']);
            
            // Daftar Guru
            fputcsv($handle, ['DAFTAR GURU']);
            fputcsv($handle, ['No', 'Nama Guru', 'NIK', 'Status']);
            
            $no = 1;
            foreach ($data['teachers'] as $teacher) {
                $isReported = isset($data['teacherStatus'][$teacher->id]) ? $data['teacherStatus'][$teacher->id] : false;
                $status = $isReported ? 'Sudah Mengisi' : 'Belum Mengisi';
                $teacherName = $teacher->nama_guru ?? $teacher->name ?? '-';
                $teacherNik = $teacher->nik ?? '-';
                
                fputcsv($handle, [$no++, $teacherName, $teacherNik, $status]);
            }
            
            // Kelas Tanpa Catatan
            if ($data['unreported']->count() > 0) {
                fputcsv($handle, ['']);
                fputcsv($handle, ['KELAS TANPA CATATAN']);
                fputcsv($handle, ['No', 'Kelas', 'Mata Pelajaran', 'Guru']);
                
                $no = 1;
                foreach ($data['unreported'] as $class) {
                    fputcsv($handle, [
                        $no++,
                        $class->code ?? $class->nama_kelas ?? '-',
                        $class->subject ?? $class->nama_mapel ?? '-',
                        $class->teacher ?? $class->nama_guru ?? '-'
                    ]);
                }
            }
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function laporanIndex(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $period = $request->get('period', 'weekly');

        // Ambil semua guru
        $teachers = DB::table('guru')->get();
        
        // Ambil jurnal berdasarkan tanggal
        $jurnals = DB::table('jurnals')
            ->whereDate('tanggal', $date)
            ->get();

        // Hitung status per guru
        $teacherStatus = [];
        foreach ($teachers as $teacher) {
            $isReported = false;
            foreach ($jurnals as $jurnal) {
                if ($jurnal->guru_id == $teacher->id) {
                    $isReported = true;
                    break;
                }
            }
            $teacherStatus[$teacher->id] = $isReported;
        }

        $data = [
            'title' => 'Laporan Kepatuhan Guru',
            'date' => $date,
            'period' => $period,
            'complianceRate' => $this->calculateComplianceRate(),
            'onTimeCount' => $this->getOnTimeCount(),
            'lateCount' => $this->getLateCount(),
            'teachers' => $teachers,
            'jurnals' => $jurnals,
            'teacherStatus' => $teacherStatus,
            'unreported' => DB::table('unreported_classes')
                ->whereDate('date', $date)
                ->where('reported', false)
                ->get(),
        ];

        return view('humas.laporan', $data);
    }
}