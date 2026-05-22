@extends('layouts.admin')

@section('title-suffix', ' · 商品類別管理')

@section('content')
    <x-admin.page-head
        title="商品類別管理"
        subtitle="管理商品分類項目"
        :breadcrumbs="[['label' => '首頁', 'url' => 'admin/'], ['label' => '商品類別管理']]"
    >
        <x-slot:actions>
            <x-admin.button as="a" :href="url('admin/product.category/edit/0')" iconLeft="add">新增類別</x-admin.button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.filter-card :action="url('admin/product.category/list')" title="篩選">
        <x-admin.input
            name="keyword"
            label="關鍵字"
            :value="$filters['keyword'] ?? ''"
            placeholder="搜尋類別名稱"
            icon="search"
        />
        <x-admin.select
            name="is_active"
            label="狀態"
            :options="$statusOptions ?? []"
            :value="$filters['is_active'] ?? ''"
            placeholder="全部狀態"
        />
    </x-admin.filter-card>

    @if (empty($data))
        <x-admin.card>
            <x-admin.empty-state icon="category" title="尚無類別資料" description="請新增類別或調整篩選條件。" />
        </x-admin.card>
    @else
        <div class="admin-card admin-card-flush">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th data-sortable="name">名稱</th>
                            <th data-sortable="sort_order" data-sort-type="number">排序</th>
                            <th data-sortable="product_count" data-sort-type="number">商品數</th>
                            <th data-sortable="status">狀態</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                            <tr>
                                <td class="admin-strong">{{ $row['name'] }}</td>
                                <td>{{ $row['sort_order'] }}</td>
                                <td>{{ $row['products_count'] ?? 0 }}</td>
                                <td>
                                    @if ((int) ($row['is_active'] ?? STATUS_INACTIVE) === STATUS_ACTIVE)
                                        <x-admin.badge tone="success">{{ $row['is_active_display'] }}</x-admin.badge>
                                    @else
                                        <x-admin.badge tone="neutral">{{ $row['is_active_display'] }}</x-admin.badge>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ asset('admin/product.category/edit/' . $row['id']) }}" class="admin-link-action">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                        <span>編輯</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-stitch-pagination :paginator="$pagination" :filters="$filters" />
        </div>
    @endif
@endsection
