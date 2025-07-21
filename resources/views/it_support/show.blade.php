@extends('master')

@section('title', 'Detail Laporan')

@section('content')
<div class="container mt-4">

    <h3>Detail Laporan</h3>

    @if (session('success'))
        <div class="alert alert-success mt-2">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger mt-2">{{ session('error') }}</div>
    @endif

    <div class="card mt-4">
        <div class="card-body">
            <p><strong>Nama Pelapor:</strong> {{ $report->reporter_name }}</p>
            <p><strong>Kontak:</strong> {{ $report->contact }}</p>
            <p><strong>Lokasi:</strong> {{ $report->location->name ?? '-' }}</p>
            <p><strong>Deskripsi:</strong> {{ $report->description }}</p>
            <p><strong>Status:</strong> 
                @if ($report->status === 'waiting')
                    <span class="badge bg-warning text-dark">Waiting</span>
                @elseif ($report->status === 'accepted')
                    <span class="badge bg-success">Accepted</span>
                @elseif ($report->status === 'completed')
                    <span class="badge bg-secondary">Completed</span>
                @endif
            </p>

            {{-- Tombol Accept --}}
            @if ($report->status === 'waiting')
                <form action="{{ route('report.accept', $report->id) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="mb-2">
                        <label for="team">Tambahkan Tim (opsional):</label>
                        <select name="team[]" class="form-control select2" multiple>
                            @foreach ($allITSupports as $it)
                                @if ($it->id !== auth()->id())
                                    <option value="{{ $it->id }}">{{ $it->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Accept & Ambil Tugas</button>
                </form>
            @endif

            {{-- Form Tambah Detail Masalah --}}
            @if ($report->status === 'accepted')
                <form action="{{ route('report.addDetail', $report->id) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="form-group">
                        <label for="item_id">Item Rusak:</label>
                        <select name="item_id" class="form-control select2" required>
                            <option value="">-- Pilih Item --</option>
                            @foreach ($items as $item)
                                @php
                                    $count = $itemCounts[$item->id] ?? 0;
                                    $label = $item->name . ($count > 0 ? " ({$count}x laporan)" : '');
                                @endphp
                                <option value="{{ $item->id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mt-2">
                        <label for="uraian_masalah">Uraian Masalah:</label>
                        <textarea name="uraian_masalah" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="form-group mt-2">
                        <label for="tindakan">Tindakan Perbaikan:</label>
                        <textarea name="tindakan" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="form-group mt-2">
    <label for="surat_jalan_date">Tanggal Surat Jalan:</label>
    <input type="date" name="surat_jalan_date" class="form-control" required
           value="{{ old('surat_jalan_date', $report->surat_jalan_date) }}">
</div>



                    <button type="submit" class="btn btn-success mt-2">Simpan Detail</button>
                </form>
            @endif

            {{-- Opsi pindahkan ke divisi lain --}}
            @if ($report->status !== 'completed')
                <form action="{{ route('report.pindahDivisi', $report->id) }}" method="POST" class="mt-3">
                    @csrf
                    <div class="form-group">
                        <label for="catatan">Pindahkan ke Divisi Lain (Catatan):</label>
                        <textarea name="catatan" class="form-control" rows="2" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-warning mt-2">Pindahkan</button>
                </form>
            @endif

            {{-- Tombol Cetak PDF --}}
            <div class="mt-4">
                @if ($report->status === 'accepted')
                    <a href="{{ route('report.surat', $report->id) }}" class="btn btn-outline-secondary" target="_blank">
                        <i class="fas fa-file-pdf"></i> Cetak Surat Tugas
                    </a>
                @else
                    <button class="btn btn-secondary" disabled>
                        <i class="fas fa-lock"></i> Cetak Surat (Menunggu Accept)
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Tampilkan detail tindakan jika sudah diisi --}}
    @if ($report->details->count())
        <h5 class="mt-4">Detail Tindakan:</h5>
        <ul class="list-group">
            @foreach ($report->details as $detail)
                <li class="list-group-item">
                    <strong>Item:</strong> {{ $detail->item->name ?? '-' }}<br>
                    <strong>Masalah:</strong> {{ $detail->uraian_masalah }}<br>
                    <strong>Tindakan:</strong> {{ $detail->tindakan }}
                    <div class="mt-2">
    <a href="{{ route('report_detail.edit', $detail->id) }}" class="btn btn-sm btn-warning">Edit</a>

    <form action="{{ route('report_detail.destroy', $detail->id) }}" method="POST" style="display:inline-block;" 
          onsubmit="return confirm('Yakin ingin menghapus detail ini?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-danger">Hapus</button>
    </form>
</div>




                </li>
            @endforeach
        </ul>
    @endif

</div>
@endsection
