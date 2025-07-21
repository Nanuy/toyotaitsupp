@extends('master')
@section('title','Chart: Perangkat Rusak')

@section('content')
<div class="container mt-4">
    <h4 class="mb-4">Perangkat Rusak Berdasarkan Divisi, Cabang & Pelapor</h4>
    <canvas id="perangkatChart" style="height:400px"></canvas>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = @json($labels);
const data   = @json($data);

new Chart(document.getElementById('perangkatChart'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Jumlah Laporan',
            data: data,
            backgroundColor: 'rgba(54, 162, 235, 0.7)'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Top Kasus Perangkat Rusak Berdasarkan Cabang, Divisi & Pelapor'
            },
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
