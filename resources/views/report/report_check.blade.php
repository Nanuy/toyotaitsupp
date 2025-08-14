<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Laporan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-search me-2"></i>Cek Status Laporan</h5>
                    </div>
                    <div class="card-body">
                        <!-- Error message display -->
                        <div id="error-message" class="alert alert-danger d-none"></div>

                        <form method="POST" action="/cek-laporan" id="reportForm">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-barcode me-1"></i>Kode Laporan</label>
                                <input type="text" name="report_code" id="report_code" class="form-control" required placeholder="Contoh: RPT-000123">
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-lock me-1"></i>Password</label>
                                <input type="text" name="report_pass" id="report_pass" class="form-control" required placeholder="6 karakter unik">
                            </div>

                            <button type="submit" class="btn btn-info w-100" id="submitBtn">
                                <i class="fas fa-search me-1"></i>Cek Laporan
                            </button>
                        </form>
                           
                    </div>
                    <div class="d-flex justify-content-between">
                                <a href="{{ route('report.public') }}" class="btn btn-secondary btn-icon-split">
                                    <span class="icon text-white-50"><i class="fa-solid fa-arrow-left"></i></span>
                                    <span class="text">Kembali</span>
                                </a>
                            </div>
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form handling - ready for backend integration
        document.getElementById('reportForm').addEventListener('submit', function(e) {
            // Remove preventDefault() to allow normal form submission
            // e.preventDefault();
            
            const reportCode = document.getElementById('report_code').value.trim();
            const reportPass = document.getElementById('report_pass').value.trim();
            
            // Basic validation
            if (!reportCode || !reportPass) {
                e.preventDefault();
                showError('Kode laporan dan password harus diisi!');
                return;
            }
        });
        
        function showError(message) {
            const errorDiv = document.getElementById('error-message');
            errorDiv.textContent = message;
            errorDiv.classList.remove('d-none');
        }
    </script>
</body>
</html>