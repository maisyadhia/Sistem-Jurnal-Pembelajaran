<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsAdminActivity;
use App\Models\Student;
use App\Models\StudentClassHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NilaiAsat;

class KenaikanKelasController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request)
    {
        $tahunAjaranAktif = config('tahun_ajaran.aktif');
        $tahunAjaranBaru = $request->get('tahun_ajaran_baru', $this->getTahunBerikutnya($tahunAjaranAktif));

        // Siswa aktif
        $siswaAktif = Student::where('tahun_ajaran', $tahunAjaranAktif)
            ->where('status', 'aktif')
            ->orderBy('class')
            ->orderBy('name')
            ->get();

        // 🔥 SISWA LULUS
        $siswaLulus = Student::where('tahun_ajaran', $tahunAjaranAktif)
            ->where('status', 'lulus')
            ->orderBy('class')
            ->orderBy('name')
            ->get();

        // 🔥 SISWA PINDAH
        $siswaPindah = Student::where('tahun_ajaran', $tahunAjaranAktif)
            ->where('status', 'pindah')
            ->orderBy('class')
            ->orderBy('name')
            ->get();

        // 🔥 SISWA TINGGAL KELAS
        $siswaTinggal = Student::where('tahun_ajaran', $tahunAjaranAktif)
            ->where('status', 'tinggal_kelas')
            ->orderBy('class')
            ->orderBy('name')
            ->get();

        // Daftar kelas baru
        $daftarKelas = DB::table('kelas_master')
            ->where('tahun_ajaran', $tahunAjaranBaru)
            ->where('is_aktif', true)
            ->orderBy('nama_kelas')
            ->get();

        if ($daftarKelas->isEmpty()) {
            $daftarKelas = DB::table('kelas_master')->orderBy('nama_kelas')->get();
        }

        $totalSiswa = $siswaAktif->count();

        // Riwayat kenaikan kelas
        $riwayatKenaikan = StudentClassHistory::with('student')
            ->orderBy('created_at', 'desc')
        ->paginate(20);

        return view('admin.kenaikan-kelas.index', compact(
            'siswaAktif',
            'siswaLulus',
            'siswaPindah',
            'siswaTinggal',
            'daftarKelas',
            'tahunAjaranAktif',
            'tahunAjaranBaru',
            'totalSiswa',
            'riwayatKenaikan'
        ));
    }

    public function proses(Request $request)
    {
        $request->validate([
            'tahun_ajaran_baru' => 'required|string',
            'siswa_ids' => 'required|array|min:1',
            'siswa_ids.*' => 'exists:students,id',
            'kelas_baru' => 'required|array',
        ]);

        $tahunBaru = $request->tahun_ajaran_baru;
        $tahunLama = config('tahun_ajaran.aktif');
        $berhasil = 0;
        $gagal = 0;

        DB::beginTransaction();
        try {
            foreach ($request->siswa_ids as $siswaId) {
                $kelasBaru = $request->kelas_baru[$siswaId] ?? null;
                if (!$kelasBaru) { $gagal++; continue; }

                $siswa = Student::find($siswaId);
                if (!$siswa) { $gagal++; continue; }

                StudentClassHistory::create([
                    'student_id' => $siswaId,
                    'tahun_ajaran' => $tahunLama,
                    'kelas' => $siswa->class,
                    'status' => 'naik',
                    'keterangan' => "Naik dari {$siswa->class} ke {$kelasBaru}",
                ]);

                $oldData = $siswa->toArray();
                $siswa->update([
                    'tahun_ajaran' => $tahunBaru,
                    'class' => $kelasBaru,
                    'status' => 'aktif',
                ]);

                $this->logActivity('update', 'siswa',
                    "Naik kelas: {$siswa->name} dari {$oldData['class']} ke {$kelasBaru}",
                    $oldData, ['kelas_baru' => $kelasBaru, 'tahun_ajaran' => $tahunBaru]
                );
                $berhasil++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Gagal: ' . $e->getMessage());
        }

        $msg = "✅ {$berhasil} siswa berhasil naik kelas ke tahun ajaran {$tahunBaru}";
        if ($gagal > 0) $msg .= " ({$gagal} gagal)";
        return redirect()->route('kenaikan-kelas.index')->with('success', $msg);
    }

    public function tinggalKelas($id)
    {
        $siswa = Student::find($id);
        if (!$siswa) return back()->with('error', '❌ Siswa tidak ditemukan');

        DB::beginTransaction();
        try {
            StudentClassHistory::create([
                'student_id' => $siswa->id,
                'tahun_ajaran' => $siswa->tahun_ajaran,
                'kelas' => $siswa->class,
                'status' => 'tinggal',
                'keterangan' => 'Tinggal kelas di ' . $siswa->class,
            ]);

            $oldData = $siswa->toArray();
            $siswa->update(['status' => 'tinggal_kelas']);

            $this->logActivity('update', 'siswa', "Siswa {$siswa->name} tinggal kelas", $oldData, ['status' => 'tinggal_kelas']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Gagal: ' . $e->getMessage());
        }

        return back()->with('success', "✅ {$siswa->name} ditandai tinggal kelas");
    }

    public function luluskan($id)
    {
        $siswa = Student::find($id);
        if (!$siswa) return back()->with('error', '❌ Siswa tidak ditemukan');

        DB::beginTransaction();
        try {
            StudentClassHistory::create([
                'student_id' => $siswa->id,
                'tahun_ajaran' => $siswa->tahun_ajaran,
                'kelas' => $siswa->class,
                'status' => 'lulus',
                'keterangan' => 'Lulus dari ' . $siswa->class,
            ]);

            $oldData = $siswa->toArray();
            $siswa->update(['status' => 'lulus', 'tanggal_keluar' => now()->toDateString()]);

            $this->logActivity('update', 'siswa', "Siswa {$siswa->name} lulus", $oldData, ['status' => 'lulus']);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Gagal: ' . $e->getMessage());
        }

        return back()->with('success', "✅ {$siswa->name} berhasil diluluskan");
    }

    public function pindah(Request $request, $id)
    {
        $request->validate(['sekolah_tujuan' => 'required|string|max:255']);

        $siswa = Student::find($id);
        if (!$siswa) return back()->with('error', '❌ Siswa tidak ditemukan');

        DB::beginTransaction();
        try {
            StudentClassHistory::create([
                'student_id' => $siswa->id,
                'tahun_ajaran' => $siswa->tahun_ajaran,
                'kelas' => $siswa->class,
                'status' => 'pindah',
                'keterangan' => 'Pindah ke ' . $request->sekolah_tujuan,
            ]);

            $oldData = $siswa->toArray();
            $siswa->update([
                'status' => 'pindah',
                'tanggal_keluar' => now()->toDateString(),
                'keterangan' => 'Pindah ke ' . $request->sekolah_tujuan,
            ]);

            $this->logActivity('update', 'siswa',
                "Siswa {$siswa->name} pindah ke {$request->sekolah_tujuan}",
                $oldData, ['status' => 'pindah', 'sekolah_tujuan' => $request->sekolah_tujuan]
            );
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Gagal: ' . $e->getMessage());
        }

        return back()->with('success', "✅ {$siswa->name} ditandai pindah");
    }

    public function bagiKelasBerdasarkanNilai(Request $request)
    {
        $request->validate([
            'tahun_ajaran_baru' => 'required|string',
            'jumlah_kelas' => 'required|integer|min:1|max:10',
        ]);

        $tahunAjaranAktif = config('tahun_ajaran.aktif');
        $tahunAjaranBaru = $request->tahun_ajaran_baru;
        $jumlahKelas = (int) $request->jumlah_kelas;

        // Ambil siswa aktif
        $siswa = Student::where('tahun_ajaran', $tahunAjaranAktif)
            ->where('status', 'aktif')
            ->get();

        if ($siswa->isEmpty()) {
            return back()->with('error', '❌ Tidak ada siswa aktif');
        }

        // Ambil nilai ASAT
        $nilaiMap = NilaiAsat::where('tahun_ajaran', $tahunAjaranAktif)
            ->pluck('nilai_total', 'student_id')
            ->toArray();

        // Cek siswa tanpa nilai
        $siswaTanpaNilai = $siswa->filter(function($s) use ($nilaiMap) {
            return !isset($nilaiMap[$s->id]) || $nilaiMap[$s->id] == 0;
        });

        if ($siswaTanpaNilai->count() > 0) {
            return back()->with('error', 
                "❌ {$siswaTanpaNilai->count()} siswa belum punya nilai ASAT"
            );
        }

        DB::beginTransaction();
        try {
            // Kelompokkan per tingkat
            $siswaPerTingkat = [];
            foreach ($siswa as $s) {
                preg_match('/^(\d+)/', $s->class, $matches);
                $tingkat = (int) ($matches[1] ?? 1);
                
                if ($tingkat >= 6) {
                    $s->update(['status' => 'lulus', 'tanggal_keluar' => now()->toDateString()]);
                    continue;
                }
                
                $tingkatBaru = $tingkat + 1;
                $siswaPerTingkat[$tingkatBaru][] = $s;
            }

            // Bagi per tingkat
            foreach ($siswaPerTingkat as $tingkat => $daftarSiswa) {
                // Buat nama kelas
                $daftarKelas = [];
                for ($i = 0; $i < $jumlahKelas; $i++) {
                    $daftarKelas[] = $tingkat . chr(65 + $i);
                }

                // Pisahkan L/P
                $siswaL = array_filter($daftarSiswa, fn($s) => ($s->gender ?? 'L') === 'L');
                $siswaP = array_filter($daftarSiswa, fn($s) => ($s->gender ?? 'P') === 'P');

                // Urutkan berdasarkan nilai (pakai array temporary, BUKAN properti model)
                usort($siswaL, function($a, $b) use ($nilaiMap) {
                    $nilaiA = (float) ($nilaiMap[$a->id] ?? 0);
                    $nilaiB = (float) ($nilaiMap[$b->id] ?? 0);
                    return $nilaiB <=> $nilaiA;
                });
                usort($siswaP, function($a, $b) use ($nilaiMap) {
                    $nilaiA = (float) ($nilaiMap[$a->id] ?? 0);
                    $nilaiB = (float) ($nilaiMap[$b->id] ?? 0);
                    return $nilaiB <=> $nilaiA;
                });

                $perGender = 15;

                // 6A, 6B, 6C
                $akademik = [];
                for ($i = 0; $i < 3; $i++) {
                    $akademik[$i]['L'] = array_slice($siswaL, $i * $perGender, $perGender);
                    $akademik[$i]['P'] = array_slice($siswaP, $i * $perGender, $perGender);
                }

                for ($i = 0; $i < 3; $i++) {
                    if (!isset($daftarKelas[$i])) break;
                    $namaKelas = $daftarKelas[$i];
                    $siswaKelas = array_merge($akademik[$i]['L'], $akademik[$i]['P']);
                    foreach ($siswaKelas as $s) {
                        $s->update(['class' => $namaKelas, 'tahun_ajaran' => $tahunAjaranBaru]);
                    }
                }

                // Sisa → 6D, 6E, 6F, 6G
                $kelasSisa = array_slice($daftarKelas, 3);
                
                if (count($kelasSisa) > 0) {
                    $sisaL = array_slice($siswaL, $perGender * 3);
                    $sisaP = array_slice($siswaP, $perGender * 3);

                    shuffle($sisaL);
                    shuffle($sisaP);

                    $jumlahKelasSisa = count($kelasSisa);

                    $idxL = 0;
                    foreach ($sisaL as $s) {
                        $kelasIdx = $idxL % $jumlahKelasSisa;
                        $s->update(['class' => $kelasSisa[$kelasIdx], 'tahun_ajaran' => $tahunAjaranBaru]);
                        $idxL++;
                    }

                    $idxP = 0;
                    foreach ($sisaP as $s) {
                        $kelasIdx = $idxP % $jumlahKelasSisa;
                        $s->update(['class' => $kelasSisa[$kelasIdx], 'tahun_ajaran' => $tahunAjaranBaru]);
                        $idxP++;
                    }
                }
            }

            $this->logActivity('update', 'siswa',
                "Pembagian kelas berdasarkan nilai ASAT: {$siswa->count()} siswa",
                null, ['tahun_ajaran' => $tahunAjaranBaru]
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Gagal: ' . $e->getMessage());
        }

        return back()->with('success', "✅ {$siswa->count()} siswa berhasil dibagi berdasarkan nilai ASAT");
    }

    private function getTahunBerikutnya($tahun)
    {
        $parts = explode('/', $tahun);
        if (count($parts) === 2) return ((int)$parts[0] + 1) . '/' . ((int)$parts[1] + 1);
        return '2027/2028';
    }

    public function batalkanStatus($id)
    {
        $siswa = Student::find($id);
        if (!$siswa) return back()->with('error', '❌ Siswa tidak ditemukan');

        DB::beginTransaction();
        try {
            $oldData = $siswa->toArray();
            $siswa->update([
                'status' => 'aktif',
                'tanggal_keluar' => null,
                'keterangan' => null,
            ]);

            $this->logActivity('update', 'siswa',
                "Batalkan status siswa: {$siswa->name}",
                $oldData, ['status' => 'aktif']
            );
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Gagal: ' . $e->getMessage());
        }

        return back()->with('success', "✅ Status {$siswa->name} dikembalikan ke AKTIF");
    }
}