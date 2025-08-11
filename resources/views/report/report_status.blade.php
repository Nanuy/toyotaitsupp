@extends('master')

@section('title', 'Status Laporan')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4"><i class="fas fa-clipboard-check me-2"></i>Status Laporan</h3>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="mb-3 text-primary">
                <i class="fas fa-user me-2"></i> {{ $report->reporter_name }}
            </h5>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong><i class="fas fa-phone-alt me-2 text-success"></i>Kontak:</strong> +{{ $report->contact }}
                </li>
                <li class="list-group-item">
                    <strong><i class="fas fa-map-marker-alt me-2 text-danger"></i>Lokasi:</strong> {{ $report->location->name ?? '-' }}
                </li>
                <li class="list-group-item">
                    <strong><i class="fas fa-align-left me-2 text-info"></i>Deskripsi:</strong> {{ $report->description }}
                </li>
                <li class="list-group-item">
                    <strong><i class="fas fa-info-circle me-2 text-warning"></i>Status:</strong>

                    @if($report->status == 'done')
                        <span class="badge rounded-pill bg-primary px-3 py-2">
                            <i class="fas fa-check-circle me-1"></i> Selesai
                        </span>
                    @elseif($report->status == 'in_progress')
                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2">
                            <i class="fas fa-spinner fa-spin me-1"></i> Sedang Ditangani
                        </span>
                    @elseif($report->status == 'accepted')
                        <span class="badge rounded-pill bg-info text-dark px-3 py-2">
                            <i class="fas fa-user-check me-1"></i> Diterima IT Support
                        </span>
                    @else
                        <span class="badge rounded-pill bg-danger px-3 py-2">
                            <i class="fas fa-hourglass-start me-1"></i> Menunggu Diproses
                        </span>
                    @endif
                </li>
                <li class="list-group-item">
                    <strong><i class="fas fa-clock me-2 text-secondary"></i>Waktu Laporan:</strong> {{ $report->created_at->format('d M Y H:i') }}
                </li>
            </ul>
        </div>
    </div>

    <div class="card shadow-sm mt-4 border-0">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="fas fa-users-cog me-2 text-primary"></i>Tim IT Support yang Menangani:
            </h5>
            @if($report->itSupports && $report->itSupports->isNotEmpty())
                <ul class="list-group">
                    @foreach($report->itSupports as $user)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-user-check me-2 text-success"></i>{{ $user->name }}</span>
                            @if(isset($user->pivot->accepted_at))
                                <span class="badge bg-light text-dark">
                                    <i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($user->pivot->accepted_at)->format('d M Y H:i') }}
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted"><em>Belum ada yang menangani laporan ini.</em></p>
            @endif
        </div>
    </div>

    @if($report->status == 'accepted' || $report->status == 'in_progress')
    <div class="card mt-4 border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="fas fa-signature me-2 text-primary"></i> Tanda Tangan Surat Tugas</h5>

            @php
    $signature = $report->signatures()->where('role', 'reporter')->latest()->first();
@endphp

            @if($signature)
                <div class="alert alert-success mb-3">
                    <h6><i class="fas fa-check-circle me-2"></i>Tanda Tangan Sudah Tersimpan:</h6>
                    <img src="{{ asset('storage/' . $signature->signature_path) }}" 
                        alt="Tanda Tangan" 
                        style="max-width: 300px; border: 1px solid #ddd; padding: 10px; background: white; border-radius: 5px;">
                    <br><small class="text-muted mt-2 d-block">Upload ulang untuk mengganti tanda tangan.</small>
                </div>
            @endif
            <form method="POST" action="{{ route('report.uploadSignature', $report->id) }}" enctype="multipart/form-data" id="signatureForm">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Pilih Metode:</label>
                    <select class="form-select" id="signatureType" name="signature_type" onchange="toggleSignatureInput()">
                        <option value="digital">Gambar Tanda Tangan</option>
                        <option value="manual">Upload File Gambar</option>
                    </select>
                </div>

                <!-- Gambar Tanda Tangan -->
                <div id="digitalSignatureDiv" class="mb-3">
                    <label class="form-label">Gambar Tanda Tangan Anda:</label>
                    <div style="border: 2px dashed #ccc; border-radius: 8px; background: #fafafa; padding: 10px;">
                        <div id="canvasContainer" style="position: relative; width: 100%; max-width: 600px;">
                            <canvas id="signatureCanvas" 
                                    style="display: block; border: 1px solid #ddd; border-radius: 5px; cursor: crosshair; background: white; width: 100%; height: 200px;" 
                                    width="600" 
                                    height="200">
                                Browser Anda tidak mendukung canvas
                            </canvas>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="clearSignature()">
                                <i class="fas fa-eraser me-1"></i>Hapus
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="undoSignature()">
                                <i class="fas fa-undo me-1"></i>Undo
                            </button>
                            <span class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>Gunakan mouse atau jari untuk menggambar
                            </span>
                        </div>
                    </div>
                    <input type="hidden" name="digital_signature" id="digitalSignatureInput">
                </div>

                <!-- Upload File -->
                <div id="manualSignatureDiv" class="mb-3" style="display:none;">
                    <label class="form-label">Upload Tanda Tangan:</label>
                    <input type="file" 
                           name="manual_signature" 
                           accept="image/png,image/jpg,image/jpeg" 
                           class="form-control"
                           onchange="previewUploadedSignature(this)">
                    <small class="text-muted">Format: PNG, JPG, JPEG. Maksimal 2MB.</small>
                    
                    <div id="uploadPreview" class="mt-3" style="display:none;">
                        <img id="uploadPreviewImg" src="" alt="Preview" style="max-width: 300px; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    <i class="fas fa-save me-1"></i>Simpan Tanda Tangan
                </button>
            </form>
        </div>
    </div>
    @endif

    @if($report->status == 'done' && $report->pdf_path)
        <div class="text-center mt-4">
            <a href="{{ asset('storage/' . $report->pdf_path) }}" class="btn btn-outline-success" target="_blank">
                <i class="fas fa-file-pdf me-2"></i>Unduh Surat Tugas
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Variables untuk signature drawing
let canvas, ctx;
let isDrawing = false;
let strokes = []; // untuk undo functionality
let currentStroke = [];

