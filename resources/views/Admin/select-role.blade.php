<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YoYoAdmin | 選擇角色</title>

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
<div class="login-box" style="width: 450px;">
    <div class="login-logo">
        <a href="#"><b>YoYo</b>Admin</a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">請選擇要使用的角色</p>

            <div class="row">
                @foreach($roles as $role)
                    @php
                        $isActive = ($currentRole['id'] ?? null) == $role['id'];
                    @endphp
                    <div class="col-6 mb-3">
                        <form action="{{ url('admin/select-role') }}" method="post">
                            @csrf
                            <input type="hidden" name="role_id" value="{{ $role['id'] }}">
                            <button type="submit" class="btn btn-block p-3 text-center border {{ $isActive ? 'border-primary bg-light' : 'border-secondary' }}" style="border-radius: 10px; height: 120px; transition: all 0.2s; display: flex; flex-direction: column; align-items: center; justify-content: center;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow='none'">
                                <div class="mb-2">
                                    <i class="fas fa-user-shield {{ $isActive ? 'text-primary' : 'text-muted' }}" style="font-size: 2rem;"></i>
                                </div>
                                <div class="font-weight-bold {{ $isActive ? 'text-primary' : 'text-dark' }}">{{ $role['role_name'] }}</div>
                                <small class="mt-1 d-block {{ $isActive ? 'text-muted' : '' }}" style="{{ $isActive ? '' : 'visibility: hidden;' }}">目前使用中</small>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
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
