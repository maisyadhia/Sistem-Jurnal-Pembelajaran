<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    use LogsAdminActivity;

    // Mapping jam ke waktu (HANYA JAM YANG DIISI)
    private $jamMapping = [
        0 => ['mulai' => '06:30', 'selesai' => '07:00'],
        1 => ['mulai' => '07:00', 'selesai' => '07:35'],
        2 => ['mulai' => '07:35', 'selesai' => '08:10'],
        3 => ['mulai' => '08:10', 'selesai' => '08:45'],
        4 => ['mulai' => '08:45', 'selesai' => '09:20'],
        // 09:20 - 09:35 ISHOMA (tidak masuk jam pelajaran)
        5 => ['mulai' => '09:35', 'selesai' => '10:10'],
        6 => ['mulai' => '10:10', 'selesai' => '10:45'],
        7 => ['mulai' => '10:45', 'selesai' => '11:20'],
        // 11:20 - 11:35 ISHOMA (tidak masuk jam pelajaran)
        8 => ['mulai' => '11:35', 'selesai' => '12:10'],
        // 12:10 - 12:45 Sholat Dhuhur Berjamaah (tidak masuk jam pelajaran)
        9 => ['mulai' => '12:45', 'selesai' => '13:20'],
        10 => ['mulai' => '13:20', 'selesai' => '13:55'],
    ];

    // Label jam untuk ditampilkan
    private $jamLabel = [
        0 => 'Jam 0 ',
        1 => 'Jam 1 ',
        2 => 'Jam 2 ',
        3 => 'Jam 3 ',
        4 => 'Jam 4 ',
        5 => 'Jam 5 ',
        6 => 'Jam 6 ',
        7 => 'Jam 7 ',
        8 => 'Jam 8 ',
        9 => 'Jam 9 ',
        10 => 'Jam 10 ',
    ];

    public function index()
    {
        $jadwal = DB::table('jadwals')
            ->join('guru', 'jadwals.guru_id', '=', 'guru.id')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->select(
                'jadwals.*',
                'guru.nama_guru',
                'kelas_master.nama_kelas',
                'mapel_master.nama_mapel'
            )
            ->orderBy('jadwals.hari')
            ->orderBy('jadwals.jam_ke')
            ->get();

        $groupedJadwal = $jadwal->groupBy(function($item) {
            return $item->hari . '|' . $item->guru_id . '|' . $item->kelas_id . '|' . $item->mapel_id;
        });

        return view('admin.data-master.jadwal', compact('groupedJadwal'));
    }

    public function create()
    {
        $guru = DB::table('guru')->orderBy('nama_guru')->get();
        $kelas = DB::table('kelas_master')->orderBy('nama_kelas')->get();
        $mapel = DB::table('mapel_master')->orderBy('nama_mapel')->get();
        
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jamKe = range(0, 10);
        $jamMapping = $this->jamMapping;
        $jamLabel = $this->jamLabel;
        
        return view('admin.data-master.jadwal-create', compact('guru', 'kelas', 'mapel', 'hari', 'jamKe', 'jamMapping', 'jamLabel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'kelas_id' => 'required|exists:kelas_master,id',
            'mapel_id' => 'required|exists:mapel_master,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_ke' => 'required|array|min:1',
            'jam_ke.*' => 'integer|min:0|max:10',
        ]);

        $inserted = 0;
        $duplicateErrors = [];

        foreach ($request->jam_ke as $jam) {
            // Cek duplikasi
            $exists = DB::table('jadwals')
                ->where('guru_id', $request->guru_id)
                ->where('hari', $request->hari)
                ->where('jam_ke', $jam)
                ->exists();

            if ($exists) {
                $duplicateErrors[] = "Jam ke-{$jam} sudah terisi untuk guru ini di hari {$request->hari}";
                continue;
            }

            // Ambil waktu dari mapping
            $waktu = $this->jamMapping[$jam] ?? ['mulai' => '00:00', 'selesai' => '00:00'];

            DB::table('jadwals')->insert([
                'guru_id' => $request->guru_id,
                'kelas_id' => $request->kelas_id,
                'mapel_id' => $request->mapel_id,
                'hari' => $request->hari,
                'jam_ke' => $jam,
                'jam_mulai' => $waktu['mulai'],
                'jam_selesai' => $waktu['selesai'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $inserted++;
        }

        if ($inserted === 0) {
            return back()->withErrors(['jam_ke' => implode(', ', $duplicateErrors)])->withInput();
        }

        $this->logActivity(
            'create',
            'jadwal',
            "Menambahkan {$inserted} jadwal baru",
            null,
            $request->all()
        );

        $message = "{$inserted} jadwal berhasil ditambahkan!";
        if (!empty($duplicateErrors)) {
            $message .= " (" . implode(', ', $duplicateErrors) . ")";
        }

        return redirect()->route('data-master.jadwal')
            ->with('success', $message);
    }

    public function edit($id)
    {
        $jadwal = DB::table('jadwals')
            ->join('guru', 'jadwals.guru_id', '=', 'guru.id')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->select(
                'jadwals.*',
                'guru.nama_guru',
                'kelas_master.nama_kelas',
                'mapel_master.nama_mapel'
            )
            ->where('jadwals.id', $id)
            ->first();

        if (!$jadwal) {
            return redirect()->route('data-master.jadwal')
                ->with('error', 'Jadwal tidak ditemukan!');
        }

        $jadwalGroup = DB::table('jadwals')
            ->where('guru_id', $jadwal->guru_id)
            ->where('kelas_id', $jadwal->kelas_id)
            ->where('mapel_id', $jadwal->mapel_id)
            ->where('hari', $jadwal->hari)
            ->get();

        $jamKeList = $jadwalGroup->pluck('jam_ke')->toArray();

        $guru = DB::table('guru')->orderBy('nama_guru')->get();
        $kelas = DB::table('kelas_master')->orderBy('nama_kelas')->get();
        $mapel = DB::table('mapel_master')->orderBy('nama_mapel')->get();
        
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jamKe = range(0, 10);
        $jamMapping = $this->jamMapping;
        $jamLabel = $this->jamLabel;
        
        return view('admin.data-master.jadwal-edit', compact('jadwal', 'jadwalGroup', 'jamKeList', 'guru', 'kelas', 'mapel', 'hari', 'jamKe', 'jamMapping', 'jamLabel'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'kelas_id' => 'required|exists:kelas_master,id',
            'mapel_id' => 'required|exists:mapel_master,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_ke' => 'required|array|min:1',
            'jam_ke.*' => 'integer|min:0|max:10',
        ]);

        $oldJadwal = DB::table('jadwals')
            ->join('guru', 'jadwals.guru_id', '=', 'guru.id')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->select('jadwals.*', 'guru.nama_guru', 'kelas_master.nama_kelas', 'mapel_master.nama_mapel')
            ->where('jadwals.id', $id)
            ->first();

        if (!$oldJadwal) {
            return redirect()->route('data-master.jadwal')
                ->with('error', 'Jadwal tidak ditemukan!');
        }

        DB::table('jadwals')
            ->where('guru_id', $oldJadwal->guru_id)
            ->where('kelas_id', $oldJadwal->kelas_id)
            ->where('mapel_id', $oldJadwal->mapel_id)
            ->where('hari', $oldJadwal->hari)
            ->delete();

        $inserted = 0;
        foreach ($request->jam_ke as $jam) {
            $waktu = $this->jamMapping[$jam] ?? ['mulai' => '00:00', 'selesai' => '00:00'];

            DB::table('jadwals')->insert([
                'guru_id' => $request->guru_id,
                'kelas_id' => $request->kelas_id,
                'mapel_id' => $request->mapel_id,
                'hari' => $request->hari,
                'jam_ke' => $jam,
                'jam_mulai' => $waktu['mulai'],
                'jam_selesai' => $waktu['selesai'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $inserted++;
        }

        $this->logActivity(
            'update',
            'jadwal',
            "Mengupdate {$inserted} jadwal",
            $oldJadwal,
            $request->all()
        );

        return redirect()->route('data-master.jadwal')
            ->with('success', "{$inserted} jadwal berhasil diupdate!");
    }

    public function destroy($id)
    {
        $jadwal = DB::table('jadwals')->where('id', $id)->first();
        
        if (!$jadwal) {
            return redirect()->route('data-master.jadwal')
                ->with('error', 'Jadwal tidak ditemukan!');
        }

        $deleted = DB::table('jadwals')
            ->where('guru_id', $jadwal->guru_id)
            ->where('kelas_id', $jadwal->kelas_id)
            ->where('mapel_id', $jadwal->mapel_id)
            ->where('hari', $jadwal->hari)
            ->delete();

        $this->logActivity(
            'delete',
            'jadwal',
            "Menghapus {$deleted} jadwal",
            $jadwal,
            null
        );

        return redirect()->route('data-master.jadwal')
            ->with('success', "{$deleted} jadwal berhasil dihapus!");
    }
}