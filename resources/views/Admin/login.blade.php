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
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('admin-layout/dist/css/adminlte.min.css')}}">

    <!-- 登入頁樣式 -->
    @vite('resources/css/login.css')

    <!-- jQuery -->
    <script src="{{asset('admin-layout/plugins/jquery/jquery.min.js')}}"></script>

    @include('Share/message-alert')
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>YoYo</b>Admin</a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">歡迎回來，請登入您的帳號</p>

            <form action="{{ url('admin/login') }}" method="post">
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
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">登入</button>
                    </div>
                </div>
            </form>
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
