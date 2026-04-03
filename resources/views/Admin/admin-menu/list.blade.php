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
                        <span class="text-primary">選單管理</span>
                    </nav>
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">選單管理</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">管理後台側邊欄選單結構與排序</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ asset('admin/admin.menu/edit/0') }}" class="bg-gradient-to-r from-[#667eea] to-[#764ba2] text-white px-6 py-2.5 rounded-xl flex items-center gap-2 shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all duration-200 no-underline">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span class="text-[0.875rem] font-semibold">新增選單</span>
                    </a>
                </div>
            </div>

            {{-- 資料表格 --}}
            @if (empty($data))
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-12 text-center">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant/40 mb-3 block">menu</span>
                    <p class="text-outline text-[0.875rem]">尚無選單資料</p>
                </div>
            @else
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse" id="dataTable">
                            <thead>
                            <tr class="bg-surface-container-low">
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">#</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">操作</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">ID</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">類型</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">所屬群組</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">名稱</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">URL</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">排序</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">狀態</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">建立時間</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/10">
                            @foreach ($data as $key => $row)
                                <tr class="hover:bg-surface-container-low transition-colors duration-200">
                                    <td class="px-6 py-5 text-[0.875rem] font-medium text-outline">{{ $key + 1 }}</td>
                                    <td class="px-6 py-5">
                                        <a href="{{ asset('admin/admin.menu/edit/' . $row['id']) }}" class="flex items-center gap-1.5 text-primary hover:bg-primary/5 px-3 py-1.5 rounded-lg transition-colors font-semibold text-[0.875rem] no-underline">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                            編輯
                                        </a>
                                    </td>
                                    <td class="px-6 py-5 text-[0.875rem] font-mono font-medium text-on-surface-variant">{{ $row['id'] }}</td>
                                    <td class="px-6 py-5">
                                        @if (($row['parent_id'] ?? 0) == 0)
                                            <span class="px-3 py-1 text-[0.75rem] font-bold rounded-full bg-blue-50 text-blue-600">群組</span>
                                        @else
                                            <span class="px-3 py-1 text-[0.75rem] font-bold rounded-full bg-purple-50 text-purple-600">選單項目</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-[0.875rem] text-on-surface-variant">{{ $row['parent_display'] ?? '-' }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] font-medium text-on-surface">{{ $row['name'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] font-mono text-outline">{{ $row['url'] ?? '' }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-on-surface">{{ $row['sort_order'] ?? 0 }}</td>
                                    <td class="px-6 py-5">
                                        @if (($row['is_active'] ?? 1) == 1)
                                            <span class="px-3 py-1 text-[0.75rem] font-bold rounded-full bg-emerald-50 text-emerald-600">啟用</span>
                                        @else
                                            <span class="px-3 py-1 text-[0.75rem] font-bold rounded-full bg-slate-100 text-slate-500">停用</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-[0.875rem] text-outline">{{ $row['created_at'] ?? '' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        $(function () {
            $('#dataTable').DataTable({
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
