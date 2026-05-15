@extends('layouts.admin')

@section('title-suffix', ' · 登入日誌詳情')

@section('content')
    <x-admin.page-head
        title="登入日誌詳情"
        subtitle="查看登入活動的完整記錄"
        :breadcrumbs="[
            ['label' => '首頁', 'url' => 'admin/'],
            ['label' => '登入日誌', 'url' => 'admin/admin.login-log/list'],
            ['label' => '詳情'],
        ]"
    >
        <x-slot:actions>
            <a href="{{ asset('admin/admin.login-log/list') }}" class="admin-btn admin-btn-outline">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                <span>返回列表</span>
            </a>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.card title="基本資訊">
        <dl class="admin-meta-list admin-meta-list-2col">
            <dt>日誌 ID</dt>
            <dd class="admin-text-mono admin-text-primary">{{ $data['id'] }}</dd>

            <dt>登入帳號</dt>
            <dd class="admin-strong">{{ $data['account'] }}</dd>

            <dt>帳號姓名</dt>
            <dd>{{ $data['employee_name'] }}</dd>

            @if (!empty($data['employee_id']))
                <dt>帳號 ID</dt>
                <dd class="admin-text-mono">{{ $data['employee_id'] }}</dd>
            @endif

            <dt>操作類型</dt>
            <dd>
                @if ($data['action'] === 'login')
                    <x-admin.badge tone="info">{{ $data['action_display'] }}</x-admin.badge>
                @else
                    <x-admin.badge tone="neutral">{{ $data['action_display'] }}</x-admin.badge>
                @endif
            </dd>

            <dt>狀態</dt>
            <dd>
                @if ($data['status'] == 1)
                    <x-admin.badge tone="success">{{ $data['status_display'] }}</x-admin.badge>
                @else
                    <x-admin.badge tone="danger">{{ $data['status_display'] }}</x-admin.badge>
                @endif
            </dd>

            @if (!empty($data['fail_reason']))
                <dt>失敗原因</dt>
                <dd><span class="admin-diff-pill admin-diff-pill-old">{{ $data['fail_reason'] }}</span></dd>
            @endif

            <dt>IP 位址</dt>
            <dd class="admin-text-mono">{{ $data['ip_address'] }}</dd>

            <dt>操作時間</dt>
            <dd class="admin-text-mono">{{ $data['operated_at'] }}</dd>

            <dt>記錄建立時間</dt>
            <dd class="admin-text-mono">{{ $data['created_at'] }}</dd>
        </dl>
    </x-admin.card>
@endsection
