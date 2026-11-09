<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\NilaiAsatImport;
use App\Models\Student;
use App\Models\NilaiAsat;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class NilaiAsatController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request)
    {
        $tahunAjaran = $request->get('tahun_ajaran', config('tahun_ajaran.aktif'));
        $kelas = $request->get('kelas');

        $query = NilaiAsat::with('student')
            ->where('tahun_ajaran', $tahunAjaran);

        if ($kelas) {
            $query->where('kelas', $kelas);
        }

        $nilaiAsat = $query->orderBy('kelas')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        $totalNilai = NilaiAsat::where('tahun_ajaran', $tahunAjaran)->count();
        $totalSiswa = Student::where('tahun_ajaran', $tahunAjaran)
            ->where('status', 'aktif')
            ->count();
        $belumInput = $totalSiswa - $totalNilai;

        $daftarKelas = DB::table('kelas_master')
            ->where('tahun_ajaran', $tahunAjaran)
            ->orderBy('nama_kelas')
            ->pluck('nama_kelas');

        return view('admin.nilai-asat.index', compact(
            'nilaiAsat',
            'tahunAjaran',
            'kelas',
            'daftarKelas',
            'totalNilai',
            'totalSiswa',
            'belumInput'
        ));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
            'tahun_ajaran' => 'required|string',
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu',
            'file.mimes' => 'File harus berformat .xlsx, .xls, atau .csv',
            'file.max' => 'Ukuran file maksimal 10MB',
        ]);

        try {
            $import = new NilaiAsatImport($request->tahun_ajaran);
            Excel::import($import, $request->file('file'));

            $this->logActivity(
                'import',
                'nilai_asat',
                "Import nilai ASAT: {$import->berhasil} berhasil, {$import->gagal} gagal",
                null,
                ['tahun_ajaran' => $request->tahun_ajaran]
            );

            $message = "✅ Import selesai: {$import->berhasil} berhasil";
            if ($import->gagal > 0) {
                $message .= ", {$import->gagal} gagal";
            }

            if (!empty($import->tidakDitemukan)) {
                $nisnList = implode(', ', array_slice($import->tidakDitemukan, 0, 5));
                $message .= " (NISN tidak ditemukan: {$nisnList}...)";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', '❌ Gagal import: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_nilai_asat.csv"',
        ];

        $callback = function() {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            
            fputcsv($handle, ['nisn', 'nama', 'kelas', 'rata_rata']);
            fputcsv($handle, ['1234567890', 'Aditya Pratama', '5A', '85.50']);
            fputcsv($handle, ['0112235566', 'Mutiara Sari', '5A', '90.00']);
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export(Request $request)
    {
        $tahunAjaran = $request->get('tahun_ajaran', config('tahun_ajaran.aktif'));

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="nilai_asat_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($tahunAjaran) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            
            fputcsv($handle, ['nisn', 'nama', 'kelas', 'rata_rata']);
            
            $nilai = NilaiAsat::with('student')
                ->where('tahun_ajaran', $tahunAjaran)
                ->orderBy('kelas')
                ->get();
                
            foreach ($nilai as $n) {
                fputcsv($handle, [
                    $n->student->nisn ?? '-',
                    $n->student->name ?? '-',
                    $n->kelas,
                    number_format((float) $n->nilai_total, 2, '.', ''),
                ]);
            }
            
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy($id)
    {
        $nilai = NilaiAsat::find($id);
        
        if (!$nilai) {
            return back()->with('error', '❌ Data tidak ditemukan');
        }

        $nilai->delete();

        return back()->with('success', '✅ Nilai ASAT berhasil dihapus');
    }
}