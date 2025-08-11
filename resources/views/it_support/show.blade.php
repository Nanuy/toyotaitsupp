@extends('master')

@section('title', 'Detail Laporan')

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

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Laporan #{{ $report->id }}</h1>
    <a href="{{ route('it.reports') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Daftar
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

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Debug Info - Hapus setelah masalah teratasi --}}
    <!-- <div class="alert alert-info">
        <strong>Debug Info:</strong><br>
        User logged in: {{ auth()->check() ? 'YES' : 'NO' }}<br>
        @if(auth()->check())
            User ID: {{ auth()->user()->id ?? 'NULL' }}<br>
            User name: {{ auth()->user()->name ?? 'NULL' }}<br>
            User email: {{ auth()->user()->email ?? 'NULL' }}<br>
            User role: {{ auth()->user()->role ?? 'NULL' }}<br>
            Is IT Support (role): {{ auth()->user()->role === 'it_supp' ? 'YES' : 'NO' }}<br>
        @endif
        Report status: {{ $report->status }}<br>
        Is Accepted: {{ $report->status === 'accepted' ? 'YES' : 'NO' }}<br>
        Should show Next Day button: {{ (auth()->check() && auth()->user()->role === 'it_supp' && $report->status === 'accepted') ? 'YES' : 'NO' }}
    </div> -->

    {{-- Main Report Info --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Informasi Laporan</h5>
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
                                        <span class="badge bg-warning text-dark fs-6">
                                            <i class="fas fa-clock"></i> Menunggu
                                        </span>
                                        @break
                                    @case('accepted')
                                        <span class="badge bg-info text-white fs-6">
                                            <i class="fas fa-cog"></i> Dikerjakan
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success fs-6">
                                            <i class="fas fa-check"></i> Selesai
                                        </span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary fs-6">{{ $report->status }}</span>
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
                        <a href="{{ asset('storage/reports/' . $report->image) }}" target="_blank" data-bs-toggle="modal" data-bs-target="#imageModal">
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
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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

    {{-- Action Buttons Section --}}
    @if (auth()->check())
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Aksi</h5>
            </div>
            <div class="card-body">
                {{-- Accept Button --}}
                @if ($report->status === 'waiting')
                    <form action="{{ route('report.accept', $report->id) }}" method="POST" class="mb-4">
                        @csrf
                        @if(isset($allITSupports) && $allITSupports->count() > 1)
                            <div class="mb-3">
                                <label class="form-label">Tambahkan Tim (opsional):</label>
                                <div class="border rounded p-3 bg-light">
                                    <div class="row">
                                        @php $counter = 0; @endphp
                                        @foreach ($allITSupports as $it)
                                            @if ($it->id !== auth()->id())
                                                @php $counter++; @endphp
                                                <div class="col-md-6 col-lg-4 col-xl-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="team[]" 
                                                            value="{{ $it->id }}" id="team_{{ $it->id }}">
                                                        <label class="form-check-label" for="team_{{ $it->id }}">
                                                            {{ $it->name }}
                                                        </label>
                                                    </div>
                                                </div>
                                                {{-- Pindah ke baris baru setelah 4 item --}}
                                                @if ($counter % 4 == 0 && !$loop->last)
                                                    </div>
                                                    <div class="row">
                                                @endif
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <small class="text-muted">Pilih satu atau lebih anggota tim untuk berkolaborasi</small>
                            </div>
                        @endif
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-1"></i> Accept & Ambil Tugas
                        </button>
                    </form>
                @endif

                {{-- Add Detail Form --}}
                @if ($report->status === 'accepted')
                    <form action="{{ route('report.addDetail', $report->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="d-flex align-items-center mb-3">
                            <h6 class="text-primary mb-0 me-2">Tambah Detail Perbaikan</h6>
                            <i class="fas fa-tools text-primary"></i>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="item_id" class="form-label">Item Rusak <span class="text-danger">*</span></label>
                                    <select name="item_id" class="form-select" required>
                                        <option value="">-- Pilih Item --</option>
                                        @if(isset($items))
                                            @foreach ($items as $item)
                                                @php
                                                    $count = isset($itemCounts) ? ($itemCounts[$item->id] ?? 0) : 0;
                                                    $label = $item->name . ($count > 0 ? " ({$count}x laporan)" : '');
                                                @endphp
                                                <option value="{{ $item->id }}">{{ $label }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                {{-- Kolom kosong untuk keseimbangan layout atau tambahan field lain --}}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="uraian_masalah" class="form-label">Uraian Masalah <span class="text-danger">*</span></label>
                            <textarea name="uraian_masalah" class="form-control" rows="3" required 
                                      placeholder="Jelaskan masalah secara detail...">{{ old('uraian_masalah') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="tindakan" class="form-label">Tindakan Perbaikan <span class="text-danger">*</span></label>
                            <textarea name="tindakan" class="form-control" rows="3" required 
                                      placeholder="Jelaskan tindakan yang dilakukan...">{{ old('tindakan') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Simpan Detail
                        </button>
                    </form>
                @endif

                {{-- Item Management Section --}}
                @if ($report->status === 'accepted' || $report->status === 'in_progress')
                    <div class="card mt-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-list me-2"></i>Manajemen Item dalam Laporan
                            </h6>
                        </div>
                        <div class="card-body">
                            {{-- Current Items List --}}
                            <div class="mb-4">
                                <h6 class="text-primary">Item yang Dilaporkan:</h6>
                                @if($report->details && $report->details->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Uraian Masalah</th>
                                                    <th>Tindakan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($report->details as $detail)
                                                    <tr>
                                                        <td>{{ $detail->item->name ?? 'Tidak ada' }}</td>
                                                        <td>{{ $detail->uraian_masalah }}</td>
                                                        <td>{{ $detail->tindakan }}</td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary" 
                                                                    onclick="editItem({{ $detail->id }}, '{{ $detail->item->name ?? '' }}', '{{ $detail->uraian_masalah }}', '{{ $detail->tindakan }}')">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            @if($report->details->count() > 1)
                                                                <button class="btn btn-sm btn-outline-danger" 
                                                                        onclick="removeItem({{ $detail->id }})">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">Belum ada item yang dilaporkan.</p>
                                @endif
                            </div>

                            {{-- Add New Item Form --}}
                            <div class="border-top pt-3">
                                <h6 class="text-success">Tambah Item Baru:</h6>
                                <form id="addItemForm" class="mt-3">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="new_item_id" class="form-label">Item Rusak <span class="text-danger">*</span></label>
                                                <select id="new_item_id" name="item_id" class="form-select" required>
                                                    <option value="">-- Pilih Item --</option>
                                                    @if(isset($items))
                                                        @foreach ($items as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_uraian_masalah" class="form-label">Uraian Masalah <span class="text-danger">*</span></label>
                                        <textarea id="new_uraian_masalah" name="uraian_masalah" class="form-control" rows="2" required 
                                                  placeholder="Jelaskan masalah secara detail..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_tindakan" class="form-label">Tindakan Perbaikan <span class="text-danger">*</span></label>
                                        <textarea id="new_tindakan" name="tindakan" class="form-control" rows="2" required 
                                                  placeholder="Jelaskan tindakan yang dilakukan..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus me-1"></i> Tambah Item
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Date Input Form --}}
                @if ($report->status === 'accepted')
                    <form action="{{ route('report.simpanTanggalSurat', $report->id) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-md-8">
                                <label for="surat_jalan_date" class="form-label">Tanggal Surat Jalan <span class="text-danger">*</span></label>
                                <input type="date" name="surat_jalan_date" class="form-control"
                                    value="{{ old('surat_jalan_date', $report->surat_jalan_date) }}" required>
                                <small class="text-muted">Wajib diisi sebelum mencetak surat tugas</small>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-save me-1"></i> Simpan Tanggal
                                </button>
                            </div>
                        </div>
                    </form>
                @endif

                {{-- Next Day Button - Fixed to use 'role' instead of 'position' --}}
                @if (auth()->check() && auth()->user()->role === 'it_supp' && $report->status === 'accepted')
                    <form action="{{ route('report.nextDay', $report->id) }}" method="POST" class="d-inline mb-3">
                        @csrf
                        <button 
                            type="submit" 
                            class="btn btn-outline-warning"
                            onclick="return confirm('Next Day: kode/password akan di-reset, tanda tangan dihapus. Lanjutkan?')">
                            <i class="fas fa-sync-alt me-1"></i> Next Day
                        </button>
                    </form>
                @endif

                {{-- Transfer to Other Division --}}
                @if ($report->status !== 'completed')
                    <details class="mb-3">
                        <summary class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-exchange-alt me-1"></i> Pindahkan ke Divisi Lain
                        </summary>
                        <div class="mt-3 p-3 border rounded bg-light">
                            <form action="{{ route('report.pindahDivisi', $report->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="catatan" class="form-label">Catatan Pemindahan <span class="text-danger">*</span></label>
                                    <textarea name="catatan" class="form-control" rows="2" required 
                                              placeholder="Alasan pemindahan ke divisi lain..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-paper-plane me-1"></i> Pindahkan
                                </button>
                            </form>
                        </div>
                    </details>
                @endif

                {{-- Print PDF Button --}}
                <div class="d-flex gap-2 flex-wrap">
                    @if ($report->status === 'accepted')
                        <a href="{{ route('report.surat', $report->id) }}" class="btn btn-outline-primary" target="_blank">
                            <i class="fas fa-file-pdf me-1"></i> Cetak Surat Tugas
                        </a>
                    @else
                        <button class="btn btn-outline-secondary" disabled title="Laporan harus di-accept terlebih dahulu">
                            <i class="fas fa-lock me-1"></i> Cetak Surat
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- IT Support Team & Pelapor Section --}}
@if ($report->users && $report->users->count())
<div class="card mt-4">
    <div class="card-header bg-light text-center">
        <h5 class="mb-0">Tim IT Support & Pelapor</h5>
    </div>
    <div class="card-body">
        <div class="row">
            {{-- IT Support Signatures --}}
            @foreach ($report->users as $user)
            <div class="col-md-4 mb-3 text-center">
                <p class="fw-bold mb-1">{{ $user->name }}</p>
                @php
                $signature = $report->getSignatureForUser($user->id);
                @endphp

                @if ($signature && $signature->signature_path)
                <div class="signature-container my-2">
                    <img src="{{ asset('storage/' . $signature->signature_path) }}" alt="Tanda Tangan {{ $user->name }}" class="img-fluid border rounded" style="max-height: 80px; max-width: 150px;">
                    <p class="text-success small mt-1 mb-0"><i class="fas fa-check-circle"></i> Sudah Ditandatangani</p>
                    <p class="text-muted small">{{ $signature->signed_at ? $signature->signed_at->format('d/m/Y H:i') : $signature->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @else
                <div class="signature-placeholder my-2 d-flex align-items-center justify-content-center" style="min-height: 80px;">
                    <div class="border border-dashed rounded p-3 text-muted" style="width: 150px;">
                        <i class="fas fa-signature fa-2x mb-2"></i>
                        <p class="small mb-0">Tanda tangan belum tersedia</p>
                    </div>
                </div>
                @endif
            </div>
            @endforeach

            {{-- Separator --}}
            <div class="col-12"><hr class="my-3"></div>

            {{-- Pelapor Signature --}}
            @php
            $signaturePelapor = $report->signatureByRole('user');
            @endphp
            <div class="col-12 text-center">
                <p class="fw-bold mb-1">Tanda Tangan Pelapor</p>
                @if ($signaturePelapor && $signaturePelapor->signature_path)
                <div class="signature-container my-2">
                    <img src="{{ asset('storage/' . $signaturePelapor->signature_path) }}" alt="TTD Pelapor" class="img-fluid border rounded" style="max-height: 80px; max-width: 150px;">
                    <p class="text-success small mt-1 mb-0"><i class="fas fa-check-circle"></i> Sudah Ditandatangani</p>
                    <p class="text-muted small">Ditandatangani pada: {{ \Carbon\Carbon::parse($signaturePelapor->signed_at)->format('d M Y H:i') }}</p>
                </div>
                @else
                <div class="signature-placeholder my-2 d-flex align-items-center justify-content-center" style="min-height: 80px;">
                    <div class="border border-dashed rounded p-3 text-muted" style="width: 150px;">
                        <i class="fas fa-signature fa-2x mb-2"></i>
                        <p class="small mb-0">Tanda tangan belum tersedia</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Status tanda tangan --}}
    <div class="card-footer bg-light">
        @if($report->allITSigned())
        <div class="alert alert-success mb-0 text-center py-2">
            <i class="fas fa-check-circle me-2"></i> Semua IT Support telah menandatangani laporan ini
        </div>
        @else
        <div class="alert alert-info mb-0 text-center py-2">
            <i class="fas fa-info-circle me-2"></i> {{ $report->signatures()->where('role', 'it_supp')->count() }} dari {{ $report->users->count() }} IT Support telah menandatangani
        </div>
        @endif
    </div>
</div>
@endif

    <!-- {{-- Debug Info untuk Tombol Tanda Tangan --}}
    @php
        $isAssigned = $report->itSupports->contains(function ($it) {
            return $it->id === auth()->id();
        });
    @endphp

    @if(auth()->check())
        <div class="alert alert-warning">
            <strong>Debug Tanda Tangan:</strong><br>
            User role: {{ auth()->user()->role }}<br>
            Is IT Support: {{ auth()->user()->role == 'it_supp' ? 'YES' : 'NO' }}<br>
            Is Assigned: {{ $isAssigned ? 'YES' : 'NO' }}<br>
            IT Supports count: {{ $report->itSupports ? $report->itSupports->count() : 'NULL' }}<br>
            Current user ID: {{ auth()->id() }}<br>
            @if($report->itSupports && $report->itSupports->count() > 0)
                IT Support IDs: 
                @foreach($report->itSupports as $it)
                    {{ $it->id }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
                <br>
            @endif
            Has User Signed: {{ $report->hasUserSigned(auth()->id()) ? 'YES' : 'NO' }}<br>
            Should show button: {{ (auth()->user()->role == 'it_supp' && $isAssigned && !$report->hasUserSigned(auth()->id())) ? 'YES' : 'NO' }}
        </div>
    @endif -->

    @if (auth()->check() && auth()->user()->role == 'it_supp' && $isAssigned && !$report->hasUserSigned(auth()->id()))
        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-signature me-2"></i>
                    Tanda Tangan Digital
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Silakan lakukan tanda tangan digital untuk menandai bahwa Anda telah menyelesaikan tugas ini.
                </div>
                
                <form action="{{ route('signature.store') }}" method="POST" id="signatureForm">
                    @csrf
                    <input type="hidden" name="report_id" value="{{ $report->id }}">
                    
                    {{-- Opsi untuk menggunakan tanda tangan yang tersimpan --}}
                    @if(auth()->user()->signature_path)
                    <div class="mb-3">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="useSavedSignature" name="use_saved_signature" value="1">
                            <label class="form-check-label" for="useSavedSignature">
                                Gunakan tanda tangan yang tersimpan
                            </label>
                        </div>
                        <div id="savedSignaturePreview" class="border rounded p-3 bg-light text-center d-none">
                            <img src="{{ asset('storage/' . auth()->user()->signature_path) }}" 
                                 alt="Tanda Tangan Tersimpan" 
                                 class="img-fluid" 
                                 style="max-height: 100px;">
                            <p class="text-muted small mt-2">Tanda tangan yang tersimpan di profil Anda</p>
                        </div>
                    </div>
                    @endif
                    
                    {{-- Canvas untuk tanda tangan --}}
                    <div class="mb-3" id="signatureCanvasContainer">
                        <label class="form-label">Area Tanda Tangan:</label>
                        <div class="border rounded p-3 bg-light text-center">
                            <canvas id="signatureCanvas" width="400" height="200" style="border: 2px dashed #ccc; cursor: crosshair;"></canvas>
                            <div class="mt-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSignature()">
                                    <i class="fas fa-eraser me-1"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="signature" id="signatureData">
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="saveSignature">
                            <i class="fas fa-save me-1"></i> Simpan Tanda Tangan
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearSignature()">
                            <i class="fas fa-undo me-1"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Jika sudah tanda tangan --}}
    @if (auth()->check() && auth()->user()->role == 'it_supp' && $isAssigned && $report->hasUserSigned(auth()->id()))
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    Tanda Tangan Tersimpan
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Anda telah menandatangani laporan ini.
                </div>
                @php
                    $userSignature = $report->getSignatureForUser(auth()->id());
                @endphp
                @if($userSignature)
                    <div class="text-center">
                        <p><strong>Tanda Tangan Anda:</strong></p>
                        <img src="{{ asset('storage/' . $userSignature->signature_path) }}" 
                             alt="Tanda Tangan Anda"
                             class="img-fluid border rounded"
                             style="max-height: 100px;">
                        <p class="text-muted small mt-2">
                            Ditandatangani pada: {{ $userSignature->signed_at ? $userSignature->signed_at->format('d/m/Y H:i') : $userSignature->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif
    

    {{-- Detail Actions History --}}
    @if ($report->details && $report->details->count())
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Riwayat Tindakan Perbaikan</h5>
            </div>
            <div class="card-body">
                @foreach ($report->details as $index => $detail)
                    <div class="border rounded p-3 mb-3 {{ $index % 2 == 0 ? 'bg-light' : '' }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="text-primary mb-0">
                                <i class="fas fa-wrench"></i> 
                                Tindakan #{{ $index + 1 }}
                            </h6>
                            @if(auth()->check())
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('report_detail.edit', $detail->id) }}" 
                                       class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('report_detail.destroy', $detail->id) }}" 
                                          method="POST" 
                                          style="display:inline-block;" 
                                          onsubmit="return confirm('Yakin ingin menghapus detail ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Item:</strong> {{ $detail->item->name ?? '-' }}</p>
                                <p><strong>Masalah:</strong></p>
                                <div class="border-start border-warning ps-3 mb-2">
                                    {{ $detail->uraian_masalah }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Tindakan:</strong></p>
                                <div class="border-start border-success ps-3">
                                    {{ $detail->tindakan }}
                                </div>
                            </div>
                        </div>
                        
                        @if($detail->created_at)
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> 
                                {{ $detail->created_at->format('d/m/Y H:i') }}
                            </small>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
// Auto dismiss alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// Signature Canvas
let canvas = document.getElementById('signatureCanvas');
let ctx = canvas ? canvas.getContext('2d') : null;
let isDrawing = false;

// Toggle saved signature checkbox
const useSavedSignatureCheckbox = document.getElementById('useSavedSignature');
const savedSignaturePreview = document.getElementById('savedSignaturePreview');
const signatureCanvasContainer = document.getElementById('signatureCanvasContainer');

if (useSavedSignatureCheckbox) {
    useSavedSignatureCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // Show saved signature preview, hide canvas
            if (savedSignaturePreview) savedSignaturePreview.classList.remove('d-none');
            if (signatureCanvasContainer) signatureCanvasContainer.classList.add('d-none');
        } else {
            // Hide saved signature preview, show canvas
            if (savedSignaturePreview) savedSignaturePreview.classList.add('d-none');
            if (signatureCanvasContainer) signatureCanvasContainer.classList.remove('d-none');
        }
    });
}

