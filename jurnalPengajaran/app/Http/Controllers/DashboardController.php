<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Student;
use App\Models\Note;
use Illuminate\Support\Facades\DB; // Memanggil Query Builder Laravel

class DashboardController extends Controller
{
    /**
     * Show timeline dashboard
     */
    public function timeline()
    {
        // 1. Ambil data siswa berdasarkan session login wali murid
        $studentId = session('student_id', 1);
        $student = Student::with('activities', 'notes')->find($studentId);
        
        if (!$student) {
            $student = (object) [
                'name' => 'Aditya Pratama',
                'class' => 'Kelas XI - IPA 2'
            ];
        }

        // 2. Ambil data linimasa aktivitas umum siswa
        $activities = Activity::where('student_id', $studentId)
                              ->orderBy('date_time', 'desc')
                              ->take(10)
                              ->get();

        // 3. Ambil catatan spesifik dari kolom JSON jurnals
        $jurnalHariIni = DB::table('jurnals')
            ->join('mapel_master', 'jurnals.mapel_id', '=', 'mapel_master.id')
            ->select('jurnals.*', 'mapel_master.nama_mapel')
            ->whereNotNull('jurnals.student_ids')
            ->latest('jurnals.tanggal')
            ->get();

        $note = null;

        // Looping untuk mencari apakah ada data anak ini di dalam baris-baris jurnal
        foreach ($jurnalHariIni as $jurnal) {
            $studentsData = json_decode($jurnal->student_ids, true);
            if (is_array($studentsData)) {
                foreach ($studentsData as $data) {
                    if ($data['student_id'] == $studentId) {
                        // Jika ketemu, bungkus datanya agar seragam dengan format view lamamu
                        $note = (object) [
                            'nama_mapel' => $jurnal->nama_mapel,
                            'tanggal'    => $jurnal->tanggal,
                            'materi'     => $jurnal->materi,
                            'status'     => $data['status'],
                            'body'       => $data['catatan'] ?? 'Anak Anda mengikuti pembelajaran dengan baik hari ini.'
                        ];
                        break 2; // Stop pencarian kalau sudah ketemu yang terbaru
                    }
                }
            }
        }

        // 4. Sistem Penyelamat (Fallback): Jika tidak ada catatan baru, pakai database Note lama
        if (!$note) {
            $note = Note::where('student_id', $studentId)->latest()->first();
        }

        return view('dashboard.timeline', compact('student', 'activities', 'note'));
    }
}