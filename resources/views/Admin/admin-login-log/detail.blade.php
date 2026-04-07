@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            {{-- 頁面標題區 --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <nav class="flex items-center gap-2 text-[0.75rem] text-outline-variant mb-1 uppercase tracking-widest font-semibold">
                        <a href="{{ asset('admin/') }}" class="hover:text-primary transition-colors">首頁</a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <a href="{{ asset('admin/admin.login-log/list') }}" class="hover:text-primary transition-colors">登入日誌</a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="text-primary">詳情</span>
                    </nav>
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">登入日誌詳情</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">查看登入活動的完整記錄</p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                {{-- 左側：基本信息 --}}
                <div class="w-full lg:w-2/3">
                    <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden">
                        <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-primary rounded-full"></span>
                            <h3 class="text-[0.9375rem] font-bold text-on-surface">基本資訊</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">日誌 ID</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-50 text-right font-mono text-[0.875rem] text-primary font-medium">{{ $data['id'] }}</div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">登入帳號</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-50 text-right text-[0.875rem] font-medium text-on-surface">{{ $data['account'] }}</div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">帳號姓名</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-50 text-right text-[0.875rem] text-on-surface">{{ $data['employee_name'] }}</div>
                                </div>
                                @if($data['employee_id'])
                                    <div class="flex items-center justify-between">
                                        <span class="text-[0.8125rem] text-outline">帳號 ID</span>
                                        <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-50 text-right font-mono text-[0.875rem] text-on-surface-variant">{{ $data['employee_id'] }}</div>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">操作類型</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-50 text-right">
                                        <span class="px-3 py-1 text-[0.75rem] font-bold rounded-full
                                            @if($data['action'] === 'login') bg-blue-50 text-blue-600
                                            @else bg-slate-100 text-slate-500
                                            @endif
                                        ">{{ $data['action_display'] }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">狀態</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-50 text-right">
                                        @if($data['status'] == 1)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[0.75rem] font-bold bg-emerald-50 text-emerald-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                                {{ $data['status_display'] }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-[0.75rem] font-bold bg-red-50 text-red-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                                {{ $data['status_display'] }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if($data['fail_reason'])
                                    <div class="flex items-center justify-between md:col-span-2">
                                        <span class="text-[0.8125rem] text-outline">失敗原因</span>
                                        <div class="bg-red-50 px-4 py-2 rounded-lg min-w-50 text-right text-[0.875rem] text-red-600 font-medium">{{ $data['fail_reason'] }}</div>
                                    </div>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">IP 位址</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-50 text-right font-mono text-[0.875rem] text-on-surface-variant">{{ $data['ip_address'] }}</div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">操作時間</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-50 text-right font-mono text-[0.875rem] text-outline">{{ $data['operated_at'] }}</div>
                                </div>
                                <div class="flex items-center justify-between md:col-span-2">
                                    <span class="text-[0.8125rem] text-outline">記錄建立時間</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-50 text-right font-mono text-[0.875rem] text-outline">{{ $data['created_at'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 右側：操作面板 --}}
                <div class="w-full lg:w-1/3">
                    <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden sticky top-4">
                        <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">bolt</span>
                            <h3 class="text-[0.9375rem] font-bold text-on-surface">操作面板</h3>
                        </div>
                        <div class="p-6">
                            <a href="{{ asset('admin/admin.login-log/list') }}" class="w-full py-3 btn-primary rounded-xl font-bold text-[0.875rem] flex items-center justify-center gap-2 hover:scale-[1.02] active:scale-95 transition-all duration-200 no-underline">
                                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                                返回列表
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