if (canvas && ctx) {
    // Mouse events
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);

    // Touch events for mobile
    canvas.addEventListener('touchstart', handleTouchStart);
    canvas.addEventListener('touchmove', handleTouchMove);
    canvas.addEventListener('touchend', stopDrawing);

    function startDrawing(e) {
        isDrawing = true;
        draw(e);
    }

    function draw(e) {
        if (!isDrawing) return;

        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';

        ctx.lineTo(x, y);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(x, y);
    }

    function stopDrawing() {
        if (isDrawing) {
            isDrawing = false;
            ctx.beginPath();
        }
    }

    function handleTouchStart(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousedown', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    }

    function handleTouchMove(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const mouseEvent = new MouseEvent('mousemove', {
            clientX: touch.clientX,
            clientY: touch.clientY
        });
        canvas.dispatchEvent(mouseEvent);
    }
}

function clearSignature() {
    if (ctx && canvas) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById('signatureData').value = '';
    }
}

// Handle form submission
document.getElementById('signatureForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Check if using saved signature
    const useSavedSignature = useSavedSignatureCheckbox && useSavedSignatureCheckbox.checked;
    
    if (!useSavedSignature) {
        if (!canvas) return;
        
        // Convert canvas to base64
        const signatureData = canvas.toDataURL('image/png');
        
        // Check if canvas is empty
        const isCanvasEmpty = !ctx.getImageData(0, 0, canvas.width, canvas.height).data.some(channel => channel !== 0);
        
        if (isCanvasEmpty) {
            alert('Silakan buat tanda tangan terlebih dahulu atau gunakan tanda tangan yang tersimpan!');
            return;
        }
        
        // Set signature data to hidden input
        document.getElementById('signatureData').value = signatureData;
    }
    
    // Submit the form
    this.submit();
});

