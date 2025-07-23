<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perintah Tugas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
        }
        h2 {
            text-align: center;
            text-transform: uppercase;
        }
        p {
            margin: 4px 0;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .mt-3 {
            margin-top: 20px;
        }
        .mt-2 {
            margin-top: 10px;
        }
        .mb-0 {
            margin-bottom: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }
        .no-border td, .no-border th {
            border: none !important;
        }
    </style>
</head>
<body>

    <h2>SURAT PERINTAH TUGAS</h2>
    <p class="text-center">
        No : {{ sprintf('%02d/IT/SJM/%s/%s', $report->id, strtoupper(date('m', strtotime($report->surat_jalan_date))), date('Y', strtotime($report->created_at))) }}
    </p>

    <p>Dengan surat ini diperintahkan kepada petugas-petugas berikut ini:</p>

    @php
        $names = $report->itSupports->pluck('name')->toArray();
        $positions = array_fill(0, count($names), 'IT Staff');
    @endphp

    <p><strong>Nama:</strong> {{ implode(', ', $names) }}</p>
    <p><strong>Jabatan:</strong> {{ implode(', ', $positions) }}</p>
    <p><strong>Divisi: </strong>IT</p>

    <p class="mt-3">Untuk menjalankan tugas kunjungan ke kantor / cabang yang dilakukan pada tanggal:</p>

    <table class="no-border">
        <tr>
            <td style="border: none; width: 200px;">Hari Pelaporan</td>
            <td style="border: none;">: {{ \Carbon\Carbon::parse($report->created_at)->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td style="border: none;">Nama Pelapor</td>
            <td style="border: none;">: {{ $report->reporter_name }}</td>
        </tr>
        <tr>
            <td style="border: none;">Cabang</td>
            <td style="border: none;">: {{ $report->location->name ?? '-' }}</td>
        </tr>
        <tr>
            <td style="border: none;">Divisi</td>
            <td style="border: none;">: {{ $report->division }}</td>
        </tr>
        <tr>
            <td style="border: none;">Kontak</td>
            <td style="border: none;">: {{ $report->contact }}</td>
        </tr>
        <tr>
            <td style="border: none;">Maksud dan Tujuan</td>
            <td style="border: none;">: {{ $report->description }}</td>
        </tr>
    </table>

    <p class="mt-3 mb-0">
        Depok, {{ $tanggalSurat }}
        <br><br><br><br><br>
        <strong>Andrie Sondakh</strong><br>
        IT Manager
    </p>

    <h4 class="mt-2">Detail Tugas:</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Uraian Masalah / Kegiatan</th>
                <th>Tindakan / Saran Teknis</th>
                @foreach ($report->itSupports as $it)
                    <th>Paraf {{ $it->name }}</th>
                @endforeach
                <th>Paraf Pengguna</th>
                <th>Paraf Dept Head</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($report->details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->uraian_masalah }}</td>
                    <td>{{ $detail->tindakan }}</td>
                    @foreach ($report->itSupports as $it)
                        <td></td>
                    @endforeach
                    <td></td>
                    <td></td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 4 + count($report->itSupports) }}" class="text-center">Belum ada detail tindakan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
