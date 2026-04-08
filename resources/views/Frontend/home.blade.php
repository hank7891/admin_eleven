@extends('Frontend-share.layout')

@section('content')
    <section class="relative overflow-hidden px-4 pb-16 pt-32 sm:px-6 lg:px-8 lg:pb-24 lg:pt-40" aria-label="首頁主視覺輪播">
        <div class="mx-auto grid max-w-7xl gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-end lg:gap-14">
            <div class="space-y-8">
                <span class="inline-flex items-center rounded-full border border-outline-variant/70 bg-surface-container-lowest px-4 py-2 text-[0.72rem] font-semibold uppercase tracking-[0.22em] text-primary shadow-[0_18px_34px_-28px_rgba(26,28,25,0.35)]">
                    Curated Living, Soft Rhythm
                </span>

                <div class="space-y-5">
                    <p id="heroEyebrow" class="text-[0.78rem] font-semibold uppercase tracking-[0.28em] text-secondary sm:text-[0.82rem]">
                        {{ $slides[0]['eyebrow'] ?? '' }}
                    </p>
                    <h1 id="heroTitle" class="max-w-4xl font-headline text-[2.75rem] leading-[1.08] tracking-[-0.04em] text-on-surface sm:text-[4rem] lg:text-[5.4rem]">
                        {{ $slides[0]['title'] ?? '' }}
                    </h1>
                    <p id="heroDescription" class="max-w-2xl text-[1rem] leading-8 text-on-surface/74 sm:text-[1.1rem]">
                        {{ $slides[0]['description'] ?? '' }}
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <a id="heroPrimaryCta" href="{{ $slides[0]['primary_cta']['url'] ?? '#' }}" class="frontend-btn-primary inline-flex min-h-12 items-center justify-center rounded-full px-7 py-3.5 text-[0.82rem] font-semibold uppercase tracking-[0.18em] no-underline">
                        {{ $slides[0]['primary_cta']['label'] ?? 'Explore' }}
                    </a>
                    <a id="heroSecondaryCta" href="{{ $slides[0]['secondary_cta']['url'] ?? '#' }}" class="frontend-btn-ghost inline-flex min-h-12 items-center justify-center rounded-full px-7 py-3.5 text-[0.82rem] font-semibold uppercase tracking-[0.18em] no-underline">
                        {{ $slides[0]['secondary_cta']['label'] ?? 'Learn more' }}
                    </a>
                </div>

                <div class="flex items-center gap-3 pt-4" role="tablist" aria-label="首頁輪播切換">
                    @foreach ($slides as $index => $slide)
                        <button
                            type="button"
                            class="hero-dot {{ $index === 0 ? 'is-active' : '' }}"
                            data-hero-dot
                            data-slide-index="{{ $index }}"
                            role="tab"
                            aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                            aria-controls="heroSlidePanel"
                            aria-label="切換到第 {{ $index + 1 }} 張輪播：{{ $slide['title'] }}"
                        ></button>
                    @endforeach
                    <div class="ml-auto hidden items-center gap-2 text-[0.75rem] uppercase tracking-[0.18em] text-on-surface/42 sm:flex">
                        <span>Scroll</span>
                        <span class="material-symbols-outlined text-[1rem]" aria-hidden="true">south</span>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="pointer-events-none absolute -left-10 top-8 h-32 w-32 rounded-full bg-primary/10 blur-3xl sm:h-44 sm:w-44"></div>
                <div class="pointer-events-none absolute -bottom-12 right-0 h-40 w-40 rounded-full bg-tertiary/14 blur-3xl sm:h-52 sm:w-52"></div>

                <div class="hero-media-card relative overflow-hidden rounded-[1.75rem] bg-surface-container-lowest p-3 shadow-[0_40px_80px_-48px_rgba(26,28,25,0.38)] sm:p-4">
                    <div id="heroSlidePanel" class="relative aspect-[4/5] overflow-hidden rounded-[1.3rem] bg-surface-container-low">
                        @foreach ($slides as $index => $slide)
                            <img
                                src="{{ $slide['image'] }}"
                                alt="{{ $slide['image_alt'] }}"
                                class="hero-slide-image {{ $index === 0 ? 'is-active' : '' }}"
                                data-hero-image
                                data-slide-index="{{ $index }}"
                                @if ($index > 0) loading="lazy" @endif
                            >
                        @endforeach
                        <div class="absolute inset-0 bg-linear-to-t from-on-surface/28 via-on-surface/4 to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 flex items-end justify-between gap-4 p-5 sm:p-7">
                            <div>
                                <p class="text-[0.72rem] uppercase tracking-[0.24em] text-white/78">Editorial Interior</p>
                                <p class="mt-2 font-headline text-[1.15rem] text-white sm:text-[1.4rem]">為日常留一個慢下來的位置</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" class="hero-nav-btn" data-hero-prev aria-label="上一張輪播">
                                    <span class="material-symbols-outlined text-[1.2rem]" aria-hidden="true">west</span>
                                </button>
                                <button type="button" class="hero-nav-btn" data-hero-next aria-label="下一張輪播">
                                    <span class="material-symbols-outlined text-[1.2rem]" aria-hidden="true">east</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="journal" class="border-y border-outline-variant/30 bg-surface-container-low/66 px-4 py-16 sm:px-6 lg:px-8 lg:py-24" aria-labelledby="journalHeading">
        <div class="mx-auto grid max-w-7xl gap-12 lg:grid-cols-[0.9fr_1.1fr] lg:gap-20">
            <div class="space-y-6 lg:sticky lg:top-36 lg:self-start">
                <span class="text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-primary">The Journal</span>
                <h2 id="journalHeading" class="font-headline text-[2.2rem] tracking-[-0.04em] text-on-surface sm:text-[3rem]">把生活過成一種有留白的閱讀體驗</h2>
                <p class="max-w-md text-[1rem] leading-8 text-on-surface/68">
                    五則靜態公告與編輯文章，保留足夠呼吸感與節奏，讓資訊像品牌日誌而非訊息牆。
                </p>
                <a href="#" class="inline-flex items-center gap-2 border-b border-outline pb-1 text-[0.9rem] font-medium text-on-surface transition-colors hover:text-primary no-underline">
                    More Articles
                    <span class="material-symbols-outlined text-[1rem]" aria-hidden="true">arrow_outward</span>
                </a>
            </div>

            <div class="space-y-5">
                @foreach ($journalEntries as $entry)
                    <article class="group rounded-[1.2rem] border border-outline-variant/35 bg-surface-container-lowest/86 px-6 py-6 shadow-[0_22px_44px_-34px_rgba(26,28,25,0.28)] transition-transform duration-500 hover:-translate-y-1">
                        <a href="{{ $entry['url'] }}" class="block no-underline" aria-label="閱讀文章：{{ $entry['title'] }}">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                                <div class="space-y-3">
                                    <p class="text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-secondary">
                                        {{ \Carbon\Carbon::parse($entry['date'])->format('M d, Y') }}
                                    </p>
                                    <h3 class="font-headline text-[1.5rem] leading-tight tracking-[-0.03em] text-on-surface transition-colors group-hover:text-primary sm:text-[1.8rem]">
                                        {{ $entry['title'] }}
                                    </h3>
                                    <p class="max-w-2xl text-[0.98rem] leading-7 text-on-surface/64">
                                        {{ $entry['excerpt'] }}
                                    </p>
                                </div>
                                <span class="material-symbols-outlined text-outline transition-transform group-hover:translate-x-1 group-hover:text-primary" aria-hidden="true">arrow_outward</span>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="products" class="px-4 py-18 sm:px-6 lg:px-8 lg:py-28" aria-labelledby="productHeading">
        <div class="mx-auto max-w-7xl space-y-12">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-4">
                    <span class="text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-primary">Selected Works</span>
                    <h2 id="productHeading" class="font-headline text-[2.2rem] tracking-[-0.04em] text-on-surface sm:text-[3rem]">六件靜態商品卡，呈現柔和節奏與生活感</h2>
                </div>
                <a href="#" class="inline-flex items-center gap-2 border-b border-outline pb-1 text-[0.9rem] font-medium text-on-surface transition-colors hover:text-primary no-underline">
                    More Products
                    <span class="material-symbols-outlined text-[1rem]" aria-hidden="true">arrow_outward</span>
                </a>
            </div>

            <div class="grid grid-cols-1 gap-x-8 gap-y-12 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($products as $product)
                    <article class="frontend-product-card group {{ in_array($loop->iteration, [2, 5], true) ? 'xl:translate-y-10' : '' }}">
                        <a href="#" class="block no-underline" aria-label="查看更多商品：{{ $product['name'] }}">
                            <div class="overflow-hidden rounded-[1.35rem] bg-surface-container-low shadow-[0_24px_56px_-40px_rgba(26,28,25,0.28)]">
                                <img
                                    src="{{ $product['image'] }}"
                                    alt="{{ $product['image_alt'] }}"
                                    class="aspect-[4/5] w-full object-cover transition-transform duration-700 group-hover:scale-[1.04]"
                                    loading="lazy"
                                >
                            </div>
                            <div class="mt-5 flex items-start justify-between gap-4 px-2">
                                <div>
                                    <h3 class="font-headline text-[1.3rem] tracking-[-0.02em] text-on-surface">{{ $product['name'] }}</h3>
                                    <p class="mt-2 text-[0.75rem] font-semibold uppercase tracking-[0.22em] text-on-surface/52">{{ $product['category'] }}</p>
                                </div>
                                <p class="pt-1 text-[0.96rem] font-medium text-primary">{{ $product['price'] }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section id="member" class="px-4 pb-20 pt-6 sm:px-6 lg:px-8 lg:pb-28" aria-labelledby="memberHeading">
        <div class="mx-auto max-w-6xl overflow-hidden rounded-[2rem] bg-linear-to-br from-tertiary-fixed/60 via-surface-container-high to-surface-container-lowest p-8 shadow-[0_40px_80px_-52px_rgba(26,28,25,0.28)] sm:p-12 lg:p-16">
            <div class="relative">
                <div class="pointer-events-none absolute -right-12 -top-10 h-36 w-36 rounded-full bg-primary/10 blur-3xl sm:h-52 sm:w-52"></div>
                <div class="pointer-events-none absolute -bottom-10 -left-10 h-28 w-28 rounded-full bg-secondary/10 blur-3xl sm:h-44 sm:w-44"></div>

                <div class="relative z-10 mx-auto max-w-3xl text-center">
                    <span class="text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-secondary">The Inner Circle</span>
                    <h2 id="memberHeading" class="mt-5 font-headline text-[2.4rem] tracking-[-0.04em] text-on-surface sm:text-[3.4rem]">會員專區，是更靠近品牌日常的入口</h2>
                    <p class="mx-auto mt-6 max-w-2xl text-[1rem] leading-8 text-on-surface/68 sm:text-[1.05rem]">
                        本階段先提供靜態 CTA，後續可逐步串接登入、收藏、公告與會員專屬內容。現在先讓這裡像一封溫柔邀請，而不是催促性的表單。
                    </p>

                    <div class="mt-10 flex flex-col justify-center gap-4 sm:flex-row">
                        <a href="#" class="frontend-btn-dark inline-flex min-h-12 items-center justify-center rounded-full px-8 py-3.5 text-[0.82rem] font-semibold uppercase tracking-[0.18em] no-underline">
                            建立會員帳號
                        </a>
                        <a href="#" class="frontend-btn-ghost inline-flex min-h-12 items-center justify-center rounded-full px-8 py-3.5 text-[0.82rem] font-semibold uppercase tracking-[0.18em] no-underline">
                            會員登入
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        window.__FRONTEND_HERO_SLIDES__ = @json($slides);
    </script>
@endpush


