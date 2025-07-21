@extends('master')
@section('title','Dashboard')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Dashboard Laporan IT Support</h3>

    <div class="row g-4">
        <div class="col-lg-6"><canvas id="chartItems"  style="height:320px"></canvas></div>
        <div class="col-lg-6"><canvas id="chartTeams"  style="height:320px"></canvas></div>
        <div class="col-lg-6"><canvas id="chartMonths" style="height:320px"></canvas></div>
        <div class="col-lg-6"><canvas id="chartQuarts" style="height:320px"></canvas></div>
        <div class="col-lg-12"><canvas id="chartBranch" style="height:320px"></canvas></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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


  make('chartItems','bar',   itemLabels, itemData,'Top 10 Perangkat Sering Rusak');
  make('chartTeams','bar',   teamLabels, teamData,'Frekuensi Penanganan IT Support');
  make('chartMonths','line', monthLabels,monthData,'Laporan per Bulan');
  make('chartQuarts','bar',  quartLabels,quartData,'Laporan per Triwulan');
  make('chartBranch','bar',  branchLabels,branchData,'Cabang dengan Laporan Terbanyak');
});
</script>
@endpush

