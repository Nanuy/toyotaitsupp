<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login - SETIAJAYA MOBILINDO IT Support</title>

    <!-- SB Admin 2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    
    <style>
        .toyota-logo {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.8rem;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        
        .form-control-user {
            font-size: 0.9rem;
            border-radius: 10rem;
            padding: 1.5rem 1rem;
        }
        
        .btn-user {
            font-size: 0.9rem;
            border-radius: 10rem;
            padding: 0.75rem 1rem;
        }
        
        .login-card {
            max-width: 450px;
            margin: 0 auto;
        }
        
        .system-title {
            font-size: 1.1rem;
            color: #5a5c69;
            font-weight: 600;
        }
        
        .welcome-text {
            color: #858796;
            font-size: 0.95rem;
        }
    </style>
</head>

<body class="bg-gradient-primary d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-9">
                <div class="card o-hidden border-0 shadow-lg login-card">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center mb-4">
                                <div class="toyota-logo">SETIAJAYA MOBILINDO</div>
                                <div class="system-title mb-2">IT Support System</div>
                                <h1 class="h4 text-gray-900 mb-2">Welcome Back!</h1>
                                <p class="welcome-text">Login untuk masuk ke sistem Toyota IT Support</p>
                            </div>

                            {{-- Flash Message --}}
                            @if (session('success'))
                                <div class="alert alert-success"><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}</div>
                            @endif

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li><i class="fas fa-times-circle mr-2"></i>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Form Login -->
                            <form class="user" method="POST" action="{{ route('login.custom') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control form-control-user" placeholder="Masukkan Email..." required autofocus>
                                </div>

                                <div class="form-group">
                                    <input type="password" name="password" class="form-control form-control-user" placeholder="Password" required>
                                </div>

                                <div class="form-group">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" id="customCheck" name="remember">
                                        <label class="custom-control-label" for="customCheck">Remember Me</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                                </button>
                            </form>

                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SB Admin 2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <script>
        $(document).ready(function() {
            const form = $('#loginForm');
            
            // Form submission
            form.on('submit', function(e) {
                e.preventDefault();
                
                // Clear previous errors
                $('.invalid-feedback').hide();
                $('.form-control').removeClass('is-invalid');
                $('#alertContainer').empty();
                
                let isValid = true;
                
                // Validate email
                const email = $('#email').val().trim();
                if (!email) {
                    showFieldError('email', 'emailError', 'Email wajib diisi.');
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    showFieldError('email', 'emailError', 'Format email tidak valid.');
                    isValid = false;
                }
                
                // Validate password
                const password = $('#password').val();
                if (!password) {
                    showFieldError('password', 'passwordError', 'Password wajib diisi.');
                    isValid = false;
                }
                
                if (isValid) {
                    // Show loading state
                    const submitBtn = $('button[type="submit"]');
                    const originalText = submitBtn.html();
                    submitBtn.html('<i class="fas fa-spinner fa-spin mr-2"></i>Logging in...').prop('disabled', true);
                    
                    // Simulate login process
                    setTimeout(() => {
                        // Reset button
                        submitBtn.html(originalText).prop('disabled', false);
                        
                        // Simulate different login scenarios
                        if (email === 'admin@toyota.com' && password === 'admin123') {
                            showAlert('success', 'Login berhasil! Mengalihkan ke dashboard...');
                            setTimeout(() => {
                                showAlert('info', 'Dalam aplikasi nyata, Anda akan dialihkan ke dashboard.');
                            }, 2000);
                        } else {
                            showAlert('danger', 'Email atau password salah. Silakan coba lagi.');
                        }
                    }, 1500);
                }
            });
            
            function showFieldError(fieldId, errorId, message) {
                $('#' + fieldId).addClass('is-invalid');
                $('#' + errorId).text(message).show();
            }
            
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
            
            function showAlert(type, message) {
                const alertClass = `alert-${type}`;
                const iconClass = {
                    'success': 'fas fa-check-circle',
                    'danger': 'fas fa-exclamation-triangle',
                    'info': 'fas fa-info-circle'
                };
                
                const alertHtml = `
                    <div class="alert ${alertClass} alert-dismissible fade show">
                        <i class="${iconClass[type]} mr-2"></i>
                        ${message}
                        <button type="button" class="close" data-dismiss="alert">
                            <span>&times;</span>
                        </button>
                    </div>
                `;
                
                $('#alertContainer').html(alertHtml);
                
                // Auto dismiss after 5 seconds
                setTimeout(() => {
                    $('.alert').alert('close');
                }, 5000);
            }
            
        
        
        function showForgotPassword() {
            alert('Fitur lupa password akan mengirim link reset ke email Anda.');
        }
        
        function showRegister() {
            alert('Dialihkan ke halaman registrasi...');
        }
    }); // <-- Correct closing for $(document).ready
    </script>
</body>

</html>