// Item Management Functions
document.addEventListener('DOMContentLoaded', function() {
    // Add Item Form
    const addItemForm = document.getElementById('addItemForm');
    if (addItemForm) {
        addItemForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const reportId = {{ $report->id }};
            
            fetch(`/report/${reportId}/items`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    item_id: formData.get('item_id'),
                    uraian_masalah: formData.get('uraian_masalah'),
                    tindakan: formData.get('tindakan')
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Item berhasil ditambahkan!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menambahkan item.');
            });
        });
    }
});

function editItem(detailId, itemName, uraian, tindakan) {
    // Create modal for editing
    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'editItemModal';
    modal.innerHTML = `
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editItemForm">
                        <div class="mb-3">
                            <label class="form-label">Item Rusak</label>
                            <select id="edit_item_id" class="form-select" required>
                                <option value="">-- Pilih Item --</option>
                                @if(isset($items))
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Uraian Masalah</label>
                            <textarea id="edit_uraian_masalah" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tindakan Perbaikan</label>
                            <textarea id="edit_tindakan" class="form-control" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveEditItem(${detailId})">Simpan</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Set current values
    document.getElementById('edit_uraian_masalah').value = uraian;
    document.getElementById('edit_tindakan').value = tindakan;
    
    // Show modal
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Remove modal after hidden
    modal.addEventListener('hidden.bs.modal', function() {
        document.body.removeChild(modal);
    });
}

function saveEditItem(detailId) {
    const itemId = document.getElementById('edit_item_id').value;
    const uraian = document.getElementById('edit_uraian_masalah').value;
    const tindakan = document.getElementById('edit_tindakan').value;
    const reportId = {{ $report->id }};
    
    if (!itemId || !uraian || !tindakan) {
        alert('Semua field harus diisi!');
        return;
    }
    
    fetch(`/report/${reportId}/items/${detailId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            item_id: itemId,
            uraian_masalah: uraian,
            tindakan: tindakan
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Item berhasil diupdate!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengupdate item.');
    });
}

function removeItem(detailId) {
    if (!confirm('Yakin ingin menghapus item ini?')) {
        return;
    }
    
    const reportId = {{ $report->id }};
    
    fetch(`/report/${reportId}/items/${detailId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Item berhasil dihapus!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus item.');
    });

</script>

@endsection