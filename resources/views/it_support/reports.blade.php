@extends('master')

@section('title', 'Daftar Laporan Kerusakan')

@section('content')
<div class="container mt-4">
    <h3>Daftar Laporan Masuk</h3>

    @if (session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger mt-3">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered mt-4">
        <thead>
            <tr>
                <th>#</th>
                <th>Masalah</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reports as $report)
                <tr>
                    <td>{{ $report->id }}</td>
                    <td>{{ Str::limit($report->description, 50) }}</td>
                    <td>{{ $report->location->name ?? '-' }}</td>
                    <td>
                        @if ($report->status === 'waiting')
                            <span class="badge bg-warning">Waiting</span>
                        @elseif ($report->status === 'accepted')
                            <span class="badge bg-success">Accepted</span>
                        @elseif ($report->status === 'completed')
                            <span class="badge bg-secondary">Completed</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('report.show', $report->id) }}" class="btn btn-sm btn-primary">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada laporan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
