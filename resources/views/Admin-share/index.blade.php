<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YoYoAdmin</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Font Awesome（側邊欄選單圖示） -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    @vite('resources/css/stitch.css')

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Alpine.js（側邊欄摺疊動畫） -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="font-body antialiased bg-surface text-on-surface min-h-screen">
@include('Share/message-alert')
<div class="flex min-h-screen">
    {{-- 側邊欄 --}}
    @include('Admin-share/main-sidbar')

    {{-- 主要內容區 --}}
    <div class="flex-1 ml-64 flex flex-col min-h-screen">
        {{-- 頂部導覽列 --}}
        @include('Admin-share/navbar')

        {{-- 頁面內容 --}}
        <main class="flex-1">
            @yield('content')
        </main>

        {{-- 頁尾 --}}
        @include('Admin-share/footer')
    </div>
</div>

@stack('scripts')
</body>
</html>
