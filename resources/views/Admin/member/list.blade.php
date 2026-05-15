@extends('layouts.admin')

@section('title-suffix', ' · 會員管理')

@section('content')
    <x-admin.page-head
        title="會員管理"
        subtitle="管理前台會員帳號、狀態與重設密碼"
        :breadcrumbs="[['label' => '首頁', 'url' => 'admin/'], ['label' => '會員管理']]"
    >
        <x-slot:actions>
            <x-admin.button as="a" :href="url('admin/member/edit/0')" iconLeft="add">新增會員</x-admin.button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.filter-card :action="url('admin/member/list')" title="篩選">
        <x-admin.input
            name="keyword"
            label="姓名 / Email"
            :value="$filters['keyword'] ?? ''"
            placeholder="請輸入關鍵字"
            icon="search"
        />
        <x-admin.select
            name="status_key"
            label="狀態"
            :options="$statusOptions ?? []"
            :value="$filters['status_key'] ?? ''"
            placeholder="全部狀態"
        />
        <x-admin.input
            name="date_from"
            type="date"
            label="註冊起日"
            :value="$filters['date_from'] ?? ''"
        />
        <x-admin.input
            name="date_to"
            type="date"
            label="註冊迄日"
            :value="$filters['date_to'] ?? ''"
        />
    </x-admin.filter-card>

    @if (empty($data))
        <x-admin.card>
            <x-admin.empty-state icon="group" title="尚無會員資料" description="請調整篩選條件或新增會員。" />
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
                            <th>Email</th>
                            <th>姓名</th>
                            <th>電話</th>
                            <th>性別</th>
                            <th>狀態</th>
                            <th>最後登入</th>
                            <th>註冊時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $row)
                            <tr>
                                <td class="admin-text-mute">{{ $pagination->firstItem() + $key }}</td>
                                <td>
                                    <a href="{{ asset('admin/member/edit/' . $row['id']) }}" class="admin-link-action">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                        <span>編輯</span>
                                    </a>
                                </td>
                                <td>{{ $row['id'] }}</td>
                                <td>{{ $row['email'] }}</td>
                                <td class="admin-strong">{{ $row['name'] }}</td>
                                <td>{{ $row['phone'] ?? '' }}</td>
                                <td class="admin-text-mute">{{ $row['gender_display'] }}</td>
                                <td>
                                    <x-admin.badge tone="{{ $row['status_tone'] ?? 'neutral' }}">{{ $row['status_display'] }}</x-admin.badge>
                                </td>
                                <td class="admin-text-mute">{{ $row['last_login_at'] }}</td>
                                <td class="admin-text-mute">{{ $row['created_at_display'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-stitch-pagination :paginator="$pagination" :filters="$filters" />
        </div>
    @endif
@endsection
