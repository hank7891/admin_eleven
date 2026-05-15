@extends('layouts.admin')

@section('title-suffix', ' · 會員登入日誌')

@section('content')
    <x-admin.page-head
        title="會員登入日誌"
        subtitle="追蹤前台會員登入、登出與註冊紀錄"
        :breadcrumbs="[['label' => '首頁', 'url' => 'admin/'], ['label' => '會員登入日誌']]"
    />

    <x-admin.filter-card :action="asset('admin/member.login-log/list')" title="搜尋條件">
        <x-admin.input
            name="member_keyword"
            label="會員帳號 / 姓名"
            :value="$filters['member_keyword'] ?? ''"
            placeholder="模糊搜尋..."
            icon="person_search"
        />
        <x-admin.input
            name="ip_address"
            label="IP 位址"
            :value="$filters['ip_address'] ?? ''"
            placeholder="192.168.x.x"
            icon="lan"
        />
        <x-admin.select
            name="action"
            label="操作"
            :options="$actionOptions ?? []"
            :value="$filters['action'] ?? ''"
            placeholder="全部"
        />
        <x-admin.select
            name="status"
            label="狀態"
            :options="$statusOptions ?? []"
            :value="$filters['status'] ?? ''"
            placeholder="全部"
        />
        <x-admin.input
            name="date_from"
            type="date"
            label="開始時間"
            :value="$filters['date_from'] ?? ''"
        />
        <x-admin.input
            name="date_to"
            type="date"
            label="結束時間"
            :value="$filters['date_to'] ?? ''"
        />

        <x-slot:actions>
            <a href="{{ asset('admin/member.login-log/list') }}" class="admin-btn admin-btn-outline">清除條件</a>
            <button type="submit" class="admin-btn admin-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">search</span>
                <span>搜尋日誌</span>
            </button>
        </x-slot:actions>
    </x-admin.filter-card>

    @if (!($hasFilter ?? false))
        <x-admin.card>
            <x-admin.empty-state icon="search" title="請輸入搜尋條件後查詢" description="請至少輸入一個篩選欄位以開始檢索。" />
        </x-admin.card>
    @elseif (empty($data))
        <x-admin.card>
            <x-admin.empty-state icon="inbox" title="查無符合條件的資料" description="請調整篩選條件後再次查詢。" />
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
                            <th>帳號</th>
                            <th>姓名</th>
                            <th>操作類型</th>
                            <th>狀態</th>
                            <th>IP 位址</th>
                            <th>操作時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $row)
                            <tr>
                                <td class="admin-text-mute">{{ $pagination->firstItem() + $key }}</td>
                                <td>
                                    <a href="{{ asset('admin/member.login-log/detail/' . $row['id']) }}" class="admin-link-action">
                                        <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                                        <span>詳情</span>
                                    </a>
                                </td>
                                <td class="admin-text-mono">{{ $row['id'] }}</td>
                                <td class="admin-strong">{{ $row['account'] }}</td>
                                <td>{{ $row['member_name'] }}</td>
                                <td class="admin-text-mute">{{ $row['action_display'] }}</td>
                                <td>
                                    @if ($row['status'] == 1)
                                        <x-admin.badge tone="success">{{ $row['status_display'] }}</x-admin.badge>
                                    @else
                                        <x-admin.badge tone="danger">{{ $row['status_display'] }}</x-admin.badge>
                                    @endif
                                </td>
                                <td class="admin-text-mono admin-text-mute">{{ $row['ip_address'] }}</td>
                                <td class="admin-text-mute">{{ $row['operated_at'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if (isset($pagination))
                <x-stitch-pagination :paginator="$pagination" :filters="$filters" />
            @endif
        </div>
    @endif
@endsection
