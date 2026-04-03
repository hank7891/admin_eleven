@extends('Admin-share/index')
@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite('resources/css/stitch.css')

    <div class="content-wrapper stitch-page">
        <div class="p-6 lg:p-10 space-y-8">
            {{-- 頁面標題區 --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <nav class="flex items-center gap-2 text-[0.75rem] text-outline-variant mb-1 uppercase tracking-widest font-semibold">
                        <a href="{{ asset('admin/') }}" class="hover:text-primary transition-colors">首頁</a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="text-primary">操作日誌</span>
                    </nav>
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">操作日誌</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">追蹤系統中所有的使用者行為與異動記錄</p>
                </div>
            </div>

            {{-- 搜尋篩選卡片 --}}
            <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-6">
                <form method="GET" action="{{ asset('admin/admin.log/list') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                        <div class="space-y-2">
                            <label class="text-[0.75rem] font-bold text-outline uppercase tracking-wider">操作者名稱</label>
                            <input type="text"
                                   name="operator_name"
                                   placeholder="輸入名稱..."
                                   value="{{ $filters['operator_name'] ?? '' }}"
                                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:border-primary focus:ring-primary/30 focus:ring-2 transition-all placeholder:text-outline-variant/60">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.75rem] font-bold text-outline uppercase tracking-wider">IP 位址</label>
                            <input type="text"
                                   name="ip_address"
                                   placeholder="192.168.x.x"
                                   value="{{ $filters['ip_address'] ?? '' }}"
                                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:border-primary focus:ring-primary/30 focus:ring-2 transition-all placeholder:text-outline-variant/60">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.75rem] font-bold text-outline uppercase tracking-wider">功能模組</label>
                            <select name="module"
                                    class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:border-primary focus:ring-primary/30 focus:ring-2 transition-all">
                                <option value="">全部</option>
                                @foreach ($moduleOptions ?? [] as $key => $label)
                                    <option value="{{ $key }}" {{ ($filters['module'] ?? '') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.75rem] font-bold text-outline uppercase tracking-wider">開始時間</label>
                            <input type="date"
                                   name="date_from"
                                   value="{{ $filters['date_from'] ?? '' }}"
                                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:border-primary focus:ring-primary/30 focus:ring-2 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.75rem] font-bold text-outline uppercase tracking-wider">結束時間</label>
                            <input type="date"
                                   name="date_to"
                                   value="{{ $filters['date_to'] ?? '' }}"
                                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:border-primary focus:ring-primary/30 focus:ring-2 transition-all">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-outline-variant/20">
                        <a href="{{ asset('admin/admin.log/list') }}" class="px-6 py-2.5 text-outline hover:text-on-surface font-medium text-[0.875rem] transition-colors">
                            清除條件
                        </a>
                        <button type="submit" class="px-8 py-2.5 bg-gradient-to-r from-[#667eea] to-[#764ba2] text-white rounded-xl font-bold text-[0.875rem] shadow-lg shadow-indigo-500/20 active:scale-95 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">search</span>
                            搜尋日誌
                        </button>
                    </div>
                </form>
            </div>

            {{-- 資料表格 --}}
            @if (!($hasFilter ?? false))
                {{-- 尚未搜尋 --}}
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-12 text-center">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant/40 mb-3 block">search</span>
                    <p class="text-outline text-[0.875rem]">請輸入搜尋條件後查詢</p>
                </div>
            @elseif (empty($data))
                {{-- 搜尋無結果 --}}
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-12 text-center">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant/40 mb-3 block">inbox</span>
                    <p class="text-outline text-[0.875rem]">查無符合條件的資料</p>
                </div>
            @else
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse" id="logTable">
                            <thead>
                            <tr class="bg-surface-container-low">
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">#</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">操作</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">ID</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">操作者</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">模組</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">操作類型</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">資源</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">IP 位址</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">操作時間</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/10">
                            @foreach ($data as $key => $row)
                                <tr class="hover:bg-surface-container-low transition-colors duration-200">
                                    <td class="px-6 py-5 text-[0.875rem] font-medium text-outline">{{ $key + 1 }}</td>
                                    <td class="px-6 py-5">
                                        <a href="{{ asset('admin/admin.log/detail/' . $row['id']) }}" class="flex items-center gap-1.5 text-primary hover:bg-primary/5 px-3 py-1.5 rounded-lg transition-colors font-semibold text-[0.875rem] no-underline">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                            詳情
                                        </a>
                                    </td>
                                    <td class="px-6 py-5 text-[0.875rem] font-mono font-medium text-on-surface-variant">{{ $row['id'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] font-medium text-on-surface">{{ $row['operator_name'] }}</td>
                                    <td class="px-6 py-5">
                                        <span class="px-2.5 py-1 text-[0.75rem] font-bold rounded-full bg-blue-50 text-blue-600">{{ $row['module_display'] }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-[0.875rem] text-on-surface-variant">{{ $row['action_display'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] font-mono text-outline">{{ $row['target_name'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] font-mono text-outline">{{ $row['ip_address'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-outline">{{ $row['operated_at'] }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 分頁 --}}
                    @if (isset($pagination) && $pagination->hasPages())
                        <div class="px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-outline-variant/20 bg-surface-container-low/50">
                            {{ $pagination->appends($filters)->links('pagination::bootstrap-4') }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(function () {
            $('#logTable').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
@stop
