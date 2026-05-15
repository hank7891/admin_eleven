@extends('layouts.frontend')

@section('fe-active', 'home')

@section('content')
    @if (!empty($slides))
    <section class="fe-hero" aria-roledescription="輪播" aria-label="首頁主視覺輪播">
        <div class="fe-container">
            <div class="fe-hero-glow-a" aria-hidden="true"></div>
            <div class="fe-hero-glow-b" aria-hidden="true"></div>
            <div id="heroLiveRegion" class="fe-sr-only" aria-live="polite" aria-atomic="true"></div>

            <div id="heroCarouselRegion" class="fe-hero-card" tabindex="0" aria-roledescription="carousel" aria-label="首頁主視覺輪播，使用左右方向鍵切換" data-hero-carousel>
                <div id="heroSlidePanel" class="fe-hero-frame" aria-live="off">
                    @foreach ($slides as $index => $slide)
                        <img
                            src="{{ $slide['image'] }}"
                            alt="{{ $slide['image_alt'] }}"
                            class="fe-hero-img {{ $index === 0 ? 'is-active' : '' }}"
                            data-hero-image
                            data-slide-index="{{ $index }}"
                            data-fallback-alt="{{ $slide['image_alt'] ?? '首頁輪播圖片' }}"
                            @if ($index > 0) loading="lazy" @endif
                        >
                    @endforeach
                    <a
                        id="heroSlideLink"
                        href="{{ $slides[0]['target_url'] ?? '#' }}"
                        class="fe-hero-link {{ empty($slides[0]['target_url']) ? 'hero-slide-link-disabled' : '' }}"
                        data-link-disabled="{{ empty($slides[0]['target_url']) ? '1' : '0' }}"
                        aria-label="開啟目前輪播連結"
                        @if (!empty($slides[0]['target_url']) && str_starts_with((string) $slides[0]['target_url'], 'http')) target="_blank" rel="noopener noreferrer" @endif
                    ></a>

                    <div class="fe-hero-controls">
                        <button type="button" class="fe-hero-btn" data-hero-prev aria-label="上一張輪播">
                            <span class="material-symbols-outlined" aria-hidden="true">west</span>
                        </button>
                        <button type="button" class="fe-hero-btn" data-hero-next aria-label="下一張輪播">
                            <span class="material-symbols-outlined" aria-hidden="true">east</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="fe-hero-dots" role="tablist" aria-label="首頁輪播切換">
                @foreach ($slides as $index => $slide)
                    <button
                        type="button"
                        class="fe-dot {{ $index === 0 ? 'is-active' : '' }}"
                        data-hero-dot
                        data-slide-index="{{ $index }}"
                        role="tab"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-controls="heroSlidePanel"
                        aria-label="切換到第 {{ $index + 1 }} 張輪播：{{ $slide['image_alt'] ?? ($slide['title'] ?? '首頁輪播') }}"
                    ></button>
                @endforeach
                <span class="fe-hero-scroll-hint" aria-hidden="true">Scroll <span class="material-symbols-outlined" aria-hidden="true">south</span></span>
            </div>
        </div>
    </section>
    @endif

    <section id="journal" class="fe-journal" aria-labelledby="journalHeading">
        <div class="fe-container-narrow fe-journal-inner">
            <h2 id="journalHeading" class="fe-sr-only">最新公告</h2>

            <div class="fe-journal-stack">
                @forelse ($journalEntries as $entry)
                    <article class="fe-journal-card">
                        <a href="{{ $entry['url'] }}" class="fe-journal-row" aria-label="閱讀文章：{{ $entry['title'] }}">
                            <div class="fe-journal-body">
                                <span class="fe-eyebrow is-muted">{{ $entry['date_display'] ?? '' }}</span>
                                <h3 class="fe-journal-title">{{ $entry['title'] }}</h3>
                                <p class="fe-body">{{ $entry['content_preview'] ?? ($entry['summary'] ?? '') }}</p>
                            </div>
                            <span class="material-symbols-outlined fe-arrow" aria-hidden="true">arrow_outward</span>
                        </a>
                    </article>
                @empty
                    <div class="fe-journal-empty">
                        <span class="material-symbols-outlined" aria-hidden="true">article</span>
                        <p>目前尚無已公開公告。</p>
                    </div>
                @endforelse
            </div>

            <div class="fe-journal-more">
                <a href="{{ url('announcement') }}" class="fe-link-arrow">
                    More Articles
                    <span class="material-symbols-outlined" aria-hidden="true">arrow_outward</span>
                </a>
            </div>
        </div>
    </section>

    @if (!empty($products))
    <section class="fe-section" id="products" aria-labelledby="productHeading">
        <div class="fe-container">
            <div class="fe-section-head">
                <div>
                    <span class="fe-eyebrow">Selected Works</span>
                    <h2 id="productHeading" class="fe-h1 fe-section-head-title">精選商品，呈現柔和節奏與生活感</h2>
                </div>
                <a href="{{ url('product') }}" class="fe-link-arrow">
                    More Products
                    <span class="material-symbols-outlined" aria-hidden="true">arrow_outward</span>
                </a>
            </div>

            <div class="fe-product-grid">
                @foreach ($products as $product)
                    <article class="fe-product {{ in_array($loop->iteration, [2, 5], true) ? 'is-offset' : '' }}">
                        <a href="{{ $product['url'] }}" aria-label="查看更多商品：{{ $product['name'] }}">
                            @if (!empty($product['image_url']))
                                <div class="fe-product-media">
                                    <img src="{{ $product['image_url'] }}" alt="{{ $product['image_alt'] }}" loading="lazy">
                                </div>
                            @else
                                <div class="fe-product-media" role="img" aria-label="{{ $product['name'] }}"></div>
                            @endif
                            <div class="fe-product-info">
                                <div>
                                    <h3 class="fe-product-name">{{ $product['name'] }}</h3>
                                    <p class="fe-product-cat">{{ $product['category_name'] }}</p>
                                </div>
                                <p class="fe-product-price">{{ $product['price_display'] }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section id="member" class="fe-section-tight" aria-labelledby="memberHeading">
        <div class="fe-container">
            @php($homeMember = session(MEMBER_AUTH_SESSION))
            <div class="fe-member-card">
                <div class="fe-member-card-glow" aria-hidden="true"></div>
                <div class="fe-member-content">
                    <span class="fe-eyebrow is-muted">The Inner Circle</span>
                    <h2 id="memberHeading" class="fe-h1 fe-member-title">
                        @if (!empty($homeMember))
                            {{ $homeMember['name'] ?? '會員' }}，歡迎回到這裡
                        @else
                            會員專區，是更靠近品牌日常的入口
                        @endif
                    </h2>
                    <p class="fe-body-lg fe-member-lead">
                        @if (!empty($homeMember))
                            可在會員專區管理個人資料、頭像與登入密碼，未來也會延伸到收藏與會員限定內容。
                        @else
                            建立帳號後即可管理個人資料、頭像與登入密碼，未來也會延伸到收藏與會員限定內容。
                        @endif
                    </p>
                    <div class="fe-member-cta-row">
                        @if (!empty($homeMember))
                            <a href="{{ url('member/profile') }}" class="fe-btn fe-btn-dark">進入會員專區</a>
                            <form method="POST" action="{{ url('member/logout') }}" class="fe-member-logout-form">
                                @csrf
                                <button type="submit" class="fe-btn fe-btn-ghost">會員登出</button>
                            </form>
                        @else
                            <a href="{{ url('member/register') }}" class="fe-btn fe-btn-dark">建立會員帳號</a>
                            <a href="{{ url('member/login') }}" class="fe-btn fe-btn-ghost">會員登入</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    @if (!empty($slides))
        <script>
            window.__FRONTEND_HERO_SLIDES__ = @json($slides);
        </script>
    @endif
@endpush
