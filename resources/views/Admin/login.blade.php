<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YoYoAdmin | 登入</title>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('admin-layout/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{asset('admin-layout/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('admin-layout/dist/css/adminlte.min.css')}}">

    <!-- jQuery -->
    <script src="{{asset('admin-layout/plugins/jquery/jquery.min.js')}}"></script>

    @include('Share/message-alert')

    <style>
        body.login-page {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', 'Source Sans Pro', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-box {
            width: 420px;
            max-width: 90vw;
        }

        .login-logo a {
            color: #fff;
            font-size: 2.2rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .login-logo {
            margin-bottom: 1.5rem;
        }

        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15),
                        0 8px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card-body {
            padding: 2.5rem 2rem 2rem;
        }

        .login-box-msg {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 1.8rem;
            font-weight: 400;
        }

        .input-group {
            margin-bottom: 1.2rem;
        }

        .form-control {
            border-radius: 10px 0 0 10px;
            border: 2px solid #e9ecef;
            padding: 0.7rem 1rem;
            font-size: 0.95rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            height: auto;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        }

        .input-group-text {
            border-radius: 0 10px 10px 0;
            border: 2px solid #e9ecef;
            border-left: none;
            background: #f8f9fa;
            color: #adb5bd;
            transition: border-color 0.2s ease;
        }

        .form-control:focus + .input-group-append .input-group-text,
        .input-group:focus-within .input-group-text {
            border-color: #667eea;
            color: #667eea;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 0.7rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.35);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.45);
            background: linear-gradient(135deg, #5a6fd6 0%, #6a4296 100%);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .icheck-primary > label {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .mb-1 a {
            color: #667eea;
            font-size: 0.9rem;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .mb-1 a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>YoYo</b>Admin</a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">歡迎回來，請登入您的帳號</p>

            <form action="{{asset('admin/login')}}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" name="account" class="form-control" placeholder="帳號" autocomplete="username">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="密碼" autocomplete="current-password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-7">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">
                                記住我
                            </label>
                        </div>
                    </div>
                    <div class="col-5">
                        <button type="submit" class="btn btn-primary btn-block">登入</button>
                    </div>
                </div>
            </form>

            <hr style="margin: 1.5rem 0 1rem; border-color: #e9ecef;">

            <p class="mb-1 text-center" style="margin-bottom: 0 !important;">
                <a href="forgot-password.html">忘記密碼？</a>
            </p>
        </div>
    </div>

    <div class="login-footer">
        &copy; {{ date('Y') }} YoYoAdmin
    </div>
</div>

<!-- Bootstrap 4 -->
<script src="{{asset('admin-layout/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('admin-layout/dist/js/adminlte.min.js')}}"></script>

</body>
</html>
