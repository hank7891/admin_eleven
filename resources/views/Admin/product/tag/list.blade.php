@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            <div class="flex items-end justify-between">
                <div>
                    <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '商品標籤管理']]" />
                    <h2 class="text-[1.5rem] font-bold font-headline">商品標籤管理</h2>
                </div>
                <a href="{{ asset('admin/product.tag/edit/0') }}" class="btn-primary px-6 py-2.5 rounded-xl no-underline">新增標籤</a>
            </div>

            <form method="GET" action="{{ url('admin/product.tag/list') }}" class="bg-surface-container-lowest rounded-xl p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="搜尋標籤名稱" class="bg-surface-container-low rounded-lg border-none px-4 py-3">
                    <select name="is_active" class="bg-surface-container-low rounded-lg border-none px-4 py-3">
                        <option value="">全部狀態</option>
                        @foreach ($statusOptions as $key => $label)
                            <option value="{{ $key }}" {{ (string) ($filters['is_active'] ?? '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <div class="flex gap-3">
                        <button class="btn-primary px-6 py-3 rounded-xl" type="submit">搜尋</button>
                        <a href="{{ url('admin/product.tag/list') }}" class="bg-surface-container-high px-5 py-3 rounded-xl text-on-surface no-underline">清除</a>
                    </div>
                </div>
            </form>

            <div class="bg-surface-container-lowest rounded-xl overflow-hidden">
                <table class="w-full border-collapse">
                    <thead>
                    <tr class="table-header-row">
                        <th class="px-6 py-4 text-left">名稱</th>
                        <th class="px-6 py-4 text-left">狀態</th>
                        <th class="px-6 py-4 text-left">操作</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                    @forelse ($data as $row)
                        <tr>
                            <td class="px-6 py-4">{{ $row['name'] }}</td>
                            <td class="px-6 py-4">{{ $row['is_active_display'] }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ asset('admin/product.tag/edit/' . $row['id']) }}" class="text-primary no-underline">編輯</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="px-6 py-8 text-outline" colspan="3">尚無標籤資料</td></tr>
                    @endforelse
                    </tbody>
                </table>

                @if (!empty($data))
                    <x-stitch-pagination :paginator="$pagination" :filters="$filters" />
                @endif
            </div>
        </div>
    </div>
@endsection

