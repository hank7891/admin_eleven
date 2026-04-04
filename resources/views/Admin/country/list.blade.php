@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            {{-- 頁面標題區 --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '國別管理']]" />
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">國別管理</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">維護國家名稱、縮寫與國家代碼資料</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ asset('admin/country/edit/0') }}" class="bg-gradient-to-r from-[#667eea] to-[#764ba2] text-white px-6 py-2.5 rounded-xl flex items-center gap-2 shadow-lg shadow-indigo-500/20 hover:scale-105 active:scale-95 transition-all duration-200 no-underline">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span class="text-[0.875rem] font-semibold">新增國別</span>
                    </a>
                </div>
            </div>

            {{-- 篩選區 --}}
            <form method="GET" action="{{ url('admin/country/list') }}">
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                        <div class="space-y-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px]">public</span>
                                國名
                            </label>
                            <input name="name" value="{{ $filters['name'] ?? '' }}" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem] focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/30 transition-all placeholder:text-outline-variant" placeholder="請輸入國名" type="text" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px]">tag</span>
                                國家代碼
                            </label>
                            <input name="country_code" value="{{ $filters['country_code'] ?? '' }}" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem] uppercase focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/30 transition-all placeholder:text-outline-variant" placeholder="如 TW、US" type="text" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px]">settings_accessibility</span>
                                狀態
                            </label>
                            <select name="is_active" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem] focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/30 transition-all appearance-none cursor-pointer">
                                <option value="">全部狀態</option>
                                @foreach ($statusOptions as $key => $label)
                                    <option value="{{ $key }}" {{ (isset($filters['is_active']) && $filters['is_active'] !== '' && (int) $filters['is_active'] === $key) ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 bg-gradient-to-r from-[#667eea] to-[#764ba2] text-white py-3 rounded-xl font-semibold text-[0.875rem] shadow-lg shadow-indigo-500/10 hover:brightness-110 active:scale-95 transition-all">搜尋</button>
                            <a href="{{ url('admin/country/list') }}" class="px-5 bg-surface-container-high text-on-surface py-3 rounded-xl font-semibold text-[0.875rem] hover:bg-surface-container-highest transition-colors active:scale-95 no-underline flex items-center">清除</a>
                        </div>
                    </div>
                </div>
            </form>

            {{-- 資料表格 --}}
            @if (empty($data))
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-12 text-center">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant/40 mb-3 block">travel_explore</span>
                    <p class="text-outline text-[0.875rem]">尚無國別資料</p>
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
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">國名</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">縮寫</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">國家代碼</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">狀態</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">建立時間</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/10">
                            @foreach ($data as $key => $row)
                                <tr class="hover:bg-surface-container-low transition-colors duration-200">
                                    <td class="px-6 py-5 text-[0.875rem] font-medium text-outline">{{ $pagination->firstItem() + $key }}</td>
                                    <td class="px-6 py-5">
                                        <a href="{{ asset('admin/country/edit/' . $row['id']) }}" class="flex items-center gap-1.5 text-primary hover:bg-primary/5 px-3 py-1.5 rounded-lg transition-colors font-semibold text-[0.875rem] no-underline">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                            編輯
                                        </a>
                                    </td>
                                    <td class="px-6 py-5 text-[0.875rem] font-medium text-on-surface">{{ $row['id'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] font-bold text-on-surface">{{ $row['name'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-on-surface">{{ $row['abbreviation'] ?: '--' }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] font-mono text-primary">{{ $row['country_code'] }}</td>
                                    <td class="px-6 py-5">
                                        @if (($row['is_active'] ?? STATUS_ACTIVE) == STATUS_ACTIVE)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.75rem] font-bold bg-emerald-50 text-emerald-600">{{ $row['is_active_display'] }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.75rem] font-bold bg-slate-100 text-slate-500">{{ $row['is_active_display'] }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5 text-[0.875rem] text-outline">{{ $row['created_at'] ?? '' }}</td>
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