document.addEventListener('DOMContentLoaded', function () {
    initializeSignatureCanvas();
    toggleSignatureInput(); // Set default method
});

function initializeSignatureCanvas() {
    canvas = document.getElementById('signatureCanvas');
    if (!canvas) return;
    
    // Set canvas actual size to match display size
    resizeCanvas();
    
    ctx = canvas.getContext('2d');
    
    // Set drawing style
    setupCanvasStyle();
    
    // Mouse events
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);
    
    // Touch events untuk mobile
    canvas.addEventListener('touchstart', handleTouch, { passive: false });
    canvas.addEventListener('touchmove', handleTouch, { passive: false });
    canvas.addEventListener('touchend', stopDrawing);
}

function resizeCanvas() {
    const container = document.getElementById('canvasContainer');
    const rect = container.getBoundingClientRect();
    
    // Set canvas actual dimensions to match display size
    canvas.width = rect.width;
    canvas.height = 200;
    
    // Reset style to prevent scaling
    canvas.style.width = rect.width + 'px';
    canvas.style.height = '200px';
}

function setupCanvasStyle() {
    ctx.strokeStyle = '#000000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';
    ctx.imageSmoothingEnabled = true;
}

function getCanvasCoordinates(e) {
    const rect = canvas.getBoundingClientRect();
    const scaleX = canvas.width / rect.width;
    const scaleY = canvas.height / rect.height;
    
    return {
        x: (e.clientX - rect.left) * scaleX,
        y: (e.clientY - rect.top) * scaleY
    };
}

function startDrawing(e) {
    e.preventDefault();
    isDrawing = true;
    currentStroke = [];
    
    const coords = getCanvasCoordinates(e);
    
    ctx.beginPath();
    ctx.moveTo(coords.x, coords.y);
    currentStroke.push(coords);
}

function draw(e) {
    if (!isDrawing) return;
    e.preventDefault();
    
    const coords = getCanvasCoordinates(e);
    
    ctx.lineTo(coords.x, coords.y);
    ctx.stroke();
    currentStroke.push(coords);
}

function stopDrawing() {
    if (isDrawing) {
        isDrawing = false;
        if (currentStroke.length > 0) {
            strokes.push([...currentStroke]);
        }
        currentStroke = [];
    }
}

function handleTouch(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 
                                    e.type === 'touchmove' ? 'mousemove' : 'mouseup', {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
}

function clearSignature() {
    if (ctx && canvas) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        strokes = [];
        setupCanvasStyle(); // Reset style after clearing
    }
}

function undoSignature() {
    if (strokes.length > 0) {
        strokes.pop();
        redrawCanvas();
    }
}

function redrawCanvas() {
    if (!ctx || !canvas) return;
    
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    setupCanvasStyle(); // Reset style
    
    strokes.forEach(stroke => {
        if (stroke.length > 0) {
            ctx.beginPath();
            ctx.moveTo(stroke[0].x, stroke[0].y);
            
            for (let i = 1; i < stroke.length; i++) {
                ctx.lineTo(stroke[i].x, stroke[i].y);
            }
            ctx.stroke();
        }
    });
}

function toggleSignatureInput() {
    const type = document.getElementById('signatureType').value;
    const digitalDiv = document.getElementById('digitalSignatureDiv');
    const manualDiv = document.getElementById('manualSignatureDiv');
    
    if (type === 'digital') {
        digitalDiv.style.display = 'block';
        manualDiv.style.display = 'none';
    } else {
        digitalDiv.style.display = 'none';
        manualDiv.style.display = 'block';
    }
}

function previewUploadedSignature(input) {
    const preview = document.getElementById('uploadPreview');
    const previewImg = document.getElementById('uploadPreviewImg');
    
    if (input.files && input.files[0]) {
        // Validasi ukuran file (2MB)
        if (input.files[0].size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 2MB.');
            input.value = '';
            preview.style.display = 'none';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}

// Form submission handling
document.getElementById('signatureForm').addEventListener('submit', function(e) {
    const type = document.getElementById('signatureType').value;
    
    if (type === 'digital') {
        // Cek apakah ada gambar tanda tangan
        if (strokes.length === 0) {
            e.preventDefault();
            alert('Silakan gambar tanda tangan terlebih dahulu!');
            return;
        }
        
        // Simpan canvas sebagai base64
        const signatureData = canvas.toDataURL('image/png');
        document.getElementById('digitalSignatureInput').value = signatureData;
        
    } else {
        // Validasi file upload
        const fileInput = document.querySelector('input[name="manual_signature"]');
        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            alert('Silakan pilih file tanda tangan terlebih dahulu!');
            return;
        }
    }
});

// Responsive canvas on window resize
window.addEventListener('resize', function() {
    if (canvas && ctx) {
        // Save current drawing as image data
        const imageData = canvas.toDataURL();
        
        // Resize canvas
        resizeCanvas();
        setupCanvasStyle();
        
        // Restore drawing if exists
        if (strokes.length > 0) {
            const img = new Image();
            img.onload = function() {
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            };
            img.src = imageData;
        }
    }
});
</script>
@endpush