<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class HariLiburController extends Controller
{
    public function index()
    {
        // Urutkan berdasarkan tanggal
        $hariLibur = DB::table('hari_libur')->orderBy('tanggal', 'asc')->get();
        return view('admin.hari-libur.index', compact('hariLibur'));
    }

    public function sync(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $apiUrl = "https://api-harilibur.pages.dev/api?year={$tahun}";

        try {
            $response = Http::timeout(10)->get($apiUrl);

            if ($response->failed()) {
                return back()->with('error', 'Gagal mengambil data dari API hari libur.');
            }

            $data = $response->json();
            $inserted = 0;

            foreach ($data as $item) {
                $tanggalRaw = $item['holiday_date'] ?? null;
                $keterangan = $item['holiday_name'] ?? 'Libur Nasional';

                if (!$tanggalRaw) continue;

                // Format tanggal ke Y-m-d
                $tanggal = \Carbon\Carbon::parse($tanggalRaw)->format('Y-m-d');

                // Cek apakah tanggal sudah ada
                $exists = DB::table('hari_libur')->where('tanggal', $tanggal)->exists();

                if (!$exists) {
                    DB::table('hari_libur')->insert([
                        'tanggal' => $tanggal,
                        'keterangan' => $keterangan,
                        'is_nasional' => true, 
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $inserted++;
                }
            }

            return back()->with('success', "✅ Berhasil sinkronisasi {$inserted} hari libur untuk tahun {$tahun}.");
        } catch (\Exception $e) {
            return back()->with('error', '❌ Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $libur = DB::table('hari_libur')->where('id', $id)->first();

        // 🔥 CEGAH HAPUS LIBUR NASIONAL
        if ($libur && $libur->is_nasional) {
            return back()->with('error', 'Libur Nasional tidak bisa dihapus manual. Silakan hapus melalui database jika mendesak.');
        }

        DB::table('hari_libur')->where('id', $id)->delete();
        return back()->with('success', '✅ Hari libur sekolah berhasil dihapus.');
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:hari_libur,tanggal',
            'keterangan' => 'required|string|max:255',
        ]);

        DB::table('hari_libur')->insert([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'is_nasional' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Hari libur sekolah berhasil ditambahkan!');
    }
}