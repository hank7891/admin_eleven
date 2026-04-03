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
                        <a href="{{ asset('admin/admin.log/list') }}" class="hover:text-primary transition-colors">操作日誌</a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="text-primary">詳情</span>
                    </nav>
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">操作日誌詳情</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">查看系統操作紀錄的完整細節</p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                {{-- 左側：基本信息 + 修改詳情 --}}
                <div class="w-full lg:w-2/3 space-y-6">
                    {{-- 基本信息卡片 --}}
                    <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden">
                        <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-primary rounded-full"></span>
                            <h3 class="text-[0.9375rem] font-bold text-on-surface">基本信息</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">日誌 ID</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-[200px] text-right font-mono text-[0.875rem] text-primary font-medium">{{ $data['id'] }}</div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">操作者名稱</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-[200px] text-right text-[0.875rem] text-on-surface">{{ $data['operator_name'] }}</div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">操作者 IP</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-[200px] text-right font-mono text-[0.875rem] text-on-surface-variant">{{ $data['ip_address'] }}</div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">操作模組</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-[200px] text-right">
                                        <span class="px-3 py-1 text-[0.75rem] font-bold rounded-full bg-blue-50 text-blue-600">{{ $data['module_display'] }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">操作類型</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-[200px] text-right">
                                        <span class="px-3 py-1 text-[0.75rem] font-bold rounded-full
                                            @if($data['action'] === 'create') bg-emerald-50 text-emerald-600
                                            @elseif($data['action'] === 'update') bg-amber-50 text-amber-600
                                            @elseif($data['action'] === 'delete') bg-red-50 text-red-600
                                            @else bg-slate-100 text-slate-500
                                            @endif
                                        ">{{ $data['action_display'] }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">被操作資源</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-[200px] text-right text-[0.875rem] text-on-surface">
                                        @if($data['target_id'])
                                            ID: <strong class="text-primary">{{ $data['target_id'] }}</strong>
                                            @if($data['target_name'])
                                                - {{ $data['target_name'] }}
                                            @endif
                                        @else
                                            --
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">操作時間</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-[200px] text-right font-mono text-[0.875rem] text-outline">{{ $data['operated_at'] }}</div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[0.8125rem] text-outline">記錄建立時間</span>
                                    <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-[200px] text-right font-mono text-[0.875rem] text-outline">{{ $data['created_at'] }}</div>
                                </div>
                                @if($data['remarks'])
                                    <div class="flex items-center justify-between md:col-span-2">
                                        <span class="text-[0.8125rem] text-outline">備註</span>
                                        <div class="bg-surface-container-low px-4 py-2 rounded-lg min-w-[200px] text-right text-[0.875rem] text-on-surface">{{ $data['remarks'] }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 修改詳情卡片 --}}
                    <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden">
                        <div class="px-6 py-4 border-b border-outline-variant/20 flex items-center gap-3">
                            <span class="w-1.5 h-6 bg-secondary rounded-full"></span>
                            <h3 class="text-[0.9375rem] font-bold text-on-surface">修改詳情</h3>
                        </div>
                        <div class="p-6">
                            @if(is_array($data['changes']) && count($data['changes']) > 0)
                                <div class="border border-outline-variant/20 rounded-xl overflow-hidden">
                                    <table class="w-full text-left border-collapse">
                                        <thead>
                                        <tr class="bg-surface-container-low">
                                            <th class="px-6 py-4 text-[0.8125rem] font-bold text-outline" style="width: 25%;">欄位名稱</th>
                                            <th class="px-6 py-4 text-[0.8125rem] font-bold text-outline" style="width: 37.5%;">修改前</th>
                                            <th class="px-6 py-4 text-[0.8125rem] font-bold text-outline" style="width: 37.5%;">修改後</th>
                                        </tr>
                                        </thead>
                                        <tbody class="divide-y divide-outline-variant/10">
                                        @foreach($data['changes'] as $field => $change)
                                            <tr class="hover:bg-surface-container-low transition-colors duration-200">
                                                <td class="px-6 py-5 text-[0.875rem] font-medium text-on-surface">{{ $field }}</td>
                                                <td class="px-6 py-5">
                                                    @if(is_array($change) && isset($change['old']))
                                                        <span class="bg-red-50 text-red-600 px-3 py-1 rounded-md text-[0.8125rem] font-medium">{{ is_array($change['old']) ? json_encode($change['old'], JSON_UNESCAPED_UNICODE) : ($change['old'] ?? '--') }}</span>
                                                    @else
                                                        <span class="bg-red-50 text-red-600 px-3 py-1 rounded-md text-[0.8125rem] font-medium">{{ is_array($change) ? json_encode($change, JSON_UNESCAPED_UNICODE) : ($change ?? '--') }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-5">
                                                    @if(is_array($change) && isset($change['new']))
                                                        <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-md text-[0.8125rem] font-medium">{{ is_array($change['new']) ? json_encode($change['new'], JSON_UNESCAPED_UNICODE) : ($change['new'] ?? '--') }}</span>
                                                    @else
                                                        <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded-md text-[0.8125rem] font-medium">{{ is_array($change) ? json_encode($change, JSON_UNESCAPED_UNICODE) : ($change ?? '--') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="flex items-center gap-2 p-4 bg-surface-container-low rounded-xl text-[0.875rem] text-outline">
                                    <span class="material-symbols-outlined text-[18px]">info</span>
                                    無修改詳情
                                </div>
                            @endif
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
                            <a href="{{ asset('admin/admin.log/list') }}" class="w-full py-3 bg-gradient-to-r from-[#667eea] to-[#764ba2] text-white rounded-xl font-bold text-[0.875rem] flex items-center justify-center gap-2 shadow-lg shadow-indigo-500/20 hover:scale-[1.02] active:scale-95 transition-all duration-200 no-underline">
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
