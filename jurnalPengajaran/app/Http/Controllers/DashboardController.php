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

        // 2. Tentukan batasan tanggal berdasarkan filter tombol (default: 'semua')
        $filter = $request->query('filter', 'semua');
        $startDate = null;

        if ($filter === 'hari_ini') {
            $startDate = Carbon::today()->toDateString();
        } elseif ($filter === '1_minggu') {
            $startDate = Carbon::now()->subWeek()->toDateString();
        } elseif ($filter === '1_bulan') {
            $startDate = Carbon::now()->subMonth()->toDateString();
        } elseif ($filter === 'semua') {
            $startDate = null;
        }

        // 3. Ambil entri jurnal, leftJoin tabel mapel_master & guru
        $query = DB::table('jurnals')
            ->leftJoin('mapel_master', 'jurnals.mapel_id', '=', 'mapel_master.id')
            ->leftJoin('guru', 'jurnals.guru_id', '=', 'guru.id')
            ->select(
                'jurnals.tanggal',
                'jurnals.jam_ke',
                'jurnals.materi',
                'jurnals.target_next',
                'jurnals.student_ids',
                'jurnals.guru_id',
                'jurnals.kelas_id',
                'jurnals.mapel_id',
                'mapel_master.nama_mapel',
                'guru.nama_guru'
            )
            ->whereNotNull('jurnals.student_ids');

        // Jika filter aktif, batasi tanggalnya
        if ($startDate) {
            $query->whereDate('jurnals.tanggal', '>=', $startDate);
        }

        $jurnalEntries = $query->orderByDesc('jurnals.tanggal')
            ->orderByDesc('jurnals.jam_ke')
            ->get();

        $activities = collect();
        $notes = collect();

        // 4. Looping mencari data anak di dalam JSON student_ids
        foreach ($jurnalEntries as $jurnal) {
            $studentsData = json_decode($jurnal->student_ids, true);

            if (!is_array($studentsData)) {
                continue;
            }

            foreach ($studentsData as $data) {
                if (($data['student_id'] ?? null) == $studentId) {
                    $rawStatus = isset($data['status']) ? strtolower(trim($data['status'])) : 'hadir';

                    Carbon::setLocale('id');
                    $hariJurnal = Carbon::parse($jurnal->tanggal)->translatedFormat('l');

                    $jamKeMatching = DB::table('jadwals')
                        ->where('guru_id', $jurnal->guru_id)
                        ->where('kelas_id', $jurnal->kelas_id)
                        ->where('mapel_id', $jurnal->mapel_id)
                        ->where('hari', $hariJurnal)
                        ->orderBy('jam_ke', 'asc')
                        ->pluck('jam_ke')
                        ->unique()
                        ->values();

                    $jamKeLabel = $jamKeMatching->isNotEmpty()
                        ? 'Jam ke ' . $jamKeMatching->implode(' & ')
                        : 'Jam ke-' . $jurnal->jam_ke;

                    $item = (object) [
                        'mapel'        => $jurnal->nama_mapel ?? 'Mapel',
                        'guru'         => $jurnal->nama_guru ?? 'Guru',
                        'tanggal'      => $jurnal->tanggal,
                        'jam_ke'       => $jurnal->jam_ke,
                        'jam_ke_label' => $jamKeLabel,
                        'materi'       => $jurnal->materi,
                        'target_next'  => $jurnal->target_next ?? null,
                        'status'       => $rawStatus,
                        'catatan'      => $data['catatan'] ?? null,
                    ];

                    $activities->push($item);

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

        // 5. Hitung persentase kehadiran dari data activities pada periode filter yang sama
        $totalRecords = $activities->count();
        $hadirCount   = $activities->where('status', 'hadir')->count();
        $sakitCount   = $activities->where('status', 'sakit')->count();
        $izinCount    = $activities->where('status', 'izin')->count();
        $alphaCount   = $activities->where('status', 'alpha')->count();

        $attendance = (object) [
            'percentage' => $totalRecords > 0 ? round(($hadirCount / $totalRecords) * 100) : null,
            'total'      => $totalRecords,
            'hadir'      => $hadirCount,
            'sakit'      => $sakitCount,
            'izin'       => $izinCount,
            'alpha'      => $alphaCount,
        ];

        return view('dashboard.timeline', [
            'student'       => $student,
            'activities'    => $activities->take(10),
            'notes'         => $notes,
            'attendance'    => $attendance,
            'currentFilter' => $filter
        ]);
    }
}