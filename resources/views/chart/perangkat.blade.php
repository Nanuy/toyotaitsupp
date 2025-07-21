@extends('master')
@section('title','Chart Perangkat')

@section('content')
<div class="container mt-4">
    <h4 class="text-center mb-4">Top 10 Perangkat Sering Rusak<br><small>(berdasarkan Divisi, Cabang, dan Pelapor)</small></h4>
    
    <div style="width: 100%; max-width: 900px; height: 420px; margin: auto;">
        <canvas id="chartPerangkat"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labels = @json($labels);
const data   = @json($data);

new Chart(document.getElementById('chartPerangkat'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Jumlah Kerusakan',
            data: data,
            backgroundColor: 'rgba(54, 162, 235, 0.8)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            title: {
                display: true,
                text: 'Top 10 Perangkat Rusak Berdasarkan Divisi, Cabang, dan Pelapor'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Jumlah: ' + context.raw;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Jumlah Kerusakan' }
            },
            x: {
                ticks: {
                    callback: function(value) {
                        return this.getLabelForValue(value).substring(0, 80
                            
                        );
                    },
                    maxRotation: 45,
                    minRotation: 0,
                    autoSkip: false
                }
            }
        }
    }
});
</script>
@endpush
