@extends('superadmin')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit User</h1>
        <a href="{{ route('user.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit User</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="contact">Kontak <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact" name="contact" value="{{ old('contact', $user->contact) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="jabatan">Jabatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="departemen">Departemen <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="departemen" name="departemen" value="{{ old('departemen', $user->departemen) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role <span class="text-danger">*</span></label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="it_supp" {{ (old('role', $user->role) == 'it_supp') ? 'selected' : '' }}>IT Support</option>
                                <option value="superadmin" {{ (old('role', $user->role) == 'superadmin') ? 'selected' : '' }}>Superadmin</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="signature">Tanda Tangan (PNG) <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                    @if($user->signature_path)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $user->signature_path) }}" alt="Tanda Tangan" class="img-thumbnail" style="max-width: 200px;">
                    </div>
                    @endif
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="signature" name="signature" accept="image/png">
                        <label class="custom-file-label" for="signature">Pilih file...</label>
                    </div>
                    <small class="form-text text-muted">Format file: PNG, Ukuran maksimal: 500KB</small>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('user.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Menampilkan nama file yang dipilih pada input file
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endsection