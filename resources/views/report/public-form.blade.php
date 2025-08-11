@extends('master')

@section('title', 'Form Laporan')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Form Laporan Kerusakan</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Laporan berhasil dikirim!</strong><br>
                            <small class="text-muted">
                                Simpan kode dan password berikut untuk mengecek status laporan Anda:
                            </small>
                            <div class="mt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Kode Laporan:</strong> 
                                        <span class="badge bg-primary">{{ session('success') }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Password:</strong> 
                                        <span class="badge bg-secondary">{{ session('password') ?? 'Tidak tersedia' }}</span>
                                    </div>
                                </div>
                                @if(session('report_count'))
                                    <div class="mt-2">
                                        <small class="text-info">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Ini adalah laporan ke-{{ session('report_count') }} dari nomor telepon Anda.
                                        </small>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Tampilkan error validasi --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('lapor.store') }}" enctype="multipart/form-data" id="reportForm">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="location_id" class="form-label required">
                                    <i class="fas fa-map-marker-alt me-1"></i>Pilih Lokasi Cabang
                                </label>
                                <select id="location_id" name="location_id" class="form-select select2 @error('location_id') is-invalid @enderror" required>
                                    <option disabled selected>-- Pilih Lokasi --</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                            {{ $loc->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="item_id" class="form-label required">
                                    <i class="fas fa-tools me-1"></i>Kategori Perangkat
                                </label>
                                <select id="item_id" name="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                                    <option disabled selected>-- Pilih Kategori --</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('item_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reporter_name" class="form-label required">
                                    <i class="fas fa-user me-1"></i>Nama Pelapor
                                </label>
                                <input id="reporter_name" type="text" name="reporter_name" 
                                       class="form-control @error('reporter_name') is-invalid @enderror"
                                       placeholder="Contoh: Anthony" value="{{ old('reporter_name') }}" 
                                       required maxlength="100">
                                @error('reporter_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="division" class="form-label required">
                                    <i class="fas fa-building me-1"></i>Divisi
                                </label>
                                <select id="division" name="division" class="form-select @error('division') is-invalid @enderror" required>
                                    <option disabled selected>-- Pilih Divisi --</option>
                                    <option value="IT" {{ old('division') == 'IT' ? 'selected' : '' }}>IT</option>
                                    <option value="HRD" {{ old('division') == 'HRD' ? 'selected' : '' }}>HRD</option>
                                    <option value="Finance" {{ old('division') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="Marketing" {{ old('division') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="Operations" {{ old('division') == 'Operations' ? 'selected' : '' }}>Operations</option>
                                    <option value="Other" {{ old('division') == 'Other' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                                @error('division')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="contact" class="form-label required">
                                <i class="fab fa-whatsapp me-1"></i>Kontak WhatsApp
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">+62</span>
                                <input id="contact" type="tel" name="contact" 
                                       class="form-control @error('contact') is-invalid @enderror"
                                       placeholder="8xxxxxxxxx" value="{{ old('contact') }}" 
                                       required maxlength="13" pattern="[0-9]{9,13}">
                                @error('contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Format: 8xxxxxxxxx (tanpa +62 atau 0, minimal 9 digit)</div>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label required">
                                <i class="fas fa-file-alt me-1"></i>Deskripsi Masalah
                            </label>
                            <textarea id="description" name="description" rows="4" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      required maxlength="1000"
                                      placeholder="Jelaskan masalah secara detail. Contoh: Mouse tidak berfungsi, kursor tidak bergerak ketika mouse digerakkan. Sudah dicoba di komputer lain namun tetap tidak berfungsi.">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <span id="charCount">0</span>/1000 karakter
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">
                                <i class="fas fa-camera me-1"></i>Upload Gambar (Opsional)
                            </label>
                            <input id="image" type="file" name="image" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   accept="image/jpeg,image/png,image/jpg,image/gif">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Format yang didukung: JPG, JPEG, PNG, GIF. Maksimal 2MB.
                            </div>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="confirmation" required>
                            <label class="form-check-label" for="confirmation">
                                Saya menyatakan bahwa informasi yang diberikan adalah benar dan akurat.
                            </label>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-outline-secondary me-md-2">
                                <i class="fas fa-undo me-1"></i>Reset Form
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Kirim Laporan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center mt-4">
    <a href="{{ route('report.check') }}" class="btn btn-outline-primary">
        <i class="fas fa-search me-1"></i> Cek Status Laporan Saya
    </a>
</div>

</div>

@push('styles')
<style>
    .required::after {
        content: " *";
        color: red;
    }
    
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    #imagePreview img {
        max-width: 200px;
        max-height: 200px;
        border-radius: 5px;
        border: 2px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character counter for description
    const descriptionTextarea = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    
    descriptionTextarea.addEventListener('input', function() {
        const currentLength = this.value.length;
        charCount.textContent = currentLength;
        
        if (currentLength > 900) {
            charCount.style.color = 'red';
        } else if (currentLength > 700) {
            charCount.style.color = 'orange';
        } else {
            charCount.style.color = 'green';
        }
    });
    
    // Image preview
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        imagePreview.innerHTML = '';
        
        if (file) {
            // Check file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 2MB.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'd-block';
                imagePreview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // WhatsApp number formatting
    const contactInput = document.getElementById('contact');
    contactInput.addEventListener('input', function() {
        // Remove any non-digit characters
        let value = this.value.replace(/\D/g, '');
        
        // Ensure it starts with 8 (Indonesian mobile number)
        if (value.length > 0 && !value.startsWith('8')) {
            value = value.substring(1);
        }
        
        // Limit to 13 digits
        if (value.length > 13) {
            value = value.substring(0, 13);
        }
        
        this.value = value;
    });
    
    // Form validation before submit
    const reportForm = document.getElementById('reportForm');
    reportForm.addEventListener('submit', function(e) {
        const contact = document.getElementById('contact').value;
        const itemId = document.getElementById('item_id').value;
        
        // Validate WhatsApp number
        if (contact.length < 9 || contact.length > 13) {
            e.preventDefault();
            alert('Nomor WhatsApp harus antara 9-13 digit.');
            return false;
        }
        
        if (!contact.startsWith('8')) {
            e.preventDefault();
            alert('Nomor WhatsApp harus dimulai dengan 8.');
            return false;
        }
        
        // Validate item selection
        if (!itemId || itemId === '-- Pilih Kategori --') {
            e.preventDefault();
            alert('Silakan pilih kategori perangkat.');
            return false;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Mengirim...';
    });
    
    // Initialize Select2 if available
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: '-- Pilih Lokasi --'
        });
    }
});
</script>
@endpush
@endsection