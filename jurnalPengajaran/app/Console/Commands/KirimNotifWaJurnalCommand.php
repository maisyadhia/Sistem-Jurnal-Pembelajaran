<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KirimNotifWaJurnalCommand extends Command
{
    protected $signature = 'jurnal:kirim-wa';
    protected $description = 'Kirim 1 pesan rekap WhatsApp per hari per guru berisi semua tanggungan jurnal yang belum dikerjakan';

    public function handle(): int
    {
        date_default_timezone_set('Asia/Jakarta');
        Carbon::setLocale('id');

        $now = Carbon::now('Asia/Jakarta');
        $tanggalHariIni = $now->toDateString();
        $namaHariIni = $now->translatedFormat('l');
        $tanggalTampil = $now->translatedFormat('d F Y');

        // 1. Cek akhir pekan (Sabtu = 6, Minggu = 0)
        if (in_array($now->dayOfWeek, [0, 6])) {
            $this->info("Lewati: Hari ini akhir pekan ({$namaHariIni}).");
            return self::SUCCESS;
        }

        // 2. Cek tabel hari_libur
        $isHariLibur = DB::table('hari_libur')
            ->whereDate('tanggal', $tanggalHariIni)
            ->exists();

        if ($isHariLibur) {
            $this->info("Lewati: Tanggal {$tanggalHariIni} terdaftar di tabel hari_libur.");
            return self::SUCCESS;
        }

        // 3. Cek token Fonnte
        $token = config('services.fonnte.token') ?? env('FONNTE_TOKEN');
        if (empty($token)) {
            $this->error('FONNTE_TOKEN belum diatur di file .env.');
            return self::FAILURE;
        }

        // 4. Ambil semua notifikasi belum dikerjakan (is_read = 0) beserta data guru
        $notifs = DB::table('notifications')
            ->join('guru', 'notifications.user_id', '=', 'guru.id')
            ->where('notifications.is_read', 0)
            ->whereNotNull('guru.no_hp')
            ->where('guru.no_hp', '!=', '')
            ->select(
                'notifications.id as notif_id',
                'notifications.user_id',
                'notifications.message',
                'notifications.created_at as notif_created_at',
                'guru.nama_guru',
                'guru.no_hp'
            )
            ->orderBy('notifications.created_at', 'asc')
            ->get();

        if ($notifs->isEmpty()) {
            $this->info('Tidak ada tanggungan jurnal yang belum dikerjakan (is_read = 0) dengan nomor WA valid.');
            return self::SUCCESS;
        }

        // 5. Kelompokkan per guru (1 guru = 1 pesan per hari)
        $notifPerGuru = $notifs->groupBy('user_id');

        foreach ($notifPerGuru as $guruId => $daftarNotifGuru) {
            $guru = $daftarNotifGuru->first();
            $targetWa = $this->formatNomorWa($guru->no_hp);

            if (!$targetWa) {
                $this->warn("Nomor WA tidak valid untuk guru {$guru->nama_guru} ({$guru->no_hp}).");
                continue;
            }

            $listItem = [];
            foreach ($daftarNotifGuru as $index => $item) {
                $no = $index + 1;
                $tglNotif = Carbon::parse($item->notif_created_at, 'Asia/Jakarta')->translatedFormat('d M Y');
                $listItem[] = "{$no}. [{$tglNotif}] {$item->message}";
            }

            $rincianTanggungan = implode("\n", $listItem);
            $jumlahTanggungan = $daftarNotifGuru->count();
            $linkWeb = 'https://simjarmin2malang.my.id/login';

            // === FORMAT PESAN WHATSAPP (1 PESAN MERANGKAP SEMUA TANGGUNGAN) ===
            $pesan = "Assalamu'alaikum *{$guru->nama_guru}*,\n"
                . "Rekap pengingat per *{$namaHariIni}, {$tanggalTampil}*: Anda masih memiliki *{$jumlahTanggungan}* tanggungan jurnal yang belum dikerjakan:\n\n"
                . "{$rincianTanggungan}\n\n"
                . "Silakan lengkapi jurnal melalui tautan berikut:\n"
                . "{$linkWeb}\n\n"
                . "Terima kasih.";

            try {
                $response = Http::withHeaders([
                    'Authorization' => $token,
                ])->asForm()->post('https://api.fonnte.com/send', [
                    'target'  => $targetWa,
                    'message' => $pesan,
                ]);

                if ($response->successful()) {
                    $this->info("1 pesan rekap ({$jumlahTanggungan} tanggungan) terkirim ke {$guru->nama_guru} ({$targetWa})");
                } else {
                    $this->error("Gagal kirim WA ke {$targetWa}: " . $response->body());
                    Log::warning('Fonnte gagal kirim', [
                        'guru_id'  => $guruId,
                        'target'   => $targetWa,
                        'response' => $response->body(),
                    ]);
                }
            } catch (\Throwable $e) {
                $this->error("Error koneksi Fonnte untuk {$targetWa}: " . $e->getMessage());
                Log::error('Fonnte exception', [
                    'guru_id' => $guruId,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        return self::SUCCESS;
    }

    private function formatNomorWa(?string $noHp): ?string
    {
        if (!$noHp) {
            return null;
        }

        $nomor = preg_replace('/[^0-9]/', '', $noHp);
        if (empty($nomor)) {
            return null;
        }

        if (str_starts_with($nomor, '08')) {
            $nomor = '628' . substr($nomor, 2);
        } elseif (str_starts_with($nomor, '8')) {
            $nomor = '62' . $nomor;
        }

        return $nomor;
    }
}