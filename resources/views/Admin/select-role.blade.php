@extends('Admin-share/guest')
@section('title', '選擇角色')
@section('body-class', 'overflow-hidden')

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #4b5e90 0%, #6b5680 100%);
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
    <div class="mb-12 z-10">
        <h1 class="text-4xl font-black text-white tracking-tight font-headline">YoYoAdmin</h1>
    </div>

    {{-- 角色選擇卡片 --}}
    <main class="w-full max-w-2xl bg-white rounded-2xl shadow-[0_25px_50px_-12px_rgba(0,0,0,0.25)] overflow-hidden z-10">
        <div class="p-8 md:p-12">
            {{-- 標題 --}}
            <div class="text-center mb-10">
                <h2 class="text-2xl font-bold text-on-surface mb-2">選擇角色</h2>
                <p class="text-outline">請選擇要使用的角色</p>
            </div>

            {{-- 角色網格 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($roles as $role)
                    @php
                        $isActive = ($currentRole['id'] ?? null) == $role['id'];
                    @endphp
                    <form action="{{ url('admin/select-role') }}" method="post">
                        @csrf
                        <input type="hidden" name="role_id" value="{{ $role['id'] }}">
                        <button type="submit" class="w-full group relative flex flex-col items-center p-8 rounded-xl border-2 transition-all hover:-translate-y-1 active:scale-[0.98]
                            {{ $isActive
                                ? 'bg-primary/5 border-secondary shadow-lg shadow-primary/10'
                                : 'bg-surface-container-low border-transparent hover:border-primary/30 hover:bg-surface-container' }}">
                            {{-- 圖示 --}}
                            <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 shadow-lg
                                {{ $isActive
                                    ? 'brand-mark'
                                    : 'bg-surface-container-high group-hover:bg-surface-container transition-colors' }}">
                                <span class="material-symbols-outlined text-[2rem]
                                    {{ $isActive ? 'text-white' : 'text-outline' }}">shield_person</span>
                            </div>
                            {{-- 角色名稱 --}}
                            <span class="font-bold text-[1.125rem] mb-1
                                {{ $isActive ? 'text-on-surface' : 'text-on-surface-variant' }}">{{ $role['role_name'] }}</span>
                            {{-- 使用中標籤 --}}
                            @if($isActive)
                                <span class="text-[0.75rem] font-semibold text-primary tracking-wider uppercase">目前使用中</span>
                                <div class="absolute top-3 right-3">
                                    <span class="material-symbols-outlined text-primary text-[20px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                </div>
                            @endif
                        </button>
                    </form>
                @endforeach
            </div>
        </div>
    </main>

    {{-- 頁尾 --}}
    <footer class="mt-12 text-center z-10">
        <p class="text-white/60 text-[0.75rem]">&copy; {{ date('Y') }} YoYoAdmin. All rights reserved.</p>
    </footer>
@endsection
