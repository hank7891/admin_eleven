@extends('layouts.frontend')

@section('fe-active', 'announcement')

@section('content')
    <section class="fe-page-head">
        <div class="fe-container-narrow">
            <span class="fe-eyebrow">Journal &amp; Notes</span>
            <h1 class="fe-h1 fe-page-title">公告與書寫</h1>
            <p class="fe-body-lg fe-page-lead">
                系統公告與最新公告彙整頁。僅顯示目前已生效的公告內容，保留更沉靜、可閱讀的編輯節奏。
            </p>
        </div>
    </section>

    <section class="fe-section">
        <div class="fe-container-narrow">
            <form method="GET" action="{{ url('announcement') }}" class="fe-filter-form" role="search" aria-label="公告篩選">
                <div class="fe-filter-grid">
                    <div class="fe-form-field fe-filter-keyword">
                        <label for="filter-keyword" class="fe-form-label">Search Articles</label>
                        <input id="filter-keyword" type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="輸入標題、摘要或內文關鍵字" class="fe-input">
                    </div>
                    <div class="fe-form-field">
                        <label for="filter-date-from" class="fe-form-label">Start Date</label>
                        <input id="filter-date-from" type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="fe-input">
                    </div>
                    <div class="fe-form-field">
                        <label for="filter-date-to" class="fe-form-label">End Date</label>
                        <input id="filter-date-to" type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="fe-input">
                    </div>
                </div>
                <div class="fe-filter-actions">
                    <button type="submit" class="fe-btn fe-btn-primary">Filter</button>
                    <a href="{{ url('announcement') }}" class="fe-btn fe-btn-ghost">Clear</a>
                </div>
            </form>

            @if (empty($announcements))
                <div class="fe-empty-state">
                    <span class="material-symbols-outlined" aria-hidden="true">article</span>
                    <p class="fe-body">目前沒有符合條件的公告。</p>
                </div>
            @else
                <div class="fe-journal-stack fe-journal-stack-spaced">
                    @foreach ($announcements as $announcement)
                        <article class="fe-journal-card">
                            <a href="{{ $announcement['url'] }}" class="fe-journal-row" aria-label="閱讀公告：{{ $announcement['title'] }}">
                                <div class="fe-journal-body">
                                    <span class="fe-eyebrow is-muted">{{ $announcement['date_display'] }}</span>
                                    <h2 class="fe-journal-title">{{ $announcement['title'] }}</h2>
                                    @if (!empty($announcement['summary']))
                                        <p class="fe-body">{{ $announcement['summary'] }}</p>
                                    @endif
                                    @if (!empty($announcement['content_preview']))
                                        <p class="fe-body fe-journal-preview">{{ $announcement['content_preview'] }}</p>
                                    @endif
                                </div>
                                <span class="material-symbols-outlined fe-arrow" aria-hidden="true">arrow_outward</span>
                            </a>
                        </article>
                    @endforeach
                </div>

                <div class="fe-pagination-wrap">
                    {{ $pagination->appends($filters)->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
