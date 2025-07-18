@extends('master')

@section('content')
<div class="container">
    <h2>Edit Detail Laporan</h2>

    <form action="{{ route('report_detail.update', $detail->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="item_id" class="form-label">Item Rusak</label>
            <select name="item_id" class="form-control">
                @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ $detail->item_id == $item->id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="tindakan" class="form-label">Tindakan</label>
            <input type="text" name="tindakan" class="form-control" value="{{ old('tindakan', $detail->tindakan) }}">
        </div>

        <div class="mb-3">
            <label for="uraian_masalah" class="form-label">Uraian Masalah</label>
            <textarea name="uraian_masalah" class="form-control">{{ old('uraian_masalah', $detail->uraian_masalah) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('report.show', $detail->report_id) }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
