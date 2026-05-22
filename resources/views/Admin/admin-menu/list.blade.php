@extends('layouts.admin')

@section('title-suffix', ' · 選單管理')

@section('content')
    <x-admin.page-head
        title="選單管理"
        subtitle="管理後台側邊欄選單結構與排序"
        :breadcrumbs="[['label' => '首頁', 'url' => 'admin/'], ['label' => '選單管理']]"
    >
        <x-slot:actions>
            <x-admin.button as="a" :href="url('admin/admin.menu/edit/0')" iconLeft="add">新增選單</x-admin.button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.filter-card :action="url('admin/admin.menu/list')" title="篩選">
        <x-admin.input
            name="name"
            label="名稱"
            :value="$filters['name'] ?? ''"
            placeholder="請輸入選單名稱"
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
            <x-admin.empty-state icon="menu" title="尚無選單資料" description="請新增選單或調整篩選條件。" />
        </x-admin.card>
    @else
        <div class="admin-card admin-card-flush">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th data-sortable="row_id" data-sort-type="number">#</th>
                            <th>操作</th>
                            <th data-sortable="id" data-sort-type="number">ID</th>
                            <th data-sortable="type">類型</th>
                            <th data-sortable="group">所屬群組</th>
                            <th data-sortable="name">名稱</th>
                            <th data-sortable="url">URL</th>
                            <th data-sortable="sort_order" data-sort-type="number">排序</th>
                            <th data-sortable="status">狀態</th>
                            <th data-sortable="created_at">建立時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $row)
                            <tr>
                                <td class="admin-text-mute">{{ $pagination->firstItem() + $key }}</td>
                                <td>
                                    <a href="{{ asset('admin/admin.menu/edit/' . $row['id']) }}" class="admin-link-action">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                        <span>編輯</span>
                                    </a>
                                </td>
                                <td class="admin-text-mono">{{ $row['id'] }}</td>
                                <td>
                                    @if (($row['parent_id'] ?? 0) == 0)
                                        <x-admin.badge tone="info">群組</x-admin.badge>
                                    @else
                                        <x-admin.badge tone="secondary">選單項目</x-admin.badge>
                                    @endif
                                </td>
                                <td class="admin-text-mute">{{ $row['parent_display'] ?? '-' }}</td>
                                <td class="admin-strong">{{ $row['name'] }}</td>
                                <td class="admin-text-mono admin-text-mute">{{ $row['url'] ?? '' }}</td>
                                <td>{{ $row['sort_order'] ?? 0 }}</td>
                                <td>
                                    @if (($row['is_active'] ?? 1) == STATUS_ACTIVE)
                                        <x-admin.badge tone="success">{{ $row['is_active_display'] }}</x-admin.badge>
                                    @else
                                        <x-admin.badge tone="neutral">{{ $row['is_active_display'] }}</x-admin.badge>
                                    @endif
                                </td>
                                <td class="admin-text-mute">{{ $row['created_at'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-stitch-pagination :paginator="$pagination" :filters="$filters" />
        </div>
    @endif
@endsection
