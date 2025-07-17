<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - Toyota IT Support</title>

    <!-- Fonts and CSS -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>

<body class="bg-gradient-primary d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h1 class="h4 text-gray-900">Buat Akun Baru</h1>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                {{ $errors->first() }}
                            </div>
                        @endif

                        <form class="user" method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="form-group">
                                <input type="text" name="name" class="form-control form-control-user"
                                    placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                            </div>

                            <div class="form-group">
                                <input type="email" name="email" class="form-control form-control-user"
                                    placeholder="Alamat Email" value="{{ old('email') }}" required>
                            </div>

                            <div class="form-group">
                            <input type="text" name="jabatan" class="form-control form-control-user"
                                placeholder="Jabatan (contoh: IT Support, Admin)" value="{{ old('jabatan') }}" required>
                            </div>

                            <div class="form-group">
                            <input type="text" name="departemen" class="form-control form-control-user"
                                placeholder="Departemen (contoh: IT, HRD, Finance)" value="{{ old('departemen') }}" required>
                            </div>


                            <div class="form-group">
                                <input type="password" name="password" class="form-control form-control-user"
                                    placeholder="Password" required>
                            </div>

                            <div class="form-group">
                                <input type="password" name="password_confirmation" class="form-control form-control-user"
                                    placeholder="Konfirmasi Password" required>
                            </div>

                            <div class="form-group">
                                <select name="role" class="form-control form-control-user" required>
                                    <option value="">Pilih Role</option>
                                    <option value="it_supp">IT Support</option>
                                    <option value="superadmin">Superadmin</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                Daftar
                            </button>
                        </form>

                        <hr>
                        <div class="text-center">
                            <a class="small" href="{{ route('login') }}">Sudah punya akun? Login!</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
</body>

</html>
