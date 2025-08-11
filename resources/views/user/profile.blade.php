@extends(Auth::user()->role == 'superadmin' ? 'superadmin' : 'layouts.itsupport')

@section('title', 'Edit Profil')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Profil</h1>
        <a href="{{ route('dashboard.it') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Dashboard
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

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

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Profil</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img class="img-profile rounded-circle" src="{{ asset('img/undraw_profile.svg') }}" style="width: 150px; height: 150px;">
                    </div>
                    <h5 class="text-center">{{ $user->name }}</h5>
                    <p class="text-center text-muted">
                        <span class="badge badge-{{ $user->role == 'superadmin' ? 'danger' : 'primary' }}">
                            {{ $user->role == 'superadmin' ? 'Superadmin' : 'IT Support' }}
                        </span>
                    </p>
                    <hr>
                    <div class="mb-2">
                        <strong>Jabatan:</strong> {{ $user->jabatan }}
                    </div>
                    <div class="mb-2">
                        <strong>Departemen:</strong> {{ $user->departemen }}
                    </div>
                    <div class="mb-2">
                        <strong>Email:</strong> {{ $user->email }}
                    </div>
                    <div class="mb-2">
                        <strong>Kontak:</strong> {{ $user->contact }}
                    </div>
                </div>
            </div>

            @if($user->signature_path)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tanda Tangan</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $user->signature_path) }}" alt="Tanda Tangan" class="img-fluid" style="max-width: 100%; max-height: 200px;">
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Profil</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="contact">Kontak <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact" name="contact" value="{{ old('contact', $user->contact) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                        <div class="form-group">
                            <label for="signature">Tanda Tangan (PNG) <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="signature" name="signature" accept="image/png">
                                <label class="custom-file-label" for="signature">Pilih file...</label>
                            </div>
                            <small class="form-text text-muted">Format file: PNG, Ukuran maksimal: 500KB</small>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
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