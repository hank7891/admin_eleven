@extends('layouts.admin')

@section('title-suffix', ' · 輪播管理')

@section('content')
    <x-admin.page-head
        title="輪播管理"
        subtitle="維護首頁 Hero 輪播圖片、標語、排序與顯示時段"
        :breadcrumbs="[['label' => '首頁', 'url' => 'admin/'], ['label' => '輪播管理']]"
    >
        <x-slot:actions>
            <x-admin.button as="a" :href="url('admin/hero-slide/edit/0')" iconLeft="add">新增輪播</x-admin.button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.filter-card :action="url('admin/hero-slide/list')" title="篩選">
        <x-admin.input
            name="keyword"
            label="關鍵字"
            :value="$filters['keyword'] ?? ''"
            placeholder="搜尋眉標 / 標題 / 說明"
            icon="slideshow"
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
            <x-admin.empty-state icon="slideshow" title="尚無輪播資料" description="請新增輪播或調整篩選條件。" />
        </x-admin.card>
    @else
        <div class="admin-hero-grid">
            @foreach ($data as $row)
                <article class="admin-card admin-hero-card">
                    <div class="admin-hero-card-media">
                        @if (!empty($row['image_url']))
                            <img src="{{ $row['image_url'] }}" alt="{{ $row['image_alt'] ?: $row['title'] }}">
                        @else
                            <div class="admin-hero-card-empty" aria-hidden="true">
                                <span class="material-symbols-outlined">image</span>
                            </div>
                        @endif
                    </div>
                    <div class="admin-hero-card-body">
                        <div class="admin-hero-card-head">
                            <div>
                                <p class="admin-eyebrow">{{ $row['eyebrow'] ?: '未設定眉標' }}</p>
                                <h3 class="admin-hero-card-title">{{ $row['title'] }}</h3>
                            </div>
                            @if ((int) ($row['is_active'] ?? STATUS_INACTIVE) === STATUS_ACTIVE)
                                <x-admin.badge tone="success">{{ $row['is_active_display'] }}</x-admin.badge>
                            @else
                                <x-admin.badge tone="neutral">{{ $row['is_active_display'] }}</x-admin.badge>
                            @endif
                        </div>

                        <p class="admin-text-mute admin-hero-card-desc">{{ $row['description_preview'] ?: '尚無說明文字' }}</p>

                        <dl class="admin-hero-card-meta">
                            <dt>排序</dt><dd>{{ $row['sort_order'] }}</dd>
                            <dt>開始時間</dt><dd>{{ $row['start_at_display'] }}</dd>
                            <dt>結束時間</dt><dd>{{ $row['end_at_display'] }}</dd>
                            <dt>圖片點擊連結</dt><dd>{{ !empty($row['target_url']) ? '已設定' : '未設定' }}</dd>
                        </dl>

                        <div class="admin-hero-card-actions">
                            <a href="{{ asset('admin/hero-slide/edit/' . $row['id']) }}" class="admin-link-action">
                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                <span>編輯</span>
                            </a>
                            <button
                                type="button"
                                class="admin-btn admin-btn-sm {{ (int) ($row['is_active'] ?? STATUS_INACTIVE) === STATUS_ACTIVE ? 'admin-btn-muted' : 'admin-btn-primary' }}"
                                data-toggle-active-url="{{ url('admin/hero-slide/toggle-active/' . $row['id']) }}"
                            >
                                <span class="material-symbols-outlined" aria-hidden="true">toggle_on</span>
                                <span>{{ (int) ($row['is_active'] ?? STATUS_INACTIVE) === STATUS_ACTIVE ? '停用' : '啟用' }}</span>
                            </button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <x-stitch-pagination :paginator="$pagination" :filters="$filters" />
    @endif
@endsection
