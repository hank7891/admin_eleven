@extends('Admin-share/guest')
@section('title', '登入')
@section('body-class', 'overflow-hidden')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endpush

@section('background')
    {{-- 裝飾背景元素 --}}
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-white/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-indigo-500/20 rounded-full blur-[120px] pointer-events-none"></div>
@endsection

@section('content')
    {{-- 品牌標誌 --}}
    <div class="mb-8 text-center">
        <h1 class="text-5xl font-black text-white tracking-tight drop-shadow-lg font-headline">YoYoAdmin</h1>
    </div>

    {{-- 登入卡片 --}}
    <main class="w-full max-w-md bg-white rounded-xl shadow-[0_25px_50px_-12px_rgba(0,0,0,0.25)] p-10 relative z-10">
        <div class="flex flex-col gap-8">
            {{-- 標題 --}}
            <div class="space-y-2">
                <h2 class="text-2xl font-bold text-on-surface">歡迎回來</h2>
                <p class="text-outline font-medium">請登入您的帳號</p>
            </div>

            {{-- 表單 --}}
            <form action="{{ url('admin/login') }}" method="post" class="space-y-6">
                @csrf

                {{-- 帳號 --}}
                <div class="space-y-1.5">
                    <label class="text-[0.875rem] font-semibold text-on-surface-variant ml-1">帳號</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-outline-variant group-focus-within:text-primary transition-colors">
                            <span class="material-symbols-outlined">person</span>
                        </div>
                        <input type="text" name="account" placeholder="請輸入使用者名稱" autocomplete="username"
                               class="block w-full pl-11 pr-4 py-3.5 bg-surface-container-low border-0 rounded-lg ring-1 ring-outline-variant/30 focus:ring-2 focus:ring-primary transition-all outline-none text-on-surface placeholder:text-outline-variant/60">
                    </div>
                </div>

                {{-- 密碼 --}}
                <div class="space-y-1.5">
                    <label class="text-[0.875rem] font-semibold text-on-surface-variant ml-1">密碼</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-outline-variant group-focus-within:text-primary transition-colors">
                            <span class="material-symbols-outlined">lock</span>
                        </div>
                        <input type="password" name="password" placeholder="請輸入您的密碼" autocomplete="current-password"
                               class="block w-full pl-11 pr-4 py-3.5 bg-surface-container-low border-0 rounded-lg ring-1 ring-outline-variant/30 focus:ring-2 focus:ring-primary transition-all outline-none text-on-surface placeholder:text-outline-variant/60">
                    </div>
                </div>

                {{-- 登入按鈕 --}}
                <button type="submit" class="w-full bg-gradient-to-r from-[#667eea] to-[#764ba2] text-white font-bold py-4 rounded-lg shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 active:scale-95 transition-all duration-200 flex items-center justify-center gap-2">
                    登入
                    <span class="material-symbols-outlined text-[20px]">login</span>
                </button>
            </form>
        </div>
    </main>

    {{-- 頁尾 --}}
    <footer class="mt-12 text-center">
        <p class="text-white/60 text-[0.875rem] font-medium tracking-wide">&copy; {{ date('Y') }} YoYoAdmin. All rights reserved.</p>
    </footer>
@endsection
