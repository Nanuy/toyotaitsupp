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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }
        .no-border {
            border: none;
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
    </style>
</head>
<body>

    <h2>SURAT PERINTAH TUGAS</h2>
    <p style="text-align: center;">
    No : {{ sprintf('%02d/IT/SJM/%s/%s', $report->id, strtoupper(date('m', strtotime($report->surat_jalan_date))), date('Y', strtotime($report->created_at))) }}
</p>

    <p>Dengan surat ini diperintahkan kepada petugas-petugas berikut ini:</p>

    <table class="no-border">
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Jabatan</th>
                <th>Divisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report->itSupports as $it)
                <tr>
                    <td>{{ $it->name }}</td>
                    <td>IT Staff</td>
                    <td>IT</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="mt-3">Untuk menjalankan tugas kunjungan ke kantor / cabang pada :</p>

    <table class="no-border">

    <tr>
        <td width="200px">Hari Pelaporan</td>
        <td>: {{ \Carbon\Carbon::parse($report->created_at)->translatedFormat('l, d F Y') }}</td>
    </tr>

    <tr>
        <td>Nama Pelapor</td>
        <td>: {{ $report->reporter_name }}</td>
    </tr>

    <tr>
        <td>Cabang</td>
        <td>: {{ $report->location->name ?? '-' }}</td>
    </tr>

    <tr>
        <td>Divisi</td>
        <td>: {{ $report->division }}</td>
    </tr>
    
    <tr>
        <td>Kontak</td>
        <td>: {{ $report->contact }}</td>
    </tr>
 
    <tr>
        <td>Maksud dan Tujuan</td>
        <td>: {{ $report->description }}</td>
    </tr>

</table>

    <p class="mt-3 mb-0">
        Depok, {{ $tanggalSurat }}
    <br><br><br><br>
     <br>
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
</body>
</html>
