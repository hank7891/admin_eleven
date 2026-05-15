@extends('layouts.frontend')

@section('fe-active', 'announcement')

@section('content')
    <section class="fe-section fe-announcement-detail">
        <div class="fe-container-narrow">
            <a href="{{ url('announcement') }}" class="fe-back-link">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                返回公告列表
            </a>

            <header class="fe-article-head">
                <span class="fe-eyebrow">Announcement</span>
                <h1 class="fe-h1 fe-article-title">{{ $data['title'] }}</h1>
                <div class="fe-article-meta">
                    <span>{{ $data['date_full_display'] }}</span>
                    <span aria-hidden="true">•</span>
                    <span>Aura Editorial</span>
                </div>
                @if (!empty($data['summary']))
                    <p class="fe-body-lg fe-article-summary">{{ $data['summary'] }}</p>
                @endif
            </header>

            <article class="fe-article-body">
                <div class="fe-article-card">
                    @foreach ($data['content_lines'] as $line)
                        @if (trim($line) !== '')
                            <p>{{ $line }}</p>
                        @endif
                    @endforeach
                </div>
            </article>
        </div>
    </section>

    @if (!empty($moreAnnouncements))
        <section class="fe-section fe-section-soft" aria-label="更多公告">
            <div class="fe-container">
                <div class="fe-section-head">
                    <div>
                        <span class="fe-eyebrow">More Articles</span>
                        <h2 class="fe-h2 fe-section-head-title">更多公告</h2>
                    </div>
                    <a href="{{ url('announcement') }}" class="fe-link-arrow">
                        View All Entries
                        <span class="material-symbols-outlined" aria-hidden="true">arrow_outward</span>
                    </a>
                </div>

                <div class="fe-journal-stack fe-journal-stack-spaced">
                    @foreach ($moreAnnouncements as $announcement)
                        <article class="fe-journal-card">
                            <a href="{{ $announcement['url'] }}" class="fe-journal-row" aria-label="閱讀公告：{{ $announcement['title'] }}">
                                <div class="fe-journal-body">
                                    <span class="fe-eyebrow is-muted">{{ $announcement['date_display'] }}</span>
                                    <h3 class="fe-journal-title">{{ $announcement['title'] }}</h3>
                                    <p class="fe-body">{{ $announcement['content_preview'] }}</p>
                                </div>
                                <span class="material-symbols-outlined fe-arrow" aria-hidden="true">arrow_outward</span>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
