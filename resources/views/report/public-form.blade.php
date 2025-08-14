<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Laporan IT Support</title>
    
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <style>
        body {
            background-color: #e9ecef;
            padding: 30px 0;
            font-family: 'Nunito', sans-serif;
        }
        .card {
            border: none;
            border-radius: 1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background: linear-gradient(45deg, #4e73df, #224abe);
            color: white;
            border-bottom: none;
            padding: 2rem;
            border-radius: 1rem 1rem 0 0;
            text-align: center;
        }
        .card-header h4 {
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }
        .form-label {
            font-weight: 600;
            color: #5a5c69;
        }
        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid #d1d3e2;
            padding: 0.75rem 1rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-control:focus, .form-select:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        .required::after {
            content: "*";
            color: #e74a3b;
            margin-left: 4px;
        }
        .btn-icon-split .icon {
            padding: 0.5rem 0.75rem;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem 0 0 0.5rem;
        }
        .btn-icon-split .text {
            padding: 0.5rem 0.75rem;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
        }
        #imagePreview img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            border: 2px dashed #d1d3e2;
            padding: 5px;
        }
        .select2-container .select2-selection--single {
            height: calc(2.25rem + 14px) !important;
            padding: 0.75rem 1rem;
        }
        .select2-container .select2-selection--single .select2-selection__arrow {
            height: calc(2.25rem + 14px);
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-9">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary py-4">
                        <h4 class="m-0 font-weight-bold text-white">
                            <i class="fas fa-headset me-2"></i>Formulir Pelaporan IT Support
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Laporan berhasil dikirim!</strong><br>
                                <small class="text-muted">
                                    Simpan kode dan password berikut untuk mengecek status laporan Anda:
                                </small>
                                <div class="mt-2">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <strong>Kode Laporan:</strong> 
                                            <span class="badge bg-primary">{{ session('success') }}</span>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <strong>Password:</strong> 
                                            <span class="badge bg-secondary">{{ session('password') ?? 'Tidak tersedia' }}</span>
                                        </div>
                                    </div>
                                    @if(session('report_count'))
                                        <small class="text-info d-block mt-2">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Ini adalah laporan ke-{{ session('report_count') }} dari nomor telepon Anda.
                                        </small>
                                    @endif
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Terjadi kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('lapor.store') }}" enctype="multipart/form-data" id="reportForm">
                            @csrf

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label for="location_id" class="form-label required">
                                        <i class="fas fa-map-marker-alt me-2"></i>Pilih Lokasi Cabang
                                    </label>
                                    <select id="location_id" name="location_id" class="form-select select2" required>
                                        <option value="" disabled selected>-- Pilih Lokasi --</option>
                                        @foreach($locations as $loc)
                                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                                {{ $loc->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="item_id" class="form-label required">
                                        <i class="fas fa-tools me-2"></i>Kategori Perangkat
                                    </label>
                                    <select id="item_id" name="item_id" class="form-select select2" required>
                                        <option value="" disabled selected>-- Pilih Kategori --</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label for="reporter_name" class="form-label required">
                                        <i class="fas fa-user me-2"></i>Nama Pelapor
                                    </label>
                                    <input id="reporter_name" type="text" name="reporter_name" 
                                           class="form-control"
                                           placeholder="Contoh: Anthony" value="{{ old('reporter_name') }}" 
                                           required maxlength="100">
                                </div>
                                <div class="col-md-6">
                                    <label for="division" class="form-label required">
                                        <i class="fas fa-building me-2"></i>Divisi
                                    </label>
                                    <select id="division" name="division" class="form-select select2" required>
                                        <option value="" disabled selected>-- Pilih Divisi --</option>
                                        <option value="IT" {{ old('division') == 'IT' ? 'selected' : '' }}>IT</option>
                                        <option value="HRD" {{ old('division') == 'HRD' ? 'selected' : '' }}>HRD</option>
                                        <option value="Finance" {{ old('division') == 'Finance' ? 'selected' : '' }}>Finance</option>
                                        <option value="Marketing" {{ old('division') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                        <option value="Operations" {{ old('division') == 'Operations' ? 'selected' : '' }}>Operations</option>
                                        <option value="Other" {{ old('division') == 'Other' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="contact" class="form-label required">
                                    <i class="fab fa-whatsapp me-2"></i>Kontak WhatsApp
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">+62</span>
                                    <input id="contact" type="tel" name="contact" 
                                           class="form-control"
                                           placeholder="8xxxxxxxxxx" value="{{ old('contact') }}" 
                                           required maxlength="13" pattern="[0-9]{9,13}">
                                </div>
                                <div class="form-text text-muted">Format: 8xxxxxxxxxx (tanpa +62 atau 0, minimal 9 digit)</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="description" class="form-label required">
                                    <i class="fas fa-file-alt me-2"></i>Deskripsi Masalah
                                </label>
                                <textarea id="description" name="description" rows="5" 
                                          class="form-control" 
                                          required maxlength="1000"
                                          placeholder="Jelaskan masalah secara detail, langkah-langkah yang sudah dicoba, dan kapan masalah mulai terjadi.">{{ old('description') }}</textarea>
                                <div class="form-text text-end">
                                    <span id="charCount">0</span>/1000 karakter
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="image" class="form-label">
                                    <i class="fas fa-camera me-2"></i>Upload Gambar (Opsional)
                                </label>
                                <input id="image" type="file" name="image" 
                                       class="form-control" 
                                       accept="image/jpeg,image/png,image/jpg,image/gif">
                                <div class="form-text text-muted">
                                    Format yang didukung: JPG, JPEG, PNG, GIF. Maksimal 2MB.
                                </div>
                                <div id="imagePreview" class="mt-3 text-center"></div>
                            </div>

                            <div class="mb-4 form-check">
                                <input type="checkbox" class="form-check-input" id="confirmation" required>
                                <label class="form-check-label" for="confirmation">
                                    Saya menyatakan bahwa informasi yang diberikan adalah **benar dan akurat**.
                                </label>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('report.public') }}" class="btn btn-secondary btn-icon-split">
                                    <span class="icon text-white-50"><i class="fa-solid fa-arrow-left"></i></span>
                                    <span class="text">Kembali</span>
                                </a>
                                <button type="submit" class="btn btn-primary btn-icon-split">
                                    <span class="icon text-white-50"><i class="fas fa-paper-plane"></i></span>
                                    <span class="text">Kirim Laporan</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Character counter for description
        const descriptionTextarea = document.getElementById('description');
        const charCount = document.getElementById('charCount');
        
        descriptionTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            charCount.textContent = currentLength;
            
            if (currentLength > 900) {
                charCount.style.color = '#e74a3b';
            } else if (currentLength > 700) {
                charCount.style.color = '#f6c23e';
            } else {
                charCount.style.color = '#1cc88a';
            }
        });
        
        // Image preview
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            imagePreview.innerHTML = '';
            
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar. Maksimal 2MB.');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-fluid';
                    imagePreview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
        
        // WhatsApp number formatting
        const contactInput = document.getElementById('contact');
        contactInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            if (value.length > 0 && value.startsWith('0')) {
                value = value.substring(1);
            }
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
            const locationId = document.getElementById('location_id').value;

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

            if (!itemId || itemId === '') {
                e.preventDefault();
                alert('Silakan pilih kategori perangkat.');
                return false;
            }
            
            if (!locationId || locationId === '') {
                e.preventDefault();
                alert('Silakan pilih lokasi cabang.');
                return false;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="text ms-2">Mengirim...</span>';
        });
        
        // Initialize Select2 with Bootstrap 5 theme
        $(document).ready(function() {
            $('.select2').select2({
                theme: "bootstrap-5",
                width: $( this ).data( 'width' ) ? $( this ).data( 'width' ) : $( this ).hasClass( 'w-100' ) ? '100%' : 'style',
                placeholder: $( this ).data( 'placeholder' ),
            });
        });
    });
    </script>
</body>
</html>