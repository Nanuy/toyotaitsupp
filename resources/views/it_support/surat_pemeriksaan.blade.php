<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Pemeriksaan Perangkat IT</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            width: 80px;
            height: auto;
            margin-bottom: 10px;
        }
        .company-name {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 10px;
            color: #666;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 30px 0 20px 0;
        }
        .no-border {
            border: none;
            width: 100%;
        }
        .no-border td {
            border: none;
            padding: 3px 0;
            vertical-align: top;
        }
        .content-table {
            width: 100%;
            margin: 20px 0;
        }
        .content-table td {
            padding: 5px;
            vertical-align: top;
        }
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }
        .signature-box {
            text-align: center;
            vertical-align: top;
        }
        .signature-img {
            height: 60px;
            margin: 10px 0;
        }
        .footer-note {
            margin-top: 30px;
            font-size: 10px;
            text-align: justify;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">SETIAJAYA TOYOTA</div>
        <div class="subtitle">Authorized Toyota Dealer</div>
    </div>

    <div class="title">
        BERITA ACARA PEMERIKSAAN PERANGKAT IT<br>
        NO : {{ $nomorSurat }}
    </div>

    <p>Berdasarkan permintaan / komplain dari pengguna dengan data di bawah ini :</p>

    <table class="content-table">
        <tr>
            <td width="150">Nama User / Pemohon</td>
            <td width="10">:</td>
            <td>{{ $report->reporter_name }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $report->division ?? '-' }}</td>
        </tr>
        <tr>
            <td>Lokasi</td>
            <td>:</td>
            <td>{{ $report->location->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jenis Perangkat</td>
            <td>:</td>
            <td>{{ $report->item->name ?? 'Mesin Absen' }}</td>
        </tr>
        <tr>
            <td>Merek / Tipe</td>
            <td>:</td>
            <td>{{ $report->merek_tipe ?? '-' }}</td>
        </tr>
        <tr>
            <td>Keluhan</td>
            <td>:</td>
            <td>{{ $report->description }}</td>
        </tr>
    </table>

    <p>Telah di lakukan pemeriksaan / tindakan teknis oleh petugas IT dengan hasil sebagai berikut :</p>

    <table class="content-table">
        <tr>
            <td width="150">Permasalahan</td>
            <td width="10">:</td>
            <td>
                @if($report->details && $report->details->count() > 0)
                    @foreach($report->details as $detail)
                        {{ $detail->uraian_masalah }}@if(!$loop->last), @endif
                    @endforeach
                @else
                    Mesin absen susah absen jari dan hasil penarikan absen sering tidak fail
                @endif
            </td>
        </tr>
        <tr>
            <td>Dampak Ditimbulkan</td>
            <td>:</td>
            <td>{{ $report->dampak_ditimbulkan ?? 'Susah absen' }}</td>
        </tr>
        <tr>
            <td>Tindakan Yang Dilakukan</td>
            <td>:</td>
            <td>
                @if($report->tindakan_dilakukan)
                    {{ $report->tindakan_dilakukan }}
                @elseif($report->details && $report->details->count() > 0)
                    @foreach($report->details as $detail)
                        {{ $detail->tindakan }}@if(!$loop->last), @endif
                    @endforeach
                @else
                    Sudah dilakukan format sdcard tetap masih tidak bisa
                @endif
            </td>
        </tr>
        <tr>
            <td>Rekomendasi Teknis</td>
            <td>:</td>
            <td>{{ $report->rekomendasi_teknis ?? 'Pembelian Mesin absen baru' }}</td>
        </tr>
        <tr>
            <td>Spesifikasi Pengadaan</td>
            <td>:</td>
            <td>{{ $report->spesifikasi_pengadaan ?? 'Solution X903' }}</td>
        </tr>
    </table>

    <div class="footer-note">
        Demikian Berita Acara ini kami buat dengan sebenar-benarnya untuk di jadikan sebagai rujukan 
        pengadaan perangkat / layanan service pihak ketiga. Atas perhatian dan kerjasamanya diucapkan 
        terima kasih.
    </div>

    <table class="signature-section">
        <tr>
            <td width="50%" class="signature-box">
                <p>Dibuat Oleh</p>
                @php
                    $itSignature = $report->signatures->firstWhere('role', 'it_supp');
                @endphp
                @if ($itSignature && $itSignature->signature_path)
                    <img src="{{ public_path('storage/' . $itSignature->signature_path) }}" class="signature-img">
                @else
                    <div style="height: 60px; margin: 10px 0;"></div>
                @endif
                <br>
                <strong>
                    @if($report->itSupports && $report->itSupports->count() > 0)
                        {{ $report->itSupports->first()->name }}
                    @else
                        Victor Hendrik
                    @endif
                </strong>
            </td>
            <td width="50%" class="signature-box">
                <p>Depok, {{ $tanggalSurat }}</p>
                <p>Diperiksa Oleh</p>
                @php
                    $managerSignature = $report->signatures->firstWhere('role', 'superadmin');
                @endphp
                @if ($managerSignature && $managerSignature->signature_path)
                    <img src="{{ public_path('storage/' . $managerSignature->signature_path) }}" class="signature-img">
                @else
                    <div style="height: 60px; margin: 10px 0;"></div>
                @endif
                <br>
                <strong>Andrie Sondakh</strong>
            </td>
        </tr>
    </table>
</body>
</html>