<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal_Mengajar_SIJAMPANG_{{ $tglDownload ?? \Carbon\Carbon::now('Asia/Jakarta')->format('d-m-Y') }}</title>
    <style>
        /* CETAKAN A4 LANDSCAPE */
        @page {
            size: A4 landscape;
            margin: 10mm 12mm 10mm 12mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            margin: 0;
            padding: 0;
            background-color: #fff;
            color: #000;
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.25;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 10px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .uppercase {
            text-transform: uppercase;
        }

        /* KOP SURAT PROPORSIONAL */
        .kop-container {
            position: relative;
            width: 100%;
            border-bottom: 2.5px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
            min-height: 75px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kop-logo {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .kop-logo img {
            width: 70px;
            height: auto;
            max-height: 80px;
            object-fit: contain;
            display: block;
        }

        .kop-text {
            text-align: center;
            width: 100%;
            padding-left: 80px;
            padding-right: 20px;
        }

        .kop-text .sekolah {
            font-size: 15pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .kop-text .alamat {
            font-size: 8.5pt;
            font-style: italic;
            color: #222;
        }

        .header-title {
            font-size: 12pt;
            margin: 10px 0 12px 0;
            letter-spacing: 0.5px;
            text-align: center;
        }

        /* METADATA HEADER */
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 4px;
            font-size: 9.5pt;
            vertical-align: top;
        }

        /* TABEL DATA */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000;
            padding: 6px 6px;
            font-size: 9pt;
            vertical-align: top;
        }

        .data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
            vertical-align: middle;
        }

        /* TTD AREA 2 KOLOM */
        .ttd-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            page-break-inside: avoid;
        }

        .ttd-table td {
            font-size: 10pt;
            vertical-align: top;
        }

        .ttd-box {
            min-height: 70px;
            height: 70px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin: 4px 0;
        }

        .img-ttd {
            max-height: 65px;
            max-width: 180px;
            width: auto;
            height: auto;
            object-fit: contain;
            display: inline-block;
        }

        /* FOOTER SIJAMPANG */
        .sijampang-footer {
            margin-top: 25px;
            padding-top: 4px;
            border-top: 1px solid #cbd5e1;
            width: 100%;
            font-size: 8pt;
            color: #475569;
            font-style: italic;
            font-family: Arial, sans-serif;
            page-break-inside: avoid;
        }

        /* RESPONSIVE MOBILE */
        @media screen and (max-width: 768px) {
            .container {
                padding: 5px;
            }

            .kop-container {
                flex-direction: column;
                min-height: auto;
                text-align: center;
                padding-bottom: 10px;
            }

            .kop-logo {
                position: static;
                transform: none;
                margin-bottom: 6px;
            }

            .kop-text {
                padding-left: 0;
                padding-right: 0;
            }

            .kop-text .sekolah {
                font-size: 12pt;
            }

            .kop-text .alamat {
                font-size: 7.5pt;
            }

            .meta-table td {
                font-size: 8.5pt;
            }

            .data-table th,
            .data-table td {
                font-size: 8pt;
                padding: 4px;
            }

            .overflow-mobile {
                overflow-x: auto;
            }

            .ttd-table td {
                display: block;
                width: 100% !important;
                margin-bottom: 20px;
            }
        }

        @media print {
            .no-print {
                display: none !important;
            }

            .container {
                padding: 0;
            }

            .img-ttd {
                display: inline-block !important;
                visibility: visible !important;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Panel Tombol Cetak -->
        <div class="no-print" style="margin-bottom: 12px; text-align: right;">
            <button onclick="triggerPrint()"
                style="padding: 9px 20px; background: #0d9488; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; font-family: sans-serif; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                🖨️ Cetak / Simpan Ke PDF
            </button>
        </div>

        <!-- KOP LEMBAGA -->
        <div class="kop-container">
            <div class="kop-logo">
                <img src="{{ asset('logoMIN2.png') }}"
                    onerror="this.onerror=null; this.src='{{ asset('images/logoMIN2.png') }}';" alt="Logo Sekolah" />
            </div>
            <div class="kop-text">
                <div class="sekolah uppercase">{{ $satuanPendidikan }}</div>
                <div class="alamat">Jl. Kemantren II No.26, Bandungrejosari, Kec. Sukun, Kota Malang, Jawa Timur 65148
                </div>
            </div>
        </div>

        <div class="header-title font-bold">
            JURNAL MENGAJAR HARIAN
        </div>

        <!-- METADATA HEADER DINAMIS -->
        <table class="meta-table font-bold">
            <tr>
                <td width="16%">Nama Penyusun</td>
                <td width="2%">:</td>
                <td width="32%">{{ $namaPenyusun }}</td>
                <td width="16%">Kelas</td>
                <td width="2%">:</td>
                <td width="32%">{{ $faseKelas }}</td>
            </tr>
            <tr>
                <td>Satuan Pendidikan</td>
                <td>:</td>
                <td>{{ $satuanPendidikan }}</td>
                <td>Tahun Ajaran</td>
                <td>:</td>
                <td>{{ $tahunAjaran }}</td>
            </tr>
            <tr>
                <td>Mata Pelajaran</td>
                <td>:</td>
                <td>{{ $mataPelajaran }}</td>
                <td>SEMESTER</td>
                <td>:</td>
                <td>{{ $semester }}</td>
            </tr>
        </table>

        <!-- TABEL DATA JURNAL -->
        <div class="overflow-mobile">
            <table class="data-table">
                <thead>
                    <tr>
                        <th width="3%">No</th>
                        <th width="12%">Hari/ Tgl</th>
                        <th width="7%">Jam</th>
                        <th width="5%">Kelas</th>
                        <th width="9%">Mapel</th>
                        <th width="5%">ATP</th>
                        <th width="18%">Materi Pokok</th>
                        <th width="18%">Ringkasan Kegiatan Inti</th>
                        <th width="11%">Penilaian</th>
                        <th width="12%">Ket./Refleksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jurnals as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->hari_format }}</strong><br>
                                {{ $item->tanggal_format }}
                            </td>
                            <td class="text-center font-bold">{{ $item->jam_sesi_display }}</td>
                            <td class="text-center">{{ $item->nama_kelas }}</td>
                            <td class="text-center">{{ $item->nama_mapel }}</td>
                            <td class="text-center">{{ $item->atp_code }}</td>
                            <td>{{ $item->materi ?? '-' }}</td>
                            <td>{{ $item->target_next ?? 'Pelaksanaan KBM dan pembahasan materi' }}</td>
                            <td>{{ $item->penilaian_text }}</td>
                            <td>{{ $item->refleksi_text }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center" style="padding: 15px;">Belum ada data jurnal mengajar yang
                                tersimpan untuk periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- KOLOM TANDA TANGAN DUA SISI (KEPALA SEKOLAH & GURU) -->
        <table class="ttd-table" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
            <!-- BARIS 1: JABATAN & TANGGAL -->
            <tr>
                <td style="width: 65%; text-align: left; vertical-align: bottom; padding: 0;">
                    Kepala {{ $satuanPendidikan }},
                </td>
                <td style="width: 35%; text-align: left; vertical-align: bottom; padding: 0;">
                    <div style="float: right; text-align: left; min-width: 200px;">
                        Malang, {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}<br>
                        Guru,
                    </div>
                </td>
            </tr>

            <!-- BARIS 2: KOTAK TTD / UPLOAD FILE -->
            <tr>
                <td style="vertical-align: middle; padding: 0;">
                    <div class="ttd-box"
                        style="height: 70px; display: flex; flex-direction: column; justify-content: center; align-items: flex-start; margin: 4px 0;">
                        <img id="imgKepala" class="img-ttd"
                            style="display: none; max-height: 65px; max-width: 180px; object-fit: contain;"
                            alt="TTD Kepala Sekolah" />
                        <div class="no-print" style="margin-top: 4px;">
                            <input type="file" accept="image/*" onchange="previewTTD(event, 'imgKepala')"
                                style="font-size: 10px;" />
                        </div>
                    </div>
                </td>
                <td style="vertical-align: middle; padding: 0;">
                    <div style="float: right; text-align: left; min-width: 200px;">
                        <div class="ttd-box"
                            style="height: 70px; display: flex; flex-direction: column; justify-content: center; align-items: flex-start; margin: 4px 0;">
                            <img id="imgGuru" class="img-ttd"
                                style="display: none; max-height: 65px; max-width: 180px; object-fit: contain;"
                                alt="TTD Guru" />
                            <div class="no-print" style="margin-top: 4px;">
                                <input type="file" accept="image/*" onchange="previewTTD(event, 'imgGuru')"
                                    style="font-size: 10px;" />
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            <!-- BARIS 3: NAMA & NIP (SEJAJAR HORIZONTAL) -->
            <tr>
                <td style="vertical-align: top; padding: 0;">
                    <strong><u>{{ $namaKepala ?? 'NANANG SUKMAWAN SETYABUDI, S.Pd, M.PdI' }}</u></strong><br>
                    <span>NIP. {{ $nipKepala ?? '1978112720050111002' }}</span>
                </td>
                <td style="vertical-align: top; padding: 0;">
                    <div style="float: right; text-align: left; min-width: 200px;">
                        <strong><u>{{ $namaPenyusun }}</u></strong><br>
                        @php
                            $digitOnly = preg_replace('/[^0-9]/', '', (string) ($nipPenyusun ?? ''));
                            $tampilanNip = (strlen($digitOnly) === 18) ? $digitOnly : '-';
                        @endphp
                        <span>NIP. {{ $tampilanNip }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- FOOTER CUSTOM SIJAMPANG -->
        <table class="sijampang-footer">
            <tr>
                <td style="text-align: left;">
                    <strong>SIJAMPANG</strong> — Sistem Informasi Jurnal Pengajaran
                </td>
                <td style="text-align: right;">
                    Dokumen Resmi {{ $satuanPendidikan }}
                </td>
            </tr>
        </table>
    </div>

    <script>
        function previewTTD(event, targetId) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.getElementById(targetId);
                    img.src = e.target.result;
                    img.style.display = 'inline-block';
                }
                reader.readAsDataURL(file);
            }
        }

        function triggerPrint() {
            window.print();
        }
    </script>
</body>

</html>