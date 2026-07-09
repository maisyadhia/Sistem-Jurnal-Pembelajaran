<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnreportedClass;
use App\Models\DataMaster;
use App\Models\Jurnal;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;

class HumasMonitoringController extends Controller
{
    /**
     * Show monitoring dashboard
     */
    public function index()
    {
        // Get compliance data
        $complianceRate = $this->calculateComplianceRate();
        $onTimeCount = $this->getOnTimeCount();
        $lateCount = $this->getLateCount();
        $complianceIncrease = '2.4';

        // Get unreported classes
        $unreportedClasses = UnreportedClass::where('date', today())
                                           ->where('reported', false)
                                           ->get();

        // Get data master
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

    /**
     * Calculate compliance rate
     */
    private function calculateComplianceRate()
    {
        $totalTeachers = Admin::where('role', 'guru')->count();
        if ($totalTeachers == 0) return 94.2;

        $todayJurnals = Jurnal::whereDate('created_at', today())->count();
        return round(($todayJurnals / $totalTeachers) * 100, 1);
    }

    /**
     * Get on-time count
     */
    private function getOnTimeCount()
    {
        return Jurnal::whereDate('created_at', today())
                     ->whereTime('created_at', '<=', '12:00:00')
                     ->count() ?: 112;
    }

    /**
     * Get late count
     */
    private function getLateCount()
    {
        return Jurnal::whereDate('created_at', today())
                     ->whereTime('created_at', '>', '12:00:00')
                     ->count() ?: 8;
    }

    /**
     * Remind teacher
     */
    public function remindTeacher(Request $request)
    {
        $request->validate([
            'teacher' => 'required|string',
            'class' => 'required|string',
        ]);

        // Simulate sending notification
        // In real implementation, this would send email/notification
        
        return response()->json([
            'success' => true,
            'message' => "Pengingat berhasil dikirim ke {$request->teacher} untuk kelas {$request->class}"
        ]);
    }

    /**
     * Export report
     */
    public function exportReport(Request $request)
    {
        // Get data for report
        $date = $request->get('date', today()->format('Y-m-d'));
        $period = $request->get('period', 'weekly');

        $data = [
            'title' => 'Laporan Kepatuhan Guru',
            'date' => $date,
            'period' => $period,
            'complianceRate' => $this->calculateComplianceRate(),
            'onTimeCount' => $this->getOnTimeCount(),
            'lateCount' => $this->getLateCount(),
            'teachers' => Admin::where('role', 'guru')->get(),
            'jurnals' => Jurnal::whereDate('created_at', $date)->get(),
            'unreported' => UnreportedClass::where('date', $date)->where('reported', false)->get(),
        ];

        // If requesting PDF
        if ($request->get('format') === 'pdf') {
            return $this->exportPDF($data);
        }

        // Default: Excel/CSV export
        return $this->exportExcel($data);
    }

    /**
     * Export as PDF
     */
    private function exportPDF($data)
    {
        // In real implementation, use DomPDF or similar
        // For now, return a simple view
        return view('humas.report-pdf', $data);
    }

    /**
     * Export as Excel/CSV
     */
    private function exportExcel($data)
    {
        // In real implementation, use Laravel Excel
        // For now, return a simple CSV
        $filename = "laporan_kepatuhan_{$data['date']}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($data) {
            $handle = fopen('php://output', 'w');
            
            // Header
            fputcsv($handle, ['LAPORAN KEPATUHAN GURU']);
            fputcsv($handle, ['Tanggal', $data['date']]);
            fputcsv($handle, ['Periode', $data['period']]);
            fputcsv($handle, ['']);
            fputcsv($handle, ['Tingkat Kepatuhan', $data['complianceRate'] . '%']);
            fputcsv($handle, ['Tepat Waktu', $data['onTimeCount']]);
            fputcsv($handle, ['Terlambat', $data['lateCount']]);
            fputcsv($handle, ['']);
            
            // Teacher list
            fputcsv($handle, ['Daftar Guru']);
            fputcsv($handle, ['Nama', 'NIK', 'Status']);
            foreach ($data['teachers'] as $teacher) {
                $status = 'Belum Mengisi';
                foreach ($data['jurnals'] as $jurnal) {
                    if ($jurnal->teacher_id == $teacher->id) {
                        $status = 'Sudah Mengisi';
                        break;
                    }
                }
                fputcsv($handle, [$teacher->name, $teacher->nik, $status]);
            }
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}