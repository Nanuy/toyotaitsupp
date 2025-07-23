@extends('master')

@section('title', 'Form Laporan')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Form Laporan Kerusakan</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('lapor.store') }}" enctype="multipart/form-data">

        @csrf

        <div class="mb-3">
            <label for="reporter_name" class="form-label">Nama Pelapor (dan Divisi)</label>
            <input type="text" name="reporter_name" class="form-control" placeholder="Contoh: Yunan - HRD" required>
        </div>

        <div class="form-group">
  <label>Divisi</label>
  <input name="division" type="text" class="form-control" required>
</div>


        <div class="mb-3">
            <label for="contact" class="form-label">Kontak Whatsapp</label>
            <input type="text" name="contact" class="form-control" required placeholder="Contoh: 08xxxxxxx">
        </div>

        <div class="mb-3">
            <label for="location_id" class="form-label">Pilih Lokasi Cabang</label>
            <select name="location_id" class="form-select select2" required>
                <option disabled selected>-- Pilih Lokasi --</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Deskripsi Masalah (oleh pelapor)</label>
            <textarea name="description" rows="4" class="form-control" required placeholder="Mouse Rusak"></textarea>
        </div>

        <div class="mb-3">
        <label for="image">Upload Gambar (Opsional)</label>
        <input type="file" name="image" id="image" class="form-control" accept="image/*">
    </div>

        <button type="submit" class="btn btn-primary">Kirim Laporan</button>
    </form>
</div>
@endsection
