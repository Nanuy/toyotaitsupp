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
    @php
    $bulan = date('m', strtotime($report->surat_jalan_date));
    $tahun = date('Y', strtotime($report->surat_jalan_date));

    $monthlyCount = \App\Models\Report::whereMonth('surat_jalan_date', $bulan)
        ->whereYear('surat_jalan_date', $tahun)
        ->where('surat_jalan_date', '<=', $report->surat_jalan_date)
        ->orderBy('surat_jalan_date')
        ->pluck('id');

    // Cari urutan report ini di antara laporan bulan itu
    $currentIndex = $monthlyCount->search($report->id) + 1;
@endphp

    <h2>SURAT PERINTAH TUGAS</h2>
    <p class="text-center">
    No : {{ sprintf('%03d/IT/SJM/%s/%s', $currentIndex, $bulan, $tahun) }}
</p>

    <p>Dengan surat ini diperintahkan kepada petugas-petugas berikut ini:</p>

    @php
        $names = $report->itSupports->pluck('name')->toArray();
    @endphp

    <table class="no-border">
        <tr>
            <td style="width: 200px;">Nama</td>
            <td>:{{ implode(', ', $names) }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:IT Staff</td>
        </tr>
        <tr>
            <td>Divisi</td>
            <td>:IT</td>
        </tr>

    </table>

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

    @php
    $managerSignature = $report->SignatureByRole('manager'); // atau 'it_manager', tergantung role-nya
@endphp

<p class="mt-3 mb-0">
    Depok, {{ $tanggalSurat }}<br><br>

    @if ($managerSignature && file_exists(public_path('storage/' . $managerSignature->signature_path)))
        <img src="{{ public_path('storage/' . $managerSignature->signature_path) }}" style="height: 50px;"><br>
    @else
        <br><br> {{-- Jika tidak ada tanda tangan, beri spasi kosong --}}
    @endif

    <strong>Andrie Sondakh</strong><br>
    IT Manager
</p>


    <h4 class="mt-2">Detail Tugas:</h4>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Uraian Masalah / Kegiatan</th>
            <th>Tindakan / Saran Teknis</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($report->details as $index => $detail)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $detail->uraian_masalah }}</td>
                <td>{{ $detail->tindakan }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ 4 + count($report->itSupports) }}" class="text-center">Belum ada detail tindakan.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<p><br><br><br><br><br><br></p>
<div style="page-break-before: always;">
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            @foreach ($report->itSupports as $it)
                <th>Paraf {{ $it->name }}</th>
            @endforeach
            <th>Paraf Pengguna</th>
            <th>Paraf Dept Head</th>
            <th>Paraf Super Admin</th>
        </thead>
        <tbody>
            {{-- TTD IT SUPPORT --}}
            @foreach ($report->itSupports as $it)
                @php
                    $ttd = $report->signatures->firstWhere('user_id', $it->id);
                @endphp
                <td>
                    @if ($ttd && $ttd->signature_path)
                        <img src="{{ public_path('storage/' . $ttd->signature_path) }}" style="height: 50px;">
                    @else
                        <em>—</em>
                    @endif
                </td>
            @endforeach

            {{-- TTD PENGGUNA --}}
            @php
                $ttdReporter = $report->signatureByRole('user');
            @endphp
            <td>
                @if ($ttdReporter && $ttdReporter->signature_path)
                    <img src="{{ public_path('storage/' . $ttdReporter->signature_path) }}" style="height: 50px;">
                @else
                    <em>—</em>
                @endif
            </td>

            {{-- TTD DEPT HEAD --}}
            @php
                $ttdDept = $report->signatures->firstWhere('role', 'dept_head');
            @endphp
            <td>
                @if ($ttdDept && $ttdDept->signature_path)
                    <img src="{{ public_path('storage/' . $ttdDept->signature_path) }}" style="height: 50px;">
                @else
                    <em>—</em>
                @endif
            </td>
            
            {{-- TTD SUPER ADMIN --}}
            @php
                $ttdSuperadmin = $report->signatures->firstWhere('role', 'superadmin');
            @endphp
            <td>
                @if ($ttdSuperadmin && $ttdSuperadmin->signature_path)
                    <img src="{{ public_path('storage/' . $ttdSuperadmin->signature_path) }}" style="height: 50px;">
                @else
                    <em>—</em>
                @endif
            </td>
        </tbody>
    </table>
</div>




</body>
</html>
