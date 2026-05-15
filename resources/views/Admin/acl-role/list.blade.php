@extends('layouts.admin')

@section('title-suffix', ' · 角色管理')

@section('content')
    <x-admin.page-head
        title="角色管理"
        subtitle="管理系統角色與權限分配"
        :breadcrumbs="[['label' => '首頁', 'url' => 'admin/'], ['label' => '角色管理']]"
    >
        <x-slot:actions>
            <x-admin.button as="a" :href="url('admin/acl.role/edit/0')" iconLeft="add">新增角色</x-admin.button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.filter-card :action="url('admin/acl.role/list')" title="篩選">
        <x-admin.input
            name="role_name"
            label="角色名稱"
            :value="$filters['role_name'] ?? ''"
            placeholder="請輸入角色名稱"
            icon="search"
        />
    </x-admin.filter-card>

    @if (empty($data))
        <x-admin.card>
            <x-admin.empty-state icon="shield_person" title="尚無角色資料" description="請新增角色或調整篩選條件。" />
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
                            <th>角色名稱</th>
                            <th>建立時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $row)
                            <tr>
                                <td class="admin-text-mute">{{ $pagination->firstItem() + $key }}</td>
                                <td>
                                    <a href="{{ asset('admin/acl.role/edit/' . $row['id']) }}" class="admin-link-action">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                        <span>編輯</span>
                                    </a>
                                </td>
                                <td>{{ $row['id'] }}</td>
                                <td>
                                    <x-admin.badge tone="info">{{ $row['role_name'] }}</x-admin.badge>
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
