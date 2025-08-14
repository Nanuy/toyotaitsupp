@extends('layouts.itsupport')

@section('title', 'Form Pemeriksaan Perangkat IT')

@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Form Pemeriksaan Perangkat IT</h1>
    <a href="{{ route('it.show', $report->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Detail
    </a>

    <a href="{{ route('it.show', $report->id) }}" class="btn btn-secondary">
        <i class="fas fa-times me-1"></i> Batal
    </a>
</div>

{{-- Alert Messages --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Info Laporan --}}
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Informasi Laporan #{{ $report->id }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Pelapor:</strong> {{ $report->reporter_name }}</p>
                <p><strong>Lokasi:</strong> {{ $report->location->name ?? '-' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Divisi:</strong> {{ $report->division ?? '-' }}</p>
                <p><strong>Tanggal:</strong> {{ $report->created_at->format('d/m/Y') }}</p>
            </div>
        </div>
        <p><strong>Deskripsi Masalah:</strong></p>
        <div class="border rounded p-3 bg-light">
            {{ $report->description }}
        </div>
    </div>
</div>

{{-- Form Data Pemeriksaan --}}
<div class="card">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-clipboard-check me-2"></i>Data Pemeriksaan Perangkat IT
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('report.update-inspection', $report->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Merek / Tipe <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="merek_tipe" 
                               value="{{ $report->merek_tipe }}" 
                               placeholder="Contoh: All BP" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Dampak Ditimbulkan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="dampak_ditimbulkan" rows="2" 
                                  placeholder="Contoh: Susah absen" required>{{ $report->dampak_ditimbulkan }}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Tindakan Yang Dilakukan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="tindakan_dilakukan" rows="3" 
                                  placeholder="Contoh: Laporan masuk, Pengadaan ms office baru" required>{{ $report->tindakan_dilakukan }}</textarea>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label class="form-label">Rekomendasi Teknis <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rekomendasi_teknis" rows="2" 
                                  placeholder="Contoh: Pembelian Mesin absen baru" required>{{ $report->rekomendasi_teknis }}</textarea>
                    </div>
                </div>
            </div>
            
            <div class="form-group mb-4">
                <label class="form-label">Spesifikasi Pengadaan <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="spesifikasi_pengadaan" 
                       value="{{ $report->spesifikasi_pengadaan }}" 
                       placeholder="Contoh: Solution X903" required>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan Data Pemeriksaan
                </button>
                <a href="{{ route('it.show', $report->id) }}" class="btn btn-secondary">
                    <i class="fas fa-times me-1"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tombol Generate Surat (jika data sudah lengkap) --}}
@if($report->merek_tipe && $report->dampak_ditimbulkan && $report->tindakan_dilakukan && $report->rekomendasi_teknis && $report->spesifikasi_pengadaan)
<div class="card mt-4">
    <div class="card-body text-center">
        <h6 class="mb-3">Data pemeriksaan sudah lengkap!</h6>
        <a href="{{ route('report.surat-pemeriksaan', $report->id) }}" 
           class="btn btn-success" target="_blank">
            <i class="fas fa-file-pdf me-1"></i> Generate Surat Pemeriksaan PDF
        </a>
    </div>
</div>
@endif
@endsection