@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '公告管理']]" />
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">公告管理</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">維護全系統公告與一般公告的顯示時段與狀態</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ asset('admin/announcement/edit/0') }}" class="btn-primary px-6 py-2.5 rounded-xl flex items-center gap-2 hover:scale-105 active:scale-95 transition-all duration-200 no-underline">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span class="text-[0.875rem] font-semibold">新增公告</span>
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ url('admin/announcement/list') }}">
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-6">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-end">
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px]">search</span>
                                關鍵字
                            </label>
                            <input name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem] focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/30 transition-all placeholder:text-outline-variant" placeholder="搜尋標題 / 大綱 / 內文" type="text" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline">類型</label>
                            <select name="type" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem] focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/30 transition-all appearance-none cursor-pointer">
                                <option value="">全部類型</option>
                                @foreach ($typeOptions as $key => $label)
                                    <option value="{{ $key }}" {{ (string) ($filters['type'] ?? '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline">狀態</label>
                            <select name="is_active" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem] focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/30 transition-all appearance-none cursor-pointer">
                                <option value="">全部狀態</option>
                                @foreach ($statusOptions as $key => $label)
                                    <option value="{{ $key }}" {{ (string) ($filters['is_active'] ?? '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline">開始日期（起）</label>
                            <input type="date" name="start_from" value="{{ $filters['start_from'] ?? '' }}" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem] focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/30 transition-all" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline">開始日期（迄）</label>
                            <input type="date" name="start_to" value="{{ $filters['start_to'] ?? '' }}" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem] focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/30 transition-all" />
                        </div>
                        <div class="md:col-span-5 flex gap-3 justify-end">
                            <button type="submit" class="btn-primary px-8 py-3 rounded-xl font-semibold text-[0.875rem] active:scale-95 transition-all">搜尋</button>
                            <a href="{{ url('admin/announcement/list') }}" class="px-5 bg-surface-container-high text-on-surface py-3 rounded-xl font-semibold text-[0.875rem] hover:bg-surface-container-highest transition-colors active:scale-95 no-underline flex items-center">清除</a>
                        </div>
                    </div>
                </div>
            </form>

            @if (empty($data))
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-12 text-center">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant/40 mb-3 block">campaign</span>
                    <p class="text-outline text-[0.875rem]">尚無公告資料</p>
                </div>
            @else
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse" id="dataTable">
                            <thead>
                            <tr class="table-header-row">
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">#</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">操作</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">類型</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">標題</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">狀態</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">開始時間</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">結束時間</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">建立者</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/10">
                            @foreach ($data as $key => $row)
                                <tr class="hover:bg-surface-container-low transition-colors duration-200">
                                    <td class="px-6 py-5 text-[0.875rem] font-medium text-outline">{{ $pagination->firstItem() + $key }}</td>
                                    <td class="px-6 py-5">
                                        <a href="{{ asset('admin/announcement/edit/' . $row['id']) }}" class="flex items-center gap-1.5 text-primary hover:bg-primary/5 px-3 py-1.5 rounded-lg transition-colors font-semibold text-[0.875rem] no-underline">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                            編輯
                                        </a>
                                    </td>
                                    <td class="px-6 py-5 text-[0.875rem] text-on-surface">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[0.75rem] font-bold {{ $row['type_badge_class'] ?? 'bg-blue-50 text-blue-700' }}">
                                            {{ $row['type_display'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-[0.875rem] font-medium text-on-surface">
                                        <div class="font-bold">{{ $row['title'] }}</div>
                                        @if(!empty($row['summary']))
                                            <div class="mt-1 text-outline text-[0.8125rem]">{{ $row['summary'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-[0.875rem]">
                                        @if (($row['is_active'] ?? STATUS_ACTIVE) == STATUS_ACTIVE)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.75rem] font-bold bg-emerald-50 text-emerald-600">{{ $row['is_active_display'] }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.75rem] font-bold bg-slate-100 text-slate-500">{{ $row['is_active_display'] }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-[0.875rem] text-outline">{{ $row['start_at_display'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-outline">{{ $row['end_at_display'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-on-surface">{{ $row['creator_name'] ?: '--' }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <x-stitch-pagination :paginator="$pagination" :filters="$filters" />
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
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
    @endpush
@stop


