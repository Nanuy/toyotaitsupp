@extends('layouts.itsupport')

@section('title', 'Dashboard IT Support - Analisis Chart')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line mr-2"></i>Analisis Chart IT Support
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
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Laporan Menunggu
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="waiting-reports">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                                Laporan Diterima
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="accepted-reports">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Laporan Selesai
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="completed-reports">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row">
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-pie mr-2"></i>Distribusi Status Laporan
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="downloadChart('statusChart', 'distribusi-status')">
                                <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                                Download Chart
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users mr-2"></i>Performa IT Support
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="downloadChart('performanceChart', 'performa-it-support')">
                                <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                                Download Chart
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="performanceChart"></canvas>
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
                        <i class="fas fa-chart-line mr-2"></i>Tren Laporan Bulanan
                    </h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="#" onclick="downloadChart('monthlyChart', 'tren-bulanan')">
                                <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                                Download Chart
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyChart"></canvas>
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
                            <a class="dropdown-item" href="#" onclick="downloadChart('topItemsChart', 'top-items')">
                                <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                                Download Chart
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="topItemsChart"></canvas>
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
</div>

<!-- Floating Action Button -->
<div class="fab-container">
    <div class="fab-main" onclick="toggleFab()">
        <i class="fas fa-plus" id="fab-icon"></i>
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
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
<!-- Libraries for chart download functionality -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<style>
/* Floating Action Button Styles */
.fab-container {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 1000;
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

.fab-option:hover .fab-tooltip {
    opacity: 1;
}

/* Loading states */
.loading-row {
    text-align: center;
    padding: 20px;
    color: #858796;
}

/* Status badges */
.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-pending { background-color: #fef3cd; color: #856404; }
.status-accepted { background-color: #cff4fc; color: #055160; }
.status-completed { background-color: #d1e7dd; color: #0f5132; }
.status-cancelled { background-color: #f8d7da; color: #721c24; }
</style>

<script>
    let statusChart, performanceChart, monthlyChart, topItemsChart;
    let currentData = {};
    let fabOpen = false;
    let currentPage = 1;

    document.addEventListener('DOMContentLoaded', function () {
        // Set default date (last 30 days)
        setQuickFilter('month');
        fetchChartData();
        loadRecentReports();
        loadReportsTable();

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
    });

    function showLoading(show) {
        document.getElementById('loading-indicator').style.display = show ? 'block' : 'none';
    }

    function updateTopItemsTitle() {
        const limit = document.getElementById('limit_data').value;
        const title = limit === 'all' ? 'Semua Item yang Dilaporkan' : `Top ${limit} Item Paling Sering Dilaporkan`;
        document.getElementById('top-items-title').textContent = title;
    }

    function setQuickFilter(period) {
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
        if (event && event.target) {
            event.target.classList.add('active');
        }
        
        fetchChartData();
    }

    function clearFilters() {
        document.getElementById('filter-form').reset();
        document.getElementById('sort_by').value = 'total_desc';
        document.getElementById('limit_data').value = '5';
        document.getElementById('chart_type').value = 'bar';
        updateTopItemsTitle();
        fetchChartData();
    }

    function fetchChartData() {
        showLoading(true);
        
        const formData = new FormData(document.getElementById('filter-form'));
        const params = new URLSearchParams();
        
        for (let [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(`/itsupport/charts?${params.toString()}`, {
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
            updateStatusChart(data.statusData);
            updatePerformanceChart(data.itSupportPerformanceData);
            updateMonthlyChart(data.monthlyData);
            updateTopItemsChart(data.topItemsData);
            showLoading(false);
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            showLoading(false);
            showErrorMessage('Terjadi kesalahan saat memuat data. Silakan coba lagi.');
        });
    }

    function loadRecentReports() {
        fetch('/reports/widgets', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            updateRecentReports(data.recent_reports);
        })
        .catch(error => console.error('Error loading recent reports:', error));
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
                        <h6 class="mb-1 font-weight-bold text-sm">${report.location}</h6>
                        <p class="mb-1 text-xs text-muted">${report.item}</p>
                        <small class="text-muted">${report.date}</small>
                    </div>
                    <span class="status-badge status-${report.status.toLowerCase().replace(' ', '-')}">
                        ${report.status}
                    </span>
                </div>
            </div>
        `).join('');
        
        container.innerHTML = html;
    }

    function updateSummaryCards(summary) {
        if (summary) {
            document.getElementById('total-reports').textContent = summary.totalReports || 0;
            document.getElementById('waiting-reports').textContent = summary.waitingReports || 0;
            document.getElementById('accepted-reports').textContent = summary.acceptedReports || 0;
            document.getElementById('completed-reports').textContent = summary.completedReports || 0;
        }
    }

    function updateStatusChart(data) {
        if (statusChart) statusChart.destroy();
        const ctx = document.getElementById('statusChart').getContext('2d');
        
        statusChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: ['#f6c23e', '#36b9cc', '#1cc88a', '#e74a3b', '#858796'],
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

    function updatePerformanceChart(data) {
        if (performanceChart) performanceChart.destroy();
        const ctx = document.getElementById('performanceChart').getContext('2d');
        
        performanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: data.values,
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 1
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
                    y: { 
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    function updateMonthlyChart(data) {
        if (monthlyChart) monthlyChart.destroy();
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        
        monthlyChart = new Chart(ctx, {
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
                        title: { 
                            display: true, 
                            text: 'Bulan',
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

    function updateTopItemsChart(data) {
        if (topItemsChart) topItemsChart.destroy();
        const ctx = document.getElementById('topItemsChart').getContext('2d');
        
        topItemsChart = new Chart(ctx, {
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

    function refreshAllData() {
        fetchChartData();
        loadRecentReports();
        toggleFab();
        showSuccessMessage('Data berhasil diperbarui');
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
            { id: 'statusChart', name: 'distribusi-status' },
            { id: 'performanceChart', name: 'performa-it-support' },
            { id: 'monthlyChart', name: 'tren-bulanan' },
            { id: 'topItemsChart', name: 'top-items' }
        ];

        const zip = new JSZip();
        const promises = [];

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
            link.download = `itsupport-charts-${new Date().toISOString().split('T')[0]}.zip`;
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
                ['Laporan Menunggu', currentData.summary.waitingReports || 0],
                ['Laporan Diterima', currentData.summary.acceptedReports || 0],
                ['Laporan Selesai', currentData.summary.completedReports || 0],
                ['Rata-rata per Hari', (currentData.summary.avgPerDay || 0).toFixed(1)]
            ];
            const summaryWS = XLSX.utils.aoa_to_sheet(summaryData);
            XLSX.utils.book_append_sheet(wb, summaryWS, 'Ringkasan');
        }

        // Add status data
        if (currentData.statusData) {
            const statusData = [['Status', 'Jumlah Laporan']];
            currentData.statusData.labels.forEach((label, index) => {
                statusData.push([label, currentData.statusData.values[index]]);
            });
            const statusWS = XLSX.utils.aoa_to_sheet(statusData);
            XLSX.utils.book_append_sheet(wb, statusWS, 'Data Status');
        }

        // Add performance data
        if (currentData.itSupportPerformanceData) {
            const performanceData = [['IT Support', 'Jumlah Laporan']];
            currentData.itSupportPerformanceData.labels.forEach((label, index) => {
                performanceData.push([label, currentData.itSupportPerformanceData.values[index]]);
            });
            const performanceWS = XLSX.utils.aoa_to_sheet(performanceData);
            XLSX.utils.book_append_sheet(wb, performanceWS, 'Data Performa');
        }

        // Add monthly data
        if (currentData.monthlyData) {
            const monthlyData = [['Bulan', 'Jumlah Laporan']];
            currentData.monthlyData.labels.forEach((label, index) => {
                monthlyData.push([label, currentData.monthlyData.values[index]]);
            });
            const monthlyWS = XLSX.utils.aoa_to_sheet(monthlyData);
            XLSX.utils.book_append_sheet(wb, monthlyWS, 'Data Bulanan');
        }

        // Add top items data
        if (currentData.topItemsData) {
            const topItemsData = [['Item', 'Jumlah Laporan']];
            currentData.topItemsData.labels.forEach((label, index) => {
                topItemsData.push([label, currentData.topItemsData.values[index]]);
            });
            const topItemsWS = XLSX.utils.aoa_to_sheet(topItemsData);
            XLSX.utils.book_append_sheet(wb, topItemsWS, 'Top Items');
        }

        // Save the file
        const filename = `itsupport-chart-data-${new Date().toISOString().split('T')[0]}.xlsx`;
        XLSX.writeFile(wb, filename);
        showSuccessMessage('Data chart berhasil didownload sebagai Excel');
    }

    function exportData() {
        const params = new URLSearchParams(new FormData(document.getElementById('filter-form')));
        window.open(`/itsupport/export?${params.toString()}`, '_blank');
        toggleFab();
    }

    function showErrorMessage(message) {
        alert('Error: ' + message);
    }

    function showSuccessMessage(message) {
        console.log('Success: ' + message);
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
</script>
@endpush 