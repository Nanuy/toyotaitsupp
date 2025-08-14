@extends('layouts.itsupport')

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

                <!-- Filter Kategori -->
                <div class="d-flex align-items-center me-3">
                    <label for="categoryFilter" class="form-label me-2 mb-0">Filter Kategori:</label>
                    <select id="categoryFilter" class="form-select" style="width: auto;">
                        <option value="">Semua Cabang</option>
                        <option value="SJM">SJM Only</option>
                        <option value="Non SJM">Non SJM Only</option>
                    </select>
                </div>

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

    <!-- Widget Cards per Cabang -->
    <div class="row" id="cabangWidgets">
        <!-- Cards akan dimuat via AJAX -->
    </div>

    <!-- Main Content -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="fw-bold">Selamat Datang, Superadmin!</h4>
            <p class="text-muted">Gunakan menu navigasi di atas untuk mengelola sistem pelaporan dan pengguna.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load initial widgets
    loadCabangWidgets();
    
    // Handle category filter change
    $('#categoryFilter').on('change', function() {
        loadCabangWidgets();
    });
    
    function loadCabangWidgets() {
        const categoryFilter = $('#categoryFilter').val();
        
        $.ajax({
            url: '{{ route("chart.widgets") }}',
            method: 'GET',
            data: {
                category_filter: categoryFilter,
                period: 30
            },
            success: function(response) {
                renderCabangWidgets(response.all_locations);
            },
            error: function(xhr, status, error) {
                console.error('Error loading cabang widgets:', error);
                $('#cabangWidgets').html('<div class="col-12"><div class="alert alert-danger">Error loading data</div></div>');
            }
        });
    }
    
    function renderCabangWidgets(locations) {
        let html = '';
        const colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary', 'dark'];
        
        locations.forEach(function(location, index) {
            const colorClass = colors[index % colors.length];
            const categoryBadge = location.category === 'SJM' ? 
                '<span class="badge badge-success badge-sm">SJM</span>' : 
                '<span class="badge badge-info badge-sm">Non SJM</span>';
            
            html += `
                <div class="col-xl-3 col-md-6 col-sm-6 mb-4">
                    <div class="card border-left-${colorClass} shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-${colorClass} text-uppercase mb-1">
                                        ${location.name} ${categoryBadge}
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${location.total}</div>
                                    <div class="text-xs text-muted">Record Count</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-building fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        $('#cabangWidgets').html(html);
    }
});
</script>
@endpush
