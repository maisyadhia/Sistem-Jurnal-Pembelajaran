<?php

namespace App\Http\Controllers;

use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use App\Models\UnreportedClass;
use App\Models\DataMaster;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;

class HumasMonitoringController extends Controller
{
    use LogsAdminActivity;

    public function index()
    {
        $complianceRate = $this->calculateComplianceRate();
        $onTimeCount = $this->getOnTimeCount();
        $lateCount = $this->getLateCount();
        $complianceIncrease = '2.4';

        $unreportedClasses = UnreportedClass::where('date', today())
                                           ->where('reported', false)
                                           ->get();

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
            ]);

            // Log aktivitas remind
            $this->logActivity(
                'remind',
                'monitoring',
                "Mengirim pengingat ke guru {$request->teacher} untuk kelas {$request->class}",
                null,
                ['teacher' => $request->teacher, 'class' => $request->class]
            );

            // Cari guru di database
            $guru = DB::table('guru')
                ->where('nama_guru', 'like', '%' . $request->teacher . '%')
                ->first();

            if (!$guru) {
                $guru = DB::table('admins')
                    ->where('name', 'like', '%' . $request->teacher . '%')
                    ->first();
            }

            // Simpan ke tabel notifications
            if ($guru) {
                DB::table('notifications')->insert([
                    'user_id' => $guru->id,
                    'user_type' => 'guru',
                    'message' => "Pengingat: Kelas {$request->class} belum mengisi jurnal hari ini.",
                    'link' => route('guru.pilih.sesi'),
                    'is_read' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            \Illuminate\Support\Facades\Log::info('Reminder sent', [
                'teacher' => $request->teacher,
                'class' => $request->class,
                'guru_id' => $guru->id ?? null,
                'sent_by' => session('user_name'),
                'sent_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Pengingat berhasil dikirim ke {$request->teacher} untuk kelas {$request->class}",
                'guru_found' => $guru ? true : false
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Reminder failed: ' . $e->getMessage());
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

        $teachers = DB::table('guru')->get();
        $jurnals = DB::table('jurnals')
            ->whereDate('tanggal', $date)
            ->get();

        $data = [
            'title' => 'Laporan Kepatuhan Guru',
            'date' => $date,
            'period' => $period,
            'complianceRate' => $this->calculateComplianceRate(),
            'onTimeCount' => $this->getOnTimeCount(),
            'lateCount' => $this->getLateCount(),
            'teachers' => $teachers,
            'jurnals' => $jurnals,
            'unreported' => UnreportedClass::where('date', $date)->where('reported', false)->get(),
        ];

        // Log aktivitas export
        $this->logActivity(
            'export',
            'report',
            "Mengekspor laporan kepatuhan periode {$period} tanggal {$date}",
            null,
            ['date' => $date, 'period' => $period, 'format' => $request->get('format', 'csv')]
        );

        if ($request->get('format') === 'pdf') {
            return view('humas.report-pdf', $data);
        }

        return $this->exportExcel($data);
    }

    private function exportExcel($data)
    {
        $filename = "laporan_kepatuhan_{$data['date']}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $handle = fopen('php://output', 'w');
            
            fputcsv($handle, ['LAPORAN KEPATUHAN GURU']);
            fputcsv($handle, ['Tanggal', $data['date']]);
            fputcsv($handle, ['Periode', $data['period']]);
            fputcsv($handle, ['']);
            fputcsv($handle, ['Tingkat Kepatuhan', $data['complianceRate'] . '%']);
            fputcsv($handle, ['Tepat Waktu', $data['onTimeCount']]);
            fputcsv($handle, ['Terlambat', $data['lateCount']]);
            fputcsv($handle, ['']);
            
            fputcsv($handle, ['Daftar Guru']);
            fputcsv($handle, ['Nama', 'Status']);
            foreach ($data['teachers'] as $teacher) {
                $status = 'Belum Mengisi';
                foreach ($data['jurnals'] as $jurnal) {
                    if ($jurnal->guru_id == $teacher->id) {
                        $status = 'Sudah Mengisi';
                        break;
                    }
                }
                fputcsv($handle, [$teacher->nama_guru, $status]);
            }
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}