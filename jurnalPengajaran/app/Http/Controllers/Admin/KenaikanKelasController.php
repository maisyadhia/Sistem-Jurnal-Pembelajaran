<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsAdminActivity;
use App\Models\Student;
use App\Models\StudentClassHistory;
use App\Models\KelasMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\NilaiAsat;

class KenaikanKelasController extends Controller
{
    use LogsAdminActivity;

    public function index(Request $request)
    {
        // 1. Ambil default dari config (env)
        $tahunAjaranAktif = $request->get('tahun_ajaran_aktif', config('tahun_ajaran.aktif'));
        $tahunAjaranBaru = $request->get('tahun_ajaran_baru', $this->getTahunBerikutnya($tahunAjaranAktif));

        // 🔥 2. AMBIL LIST TAHUN AJARAN DARI DATABASE (KELAS_MASTER)
        $listTahunAjaran = KelasMaster::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'asc')
            ->pluck('tahun_ajaran')
            ->toArray();

        // Antisipasi jika tabel masih kosong atau tahun aktif belum ada di DB
        if (!in_array($tahunAjaranAktif, $listTahunAjaran)) {
            $listTahunAjaran[] = $tahunAjaranAktif;
        }
        if (!in_array($tahunAjaranBaru, $listTahunAjaran)) {
            $listTahunAjaran[] = $tahunAjaranBaru;
        }
        
        // Urutkan ulang dari tahun terlama ke terbaru
        sort($listTahunAjaran);

        $siswaAktif = Student::where('tahun_ajaran', $tahunAjaranAktif)
            ->where('status', 'aktif')
            ->orderBy('class')
            ->orderBy('name')
            ->get();

        $siswaLulus = Student::where('tahun_ajaran', $tahunAjaranAktif)
            ->where('status', 'lulus')
            ->orderBy('class')
            ->orderBy('name')
            ->get();

        $siswaPindah = Student::where('tahun_ajaran', $tahunAjaranAktif)
            ->where('status', 'pindah')
            ->orderBy('class')
            ->orderBy('name')
            ->get();

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

        $riwayatKenaikan = StudentClassHistory::with('student')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        // Jangan lupa kirim $listTahunAjaran ke view
        return view('admin.kenaikan-kelas.index', compact(
            'siswaAktif',
            'siswaLulus',
            'siswaPindah',
            'siswaTinggal',
            'daftarKelas',
            'tahunAjaranAktif',
            'tahunAjaranBaru',
            'totalSiswa',
            'riwayatKenaikan',
            'listTahunAjaran' 
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
            'mapping' => 'required|array',
            'mapping.*.limit' => 'required|integer|min:1',
            'mapping.*.kelas' => 'required|string',
        ]);

        $tahunAjaranAktif = config('tahun_ajaran.aktif');
        $tahunAjaranBaru = $request->tahun_ajaran_baru;
        
        // 1. Ambil siswa aktif
        $siswa = Student::where('tahun_ajaran', $tahunAjaranAktif)
            ->where('status', 'aktif')
            ->get();

        if ($siswa->isEmpty()) {
            return back()->with('error', '❌ Tidak ada siswa aktif');
        }

        // 2. Ambil nilai ASAT
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
            // 3. Kelompokkan per tingkat
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

            // Proses tiap tingkat
            foreach ($siswaPerTingkat as $tingkat => $daftarSiswa) {
                // Urutkan siswa dari nilai tertinggi ke terendah
                usort($daftarSiswa, function($a, $b) use ($nilaiMap) {
                    $nilaiA = (float) ($nilaiMap[$a->id] ?? 0);
                    $nilaiB = (float) ($nilaiMap[$b->id] ?? 0);
                    return $nilaiB <=> $nilaiA; // Descending
                });

                // 4. Proses Mapping Custom dari Admin
                $indexSiswa = 0;
                $totalSiswa = count($daftarSiswa);
                
                // Simpan nama kelas terakhir untuk menampung sisa
                $kelasPenampungSisa = $tingkat . 'A'; 

                foreach ($request->mapping as $map) {
                    $limit = (int) $map['limit'];
                    $namaKelas = $map['kelas'];
                    $kelasPenampungSisa = $namaKelas; // Update penampung terakhir

                    // Ambil siswa sebanyak limit
                    $siswaSlice = array_slice($daftarSiswa, $indexSiswa, $limit);
                    
                    foreach ($siswaSlice as $s) {
                        $s->update(['class' => $namaKelas, 'tahun_ajaran' => $tahunAjaranBaru]);
                    }

                    $indexSiswa += $limit;
                }

                // 5. Handle Sisa Siswa (Jika ada yang belum dapat kelas)
                if ($indexSiswa < $totalSiswa) {
                    $sisaSiswa = array_slice($daftarSiswa, $indexSiswa);
                    foreach ($sisaSiswa as $s) {
                        $s->update(['class' => $kelasPenampungSisa, 'tahun_ajaran' => $tahunAjaranBaru]);
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

        return back()->with('success', "✅ {$siswa->count()} siswa berhasil dibagi berdasarkan nilai ASAT sesuai pemetaan kelas.");
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