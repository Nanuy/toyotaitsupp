@extends('master')
@section('title','Dashboard')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Dashboard Laporan IT Support</h3>
        <div class="btn-group">
            <button class="btn btn-sm btn-success" onclick="downloadAllDashboardCharts()">
                <i class="fas fa-images mr-1"></i>Download All Charts
            </button>
            <button class="btn btn-sm btn-info" onclick="downloadDashboardDataAsExcel()">
                <i class="fas fa-file-excel mr-1"></i>Download Excel
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Top 10 Perangkat Sering Rusak</h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('chartItems', 'top-perangkat-rusak')">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <div class="card-body">
                    <canvas id="chartItems" style="height:320px"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Frekuensi Penanganan IT Support</h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('chartTeams', 'frekuensi-it-support')">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <div class="card-body">
                    <canvas id="chartTeams" style="height:320px"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Laporan per Bulan</h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('chartMonths', 'laporan-per-bulan')">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <div class="card-body">
                    <canvas id="chartMonths" style="height:320px"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Laporan per Triwulan</h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('chartQuarts', 'laporan-per-triwulan')">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <div class="card-body">
                    <canvas id="chartQuarts" style="height:320px"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Cabang dengan Laporan Terbanyak</h6>
                    <button class="btn btn-sm btn-outline-primary" onclick="downloadChart('chartBranch', 'laporan-per-cabang')">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
                <div class="card-body">
                    <canvas id="chartBranch" style="height:320px"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', ()=>{

  const itemLabels   = @json($itemLabels);
  const itemData     = @json($itemData);
  const teamLabels   = @json($teamLabels);
  const teamData     = @json($teamData);
  const monthLabels  = @json($monthLabels);
  const monthData    = @json($monthData);
  const quartLabels  = @json($quarterLabels);
  const quartData    = @json($quarterData);
  const branchLabels = @json($branchLabels);
  const branchData   = @json($branchData);

  function make(id, type, lbl, data, title) {
  const ctx = document.getElementById(id);
  if (!ctx) return;

  // Warna gradasi (biru ke ungu)
  const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
  gradient.addColorStop(0, 'rgba(54, 162, 235, 0.9)');
  gradient.addColorStop(1, 'rgba(153, 102, 255, 0.9)');

  new Chart(ctx, {
    type,
    data: {
      labels: lbl,
      datasets: [{
        label: title,
        data: data,
        backgroundColor: type === 'line' ? 'transparent' : gradient,
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 2,
        pointBackgroundColor: '#fff',
        tension: 0.4, // buat garis line agak melengkung
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        title: {
          display: true,
          text: title,
          font: {
            size: 16,
            weight: 'bold'
          }
        },
        tooltip: {
          backgroundColor: '#333',
          titleColor: '#fff',
          bodyColor: '#fff',
          borderColor: '#888',
          borderWidth: 1
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { color: '#333' },
          grid: { color: '#eee' }
        },
        x: {
          ticks: { color: '#333' },
          grid: { display: false }
        }
      },
      animation: {
        duration: 1200,
        easing: 'easeOutBounce'
      }
    }
  });
}


  make('chartItems','bar',   itemLabels, itemData,'Top 10 Perangkat Sering Rusak');
  make('chartTeams','bar',   teamLabels, teamData,'Frekuensi Penanganan IT Support');
  make('chartMonths','line', monthLabels,monthData,'Laporan per Bulan');
  make('chartQuarts','bar',  quartLabels,quartData,'Laporan per Triwulan');
  make('chartBranch','bar',  branchLabels,branchData,'Cabang dengan Laporan Terbanyak');
});

// Dashboard chart download functions
function downloadChart(chartId, filename) {
    const canvas = document.getElementById(chartId);
    if (!canvas) {
        alert('Chart tidak ditemukan');
        return;
    }

    const link = document.createElement('a');
    link.download = `${filename}-${new Date().toISOString().split('T')[0]}.png`;
    link.href = canvas.toDataURL('image/png');
    link.click();
}

function downloadAllDashboardCharts() {
    const charts = [
        { id: 'chartItems', name: 'top-perangkat-rusak' },
        { id: 'chartTeams', name: 'frekuensi-it-support' },
        { id: 'chartMonths', name: 'laporan-per-bulan' },
        { id: 'chartQuarts', name: 'laporan-per-triwulan' },
        { id: 'chartBranch', name: 'laporan-per-cabang' }
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
        link.download = `dashboard-charts-${new Date().toISOString().split('T')[0]}.zip`;
        link.href = URL.createObjectURL(content);
        link.click();
        alert('Semua chart dashboard berhasil didownload dalam ZIP');
    })
    .catch(function(error) {
        alert('Gagal membuat ZIP file: ' + error.message);
    });
}

function downloadDashboardDataAsExcel() {
    const wb = XLSX.utils.book_new();
    
    // Add items data
    const itemsData = [['Perangkat', 'Jumlah Laporan']];
    itemLabels.forEach((label, index) => {
        itemsData.push([label, itemData[index]]);
    });
    const itemsWS = XLSX.utils.aoa_to_sheet(itemsData);
    XLSX.utils.book_append_sheet(wb, itemsWS, 'Top Perangkat Rusak');

    // Add teams data
    const teamsData = [['IT Support', 'Jumlah Penanganan']];
    teamLabels.forEach((label, index) => {
        teamsData.push([label, teamData[index]]);
    });
    const teamsWS = XLSX.utils.aoa_to_sheet(teamsData);
    XLSX.utils.book_append_sheet(wb, teamsWS, 'Frekuensi IT Support');

    // Add months data
    const monthsData = [['Bulan', 'Jumlah Laporan']];
    monthLabels.forEach((label, index) => {
        monthsData.push([label, monthData[index]]);
    });
    const monthsWS = XLSX.utils.aoa_to_sheet(monthsData);
    XLSX.utils.book_append_sheet(wb, monthsWS, 'Laporan per Bulan');

    // Add quarters data
    const quartsData = [['Triwulan', 'Jumlah Laporan']];
    quartLabels.forEach((label, index) => {
        quartsData.push([label, quartData[index]]);
    });
    const quartsWS = XLSX.utils.aoa_to_sheet(quartsData);
    XLSX.utils.book_append_sheet(wb, quartsWS, 'Laporan per Triwulan');

    // Add branch data
    const branchData = [['Cabang', 'Jumlah Laporan']];
    branchLabels.forEach((label, index) => {
        branchData.push([label, branchData[index]]);
    });
    const branchWS = XLSX.utils.aoa_to_sheet(branchData);
    XLSX.utils.book_append_sheet(wb, branchWS, 'Laporan per Cabang');

    // Save the file
    const filename = `dashboard-data-${new Date().toISOString().split('T')[0]}.xlsx`;
    XLSX.writeFile(wb, filename);
    alert('Data dashboard berhasil didownload sebagai Excel');
}
</script>
@endpush
