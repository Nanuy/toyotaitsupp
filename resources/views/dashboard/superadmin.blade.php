@extends('master')

@section('title', 'Dashboard Superadmin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard Superadmin</h1>

    <div class="row">

        <!-- Jumlah IT Support -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jumlah IT Support</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">xx Orang</div>
                </div>
            </div>
        </div>

        <!-- Total Laporan -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Laporan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">xx Laporan</div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
