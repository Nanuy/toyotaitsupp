@extends('superadmin')

@section('title', 'Register - Toyota IT Support')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="text-center">
                        <div class="logo text-danger h4 font-weight-bold mb-2">SETIAJAYA MOBILINDO</div>
                        <h4 class="mb-1">IT Support System</h4>
                        <p class="text-muted mb-0">Registrasi Pengguna Baru</p>
                    </div>
                </div>

                <div class="card-body p-4">
                    {{-- Flash success message --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    {{-- Validation errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li><i class="fas fa-exclamation-circle"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="registerForm" method="POST" action="{{ route('register.custom') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label font-weight-bold">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact" class="form-label font-weight-bold">Kontak <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contact" name="contact" value="{{ old('contact') }}" placeholder="Contoh: 08123456789" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="jabatan" class="form-label font-weight-bold">Jabatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="jabatan" name="jabatan" value="{{ old('jabatan') }}" placeholder="Contoh: IT Support, Admin" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="departemen" class="form-label font-weight-bold">Departemen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="departemen" name="departemen" value="{{ old('departemen') }}" placeholder="Contoh: IT, HRD, Finance" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label font-weight-bold">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label font-weight-bold">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="role" class="form-label font-weight-bold">Role <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="role" name="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="it_supp" {{ old('role') == 'it_supp' ? 'selected' : '' }}>IT Support</option>
                                    <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="signature" class="form-label font-weight-bold">Tanda Tangan (PNG) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="signature" name="signature" accept="image/png" required>
                                <small class="text-muted">Hanya file PNG (max 500 KB).</small>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-user-plus me-2"></i> Daftar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('registerForm');
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');

        // Form validation
        form.addEventListener('submit', function(event) {
            // Cek password match
            if (password.value !== passwordConfirmation.value) {
                event.preventDefault();
                alert('Password dan konfirmasi password tidak cocok!');
                passwordConfirmation.focus();
                return false;
            }

            // Jika semua valid, form akan submit normal
        });

        // Real-time password confirmation validation
        passwordConfirmation.addEventListener('input', function() {
            if (this.value !== password.value) {
                this.setCustomValidity('Password tidak cocok');
                this.classList.add('is-invalid');
            } else {
                this.setCustomValidity('');
                this.classList.remove('is-invalid');
            }
        });

        // Initialize Select2
        $('#role').select2({
            width: '100%',
            placeholder: 'Pilih Role'
        });
    });
</script>
@endpush