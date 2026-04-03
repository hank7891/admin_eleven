<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YoYoAdmin | 系統通知</title>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    @vite('resources/css/stitch.css')
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6 font-body bg-surface text-on-surface">

    <div class="w-full max-w-md bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-10 text-center">
        <span class="material-symbols-outlined text-[3rem] text-primary mb-4 block">
            @if(($notice['type'] ?? '') === 'error') error
            @elseif(($notice['type'] ?? '') === 'warning') warning
            @elseif(($notice['type'] ?? '') === 'success') check_circle
            @else info
            @endif
        </span>
        <h2 class="text-[1.25rem] font-bold text-on-surface mb-3 font-headline">{{ $notice['title'] }}</h2>
        <p class="text-[0.875rem] text-outline mb-6">{{ $notice['message'] }}</p>
        <a href="{{ url('admin/login') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#667eea] to-[#764ba2] text-white rounded-xl font-bold text-[0.875rem] shadow-lg shadow-indigo-500/20 active:scale-95 transition-all no-underline">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            返回登入頁
        </a>
    </div>

    <p class="mt-8 text-[0.75rem] text-outline">&copy; {{ date('Y') }} YoYoAdmin</p>

</body>
</html>
