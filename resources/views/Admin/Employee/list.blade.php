@extends('layouts.admin')

@section('title-suffix', ' · 帳號管理')

@section('content')
    <x-admin.page-head
        title="帳號管理"
        subtitle="管理後台使用者帳號、角色與狀態"
        :breadcrumbs="[['label' => '首頁', 'url' => 'admin/'], ['label' => '帳號管理']]"
    >
        <x-slot:actions>
            <x-admin.button as="a" :href="url('admin/employee/edit/0')" iconLeft="add">新增帳號</x-admin.button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.filter-card :action="url('admin/employee/list')" title="篩選">
        <x-admin.input
            name="account"
            label="帳號"
            :value="$filters['account'] ?? ''"
            placeholder="請輸入帳號"
            icon="person_search"
        />
        <x-admin.input
            name="name"
            label="姓名"
            :value="$filters['name'] ?? ''"
            placeholder="請輸入姓名"
            icon="badge"
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
            <x-admin.empty-state icon="group" title="尚無帳號資料" description="請新增帳號或調整篩選條件。" />
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
                            <th>姓名</th>
                            <th>角色</th>
                            <th>性別</th>
                            <th>電話</th>
                            <th>狀態</th>
                            <th>建立時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $row)
                            <tr>
                                <td class="admin-text-mute">{{ $pagination->firstItem() + $key }}</td>
                                <td>
                                    <a href="{{ asset('admin/employee/edit/' . $row['id']) }}" class="admin-link-action">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                        <span>編輯</span>
                                    </a>
                                </td>
                                <td>{{ $row['id'] }}</td>
                                <td class="admin-strong">{{ $row['name'] }}</td>
                                <td>{{ $row['role_names'] ?? '' }}</td>
                                <td class="admin-text-mute">{{ $row['gender_display'] ?? '' }}</td>
                                <td>{{ $row['phone'] ?? '' }}</td>
                                <td>
                                    @if (($row['is_active'] ?? 1) == STATUS_ACTIVE)
                                        <x-admin.badge tone="success">{{ $row['is_active_display'] ?? '啟用' }}</x-admin.badge>
                                    @else
                                        <x-admin.badge tone="neutral">{{ $row['is_active_display'] ?? '停用' }}</x-admin.badge>
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
