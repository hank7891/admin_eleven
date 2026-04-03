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
                        <span class="text-primary">會員管理</span>
                    </nav>
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">會員管理</h2>
                </div>
                <div class="flex gap-3">
                    <a href="{{ asset('admin/employee/edit/0') }}" class="bg-gradient-to-r from-[#667eea] to-[#764ba2] text-white px-6 py-2.5 rounded-xl flex items-center gap-2 shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all duration-200 no-underline">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span class="text-[0.875rem] font-semibold">新增會員</span>
                    </a>
                </div>
            </div>

            {{-- 資料表格 --}}
            @if (empty($data))
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-12 text-center">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant/40 mb-3 block">group</span>
                    <p class="text-outline text-[0.875rem]">尚無會員資料</p>
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
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">姓名</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">角色</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">性別</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">電話</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">狀態</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">建立時間</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/10">
                            @foreach ($data as $key => $row)
                                <tr class="hover:bg-surface-container-low transition-colors duration-200">
                                    <td class="px-6 py-5 text-[0.875rem] font-medium text-outline">{{ $key + 1 }}</td>
                                    <td class="px-6 py-5">
                                        <a href="{{ asset('admin/employee/edit/' . $row['id']) }}" class="flex items-center gap-1.5 text-primary hover:bg-primary/5 px-3 py-1.5 rounded-lg transition-colors font-semibold text-[0.875rem] no-underline">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                            編輯
                                        </a>
                                    </td>
                                    <td class="px-6 py-5 text-[0.875rem] font-medium text-on-surface">{{ $row['id'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] font-bold text-on-surface">{{ $row['name'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-on-surface">{{ $row['role_names'] ?? '' }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-outline-variant">{{ $row['gender_display'] ?? '' }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-on-surface">{{ $row['phone'] ?? '' }}</td>
                                    <td class="px-6 py-5">
                                        @if (($row['is_active'] ?? 1) == 1)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.75rem] font-bold bg-emerald-50 text-emerald-600">
                                                {{ $row['is_active_display'] ?? '啟用' }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.75rem] font-bold bg-slate-100 text-slate-500">
                                                {{ $row['is_active_display'] ?? '停用' }}
                                            </span>
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
