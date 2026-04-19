@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '會員管理']]" />
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">會員管理</h2>
                </div>
                <a href="{{ asset('admin/member/edit/0') }}" class="btn-primary px-6 py-2.5 rounded-xl flex items-center gap-2 no-underline">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    <span class="text-[0.875rem] font-semibold">新增會員</span>
                </a>
            </div>

            <form method="GET" action="{{ url('admin/member/list') }}">
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-6">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6 items-end">
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline">姓名 / Email</label>
                            <input name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem]" placeholder="請輸入關鍵字" type="text" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline">狀態</label>
                            <select name="status_key" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem]">
                                <option value="">全部狀態</option>
                                @foreach ($statusOptions as $key => $label)
                                    <option value="{{ $key }}" {{ ($filters['status_key'] ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline">註冊起日</label>
                            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem]" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline">註冊迄日</label>
                            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem]" />
                        </div>
                        <div class="md:col-span-5 flex justify-end gap-3">
                            <button type="submit" class="btn-primary py-3 px-6 rounded-xl font-semibold text-[0.875rem]">搜尋</button>
                            <a href="{{ url('admin/member/list') }}" class="px-5 bg-surface-container-high text-on-surface py-3 rounded-xl font-semibold text-[0.875rem] no-underline">清除</a>
                        </div>
                    </div>
                </div>
            </form>

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
                            <tr class="table-header-row">
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">#</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">操作</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">ID</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">Email</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">姓名</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">電話</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">性別</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">狀態</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">最後登入</th>
                                <th class="px-6 py-4 text-left text-[0.8125rem] font-bold uppercase tracking-wider text-outline">註冊時間</th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/10">
                            @foreach ($data as $key => $row)
                                <tr class="hover:bg-surface-container-low transition-colors duration-200">
                                    <td class="px-6 py-5 text-[0.875rem] font-medium text-outline">{{ $pagination->firstItem() + $key }}</td>
                                    <td class="px-6 py-5">
                                        <a href="{{ asset('admin/member/edit/' . $row['id']) }}" class="flex items-center gap-1.5 text-primary hover:bg-primary/5 px-3 py-1.5 rounded-lg transition-colors font-semibold text-[0.875rem] no-underline">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                            編輯
                                        </a>
                                    </td>
                                    <td class="px-6 py-5 text-[0.875rem] text-on-surface">{{ $row['id'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-on-surface">{{ $row['email'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] font-bold text-on-surface">{{ $row['name'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-on-surface">{{ $row['phone'] ?? '' }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-outline-variant">{{ $row['gender_display'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-on-surface">{{ $row['status_display'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-outline">{{ $row['last_login_at'] }}</td>
                                    <td class="px-6 py-5 text-[0.875rem] text-outline">{{ $row['created_at_display'] }}</td>
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
                paging: false,
                lengthChange: false,
                searching: false,
                ordering: true,
                info: false,
                autoWidth: false,
                responsive: true,
            });
        });
    </script>
    @endpush
@stop

