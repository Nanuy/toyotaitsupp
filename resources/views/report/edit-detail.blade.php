@extends('master')

@section('title', 'Edit Detail Tindakan')

@section('content')
<div class="container mt-4">
    <h3>Edit Detail Tindakan</h3>

    <form action="{{ route('detail.update', $detail->id) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="item_id">Item Rusak:</label>
            <select name="item_id" class="form-control select2" required>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}" {{ $item->id == $detail->item_id ? 'selected' : '' }}>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mt-2">
            <label for="uraian_masalah">Uraian Masalah:</label>
            <textarea name="uraian_masalah" class="form-control" rows="3" required>{{ $detail->uraian_masalah }}</textarea>
        </div>

        <div class="form-group mt-2">
            <label for="tindakan">Tindakan Perbaikan:</label>
            <textarea name="tindakan" class="form-control" rows="3" required>{{ $detail->tindakan }}</textarea>
        </div>

        <button type="submit" class="btn btn-success mt-3">Simpan Perubahan</button>
        <a href="{{ route('report.show', $detail->report_id) }}" class="btn btn-secondary mt-3">Batal</a>
    </form>
</div>
@endsection

