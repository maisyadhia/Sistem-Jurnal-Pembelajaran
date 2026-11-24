<table>
    <thead>
        <tr>
            <td colspan="10" style="text-align: center; font-weight: bold; font-size: 16px;">LAPORAN KEPATUHAN GURU</td>
        </tr>
        <tr>
            <td colspan="10" style="text-align: center;">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr></tr> <!-- Baris kosong -->
        
        <tr>
            <th style="border: 1px solid #000; background-color: #1e3a8a; color: #fff; padding: 5px;">NO</th>
            <th style="border: 1px solid #000; background-color: #1e3a8a; color: #fff; padding: 5px;">TANGGAL KBM</th>
            <th style="border: 1px solid #000; background-color: #1e3a8a; color: #fff; padding: 5px;">TANGGAL INPUT</th>
            <th style="border: 1px solid #000; background-color: #1e3a8a; color: #fff; padding: 5px;">GURU</th>
            <th style="border: 1px solid #000; background-color: #1e3a8a; color: #fff; padding: 5px;">NIK</th>
            <th style="border: 1px solid #000; background-color: #1e3a8a; color: #fff; padding: 5px;">KELAS</th>
            <th style="border: 1px solid #000; background-color: #1e3a8a; color: #fff; padding: 5px;">MAPEL</th>
            <th style="border: 1px solid #000; background-color: #1e3a8a; color: #fff; padding: 5px;">MATERI</th>
            <th style="border: 1px solid #000; background-color: #1e3a8a; color: #fff; padding: 5px;">STATUS</th>
            <th style="border: 1px solid #000; background-color: #1e3a8a; color: #fff; padding: 5px;">SELISIH HARI</th>
        </tr>
    </thead>
    <tbody>
        @foreach($jurnals as $index => $jurnal)
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ \Carbon\Carbon::parse($jurnal->tanggal_kbm)->format('d/m/Y') }}</td>
            <td style="border: 1px solid #000; text-align: center;">{{ \Carbon\Carbon::parse($jurnal->tanggal_input)->format('d/m/Y H:i') }}</td>
            <td style="border: 1px solid #000;">{{ $jurnal->nama_guru }}</td>
            <td style="border: 1px solid #000;">{{ $jurnal->nik ?? '-' }}</td>
            <td style="border: 1px solid #000;">{{ $jurnal->nama_kelas }}</td>
            <td style="border: 1px solid #000;">{{ $jurnal->nama_mapel }}</td>
            <td style="border: 1px solid #000;">{{ $jurnal->materi ?? '-' }}</td>
            <td style="border: 1px solid #000; text-align: center; color: {{ $jurnal->is_terlambat ? 'red' : 'green' }}">
                {{ $jurnal->is_terlambat ? 'Terlambat' : 'Tepat Waktu' }}
            </td>
            <td style="border: 1px solid #000; text-align: center;">{{ $jurnal->selisih_hari }} hari</td>
        </tr>
        @endforeach
    </tbody>
</table>