@extends('layouts.admin')

@section('title-suffix', ' · 後台首頁')

@section('content')
    <x-admin.page-head
        title="歡迎回來，{{ $user['name'] ?? '管理員' }}"
        subtitle="今日 {{ date('Y/m/d') }} · 系統整體運行良好"
        :breadcrumbs="[['label' => '首頁']]"
    />

    {{-- KPI 指標卡（P3 階段先用 placeholder，P5 補真實 query） --}}
    @php
        $kpi = $kpi ?? [
            'member_new_month' => '—',
            'product_active' => '—',
            'announcement_unread' => '—',
            'admin_login_today' => '—',
        ];
    @endphp
    <section class="admin-kpi-grid">
        <div class="admin-kpi">
            <div class="admin-kpi-label">本月新會員</div>
            <div class="admin-kpi-value">{{ $kpi['member_new_month'] }}</div>
            <div class="admin-kpi-delta is-up">
                <span class="material-symbols-outlined" aria-hidden="true">trending_up</span>
                <span>本月新增</span>
            </div>
            <div class="admin-kpi-icon" aria-hidden="true">
                <span class="material-symbols-outlined">group_add</span>
            </div>
        </div>
        <div class="admin-kpi">
            <div class="admin-kpi-label">上架商品</div>
            <div class="admin-kpi-value">{{ $kpi['product_active'] }}</div>
            <div class="admin-kpi-delta is-up">
                <span class="material-symbols-outlined" aria-hidden="true">trending_up</span>
                <span>目前生效</span>
            </div>
            <div class="admin-kpi-icon admin-kpi-icon-warm" aria-hidden="true">
                <span class="material-symbols-outlined">inventory_2</span>
            </div>
        </div>
        <div class="admin-kpi">
            <div class="admin-kpi-label">公告未讀</div>
            <div class="admin-kpi-value">{{ $kpi['announcement_unread'] }}</div>
            <div class="admin-kpi-delta is-down">
                <span class="material-symbols-outlined" aria-hidden="true">priority_high</span>
                <span>待處理</span>
            </div>
            <div class="admin-kpi-icon admin-kpi-icon-amber" aria-hidden="true">
                <span class="material-symbols-outlined">campaign</span>
            </div>
        </div>
        <div class="admin-kpi">
            <div class="admin-kpi-label">今日後台登入</div>
            <div class="admin-kpi-value">{{ $kpi['admin_login_today'] }}</div>
            <div class="admin-kpi-delta is-up">
                <span class="material-symbols-outlined" aria-hidden="true">trending_up</span>
                <span>正常範圍</span>
            </div>
            <div class="admin-kpi-icon admin-kpi-icon-green" aria-hidden="true">
                <span class="material-symbols-outlined">verified_user</span>
            </div>
        </div>
    </section>

    {{-- Quick tools --}}
    <section class="admin-dash-mini-grid">
        <a href="{{ url('admin/product/list') }}" class="admin-quick-tile">
            <div class="admin-quick-tile-icon" aria-hidden="true">
                <span class="material-symbols-outlined">storefront</span>
            </div>
            <div>
                <div class="admin-quick-tile-name">商品管理</div>
                <div class="admin-quick-tile-sub">前往商品列表、批次上下架</div>
            </div>
        </a>
        <a href="{{ url('admin/member/list') }}" class="admin-quick-tile">
            <div class="admin-quick-tile-icon admin-quick-tile-icon-warm" aria-hidden="true">
                <span class="material-symbols-outlined">badge</span>
            </div>
            <div>
                <div class="admin-quick-tile-name">會員管理</div>
                <div class="admin-quick-tile-sub">查看會員、重設密碼</div>
            </div>
        </a>
        <a href="{{ url('admin/admin.log/list') }}" class="admin-quick-tile">
            <div class="admin-quick-tile-icon admin-quick-tile-icon-danger" aria-hidden="true">
                <span class="material-symbols-outlined">history_edu</span>
            </div>
            <div>
                <div class="admin-quick-tile-name">操作日誌</div>
                <div class="admin-quick-tile-sub">追蹤系統操作軌跡</div>
            </div>
        </a>
    </section>

    {{-- Activity + system --}}
    <section class="admin-dash-grid">
        <div class="admin-card admin-card-pad">
            <div class="admin-section-head">
                <div>
                    <h3 class="admin-section-title">近期操作日誌</h3>
                    <p class="admin-section-sub">最近 5 筆系統操作</p>
                </div>
                <a href="{{ url('admin/admin.log/list') }}" class="admin-btn admin-btn-muted admin-btn-sm">
                    <span>查看全部</span>
                    <span class="material-symbols-outlined" aria-hidden="true">arrow_outward</span>
                </a>
            </div>
            @php($recentLogs = $recentLogs ?? [])
            @if (empty($recentLogs))
                <x-admin.empty-state icon="history" title="尚無操作紀錄" description="近期未有任何後台操作。" />
            @else
                <div class="admin-activity">
                    @foreach ($recentLogs as $log)
                        <div class="admin-activity-item">
                            <div class="admin-activity-icon" aria-hidden="true">
                                <span class="material-symbols-outlined">{{ $log['icon'] ?? 'history' }}</span>
                            </div>
                            <div class="admin-activity-body">
                                <div class="admin-activity-title">{{ $log['title'] }}</div>
                                <div class="admin-activity-meta">{{ $log['meta'] }}</div>
                            </div>
                            <span class="admin-badge admin-badge-{{ $log['tone'] ?? 'neutral' }}">{{ $log['action'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="admin-card admin-card-pad">
            <div class="admin-section-head">
                <div>
                    <h3 class="admin-section-title">系統概況</h3>
                    <p class="admin-section-sub">內容資料分布</p>
                </div>
            </div>
            @php($systemStats = $systemStats ?? [])
            <div class="admin-stack admin-system-stats">
                @forelse ($systemStats as $stat)
                    <div>
                        <div class="admin-flex-between admin-system-stat-label">
                            <span class="admin-text-sm admin-text-strong">{{ $stat['label'] }}</span>
                            <span class="admin-text-sm admin-text-mute">{{ $stat['value'] }}</span>
                        </div>
                        <div class="admin-bar">
                            <span style="width: {{ $stat['percent'] }}%; {{ !empty($stat['color']) ? 'background:' . $stat['color'] . ';' : '' }}"></span>
                        </div>
                    </div>
                @empty
                    <p class="admin-text-mute admin-text-sm">系統概況待補（P5 階段補真實統計）。</p>
                @endforelse

                <div class="admin-system-meta">
                    <div class="admin-flex-between admin-system-meta-row">
                        <span class="admin-text-xs admin-text-mute admin-system-meta-key">PHP 版本</span>
                        <span class="admin-badge admin-badge-info">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="admin-flex-between admin-system-meta-row">
                        <span class="admin-text-xs admin-text-mute admin-system-meta-key">Laravel</span>
                        <span class="admin-badge admin-badge-info">{{ app()->version() }}</span>
                    </div>
                    <div class="admin-flex-between">
                        <span class="admin-text-xs admin-text-mute admin-system-meta-key">資料庫</span>
                        <span class="admin-badge admin-badge-success">連線正常</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
