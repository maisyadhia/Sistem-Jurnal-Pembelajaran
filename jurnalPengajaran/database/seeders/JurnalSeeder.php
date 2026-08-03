<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JurnalSeeder extends Seeder
{
    public function run(): void
    {
        // Mengunci ID Guru Drs. Suyanto, M.Pd (Kode: I)
        $guruId = DB::table('guru')->where('kode_guru', 'I')->value('id') ?? 2;
        $kelasId = DB::table('kelas_master')->where('nama_kelas', '5A')->value('id') ?? 1;

        // Mengambil ID Mapel Matematika & IPAS dari master database
        $mapelMat = DB::table('mapel_master')->where('kode_mapel', 'MAT')->value('id') ?? 1;

        // Ambil ID siswa dari database untuk pelengkap relasi absensi
        $siswa1 = DB::table('students')->where('name', 'Adi Hidayat')->value('id') ?? 3;
        $siswa2 = DB::table('students')->where('name', 'Mutiara Sari')->value('id') ?? 2;

        // Kosongkan tabel jurnals sebelum injeksi data dummy baru
        DB::table('jurnals')->truncate();

        DB::table('jurnals')->insert([

            [
                'guru_id' => $guruId,
                'kelas_id' => $kelasId,
                'mapel_id' => $mapelMat,
                'jam_ke' => 3,
                'materi' => 'Pengenalan Logaritma: Sifat-sifat dasar logaritma dan hubungannya dengan eksponen dasar.',
                'target_next' => 'Penerapan fungsi logaritma dalam contoh kasus perhitungan bunga majemuk.',
                'rpp_sesuai' => 1,
                'ada_absen' => 1,
                'student_ids' => json_encode([
                    ['student_id' => $siswa1, 'status' => 'Sakit', 'catatan' => 'Surat dokter menyusul '],
                    ['student_id' => $siswa2, 'status' => 'Hadir', 'catatan' => 'Sangat aktif merespon materi baru']
                ]),

                'tanggal' => '2026-07-13',
                'created_at' => Carbon::parse('2026-07-13 14:41:00'),
                'updated_at' => Carbon::parse('2026-07-13 14:41:00'),
            ],

            // ==========================================
            // 📅 JURNAL 2: MINGGU INI (2 Hari Lalu)
            // ==========================================
            [
                'guru_id' => $guruId,
                'kelas_id' => $kelasId,
                'mapel_id' => $mapelMat,
                'jam_ke' => 1,
                'materi' => 'Evaluasi Harian Matematika bab Fungsi Eksponen Aljabar.',
                'target_next' => 'Pembahasan serentak lembar jawaban kuis siswa kelas 5A.',
                'rpp_sesuai' => 1,
                'ada_absen' => 0,
                'student_ids' => json_encode([]),
                'tanggal' => '2026-07-15',
                'created_at' => Carbon::parse('2026-07-15 07:15:00'),
                'updated_at' => Carbon::parse('2026-07-15 07:15:00'),
            ],

            [
                'guru_id' => $guruId,
                'kelas_id' => $kelasId,
                'mapel_id' => $mapelMat,
                'jam_ke' => 3,
                'materi' => 'Pengenalan Logaritma: Sifat-sifat dasar logaritma dan hubungannya dengan eksponen dasar.',
                'target_next' => 'Penerapan fungsi logaritma dalam contoh kasus perhitungan bunga majemuk.',
                'rpp_sesuai' => 1,
                'ada_absen' => 1,
                'student_ids' => json_encode([
                    ['student_id' => $siswa1, 'status' => 'Alpha', 'catatan' => 'Tidak hadir tanpa kabar tertulis']
                ]),
                // 💡 6 Juli 2026 adalah hari Senin (Minggu lalu)
                'tanggal' => '2026-07-06',
                'created_at' => Carbon::parse('2026-07-06 08:20:00'),
                'updated_at' => Carbon::parse('2026-07-06 08:20:00'),
            ],

            [
                'guru_id' => $guruId,
                'kelas_id' => $kelasId,
                'mapel_id' => $mapelMat,
                'jam_ke' => 1,
                'materi' => 'Evaluasi Harian Matematika bab Fungsi Eksponen Aljabar.',
                'target_next' => 'Pembahasan serentak lembar jawaban kuis siswa kelas 5A.',
                'rpp_sesuai' => 1,
                'ada_absen' => 1,
                'student_ids' => json_encode([
                    ['student_id' => $siswa1, 'status' => 'Alpha', 'catatan' => 'Tidak hadir tanpa kabar tertulis']
                ]),
                'tanggal' => '2026-06-24',
                'created_at' => Carbon::parse('2026-06-24 08:20:00'),
                'updated_at' => Carbon::parse('2026-06-24 08:20:00'),
            ],
        ]);
    }
}