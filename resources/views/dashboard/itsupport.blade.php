@extends('master')

@section('title', 'Dashboard IT Support')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Dashboard IT Support</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <p>Halo, selamat datang kembali IT Support!</p>
            <p>Silakan cek laporan kerusakan yang belum dikerjakan atau sudah ditugaskan ke Anda.</p>
            <a href="{{ route('report.create') }}" class="btn btn-primary btn-sm">Buat Laporan Baru</a>
        </div>
    </div>
</div>
@endsection
