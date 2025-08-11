@extends('superadmin')

@section('title', 'Detail Laporan - Superadmin')

@section('content')
<style>
.table-borderless td {
    border: none !important;
    padding: 0.25rem 0.5rem;
}
details summary {
    cursor: pointer;
}
details[open] summary {
    margin-bottom: 10px;
}
.btn-group-sm .btn {
    font-size: 0.75rem;
}
#signatureCanvas {
    border: 2px dashed #ccc;
    cursor: crosshair;
    background: white;
}
.signature-container img {
    max-height: 80px;
    max-width: 150px;
    border: 1px solid #ddd;
    border-radius: 4px;
}
.signature-placeholder {
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<div class="container-fluid">
    <!-- Header Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clipboard-check mr-2"></i>Detail Laporan #{{ $report->id }}
        </h1>
        <a href="{{ route('superadmin.reports') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i>Kembali
        </a>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Main Report Info Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-info-circle mr-2"></i>Informasi Laporan
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="35%"><strong>Nama Pelapor:</strong></td>
                            <td>{{ $report->reporter_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Kontak:</strong></td>
                            <td>{{ $report->contact }}</td>
                        </tr>
                        <tr>
                            <td><strong>Divisi:</strong></td>
                            <td>{{ $report->division ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Lokasi:</strong></td>
                            <td>{{ $report->location->name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="35%"><strong>Status:</strong></td>
                            <td>
                                @switch($report->status)
                                    @case('waiting')
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i> Menunggu
                                        </span>
                                        @break
                                    @case('accepted')
                                        <span class="badge badge-info">
                                            <i class="fas fa-cog"></i> Dikerjakan
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Selesai
                                        </span>
                                        @break
                                    @default
                                        <span class="badge badge-secondary">{{ $report->status }}</span>
                                @endswitch
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Dilaporkan:</strong></td>
                            <td>{{ $report->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($report->surat_jalan_date)
                        <tr>
                            <td><strong>Tanggal Surat Jalan:</strong></td>
                            <td>{{ \Carbon\Carbon::parse($report->surat_jalan_date)->format('d/m/Y') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <div class="mt-3">
                <strong>Deskripsi Masalah:</strong>
                <div class="border rounded p-3 mt-2 bg-light">
                    {{ $report->description }}
                </div>
            </div>

            {{-- Image Section --}}
            @if ($report->image)
                <div class="mt-4">
                    <strong>Gambar Laporan:</strong>
                    <div class="mt-2">
                        <a href="{{ asset('storage/reports/' . $report->image) }}" target="_blank" data-toggle="modal" data-target="#imageModal">
                            <img src="{{ asset('storage/reports/' . $report->image) }}"
                                 alt="Gambar Laporan"
                                 class="img-thumbnail"
                                 style="max-width: 300px; max-height: 200px; cursor: pointer;">
                        </a>
                        <small class="text-muted d-block mt-1">Klik untuk memperbesar</small>
                    </div>
                </div>

                {{-- Image Modal --}}
                <div class="modal fade" id="imageModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Gambar Laporan</h5>
                                <button type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <img src="{{ asset('storage/reports/' . $report->image) }}" 
                                     alt="Gambar Laporan" 
                                     class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- IT Support Team Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-info">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-users-cog mr-2"></i>Tim IT Support
            </h6>
        </div>
        <div class="card-body">
            @if($report->itSupports && $report->itSupports->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>Nama</th>
                                <th>Tanggal Menerima</th>
                                <th>Status Tanda Tangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report->itSupports as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ isset($user->pivot->accepted_at) ? \Carbon\Carbon::parse($user->pivot->accepted_at)->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        @php
                                            $signature = $report->signatures()->where('user_id', $user->id)->first();
                                        @endphp
                                        @if($signature)
                                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Sudah Ditandatangani</span>
                                        @else
                                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Belum Ditandatangani</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle mr-2"></i>Belum ada IT Support yang menangani laporan ini.
                </div>
            @endif
        </div>
    </div>

    <!-- Report Details Card -->
    @if($report->details && $report->details->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-success">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-clipboard-list mr-2"></i>Detail Tindakan
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Item</th>
                            <th>Uraian Masalah</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report->details as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->item->name ?? 'Tidak ada' }}</td>
                                <td>{{ $detail->uraian_masalah }}</td>
                                <td>{{ $detail->tindakan }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Signatures Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-signature mr-2"></i>Tanda Tangan
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- IT Support Signatures -->
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2">Tanda Tangan IT Support</h5>
                    @if($report->signatures()->where('role', 'it_supp')->count() > 0)
                        <div class="row">
                            @foreach($report->signatures()->where('role', 'it_supp')->get() as $signature)
                                @php
                                    $user = \App\Models\User::find($signature->user_id);
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <p class="mb-1"><strong>{{ $user->name ?? 'IT Support' }}</strong></p>
                                            <img src="{{ asset('storage/' . $signature->signature_path) }}" 
                                                 alt="Tanda Tangan IT Support" 
                                                 class="img-fluid border rounded" 
                                                 style="max-height: 80px;">
                                            <p class="text-muted small mt-1">{{ \Carbon\Carbon::parse($signature->signed_at)->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Belum ada tanda tangan dari IT Support.
                        </div>
                    @endif
                </div>

                <!-- Reporter Signature -->
                <div class="col-md-6">
                    <h5 class="border-bottom pb-2">Tanda Tangan Pelapor</h5>
                    @php
                        $signaturePelapor = $report->signatureByRole('user');
                    @endphp
                    @if($signaturePelapor)
                        <div class="card">
                            <div class="card-body text-center">
                                <p class="mb-1"><strong>{{ $report->reporter_name }}</strong></p>
                                <img src="{{ asset('storage/' . $signaturePelapor->signature_path) }}" 
                                     alt="Tanda Tangan Pelapor" 
                                     class="img-fluid border rounded" 
                                     style="max-height: 80px;">
                                <p class="text-muted small mt-1">{{ \Carbon\Carbon::parse($signaturePelapor->signed_at)->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Belum ada tanda tangan dari pelapor.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Superadmin Signature Section -->
            @php
                $superadminSignature = $report->signatureByRole('superadmin');
                $hasSignedAsSuperadmin = $superadminSignature && $superadminSignature->user_id == auth()->id();
                $userHasSavedSignature = auth()->user()->signature_path !== null;
            @endphp

            <div class="mt-4 pt-3 border-top">
                <h5>Tanda Tangan Superadmin</h5>
                
                @if($hasSignedAsSuperadmin)
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i>Anda telah menandatangani laporan ini.
                    </div>
                    <div class="text-center mb-3">
                        <p><strong>Tanda Tangan Anda:</strong></p>
                        <img src="{{ asset('storage/' . $superadminSignature->signature_path) }}" 
                             alt="Tanda Tangan Superadmin"
                             class="img-fluid border rounded"
                             style="max-height: 100px;">
                        <p class="text-muted small mt-2">
                            Ditandatangani pada: {{ $superadminSignature->signed_at ? $superadminSignature->signed_at->format('d/m/Y H:i') : $superadminSignature->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                @else
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle mr-2"></i>Silakan lakukan tanda tangan digital untuk menandai bahwa Anda telah menyetujui laporan ini.
                    </div>
                    
                    <form action="{{ route('superadmin.signature.store', $report->id) }}" method="POST" id="signatureForm">
                        @csrf
                        <input type="hidden" name="signature_type" value="superadmin">
                        
                        @if($userHasSavedSignature)
                        <div class="mb-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="signature_method" id="signatureMethodDraw" value="draw" checked>
                                <label class="form-check-label" for="signatureMethodDraw">
                                    Gambar tanda tangan baru
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="signature_method" id="signatureMethodSaved" value="saved">
                                <label class="form-check-label" for="signatureMethodSaved">
                                    Gunakan tanda tangan tersimpan
                                </label>
                            </div>
                            
                            <div id="savedSignaturePreview" class="text-center mb-3 d-none">
                                <p><strong>Tanda Tangan Tersimpan:</strong></p>
                                <img src="{{ auth()->user()->signature_url }}" 
                                     alt="Tanda Tangan Tersimpan"
                                     class="img-fluid border rounded"
                                     style="max-height: 100px;">
                            </div>
                        </div>
                        @endif
                        
                        {{-- Canvas untuk tanda tangan --}}
                        <div class="mb-3" id="signatureCanvasContainer">
                            <label class="form-label">Area Tanda Tangan:</label>
                            <div class="border rounded p-3 bg-light text-center">
                                <canvas id="signatureCanvas" width="400" height="200"></canvas>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSignature()">
                                        <i class="fas fa-eraser mr-1"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="signature" id="signatureData">
                        <input type="hidden" name="use_saved_signature" id="useSavedSignature" value="0">
                        
                        <div id="saveForLaterOption" class="mb-3 {{ auth()->user()->signature_path ? 'd-none' : '' }}">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="save_for_later" id="saveForLater" value="1">
                                <label class="form-check-label" for="saveForLater">
                                    Simpan tanda tangan ini untuk digunakan nanti
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary mr-2" id="saveSignature">
                                <i class="fas fa-save mr-1"></i> Simpan Tanda Tangan
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="clearSignature()">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mb-4">
        <a href="{{ route('superadmin.report.surat', $report->id) }}" class="btn btn-success" target="_blank">
            <i class="fas fa-file-pdf mr-1"></i> Lihat Surat Tugas
        </a>
        <a href="{{ route('superadmin.reports') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
        
        @if(!$report->surat_jalan_date)
        <div class="alert alert-info mt-2" role="alert">
            <i class="fas fa-info-circle mr-1"></i> Tanggal surat jalan belum diisi oleh IT Support. Jika Anda melihat surat tugas sekarang, tanggal hari ini akan digunakan sebagai tanggal surat.
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let canvas = document.getElementById('signatureCanvas');
        if (!canvas) return; // Pastikan canvas ada
        
        // Atur ukuran canvas sesuai dengan container
        let container = canvas.parentElement;
        let containerWidth = container.clientWidth - 20; // Kurangi padding
        let canvasWidth = Math.min(containerWidth, 400); // Batasi lebar maksimum
        let canvasHeight = 200;
        
        // Atur ukuran tampilan canvas (CSS)
        canvas.style.width = canvasWidth + 'px';
        canvas.style.height = canvasHeight + 'px';
        
        // Atur ukuran canvas sebenarnya dengan mempertimbangkan pixel ratio
        let dpr = window.devicePixelRatio || 1;
        canvas.width = canvasWidth * dpr;
        canvas.height = canvasHeight * dpr;
        
        // Skala konteks sesuai dengan pixel ratio
        let ctx = canvas.getContext('2d');
        ctx.scale(dpr, dpr);
        
        // Inisialisasi SignaturePad dengan opsi yang memperbaiki sinkronisasi kursor
        let signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255, 255, 255)',
            penColor: 'rgb(0, 0, 0)',
            velocityFilterWeight: 0.7,
            minWidth: 0.5,
            maxWidth: 2.5,
            throttle: 16, // Sinkronisasi dengan refresh rate layar
            minDistance: 5 // Jarak minimum antara titik-titik
        });
        
        // Fungsi untuk membersihkan tanda tangan
        window.clearSignature = function() {
            signaturePad.clear();
        };
        
        // Fungsi untuk menangani perubahan ukuran layar
        function resizeCanvas() {
            // Simpan tanda tangan saat ini
            let data = signaturePad.toData();
            
            // Perbarui ukuran canvas
            let containerWidth = container.clientWidth - 20;
            let canvasWidth = Math.min(containerWidth, 400);
            
            // Atur ukuran tampilan canvas (CSS)
            canvas.style.width = canvasWidth + 'px';
            canvas.style.height = canvasHeight + 'px';
            
            // Atur ukuran canvas sebenarnya
            canvas.width = canvasWidth * dpr;
            canvas.height = canvasHeight * dpr;
            
            // Skala konteks sesuai dengan pixel ratio
            ctx.scale(dpr, dpr);
            
            // Pulihkan tanda tangan jika ada
            signaturePad.clear();
            if (data && data.length > 0) {
                signaturePad.fromData(data);
            }
        }
        
        // Panggil resizeCanvas saat ukuran jendela berubah
        window.addEventListener('resize', resizeCanvas);
        
        // Menangani opsi tanda tangan tersimpan
         const signatureMethodDraw = document.getElementById('signatureMethodDraw');
         const signatureMethodSaved = document.getElementById('signatureMethodSaved');
         const savedSignaturePreview = document.getElementById('savedSignaturePreview');
         const signatureCanvasContainer = document.getElementById('signatureCanvasContainer');
         const useSavedSignature = document.getElementById('useSavedSignature');
         const saveForLaterOption = document.getElementById('saveForLaterOption');
         
         if (signatureMethodDraw && signatureMethodSaved) {
             // Fungsi untuk mengganti mode tanda tangan
             function toggleSignatureMethod() {
                 if (signatureMethodDraw.checked) {
                     // Mode gambar tanda tangan
                     if (savedSignaturePreview) savedSignaturePreview.classList.add('d-none');
                     if (signatureCanvasContainer) signatureCanvasContainer.classList.remove('d-none');
                     if (useSavedSignature) useSavedSignature.value = '0';
                     if (saveForLaterOption) saveForLaterOption.classList.remove('d-none');
                 } else if (signatureMethodSaved.checked) {
                     // Mode tanda tangan tersimpan
                     if (savedSignaturePreview) savedSignaturePreview.classList.remove('d-none');
                     if (signatureCanvasContainer) signatureCanvasContainer.classList.add('d-none');
                     if (useSavedSignature) useSavedSignature.value = '1';
                     if (saveForLaterOption) saveForLaterOption.classList.add('d-none');
                 }
             }
             
             // Tambahkan event listener untuk radio buttons
             signatureMethodDraw.addEventListener('change', toggleSignatureMethod);
             signatureMethodSaved.addEventListener('change', toggleSignatureMethod);
             
             // Inisialisasi tampilan berdasarkan pilihan awal
             toggleSignatureMethod();
         }
        
        // Fungsi untuk menyimpan tanda tangan
        let signatureForm = document.getElementById('signatureForm');
        if (signatureForm) {
            signatureForm.addEventListener('submit', function(e) {
                // Jika menggunakan tanda tangan tersimpan, tidak perlu validasi canvas
                if (useSavedSignature && useSavedSignature.value === '1') {
                    return true;
                }
                
                // Validasi jika menggunakan tanda tangan yang digambar
                if (signaturePad.isEmpty()) {
                    e.preventDefault();
                    alert('Silakan buat tanda tangan terlebih dahulu!');
                    return false;
                }
                
                // Simpan data tanda tangan ke input hidden
                let signatureData = document.getElementById('signatureData');
                if (signatureData) {
                    signatureData.value = signaturePad.toDataURL();
                }
                return true;
            });
        }
    });
</script>
@endpush