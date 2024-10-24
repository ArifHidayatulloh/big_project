<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LOGIN KKI OPERATIONAL</title>

    {{-- -------- Admin LTE -------- --}}
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('lte/dist/css/adminlte.min.css') }}">
    <!-- Toastr -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/toastr/toastr.min.css') }}">

    <!-- Custom CSS -->
    <style>
        body {
            background: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            /* Tambahkan ini untuk menghapus margin body */
        }

        .login-card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            background-color: white;
            padding: 40px;
            width: 100%;
            margin-top: 50px;
            /* Membuat lebar 100% untuk mobile responsiveness */
            max-width: 400px;
            /* Batas maksimal lebar */
            
        }


        .login-logo img {
            width: 150px;
            margin-bottom: 20px;
        }

        .login-title {
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }
    </style>

    {{-- ---------- End of Admin LTE -------- --}}
    <link rel="shortcut icon" href="{{ asset('assets/images/logo_koperasi_indonesia.png') }}" type="image/x-icon">
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-card">
                    <!-- Logo -->
                    <div class="login-logo text-center">
                        <img src="{{ asset('assets/images/logo_koperasi_indonesia.png') }}" alt="Logo Koperasi">
                    </div>

                    <!-- Title -->
                    <h3 class="login-title">Login KKI Operational</h3>

                    <!-- Login Form -->
                    <form method="POST" action="/login">
                        @csrf
                        <div class="form-group">
                            <label for="nik">{{ __('NIK') }}</label>
                            <input id="nik" type="text" class="form-control @error('nik') is-invalid @enderror" name="nik" value="{{ old('nik') }}" required autocomplete="nik" autofocus>
                            @error('nik')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-block">{{ __('Login') }}</button>
                        </div>
                    </form>


                    <!-- Toastr notifications -->
                    @if (session('status'))
                        <script>
                            toastr.success('{{ session('status') }}');
                        </script>
                    @endif

                </div>
            </div>
        </div>
    </div>

    @include('partials.toastr')

    {{-- -------- Admin LTE -------- --}}
    <!-- jQuery -->
    <script src="{{ asset('lte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('lte/dist/js/adminlte.js') }}"></script>
    <script src="{{ asset('lte/plugins/toastr/toastr.min.js') }}"></script>

    {{-- -------- End of Admin LTE -------- --}}
</body>

</html>
