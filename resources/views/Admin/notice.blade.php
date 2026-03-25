<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YoYoAdmin | 系統通知</title>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('admin-layout/plugins/fontawesome-free/css/all.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('admin-layout/dist/css/adminlte.min.css')}}">

    <!-- 登入頁樣式 -->
    @vite('resources/css/login.css')
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>YoYo</b>Admin</a>
    </div>

    <div class="card">
        <div class="card-body login-card-body text-center">
            <div class="mb-3" style="font-size: 3rem;">
                <i class="fas {{ $notice['icon'] }} {{ $notice['color'] }}"></i>
            </div>
            <h5 class="font-weight-bold mb-3">{{ $notice['title'] }}</h5>
            <p class="text-muted mb-4">{{ $notice['message'] }}</p>
            <a href="{{ url('admin/login') }}" class="btn btn-primary btn-block">
                返回登入頁
            </a>
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
