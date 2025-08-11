@extends('layouts.itsupport')

@section('title', 'Dashboard IT Support')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard IT Support</h1>
</div>

<div class="row">
    <!-- Laporan Menunggu Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Laporan Menunggu</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ App\Models\Report::where('status', 'waiting')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan Diterima Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Laporan Diterima</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ App\Models\Report::where('status', 'accepted')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Laporan Selesai Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Laporan Selesai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ App\Models\Report::where('status', 'completed')->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Laporan Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Laporan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ App\Models\Report::count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-folder fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Laporan Terbaru -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Laporan Terbaru</h6>
            </div>
            <div class="card-body">
                @php
                $latestReports = App\Models\Report::orderBy('created_at', 'desc')->take(5)->get();
                @endphp
                
                @if($latestReports->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pelapor</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestReports as $report)
                                <tr>
                                    <td>{{ $report->id }}</td>
                                    <td>{{ $report->reporter_name }}</td>
                                    <td>
                                        @if($report->status == 'waiting')
                                            <span class="badge badge-warning">Menunggu</span>
                                        @elseif($report->status == 'accepted')
                                            <span class="badge badge-primary">Diterima</span>
                                        @elseif($report->status == 'completed')
                                            <span class="badge badge-success">Selesai</span>
                                        @endif
                                    </td>
                                    <td>{{ $report->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('it.show', $report->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>Belum ada laporan.</p>
                @endif
                <div class="mt-3">
                    <a href="{{ route('it.reports') }}" class="btn btn-primary btn-sm">Lihat Semua Laporan</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Profil -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Profil IT Support</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <img class="img-profile rounded-circle mb-3" src="{{ asset('img/undraw_profile.svg') }}" style="width: 100px; height: 100px;">
                    <h5>{{ Auth::user()->name }}</h5>
                    <p>{{ Auth::user()->email }}</p>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Jabatan:</strong> {{ Auth::user()->jabatan ?? 'IT Support' }}</p>
                        <p><strong>Departemen:</strong> {{ Auth::user()->departemen ?? 'IT' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Kontak:</strong> {{ Auth::user()->contact ?? '-' }}</p>
                        <p><strong>Tanda Tangan:</strong> 
                            @if(Auth::user()->signature_path)
                                <span class="text-success">Tersedia</span>
                            @else
                                <span class="text-danger">Belum ada</span>
                            @endif
                        </p>
                    </div>
                </div>
                
                @if(Auth::user()->signature_path)
                    <div class="text-center mt-3">
                        <img src="{{ asset('storage/' . Auth::user()->signature_path) }}" alt="Tanda Tangan" class="img-fluid border" style="max-height: 80px;">
                    </div>
                @endif
                
                <div class="mt-4">
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">Edit Profil</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
