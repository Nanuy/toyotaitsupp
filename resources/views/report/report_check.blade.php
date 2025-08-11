
@extends('master')

@section('title', 'Cek Status Laporan')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-search me-2"></i>Cek Status Laporan</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('report.check') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-barcode me-1"></i>Kode Laporan</label>
                            <input type="text" name="report_code" class="form-control" required placeholder="Contoh: RPT-000123">
                        </div>

                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-lock me-1"></i>Password</label>
                            <input type="text" name="report_pass" class="form-control" required placeholder="6 karakter unik">
                        </div>

                        <button type="submit" class="btn btn-info w-100">
                            <i class="fas fa-search me-1"></i>Cek Laporan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
