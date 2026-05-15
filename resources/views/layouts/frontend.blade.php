<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="member-message-endpoint" content="{{ url('share/getMessage/' . MEMBER_MESSAGE_SESSION) }}">
    <title>{{ $pageTitle ?? 'Aura & Heirloom' }}</title>
    <meta name="description" content="@yield('meta_description', 'Aura & Heirloom — 為日常留一個慢下來的位置')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Serif+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,300,0,0" rel="stylesheet">

    @vite(['resources/css/frontend.css', 'resources/js/frontend/index.js'])
    @stack('styles')
</head>
<body class="fe-app" data-fe-active="@yield('fe-active', '')">
    <x-fe.skip-link target="main-content">跳至主要內容</x-fe.skip-link>

    @include('Frontend-share.alert-banner')
    @include('Frontend-share.header')
    @include('Frontend-share.flash-message')

    <main class="fe-main" id="main-content" tabindex="-1">
        @yield('content')
    </main>

    @include('Frontend-share.footer')
    @stack('scripts')
</body>
</html>
