<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan IT Support</title>
    
    <!-- SB Admin 2 CSS -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .service-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 0.5rem;
        }
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .card-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <!-- Outer Row -->
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="text-center mt-5 mb-5">
                    <h1 class="h2 text-white mb-3"><i class="fas fa-headset"></i> Layanan IT Support</h1>
                    <p class="lead text-white-50">Pilih layanan yang Anda butuhkan</p>
                </div>

                <div class="row justify-content-center">
                    <!-- Buat Laporan Baru -->
                    <div class="col-xl-4 col-lg-5 col-md-6 mb-4">
                        <a href="{{ route('lapor.create') }}" class="card service-card h-100 text-decoration-none">
                            <div class="card-body text-center py-5">
                                <div class="text-primary mb-3">
                                    <i class="fas fa-plus-circle card-icon"></i>
                                </div>
                                <h5 class="card-title mb-3">Buat Laporan Baru</h5>
                                <p class="card-text text-muted">Laporkan kerusakan atau masalah perangkat IT Anda</p>
                            </div>
                        </a>
                    </div>

                    <!-- Cek Status Laporan -->
                    <div class="col-xl-4 col-lg-5 col-md-6 mb-4">
                        <a href="{{ route('report.check') }}" class="card service-card h-100 text-decoration-none">
                            <div class="card-body text-center py-5">
                                <div class="text-success mb-3">
                                    <i class="fas fa-search card-icon"></i>
                                </div>
                                <h5 class="card-title mb-3">Cek Status Laporan</h5>
                                <p class="card-text text-muted">Pantau perkembangan laporan yang sudah Anda buat</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
