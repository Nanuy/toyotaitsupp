@extends('layouts.itsupport')

@section('title', 'Daftar Laporan Kerusakan')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clipboard-list mr-2"></i>Daftar Laporan Masuk
        </h1>
        <div class="btn-group">
            <a href="{{ route('chart.itsupport') }}" class="btn btn-sm btn-info shadow-sm">
                <i class="fas fa-chart-line fa-sm text-white-50 mr-1"></i>Lihat Chart Analisis
            </a>
            <a href="{{ route('report.create') }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i>Buat Laporan Baru
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Laporan Waiting
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $reports->where('status', 'waiting')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Laporan Accepted
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $reports->where('status', 'accepted')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Laporan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $reports->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $reports->where('created_at', '>=', today())->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-2"></i>Daftar Laporan Masuk
            </h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                     aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Aksi:</div>
                    <a class="dropdown-item" href="{{ route('chart.itsupport') }}">
                        <i class="fas fa-chart-line fa-sm fa-fw mr-2 text-gray-400"></i>
                        Lihat Chart Analisis
                    </a>
                    <a class="dropdown-item" href="{{ route('report.create') }}">
                        <i class="fas fa-plus fa-sm fa-fw mr-2 text-gray-400"></i>
                        Buat Laporan Baru
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kode Laporan</th>
                            <th>Pelapor</th>
                            <th>Masalah</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reports as $report)
                            <tr>
                                <td>{{ $report->id }}</td>
                                <td>
                                    <span class="font-weight-bold text-primary">{{ $report->report_code }}</span>
                                </td>
                                <td>{{ $report->reporter_name }}</td>
                                <td>{{ Str::limit($report->description, 50) }}</td>
                                <td>
                                    <span class="badge badge-light">{{ $report->location->name ?? '-' }}</span>
                                </td>
                                <td>
                                    @if ($report->status === 'waiting')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock mr-1"></i>Waiting
                                        </span>
                                    @elseif ($report->status === 'accepted')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check mr-1"></i>Accepted
                                        </span>
                                    @elseif ($report->status === 'completed')
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-check-double mr-1"></i>Completed
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('report.show', $report->id) }}" 
                                       class="btn btn-sm btn-primary shadow-sm">
                                        <i class="fas fa-eye fa-sm text-white-50 mr-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-gray-500">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p class="mb-0">Tidak ada laporan masuk.</p>
                                        <small>Laporan baru akan muncul di sini.</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Access to Chart Analysis -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-left-info shadow mb-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Chart Analisis
                            </div>
                            <div class="text-gray-800 mb-2">
                                Lihat analisis mendalam dari data laporan IT Support dengan berbagai chart dan grafik interaktif.
                            </div>
                            <a href="{{ route('chart.itsupport') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-chart-line mr-1"></i>Buka Chart Analisis
                            </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
