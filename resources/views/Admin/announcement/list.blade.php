@extends('layouts.admin')

@section('title-suffix', ' · 公告管理')

@section('content')
    <x-admin.page-head
        title="公告管理"
        subtitle="維護全系統公告與一般公告的顯示時段與狀態"
        :breadcrumbs="[['label' => '首頁', 'url' => 'admin/'], ['label' => '公告管理']]"
    >
        <x-slot:actions>
            <x-admin.button as="a" :href="url('admin/announcement/edit/0')" iconLeft="add">新增公告</x-admin.button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.filter-card :action="url('admin/announcement/list')" title="篩選">
        <x-admin.input
            name="keyword"
            label="關鍵字"
            :value="$filters['keyword'] ?? ''"
            placeholder="搜尋標題 / 大綱 / 內文"
            icon="search"
        />
        <x-admin.select
            name="type"
            label="類型"
            :options="$typeOptions ?? []"
            :value="$filters['type'] ?? ''"
            placeholder="全部類型"
        />
        <x-admin.select
            name="is_active"
            label="狀態"
            :options="$statusOptions ?? []"
            :value="$filters['is_active'] ?? ''"
            placeholder="全部狀態"
        />
        <x-admin.input
            name="start_from"
            type="date"
            label="開始日期（起）"
            :value="$filters['start_from'] ?? ''"
        />
        <x-admin.input
            name="start_to"
            type="date"
            label="開始日期（迄）"
            :value="$filters['start_to'] ?? ''"
        />
    </x-admin.filter-card>

    @if (empty($data))
        <x-admin.card>
            <x-admin.empty-state icon="campaign" title="尚無公告資料" description="請新增公告或調整篩選條件。" />
        </x-admin.card>
    @else
        <div class="admin-card admin-card-flush">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>操作</th>
                            <th>類型</th>
                            <th>標題</th>
                            <th>狀態</th>
                            <th>開始時間</th>
                            <th>結束時間</th>
                            <th>建立者</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $row)
                            <tr>
                                <td class="admin-text-mute">{{ $pagination->firstItem() + $key }}</td>
                                <td>
                                    <a href="{{ asset('admin/announcement/edit/' . $row['id']) }}" class="admin-link-action">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                        <span>編輯</span>
                                    </a>
                                </td>
                                <td>
                                    <x-admin.badge tone="info">{{ $row['type_display'] }}</x-admin.badge>
                                </td>
                                <td>
                                    <div class="admin-strong">{{ $row['title'] }}</div>
                                    @if (!empty($row['summary']))
                                        <div class="admin-mute">{{ $row['summary'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if (($row['is_active'] ?? STATUS_ACTIVE) == STATUS_ACTIVE)
                                        <x-admin.badge tone="success">{{ $row['is_active_display'] }}</x-admin.badge>
                                    @else
                                        <x-admin.badge tone="neutral">{{ $row['is_active_display'] }}</x-admin.badge>
                                    @endif
                                </td>
                                <td class="admin-text-mute">{{ $row['start_at_display'] }}</td>
                                <td class="admin-text-mute">{{ $row['end_at_display'] }}</td>
                                <td>{{ $row['creator_name'] ?: '--' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-stitch-pagination :paginator="$pagination" :filters="$filters" />
        </div>
    @endif
@endsection
