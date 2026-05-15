<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="admin-message-endpoint" content="{{ url('share/getMessage/' . ADMIN_MESSAGE_SESSION) }}">
    <title>YoYoAdmin@yield('title-suffix')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0,0" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/admin.css', 'resources/js/admin/index.js'])
    @stack('styles')
</head>
<body class="admin-app">
    <x-admin.skip-link target="admin-content">跳至主要內容</x-admin.skip-link>

    <div class="admin-shell">
        @include('Admin-share.sidebar')
        <div class="admin-sidebar-backdrop" data-admin-sidebar-backdrop aria-hidden="true"></div>

        <div class="admin-main">
            @include('Admin-share.topbar')

            <main class="admin-content" id="admin-content" tabindex="-1">
                @yield('content')
            </main>

            @include('Admin-share.footer')
        </div>
    </div>

    @stack('scripts')
</body>
</html>
