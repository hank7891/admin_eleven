@extends('layouts.admin')

@section('title-suffix', ' · 國別管理')

@section('content')
    <x-admin.page-head
        title="國別管理"
        subtitle="維護國家名稱、縮寫與國家代碼資料"
        :breadcrumbs="[['label' => '首頁', 'url' => 'admin/'], ['label' => '國別管理']]"
    >
        <x-slot:actions>
            <x-admin.button as="a" :href="url('admin/country/edit/0')" iconLeft="add">新增國別</x-admin.button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.filter-card :action="url('admin/country/list')" title="篩選">
        <x-admin.input
            name="name"
            label="國名"
            :value="$filters['name'] ?? ''"
            placeholder="請輸入國名"
            icon="public"
        />
        <x-admin.input
            name="country_code"
            label="國家代碼"
            :value="$filters['country_code'] ?? ''"
            placeholder="如 TW、US"
            icon="tag"
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
            <x-admin.empty-state icon="travel_explore" title="尚無國別資料" description="請新增國別或調整篩選條件。" />
        </x-admin.card>
    @else
        <div class="admin-card admin-card-flush">
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>操作</th>
                            <th>ID</th>
                            <th>國名</th>
                            <th>縮寫</th>
                            <th>國家代碼</th>
                            <th>狀態</th>
                            <th>建立時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $row)
                            <tr>
                                <td class="admin-text-mute">{{ $pagination->firstItem() + $key }}</td>
                                <td>
                                    <a href="{{ asset('admin/country/edit/' . $row['id']) }}" class="admin-link-action">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                        <span>編輯</span>
                                    </a>
                                </td>
                                <td>{{ $row['id'] }}</td>
                                <td class="admin-strong">{{ $row['name'] }}</td>
                                <td>{{ $row['abbreviation'] ?: '--' }}</td>
                                <td class="admin-text-mono admin-text-primary">{{ $row['country_code'] }}</td>
                                <td>
                                    @if (($row['is_active'] ?? STATUS_ACTIVE) == STATUS_ACTIVE)
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
