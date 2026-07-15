<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnreportedClass;
use App\Models\DataMaster;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;

class HumasMonitoringController extends Controller
{
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
        // Ambil dari tabel guru (bukan admin)
        $totalTeachers = DB::table('guru')->count();
        if ($totalTeachers == 0) return 0;

        // Hitung jurnal hari ini dari tabel jurnals
        $todayJurnals = DB::table('jurnals')
            ->whereDate('tanggal', today())
            ->count();
            
        return round(($todayJurnals / $totalTeachers) * 100, 1);
    }

    private function getOnTimeCount()
    {
        // Misal: jurnal yang dibuat sebelum jam 12 siang
        return DB::table('jurnals')
            ->whereDate('tanggal', today())
            ->whereTime('created_at', '<=', '12:00:00')
            ->count();
    }

    private function getLateCount()
    {
        // Misal: jurnal yang dibuat setelah jam 12 siang
        return DB::table('jurnals')
            ->whereDate('tanggal', today())
            ->whereTime('created_at', '>', '12:00:00')
            ->count();
    }

    public function remindTeacher(Request $request)
    {
        $request->validate([
            'teacher' => 'required|string',
            'class' => 'required|string',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pengingat berhasil dikirim ke {$request->teacher} untuk kelas {$request->class}"
        ]);
    }

    public function exportReport(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $period = $request->get('period', 'weekly');

        $data = [
            'title' => 'Laporan Kepatuhan Guru',
            'date' => $date,
            'period' => $period,
            'complianceRate' => $this->calculateComplianceRate(),
            'onTimeCount' => $this->getOnTimeCount(),
            'lateCount' => $this->getLateCount(),
            'teachers' => DB::table('guru')->get(),
            'jurnals' => DB::table('jurnals')->whereDate('tanggal', $date)->get(),
            'unreported' => UnreportedClass::where('date', $date)->where('reported', false)->get(),
        ];

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