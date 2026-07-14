<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show timeline dashboard
     */
    public function timeline()
    {
        // 1. Ambil data siswa berdasarkan session login wali murid
        $studentId = session('student_id', 1);
        $student = Student::find($studentId);

        if (!$student) {
            $student = (object) [
                'name'  => 'Aditya Pratama',
                'class' => 'Kelas XI - IPA 2'
            ];
        }

        // 2. Ambil seluruh entri jurnal, join tabel mapel_master & guru
        $jurnalEntries = DB::table('jurnals')
            ->join('mapel_master', 'jurnals.mapel_id', '=', 'mapel_master.id')
            ->join('guru', 'jurnals.guru_id', '=', 'guru.id')
            ->select(
                'jurnals.tanggal',
                'jurnals.jam_ke',
                'jurnals.materi',
                'jurnals.student_ids',
                'mapel_master.nama_mapel',
                'guru.nama_guru'
            )
            ->whereNotNull('jurnals.student_ids')
            ->orderByDesc('jurnals.tanggal')
            ->orderByDesc('jurnals.jam_ke')
            ->get();

        $activities = collect();
        $note = null;

        // 3. Looping untuk mencari apakah ada data anak ini di dalam baris-baris jurnal
        foreach ($jurnalEntries as $jurnal) {
            $studentsData = json_decode($jurnal->student_ids, true);

            if (!is_array($studentsData)) {
                continue;
            }

            foreach ($studentsData as $data) {
                if (($data['student_id'] ?? null) == $studentId) {
                    // Bungkus datanya agar seragam dengan format view
                    $item = (object) [
                        'mapel'   => $jurnal->nama_mapel,
                        'guru'    => $jurnal->nama_guru,
                        'tanggal' => $jurnal->tanggal,
                        'jam_ke'  => $jurnal->jam_ke,
                        'materi'  => $jurnal->materi,
                        'status'  => $data['status'] ?? 'hadir',
                        'catatan' => $data['catatan'] ?? null,
                    ];

                    $activities->push($item);

                    // Catatan wali kelas paling baru yang benar-benar berisi teks
                    if (!$note && !empty($item->catatan)) {
                        $note = (object) [
                            'content' => $item->catatan,
                            'teacher' => $item->guru,
                        ];
                    }

                    break; // Stop pencarian di jurnal ini, lanjut ke jurnal berikutnya
                }
            }
        }

        return view('dashboard.timeline', [
            'student'    => $student,
            'activities' => $activities->take(10),
            'note'       => $note,
        ]);
    }
}