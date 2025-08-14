@extends('superadmin')

@section('title', 'Dashboard Superadmin - Analisis Perangkat IT')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line mr-2"></i>Analisis Perangkat IT
        </h1>
        <div class="btn-group">
            <button class="btn btn-sm btn-primary shadow-sm" onclick="exportData()">
                <i class="fas fa-download fa-sm text-white-50 mr-1"></i>Export Data
            </button>
            <button class="btn btn-sm btn-success shadow-sm" onclick="downloadAllCharts()">
                <i class="fas fa-images fa-sm text-white-50 mr-1"></i>Download All Charts
            </button>
            <button class="btn btn-sm btn-info shadow-sm" onclick="downloadChartDataAsExcel()">
                <i class="fas fa-file-excel fa-sm text-white-50 mr-1"></i>Download Excel
            </button>
        </div>
    </div>

    <!-- Filter & Sort Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-filter mr-2"></i>Filter & Pengaturan Data
            </h6>
        </div>
        <div class="card-body">
            <form id="filter-form">
                <div class="row">
                    <!-- Date Range Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="start_date" class="form-label font-weight-bold">
                            <i class="fas fa-calendar-alt mr-1"></i>Tanggal Mulai
                        </label>
                        <input type="date" class="form-control" id="start_date" name="start_date">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="end_date" class="form-label font-weight-bold">
                            <i class="fas fa-calendar-alt mr-1"></i>Tanggal Akhir
                        </label>
                        <input type="date" class="form-control" id="end_date" name="end_date">
                    </div>
                    
                    <!-- Location Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="location_filter" class="form-label font-weight-bold">
                            <i class="fas fa-map-marker-alt mr-1"></i>Filter Cabang
                        </label>
                        <select class="form-control" id="location_filter" name="location_filter">
                            <option value="">Semua Cabang</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="category_filter" class="form-label font-weight-bold">
                            <i class="fas fa-building mr-1"></i>Filter Kategori Cabang
                        </label>
                        <select class="form-control" id="category_filter" name="category_filter">
                            <option value="">Semua Cabang</option>
                            <option value="SJM">SJM Only</option>
                            <option value="Non SJM">Non SJM Only</option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="category_filter" class="form-label font-weight-bold">
                            <i class="fas fa-building mr-1"></i>Filter Kategori Cabang
                        </label>
                        <select class="form-control" id="category_filter" name="category_filter">
                            <option value="">Semua Cabang</option>
                            <option value="SJM">SJM Only</option>
                            <option value="Non SJM">Non SJM Only</option>
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="category_filter" class="form-label font-weight-bold">
                            <i class="fas fa-building mr-1"></i>Filter Kategori Cabang
                        </label>
                        <select class="form-control" id="category_filter" name="category_filter">
                            <option value="">Semua Cabang</option>
                            <option value="SJM">SJM Only</option>
                            <option value="Non SJM">Non SJM Only</option>
                        </select>
                    </div>

                    <!-- Item Category Filter -->
                    <div class="col-md-3 mb-3">
                        <label for="item_filter" class="form-label font-weight-bold">
                            <i class="fas fa-tags mr-1"></i>Filter Kategori
                        </label>
                        <select class="form-control" id="item_filter" name="item_filter">
                            <option value="">Semua Kategori</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Sort Options -->
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="sort_by" class="form-label font-weight-bold">
                            <i class="fas fa-sort mr-1"></i>Urutkan Berdasarkan
                        </label>
                        <select class="form-control" id="sort_by" name="sort_by">
                            <option value="total_desc">Jumlah Laporan (Tertinggi)</option>
                            <option value="total_asc">Jumlah Laporan (Terendah)</option>
                            <option value="name_asc">Nama (A-Z)</option>
                            <option value="name_desc">Nama (Z-A)</option>
                            <option value="date_desc">Tanggal (Terbaru)</option>
                            <option value="date_asc">Tanggal (Terlama)</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="limit_data" class="form-label font-weight-bold">
                            <i class="fas fa-list-ol mr-1"></i>Tampilkan Data
                        </label>
                        <select class="form-control" id="limit_data" name="limit_data">
                            <option value="5">Top 5</option>
                            <option value="10">Top 10</option>
                            <option value="15">Top 15</option>
                            <option value="20">Top 20</option>
                            <option value="all">Semua Data</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label for="chart_type" class="form-label font-weight-bold">
                            <i class="fas fa-chart-bar mr-1"></i>Tipe Chart
                        </label>
                        <select class="form-control" id="chart_type" name="chart_type">
                            <option value="bar">Bar Chart</option>
                            <option value="line">Line Chart</option>
                            <option value="doughnut">Doughnut Chart</option>
                            <option value="pie">Pie Chart</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <div class="w-100">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search mr-1"></i>Terapkan Filter
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Filter Options -->
                <div class="row mt-2">
                    <div class="col-12">
                        <label class="form-label font-weight-bold mb-2">
                            <i class="fas fa-clock mr-1"></i>Filter Cepat:
                        </label>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setQuickFilter('today', event)">
                                Hari Ini
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setQuickFilter('week')">
                                7 Hari
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setQuickFilter('month')">
                                30 Hari
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setQuickFilter('quarter')">
                                3 Bulan
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setQuickFilter('year')">
                                1 Tahun
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearFilters()">
                                <i class="fas fa-times mr-1"></i>Reset
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row" id="cabangWidgets">
        <!-- Cards akan dimuat via AJAX -->
    </div>

    <!-- Loading Indicator -->
    <div id="loading-indicator" class="text-center mb-4" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Sedang memuat data...</p>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4" id="summary-cards">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Laporan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="total-reports">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                                Cabang Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="active-branches">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
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
                                Kategori Item
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="item-categories">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Rata-rata/Hari
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="avg-per-day">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="row">
<div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-map-marker-alt mr-2"></i>Laporan per Cabang
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="downloadChart('cabangChart', 'laporan-cabang')">
                                <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                                Download Chart
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="cabangChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
</div>
    <!-- Charts Row 1 -->
    <div class="row">
        <!-- Chart Status -->
        <div class="col-xl-4 col-lg-6 col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Status Laporan</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink-status"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink-status">
                            <div class="dropdown-header">Aksi Chart:</div>
                            <a class="dropdown-item" href="#" onclick="downloadChart('statusChart', 'status-laporan')">Download Chart</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="statusChart" style="height: 320px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart per Kategori -->
        <div class="col-xl-4 col-lg-12 col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Laporan per Kategori</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink-kategori"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink-kategori">
                            <div class="dropdown-header">Aksi Chart:</div>
                            <a class="dropdown-item" href="#" onclick="downloadChart('kategoriChart', 'laporan-kategori')">Download Chart</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="kategoriChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <!-- Charts Row 2 -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-line mr-2"></i>Tren Laporan (Rentang Waktu)
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="downloadChart('waktuChart', 'tren-waktu')">
                                <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                                Download Chart
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="waktuChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 3 -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-star mr-2"></i><span id="top-items-title">Top 5 Item Paling Sering Dilaporkan</span>
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="downloadChart('topItemChart', 'top-items')">
                                <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                                Download Chart
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="topItemChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Reports Widget -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock mr-2"></i>Laporan Terbaru
                    </h6>
                </div>
                <div class="card-body">
                    <div id="recent-reports" style="max-height: 350px; overflow-y: auto;">
                        <!-- Recent reports will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-table mr-2"></i>Data Laporan Detail
                    </h6>
                    <div class="d-flex">
                        <input type="text" class="form-control form-control-sm mr-2" id="table-search" 
                               placeholder="Cari laporan..." style="width: 200px;">
                        <select class="form-control form-control-sm mr-2" id="table-per-page" style="width: 100px;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <button class="btn btn-sm btn-outline-primary" onclick="refreshTable()">
                            <i class="fas fa-refresh"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="reports-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tanggal</th>
                                    <th>Cabang</th>
                                    <th>Kategori</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Pelapor</th>
                                </tr>
                            </thead>
                            <tbody id="reports-table-body">
                                <!-- Table data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="table-pagination" class="d-flex justify-content-between align-items-center mt-3">
                        <!-- Pagination will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparison Modal -->
    <div class="modal fade" id="comparisonModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-chart-line mr-2"></i>Perbandingan Periode
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="comparison-form">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Periode 1 (Pembanding)</h6>
                                <div class="form-group">
                                    <label>Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="period1_start">
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="period1_end">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6>Periode 2 (Referensi)</h6>
                                <div class="form-group">
                                    <label>Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="period2_start">
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="period2_end">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-chart-bar mr-1"></i>Bandingkan
                        </button>
                    </form>
                    <div id="comparison-results" class="mt-4" style="display: none;">
                        <!-- Comparison results will be shown here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Floating Action Button -->
