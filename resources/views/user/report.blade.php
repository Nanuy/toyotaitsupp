@extends('layouts.itsupport')

@section('title', 'Form Laporan')

@section('content')
  <h1 class="h3 mb-4 text-gray-800">Form Laporan Kerusakan</h1>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('report.store') }}">
    @csrf
    <div class="form-group">
      <label>Nama Pelapor</label>
      <input name="reporter_name" type="text" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Kontak</label>
      <input name="reporter_contact" type="text" class="form-control" required>
    </div>
    <div class="form-group">
      <label>Item Rusak</label>
      <select name="item_id" class="form-control" required>
        <option value="">-- Pilih Item --</option>
        @foreach($items as $item)
          <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Lokasi</label>
      <select name="location_id" class="form-control" required>
        <option value="">-- Pilih Lokasi --</option>
        @foreach($locations as $loc)
          <option value="{{ $loc->id }}">{{ $loc->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group">
      <label>Deskripsi</label>
      <textarea name="description" class="form-control" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Kirim</button>
  </form>
@endsection
