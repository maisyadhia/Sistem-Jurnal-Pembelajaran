<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Show timeline dashboard
     */
    public function timeline(Request $request)
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

        // 2. Tentukan batasan tanggal berdasarkan filter tombol woy
        $filter = $request->query('filter', '1_minggu'); // Default filter 1 minggu
        $startDate = null;

        if ($filter === 'hari_ini') {
            $startDate = Carbon::today()->toDateString();
        } elseif ($filter === '1_minggu') {
            $startDate = Carbon::now()->subWeek()->toDateString();
        } elseif ($filter === '1_bulan') {
            $startDate = Carbon::now()->subMonth()->toDateString();
        }

        // 3. Ambil entri jurnal, join tabel mapel_master & guru
        $query = DB::table('jurnals')
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
            ->whereNotNull('jurnals.student_ids');

        // Jika filter aktif, batasi tanggalnya woy
        if ($startDate) {
            $query->whereDate('jurnals.tanggal', '>=', $startDate);
        }

        $jurnalEntries = $query->orderByDesc('jurnals.tanggal')
            ->orderByDesc('jurnals.jam_ke')
            ->get();

        $activities = collect();
        $notes = collect(); // 💡 Diubah jadi collection untuk menampung semua catatan guru

        // 4. Looping mencari data anak di dalam JSON student_ids
        foreach ($jurnalEntries as $jurnal) {
            $studentsData = json_decode($jurnal->student_ids, true);

            if (!is_array($studentsData)) {
                continue;
            }

            foreach ($studentsData as $data) {
                if (($data['student_id'] ?? null) == $studentId) {
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

                    // 💡 Masukkan semua catatan guru yang tidak kosong ke dalam list notes woy!
                    if (!empty($item->catatan)) {
                        $notes->push((object) [
                            'content' => $item->catatan,
                            'teacher' => $item->guru,
                            'mapel'   => $item->mapel,
                            'tanggal' => $item->tanggal,
                        ]);
                    }

                    break; 
                }
            }
        }

        return view('dashboard.timeline', [
            'student'    => $student,
            'activities' => $activities->take(10),
            'notes'      => $notes, // Kirim semua list catatan
            'currentFilter' => $filter // Untuk menandai tombol mana yang aktif di Blade
        ]);
    }
}