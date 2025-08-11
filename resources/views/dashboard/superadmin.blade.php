@extends('master')

@section('title', 'Dashboard Superadmin')

@section('content')
<div class="container-fluid">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light rounded shadow-sm mb-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">Superadmin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSuperadmin" aria-controls="navbarSuperadmin" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSuperadmin">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i> Register
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('reports.charts') }}">
                            <i class="fas fa-chart-bar me-1"></i> Chart
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('superadmin.reports') }}">
                            <i class="fas fa-file-alt me-1"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('superadmin.users') }}">
                            <i class="fas fa-users-cog me-1"></i> Edit User
                        </a>
                    </li>
                </ul>

                <!-- Optional: Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="fw-bold">Selamat Datang, Superadmin!</h4>
            <p class="text-muted">Gunakan menu navigasi di atas untuk mengelola sistem pelaporan dan pengguna.</p>
        </div>
    </div>
</div>
@endsection
