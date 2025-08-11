@extends('superadmin')

@section('title', 'Phone Tracking - Dashboard Superadmin')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-phone mr-2"></i>Phone Tracking
        </h1>
        <a href="{{ route('dashboard.superadmin') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i>Kembali ke Dashboard
        </a>
    </div>

    <!-- Phone Statistics Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-chart-bar mr-2"></i>Statistik Laporan per Nomor Telepon
            </h6>
        </div>
        <div class="card-body">
            @if($phoneStats->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="phoneTrackingTable">
                        <thead class="thead-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Pelapor</th>
                                <th>Nomor Telepon</th>
                                <th>Jumlah Laporan</th>
                                <th>Laporan Terakhir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($phoneStats as $index => $stat)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $stat->reporter_name }}</td>
                                    <td>
                                        <span class="badge badge-primary">{{ $stat->contact }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $stat->total_reports > 10 ? 'danger' : ($stat->total_reports > 5 ? 'warning' : 'success') }}">
                                            {{ $stat->total_reports }}x
                                        </span>
                                    </td>
                                    <td>{{ $stat->last_report_date ? \Carbon\Carbon::parse($stat->last_report_date)->format('d M Y H:i') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('phone.detail', $stat->contact) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye mr-1"></i>Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Tidak ada data tracking</h5>
                    <p class="text-muted">Belum ada nomor telepon yang melapor lebih dari 1 kali.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#phoneTrackingTable').DataTable({
        "order": [[3, "desc"]], // Sort by jumlah laporan descending
        "pageLength": 25,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });
});
</script>
@endpush
@endsection 