<div class="fab-container">
    <div class="fab-main" onclick="toggleFab()">
        <i class="fas fa-plus" id="fab-icon"></i>
    </div>
    <div class="fab-option" onclick="openComparisonModal()" style="display: none;">
        <i class="fas fa-chart-line"></i>
        <span class="fab-tooltip">Bandingkan Periode</span>
    </div>
    <div class="fab-option" onclick="refreshAllData()" style="display: none;">
        <i class="fas fa-sync-alt"></i>
        <span class="fab-tooltip">Refresh Data</span>
    </div>
    <div class="fab-option" onclick="exportData()" style="display: none;">
        <i class="fas fa-download"></i>
        <span class="fab-tooltip">Export Data</span>
    </div>
    <div class="fab-option" onclick="downloadAllCharts()" style="display: none;">
        <i class="fas fa-images"></i>
        <span class="fab-tooltip">Download All Charts</span>
    </div>
    <div class="fab-option" onclick="downloadChartDataAsExcel()" style="display: none;">
        <i class="fas fa-file-excel"></i>
        <span class="fab-tooltip">Download Excel Data</span>
    </div>
</div></div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
<!-- Libraries for chart download functionality -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    // Load initial widgets
    loadCabangWidgets();
    
    // Handle category filter change
    $('#category_filter').on('change', function() {
        loadCabangWidgets();
    });
    
    // Handle date filter change
    $('#start_date, #end_date').on('change', function() {
        loadCabangWidgets();
    });
    
    function loadCabangWidgets() {
        const categoryFilter = $('#category_filter').val();
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        
        $.ajax({
            url: '{{ route("chart.widgets") }}',
            method: 'GET',
            data: {
                category_filter: categoryFilter,
                start_date: startDate,
                end_date: endDate,
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
        const colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary', 'dark', 'light'];
        
        locations.forEach(function(location, index) {
            const colorClass = colors[index % colors.length];
            const categoryBadge = location.category === 'SJM' ? 
                '<span class="badge badge-success badge-sm">SJM</span>' : 
                '<span class="badge badge-info badge-sm">Non SJM</span>';
            
            html += `
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 mb-4">
                    <div class="card border-left-${colorClass} shadow h-100 py-2" style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%); color: white;">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                        ${location.name} ${categoryBadge}
                                    </div>
                                    <div class="h4 mb-0 font-weight-bold text-white">${location.total}</div>
                                    <div class="text-xs text-white-50">Record Count</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-building fa-2x text-white-50"></i>
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

<style>
/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 768px) {
    .container-fluid {
        padding: 10px;
    }
    
    .h3 {
        font-size: 1.5rem;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        margin-bottom: 5px;
        border-radius: 0.25rem !important;
    }
    
    .card-header .btn-group {
        flex-direction: row;
        width: auto;
    }
    
    .card-header .btn-group .btn {
        margin-bottom: 0;
    }
    
    /* Filter form responsive */
    .col-md-3 {
        margin-bottom: 1rem;
    }
    
    /* Chart containers */
    .chart-area {
        height: 250px !important;
        margin: 10px 0;
    }
    
    .chart-area canvas {
        max-height: 250px !important;
    }
    
    /* Summary cards */
    .col-xl-3 {
        margin-bottom: 1rem;
    }
    
    /* Table responsive */
    .table-responsive {
        font-size: 0.8rem;
    }
    
    .d-flex {
        flex-direction: column;
        gap: 10px;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: stretch;
    }
    
    /* Recent reports widget */
    #recent-reports {
        max-height: 200px !important;
    }
    
    /* Pagination */
    .btn-group[role="group"] {
        flex-direction: row;
        width: auto;
    }
}

@media (max-width: 576px) {
    .container-fluid {
        padding: 5px;
    }
    
    .card {
        margin-bottom: 1rem;
    }
    
    .card-header {
        padding: 0.5rem;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    .chart-area {
        height: 200px !important;
    }
    
    .chart-area canvas {
        max-height: 200px !important;
    }
    
    .h6 {
        font-size: 0.9rem;
    }
    
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    /* Hide some elements on very small screens */
    .text-xs {
        font-size: 0.7rem;
    }
    
    .fa-2x {
        font-size: 1.5em !important;
    }
}

/* ===== FLOATING ACTION BUTTON ===== */
.fab-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}

@media (max-width: 768px) {
    .fab-container {
        bottom: 15px;
        right: 15px;
    }
}

.fab-main {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(45deg, #4e73df, #224abe);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    position: relative;
}

@media (max-width: 768px) {
    .fab-main {
        width: 48px;
        height: 48px;
    }
}

.fab-main:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
}

.fab-option {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #fff;
    color: #4e73df;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 10px;
    transition: all 0.3s ease;
    position: relative;
    border: 2px solid #4e73df;
}

@media (max-width: 768px) {
    .fab-option {
        width: 40px;
        height: 40px;
        margin-bottom: 8px;
    }
}

.fab-option:hover {
    background: #4e73df;
    color: white;
    transform: scale(1.05);
}

.fab-tooltip {
    position: absolute;
    right: 60px;
    background: #333;
    color: white;
    padding: 8px 12px;
    border-radius: 4px;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
}

@media (max-width: 768px) {
    .fab-tooltip {
        right: 50px;
        font-size: 10px;
        padding: 6px 8px;
    }
}

.fab-option:hover .fab-tooltip {
    opacity: 1;
}

/* ===== TABLE IMPROVEMENTS ===== */
.table th {
    background-color: #f8f9fc;
    border-top: none;
    font-weight: 600;
    font-size: 0.875rem;
    position: sticky;
    top: 0;
    z-index: 10;
}

@media (max-width: 768px) {
    .table th {
        font-size: 0.75rem;
        padding: 0.5rem 0.25rem;
    }
    
    .table td {
        font-size: 0.75rem;
        padding: 0.5rem 0.25rem;
    }
}

.table-responsive {
    border-radius: 0.35rem;
    max-height: 500px;
    overflow-y: auto;
}

@media (max-width: 768px) {
    .table-responsive {
        max-height: 300px;
        font-size: 0.7rem;
    }
}

/* ===== LOADING STATES ===== */
.loading-row {
    text-align: center;
    padding: 20px;
    color: #858796;
}

@media (max-width: 768px) {
    .loading-row {
        padding: 15px;
        font-size: 0.8rem;
    }
}

/* ===== STATUS BADGES ===== */
.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
    text-align: center;
    min-width: 70px;
}

@media (max-width: 768px) {
    .status-badge {
        padding: 3px 8px;
        font-size: 0.65rem;
        min-width: 60px;
    }
}

.status-pending { 
    background-color: #fef3cd; 
    color: #856404; 
}

.status-waiting { 
    background-color: #fff3cd; 
    color: #856404; 
}

.status-in-progress { 
    background-color: #cff4fc; 
    color: #055160; 
}

.status-accepted { 
    background-color: #d1ecf1; 
    color: #0c5460; 
}

.status-completed { 
    background-color: #d1e7dd; 
    color: #0f5132; 
}

.status-cancelled { 
    background-color: #f8d7da; 
    color: #721c24; 
}

/* ===== CHART SPECIFIC STYLES ===== */
.chart-area {
    position: relative;
    height: 320px;
    width: 100%;
}

@media (max-width: 992px) {
    .chart-area {
        height: 280px;
    }
}

@media (max-width: 768px) {
    .chart-area {
        height: 250px;
    }
}

@media (max-width: 576px) {
    .chart-area {
        height: 200px;
    }
}

/* ===== CARD IMPROVEMENTS ===== */
.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    transition: all 0.3s;
}

.card:hover {
    box-shadow: 0 0.25rem 2rem 0 rgba(58, 59, 69, 0.2);
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

/* ===== DROPDOWN IMPROVEMENTS ===== */
.dropdown-menu {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

@media (max-width: 768px) {
    .dropdown-menu {
        font-size: 0.8rem;
    }
}

/* ===== FORM IMPROVEMENTS ===== */
.form-control {
    border: 1px solid #d1d3e2;
    border-radius: 0.35rem;
    transition: all 0.15s ease-in-out;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

@media (max-width: 768px) {
    .form-control {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
    
    .form-label {
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }
}

/* ===== BUTTON IMPROVEMENTS ===== */
.btn {
    border-radius: 0.35rem;
    transition: all 0.15s ease-in-out;
}

.btn:hover {
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .btn {
        font-size: 0.8rem;
        padding: 0.5rem 1rem;
    }
    
    .btn-sm {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
}

/* ===== UTILITY CLASSES ===== */
.text-truncate-mobile {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

@media (max-width: 768px) {
    .hide-mobile {
        display: none !important;
    }
    
    .show-mobile {
        display: block !important;
    }
    
    .text-truncate-mobile {
        max-width: 100px;
    }
}

/* ===== SCROLLBAR STYLING ===== */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

@media (max-width: 768px) {
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
}

/* ===== ANIMATION IMPROVEMENTS ===== */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

/* ===== PRINT STYLES ===== */
@media print {
    .fab-container,
    .btn-group,
    .dropdown,
    #filter-form {
        display: none !important;
    }
    
    .card {
        break-inside: avoid;
        page-break-inside: avoid;
    }
    
    .chart-area {
        height: 300px !important;
    }
}
</style>


<script>

    let cabangChart, kategoriChart, waktuChart, topItemChart, statusChart;
    let currentData = {};
    let fabOpen = false;
    let currentPage = 1;

    document.addEventListener('DOMContentLoaded', function () {
        // Set default date (last 30 days)
        setQuickFilter('month');
        fetchChartData();
        loadRecentReports();
        loadReportsTable();
        startRealTimeUpdates();
        
        // Add responsive classes
        addResponsiveClasses();

        document.getElementById('filter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            fetchChartData();
            loadReportsTable();
        });

        // Real-time filter changes
        document.getElementById('sort_by').addEventListener('change', fetchChartData);
        document.getElementById('limit_data').addEventListener('change', function() {
            updateTopItemsTitle();
            fetchChartData();
        });
        document.getElementById('chart_type').addEventListener('change', fetchChartData);

        // Table search and pagination
        document.getElementById('table-search').addEventListener('input', debounce(loadReportsTable, 500));
        document.getElementById('table-per-page').addEventListener('change', function() {
            currentPage = 1;
            loadReportsTable();
        });

        // Comparison form
        document.getElementById('comparison-form').addEventListener('submit', function(e) {
            e.preventDefault();
            loadComparisonData();
        });
        
        // Handle window resize
        window.addEventListener('resize', debounce(handleResize, 250));
    });
    
    function addResponsiveClasses() {
        // Add responsive classes to elements
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.classList.add('fade-in');
        });
        
        // Add mobile-specific classes
        if (window.innerWidth <= 768) {
            document.body.classList.add('mobile-view');
        }
    }
    
    function handleResize() {
        // Redraw charts on resize
        if (cabangChart) cabangChart.resize();
        if (kategoriChart) kategoriChart.resize();
        if (waktuChart) waktuChart.resize();
        if (topItemChart) topItemChart.resize();
        if (statusChart) statusChart.resize();
        
        // Update mobile classes
        if (window.innerWidth <= 768) {
            document.body.classList.add('mobile-view');
        } else {
            document.body.classList.remove('mobile-view');
        }
    }

    function showLoading(show) {
        document.getElementById('loading-indicator').style.display = show ? 'block' : 'none';
    }

    function updateTopItemsTitle() {
        const limit = document.getElementById('limit_data').value;
        const title = limit === 'all' ? 'Semua Item yang Dilaporkan' : `Top ${limit} Item Paling Sering Dilaporkan`;
        document.getElementById('top-items-title').textContent = title;
    }

    function setQuickFilter(period, evt) {
        const today = new Date();
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        let startDate = new Date(today);
        
        switch(period) {
            case 'today':
                startDate = new Date(today);
                break;
            case 'week':
                startDate.setDate(today.getDate() - 7);
                break;
            case 'month':
                startDate.setDate(today.getDate() - 30);
                break;
            case 'quarter':
                startDate.setMonth(today.getMonth() - 3);
                break;
            case 'year':
                startDate.setFullYear(today.getFullYear() - 1);
                break;
        }
        
        startDateInput.value = startDate.toISOString().split('T')[0];
        endDateInput.value = today.toISOString().split('T')[0];
        
        // Remove active class from all buttons and add to clicked one
        document.querySelectorAll('.btn-group .btn').forEach(btn => {
            btn.classList.remove('active');
        });
        if (evt && evt.target) {
            evt.target.classList.add('active');
        }
        
        fetchChartData();
        loadRecentReports();
        loadReportsTable();
    }

    function clearFilters() {
        document.getElementById('filter-form').reset();
        document.getElementById('sort_by').value = 'total_desc';
        document.getElementById('limit_data').value = '5';
        document.getElementById('chart_type').value = 'bar';
        updateTopItemsTitle();
        fetchChartData();
        loadReportsTable();
        loadRecentReports();
    }

    function fetchChartData() {
        showLoading(true);
        
        const formData = new FormData(document.getElementById('filter-form'));
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/reports/charts?${params.toString()}`, {
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            currentData = data;
            updateSummaryCards(data.summary);
            updateCabangChart(data.cabangData);
            updateKategoriChart(data.kategoriData);
            updateWaktuChart(data.waktuData);
            updateTopItemChart(data.topItemData);
            updateStatusChart(data.statusData);
            showLoading(false);
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            showLoading(false);
            showErrorMessage('Terjadi kesalahan saat memuat data. Silakan coba lagi.');
        });
    }

    function loadRecentReports() {
        const formData = new FormData(document.getElementById('filter-form'));
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        
        params.append('limit', '10'); // Limit to 10 recent reports

        fetch(`/reports/widgets?${params.toString()}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            updateRecentReports(data.recent_reports || []);
        })
        .catch(error => {
            console.error('Error loading recent reports:', error);
            document.getElementById('recent-reports').innerHTML = 
                '<p class="text-muted text-center">Error loading recent reports</p>';
        });
    }

    function updateRecentReports(reports) {
        const container = document.getElementById('recent-reports');
        if (!reports || reports.length === 0) {
            container.innerHTML = '<p class="text-muted text-center">Tidak ada laporan terbaru</p>';
            return;
        }

        const html = reports.map(report => `
            <div class="border-bottom pb-2 mb-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 font-weight-bold text-sm">${report.location || 'Tidak Ada'}</h6>
                        <p class="mb-1 text-xs text-muted">${report.item || 'Tidak Ada'}</p>
                        <small class="text-muted">${report.date || 'Tidak Ada'}</small>
                    </div>
                    <span class="status-badge status-${(report.status || 'pending').toLowerCase().replace(' ', '-')}">
                        ${report.status || 'Pending'}
                    </span>
                </div>
            </div>
        `).join('');
        
        container.innerHTML = html;
    }

    function loadReportsTable() {
        const formData = new FormData(document.getElementById('filter-form'));
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        
        params.append('search', document.getElementById('table-search').value);
        params.append('per_page', document.getElementById('table-per-page').value);
        params.append('page', currentPage || 1);

        fetch(`/reports/data?${params.toString()}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            updateReportsTable(data);
        })
        .catch(error => {
            console.error('Error loading table data:', error);
            document.getElementById('reports-table-body').innerHTML = 
                '<tr><td colspan="7" class="text-center text-muted">Error loading data</td></tr>';
        });
    }

    function updateReportsTable(data) {
        const tbody = document.getElementById('reports-table-body');
        
        if (!data.data || data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Tidak ada data yang ditemukan</td></tr>';
            document.getElementById('table-pagination').innerHTML = '';
            return;
        }

        const html = data.data.map(report => `
            <tr>
                <td>${report.id || 'N/A'}</td>
                <td>${report.created_at ? moment(report.created_at).format('DD/MM/YYYY HH:mm') : 'N/A'}</td>
                <td>${report.location ? report.location.name : 'Tidak Ada'}</td>
                <td>${report.item ? report.item.name : (report.details && report.details.length > 0 ? report.details[0].item?.name : 'Tidak Ada')}</td>
                <td class="text-truncate" style="max-width: 200px;" title="${report.description || report.issue_description || ''}">
                    ${report.description || report.issue_description || 'Tidak ada deskripsi'}
                </td>
                <td>
                    <span class="status-badge status-${(report.status || 'pending').toLowerCase().replace(' ', '-')}">
                        ${report.status || 'Pending'}
                    </span>
                </td>
                <td>${report.user ? report.user.name : (report.reporter_name || 'Tidak Diketahui')}</td>
            </tr>
        `).join('');
        
        tbody.innerHTML = html;
        updatePagination(data);
    }

    function updatePagination(data) {
        const pagination = document.getElementById('table-pagination');
        
        const info = `
            <div>
                Menampilkan ${data.from || 0} - ${data.to || 0} dari ${data.total || 0} data
            </div>
        `;
        
        let paginationButtons = '';
        if (data.last_page > 1) {
            paginationButtons = `
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-outline-primary ${data.current_page === 1 ? 'disabled' : ''}" 
                            onclick="changePage(${data.current_page - 1})">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="btn btn-sm btn-outline-primary disabled">
                        ${data.current_page} / ${data.last_page}
                    </span>
                    <button class="btn btn-sm btn-outline-primary ${data.current_page === data.last_page ? 'disabled' : ''}" 
                            onclick="changePage(${data.current_page + 1})">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            `;
        }
        
        pagination.innerHTML = info + paginationButtons;
    }

    function changePage(page) {
        currentPage = page;
        loadReportsTable();
    }

    function refreshTable() {
        currentPage = 1;
        loadReportsTable();
    }

    function updateSummaryCards(summary) {
        if (summary) {
            document.getElementById('total-reports').textContent = summary.totalReports || 0;
            document.getElementById('active-branches').textContent = summary.activeBranches || 0;
            document.getElementById('item-categories').textContent = summary.itemCategories || 0;
            document.getElementById('avg-per-day').textContent = (summary.avgPerDay || 0).toFixed(1);
        }
    }

    function updateCabangChart(data) {
        if (cabangChart) cabangChart.destroy();
        const ctx = document.getElementById('cabangChart').getContext('2d');
        
        const chartType = document.getElementById('chart_type').value;
        const type = ['doughnut', 'pie'].includes(chartType) ? chartType : 'bar';
        
        cabangChart = new Chart(ctx, {
            type: type,
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: data.values,
                    backgroundColor: type === 'bar' ? 'rgba(78, 115, 223, 0.8)' : [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                        '#858796', '#5a5c69', '#f8f9fc', '#3a3b45', '#2e59d9'
                    ],
                    borderColor: type === 'bar' ? 'rgba(78, 115, 223, 1)' : '#fff',
                    borderWidth: type === 'bar' ? 1 : 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: type !== 'bar',
                        position: 'bottom'
                    }
                },
                scales: type === 'bar' ? {
                    y: { 
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                } : {}
            }
        });
    }

    function updateKategoriChart(data) {
        if (kategoriChart) kategoriChart.destroy();
        const ctx = document.getElementById('kategoriChart').getContext('2d');
        
        kategoriChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    }

    function updateWaktuChart(data) {
        if (waktuChart) waktuChart.destroy();
        const ctx = document.getElementById('waktuChart').getContext('2d');
        
        waktuChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: "Jumlah Laporan",
                    data: data.values,
                    borderColor: "rgba(78, 115, 223, 1)",
                    backgroundColor: "rgba(78, 115, 223, 0.1)",
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "#fff",
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: { unit: 'day' },
                        title: { 
                            display: true, 
                            text: 'Tanggal',
                            font: { weight: 'bold' }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: { 
                            display: true, 
                            text: 'Jumlah Laporan',
                            font: { weight: 'bold' }
                        },
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    function updateTopItemChart(data) {
        if (topItemChart) topItemChart.destroy();
        const ctx = document.getElementById('topItemChart').getContext('2d');
        
        topItemChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: data.values,
                    backgroundColor: 'rgba(255, 99, 132, 0.8)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: { 
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    // FAB Functions
    function toggleFab() {
        fabOpen = !fabOpen;
        const options = document.querySelectorAll('.fab-option');
        const icon = document.getElementById('fab-icon');
        
        options.forEach((option, index) => {
            if (fabOpen) {
                option.style.display = 'flex';
                setTimeout(() => {
                    option.style.transform = `translateY(-${(index + 1) * 58}px)`;
                    option.style.opacity = '1';
                }, index * 50);
            } else {
                option.style.transform = 'translateY(0)';
                option.style.opacity = '0';
                setTimeout(() => {
                    option.style.display = 'none';
                }, 200);
            }
        });
        
        icon.style.transform = fabOpen ? 'rotate(45deg)' : 'rotate(0deg)';
    }

    function openComparisonModal() {
        $('#comparisonModal').modal('show');
        toggleFab();
    }

    function loadComparisonData() {
        const period1Start = document.getElementById('period1_start').value;
        const period1End = document.getElementById('period1_end').value;
        const period2Start = document.getElementById('period2_start').value;
        const period2End = document.getElementById('period2_end').value;

        if (!period1Start || !period1End || !period2Start || !period2End) {
            showErrorMessage('Harap isi semua tanggal untuk perbandingan');
            return;
        }

        fetch('/reports/comparison', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                period1_start: period1Start,
                period1_end: period1End,
                period2_start: period2Start,
                period2_end: period2End
            })
        })
        .then(response => response.json())
        .then(data => {
            updateComparisonResults(data);
        })
        .catch(error => {
            console.error('Error loading comparison:', error);
            showErrorMessage('Error loading comparison data');
        });
    }

    function updateComparisonResults(data) {
        const container = document.getElementById('comparison-results');
        const trend = data.comparison.trend;
        const trendIcon = trend === 'up' ? 'fa-arrow-up text-success' : 
                         trend === 'down' ? 'fa-arrow-down text-danger' : 
                         'fa-minus text-warning';
        
        const html = `
            <div class="card">
                <div class="card-body">
                    <h5>Hasil Perbandingan</h5>
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h6>Periode 1</h6>
                            <h3 class="text-primary">${data.period1.reports}</h3>
                            <small>${data.period1.start} - ${data.period1.end}</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6>Perubahan</h6>
                            <h3><i class="fas ${trendIcon}"></i> ${Math.abs(data.comparison.percentage_change)}%</h3>
                            <small>${data.comparison.difference > 0 ? '+' : ''}${data.comparison.difference} laporan</small>
                        </div>
                        <div class="col-md-4 text-center">
                            <h6>Periode 2</h6>
                            <h3 class="text-info">${data.period2.reports}</h3>
                            <small>${data.period2.start} - ${data.period2.end}</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.innerHTML = html;
        container.style.display = 'block';
    }

    function refreshAllData() {
        fetchChartData();
        loadRecentReports();
        loadReportsTable();
        toggleFab();
        showSuccessMessage('Data berhasil diperbarui');
    }

    function startRealTimeUpdates() {
        // Update data setiap 5 menit
        setInterval(() => {
            loadRecentReports();
            // Optional: update summary cards
            const formData = new FormData(document.getElementById('filter-form'));
            const params = new URLSearchParams();
            for (let [key, value] of formData.entries()) {
                if (value) params.append(key, value);
            }
            
            fetch(`/reports/realtime-stats?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    // Update real-time indicators if needed
                })
                .catch(error => console.error('Real-time update error:', error));
        }, 300000); // 5 minutes
    }

    // Utility Functions
    function downloadChart(chartId, filename) {
        const canvas = document.getElementById(chartId);
        if (!canvas) {
            showErrorMessage('Chart tidak ditemukan');
            return;
        }

        const link = document.createElement('a');
        link.download = `${filename}-${new Date().toISOString().split('T')[0]}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
        
        showSuccessMessage(`Chart ${filename} berhasil didownload`);
    }

    function downloadAllCharts() {
        const charts = [
            { id: 'cabangChart', name: 'laporan-cabang' },
            { id: 'kategoriChart', name: 'laporan-kategori' },
            { id: 'waktuChart', name: 'tren-waktu' },
            { id: 'topItemChart', name: 'top-items' }
        ];

        const zip = new JSZip();

        charts.forEach(chart => {
            const canvas = document.getElementById(chart.id);
            if (canvas) {
                const dataURL = canvas.toDataURL('image/png');
                const base64Data = dataURL.split(',')[1];
                zip.file(`${chart.name}-${new Date().toISOString().split('T')[0]}.png`, base64Data, {base64: true});
            }
        });

        zip.generateAsync({type: "blob"})
        .then(function(content) {
            const link = document.createElement('a');
            link.download = `all-charts-${new Date().toISOString().split('T')[0]}.zip`;
            link.href = URL.createObjectURL(content);
            link.click();
            showSuccessMessage('Semua chart berhasil didownload dalam ZIP');
        })
        .catch(function(error) {
            showErrorMessage('Gagal membuat ZIP file: ' + error.message);
        });
    }

    function downloadChartDataAsExcel() {
        if (!currentData || Object.keys(currentData).length === 0) {
            showErrorMessage('Data chart belum dimuat');
            return;
        }

        const wb = XLSX.utils.book_new();
        
        // Add summary data
        if (currentData.summary) {
            const summaryData = [
                ['Metrik', 'Nilai'],
                ['Total Laporan', currentData.summary.totalReports || 0],
                ['Cabang Aktif', currentData.summary.activeBranches || 0],
                ['Kategori Item', currentData.summary.itemCategories || 0],
                ['Rata-rata per Hari', (currentData.summary.avgPerDay || 0).toFixed(1)]
            ];
            const summaryWS = XLSX.utils.aoa_to_sheet(summaryData);
            XLSX.utils.book_append_sheet(wb, summaryWS, 'Ringkasan');
        }

        // Add cabang data
        if (currentData.cabangData) {
            const cabangData = [['Cabang', 'Jumlah Laporan']];
            currentData.cabangData.labels.forEach((label, index) => {
                cabangData.push([label, currentData.cabangData.values[index]]);
            });
            const cabangWS = XLSX.utils.aoa_to_sheet(cabangData);
            XLSX.utils.book_append_sheet(wb, cabangWS, 'Data Cabang');
        }

        // Add kategori data
        if (currentData.kategoriData) {
            const kategoriData = [['Kategori', 'Jumlah Laporan']];
            currentData.kategoriData.labels.forEach((label, index) => {
                kategoriData.push([label, currentData.kategoriData.values[index]]);
            });
            const kategoriWS = XLSX.utils.aoa_to_sheet(kategoriData);
            XLSX.utils.book_append_sheet(wb, kategoriWS, 'Data Kategori');
        }

        // Add waktu data
        if (currentData.waktuData) {
            const waktuData = [['Tanggal', 'Jumlah Laporan']];
            currentData.waktuData.labels.forEach((label, index) => {
                waktuData.push([label, currentData.waktuData.values[index]]);
            });
            const waktuWS = XLSX.utils.aoa_to_sheet(waktuData);
            XLSX.utils.book_append_sheet(wb, waktuWS, 'Data Waktu');
        }

        // Add top items data
        if (currentData.topItemData) {
            const topItemData = [['Item', 'Jumlah Laporan']];
            currentData.topItemData.labels.forEach((label, index) => {
                topItemData.push([label, currentData.topItemData.values[index]]);
            });
            const topItemWS = XLSX.utils.aoa_to_sheet(topItemData);
            XLSX.utils.book_append_sheet(wb, topItemWS, 'Top Items');
        }

        // Save the file
        const filename = `chart-data-${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, filename);
        showSuccessMessage('Data chart berhasil didownload sebagai Excel');
    }

    function exportData() {
        const params = new URLSearchParams(new FormData(document.getElementById('filter-form')));
        window.open(`/reports/export?${params.toString()}`, '_blank');
        toggleFab();
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function showErrorMessage(message) {
        alert('Error: ' + message);
    }

    function showSuccessMessage(message) {
        console.log('Success: ' + message);
    }

    

    function updateStatusChart(data) {
        if (statusChart) statusChart.destroy();
        const ctx = document.getElementById('statusChart').getContext('2d');
        
        // Define colors for each status
        const statusColors = {
            'waiting': '#f6c23e',     // Yellow for waiting
            'accepted': '#36b9cc',    // Blue for accepted  
            'completed': '#1cc88a'    // Green for completed
        };
        
        const backgroundColors = data.labels.map(label => statusColors[label] || '#858796');
        
        statusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels.map(label => {
                    // Capitalize first letter for display
                    return label.charAt(0).toUpperCase() + label.slice(1);
                }),
                datasets: [{
                    data: data.values,
                    backgroundColor: backgroundColors,
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: window.innerWidth <= 768 ? 'bottom' : 'right',
                        labels: {
                            padding: window.innerWidth <= 768 ? 10 : 20,
                            usePointStyle: true,
                            font: {
                                size: window.innerWidth <= 768 ? 10 : 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
</script>
@endpush