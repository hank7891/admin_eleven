@extends('layouts.admin')

@section('title-suffix', ' · 會員操作日誌詳情')

@section('content')
    <x-admin.page-head
        title="會員操作日誌詳情"
        subtitle="查看會員操作紀錄的完整細節"
        :breadcrumbs="[
            ['label' => '首頁', 'url' => 'admin/'],
            ['label' => '會員操作日誌', 'url' => 'admin/member.operation-log/list'],
            ['label' => '詳情'],
        ]"
    >
        <x-slot:actions>
            <a href="{{ asset('admin/member.operation-log/list') }}" class="admin-btn admin-btn-outline">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                <span>返回列表</span>
            </a>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.card title="基本資訊">
        <dl class="admin-meta-list admin-meta-list-2col">
            <dt>日誌 ID</dt>
            <dd class="admin-text-mono admin-text-primary">{{ $data['id'] }}</dd>

            <dt>操作者名稱</dt>
            <dd>{{ $data['operator_name'] }}</dd>

            @if (!empty($data['member_id']))
                <dt>會員 ID</dt>
                <dd class="admin-text-mono">{{ $data['member_id'] }}</dd>
            @endif

            <dt>操作者 IP</dt>
            <dd class="admin-text-mono">{{ $data['ip_address'] }}</dd>

            <dt>操作模組</dt>
            <dd><x-admin.badge tone="info">{{ $data['module_display'] }}</x-admin.badge></dd>

            <dt>操作類型</dt>
            <dd>
                @php
                    $actionTone = match ($data['action'] ?? '') {
                        'create' => 'success',
                        'update' => 'info',
                        'delete' => 'danger',
                        default => 'neutral',
                    };
                @endphp
                <x-admin.badge tone="{{ $actionTone }}">{{ $data['action_display'] }}</x-admin.badge>
            </dd>

            <dt>被操作資源</dt>
            <dd>
                @if (!empty($data['target_id']))
                    ID: <span class="admin-strong admin-text-primary">{{ $data['target_id'] }}</span>
                    @if (!empty($data['target_name']))
                        — {{ $data['target_name'] }}
                    @endif
                @else
                    --
                @endif
            </dd>

            <dt>操作時間</dt>
            <dd class="admin-text-mono">{{ $data['operated_at'] }}</dd>

            <dt>記錄建立時間</dt>
            <dd class="admin-text-mono">{{ $data['created_at'] }}</dd>

            @if (!empty($data['remarks']))
                <dt>備註</dt>
                <dd>{{ $data['remarks'] }}</dd>
            @endif
        </dl>
    </x-admin.card>

    <x-admin.card title="修改詳情">
        @if (is_array($data['changes'] ?? null) && count($data['changes']) > 0)
            <div class="admin-table-wrap">
                <table class="admin-table admin-table-diff">
                    <thead>
                        <tr>
                            <th class="admin-diff-field">欄位名稱</th>
                            <th class="admin-diff-old">修改前</th>
                            <th class="admin-diff-new">修改後</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['changes'] as $field => $change)
                            <tr>
                                <td class="admin-strong">{{ $field }}</td>
                                <td>
                                    <span class="admin-diff-pill admin-diff-pill-old">
                                        @if (is_array($change) && array_key_exists('old', $change))
                                            {{ is_array($change['old']) ? json_encode($change['old'], JSON_UNESCAPED_UNICODE) : (is_null($change['old']) ? '--' : $change['old']) }}
                                        @else
                                            {{ is_array($change) ? json_encode($change, JSON_UNESCAPED_UNICODE) : ($change ?? '--') }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="admin-diff-pill admin-diff-pill-new">
                                        @if (is_array($change) && array_key_exists('new', $change))
                                            {{ is_array($change['new']) ? json_encode($change['new'], JSON_UNESCAPED_UNICODE) : (is_null($change['new']) ? '--' : $change['new']) }}
                                        @else
                                            {{ is_array($change) ? json_encode($change, JSON_UNESCAPED_UNICODE) : ($change ?? '--') }}
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="admin-help">
                <span class="material-symbols-outlined" aria-hidden="true">info</span>
                <span>無修改詳情</span>
            </p>
        @endif
    </x-admin.card>
@endsection
