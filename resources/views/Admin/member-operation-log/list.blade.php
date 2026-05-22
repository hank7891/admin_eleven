@extends('layouts.admin')

@section('title-suffix', ' · 會員操作日誌')

@section('content')
    <x-admin.page-head
        title="會員操作日誌"
        subtitle="追蹤前台會員的個資異動與認證相關操作"
        :breadcrumbs="[['label' => '首頁', 'url' => 'admin/'], ['label' => '會員操作日誌']]"
    />

    <x-admin.filter-card :action="asset('admin/member.operation-log/list')" title="搜尋條件">
        <x-admin.input
            name="member_keyword"
            label="操作者 / 目標名稱"
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
            name="module"
            label="功能模組"
            :options="$moduleOptions ?? []"
            :value="$filters['module'] ?? ''"
            placeholder="全部"
        />
        <x-admin.select
            name="action"
            label="操作類型"
            :options="$actionOptions ?? []"
            :value="$filters['action'] ?? ''"
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
            <a href="{{ asset('admin/member.operation-log/list') }}" class="admin-btn admin-btn-outline">清除條件</a>
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
                            <th data-sortable="row_id" data-sort-type="number">#</th>
                            <th>操作</th>
                            <th data-sortable="id" data-sort-type="number">ID</th>
                            <th data-sortable="operator">操作者</th>
                            <th data-sortable="module">模組</th>
                            <th data-sortable="action">操作類型</th>
                            <th data-sortable="target">目標</th>
                            <th data-sortable="ip">IP 位址</th>
                            <th data-sortable="operated_at">操作時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $row)
                            <tr>
                                <td class="admin-text-mute">{{ $pagination->firstItem() + $key }}</td>
                                <td>
                                    <a href="{{ asset('admin/member.operation-log/detail/' . $row['id']) }}" class="admin-link-action">
                                        <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                                        <span>詳情</span>
                                    </a>
                                </td>
                                <td class="admin-text-mono">{{ $row['id'] }}</td>
                                <td class="admin-strong">{{ $row['operator_name'] }}</td>
                                <td><x-admin.badge tone="info">{{ $row['module_display'] }}</x-admin.badge></td>
                                <td>{{ $row['action_display'] }}</td>
                                <td class="admin-text-mono admin-text-mute">{{ $row['target_name'] }}</td>
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
