@extends('master')

@section('content')
<div class="container mt-5">
    <h3>Detail Laporan</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <ul class="list-group">
        <li class="list-group-item"><strong>Pelapor:</strong> {{ $report->reporter_name }}</li>
        <li class="list-group-item"><strong>Kontak:</strong> {{ $report->contact }}</li>
        <li class="list-group-item"><strong>Item:</strong> {{ optional($report->item)->name ?? '-' }}</li>
        <li class="list-group-item"><strong>Lokasi:</strong> {{ optional($report->location)->name ?? '-' }}</li>
        <li class="list-group-item"><strong>Deskripsi:</strong> {{ $report->description }}</li>
        <li class="list-group-item"><strong>Status:</strong> 
            @if($report->status === 'waiting')
                <span class="badge bg-warning text-dark">Menunggu</span>
            @elseif($report->status === 'accepted')
                <span class="badge bg-success">Diterima</span>
            @endif
        </li>
    </ul>

    @if($report->status === 'waiting')
        <form method="POST" action="{{ route('lapor.accept', $report->id) }}" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-success">ACCEPT</button>
        </form>
    @else
        <div class="alert alert-info mt-3">Laporan sudah diambil oleh IT Support.</div>
    @endif
</div>
@endsection
