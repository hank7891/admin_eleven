@extends('Frontend-share.layout')

@section('content')
    <section class="px-4 pb-16 pt-32 sm:px-6 lg:px-8 lg:pb-24 lg:pt-40">
        <div class="mx-auto max-w-7xl">
            <header class="mb-14 lg:mb-20">
                <p class="text-[0.76rem] font-semibold uppercase tracking-[0.24em] text-primary">Editorial Announcements</p>
                <h1 class="mt-4 font-headline text-[2.8rem] tracking-[-0.05em] text-on-surface sm:text-[4.8rem]">Latest News</h1>
                <p class="mt-6 max-w-2xl text-[1rem] leading-8 text-on-surface/70 sm:text-[1.08rem]">
                    系統公告與最新公告彙整頁。僅顯示目前已生效的公告內容，保留更沉靜、可閱讀的編輯節奏。
                </p>
            </header>

            <section class="mb-16 rounded-[1.4rem] bg-surface-container-low p-6 shadow-[0_20px_40px_-32px_rgba(26,28,25,0.28)] sm:p-8">
                <form method="GET" action="{{ url('announcement') }}" class="grid grid-cols-1 gap-5 md:grid-cols-[1.2fr_0.6fr_0.6fr_auto] md:items-end">
                    <div class="space-y-2">
                        <label class="block text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-on-surface/54">Search Articles</label>
                        <div class="relative">
                            <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="輸入標題、摘要或內文關鍵字" class="w-full rounded-xl border-none bg-surface-container px-4 py-3.5 pr-12 text-[0.95rem] text-on-surface placeholder:text-outline/70 focus:ring-2 focus:ring-primary/30" />
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-outline">search</span>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-on-surface/54">Start Date</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full rounded-xl border-none bg-surface-container px-4 py-3.5 text-[0.95rem] text-on-surface focus:ring-2 focus:ring-primary/30" />
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-on-surface/54">End Date</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full rounded-xl border-none bg-surface-container px-4 py-3.5 text-[0.95rem] text-on-surface focus:ring-2 focus:ring-primary/30" />
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="frontend-btn-primary inline-flex min-h-12 items-center justify-center rounded-xl px-6 py-3 text-[0.82rem] font-semibold uppercase tracking-[0.18em]">Filter</button>
                        <a href="{{ url('announcement') }}" class="frontend-btn-ghost inline-flex min-h-12 items-center justify-center rounded-xl px-6 py-3 text-[0.82rem] font-semibold uppercase tracking-[0.18em] no-underline">Clear</a>
                    </div>
                </form>
            </section>

            @if (empty($announcements))
                <div class="rounded-[1.5rem] border border-outline-variant/35 bg-surface-container-lowest px-8 py-20 text-center shadow-[0_24px_48px_-36px_rgba(26,28,25,0.24)]">
                    <span class="material-symbols-outlined text-[3rem] text-outline-variant/60">article</span>
                    <p class="mt-4 text-[1rem] text-on-surface/68">目前沒有符合條件的公告。</p>
                </div>
            @else
                <section class="space-y-18 lg:space-y-24">
                    @foreach ($announcements as $announcement)
                        <article class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-12 {{ $loop->even ? 'lg:[&>*:first-child]:order-2' : '' }}">
                            <div class="lg:col-span-5">
                                <div class="h-full min-h-72 rounded-[1.4rem] bg-surface-container-high p-6 shadow-[0_24px_46px_-34px_rgba(26,28,25,0.2)]">
                                    <div class="flex h-full flex-col justify-between rounded-[1.1rem] border border-outline-variant/30 bg-surface-container-lowest/70 p-6">
                                        <div>
                                            <span class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-[0.72rem] font-semibold uppercase tracking-[0.18em] text-primary">Announcement</span>
                                            <p class="mt-6 font-headline text-[2rem] leading-tight tracking-[-0.03em] text-on-surface">{{ $announcement['title'] }}</p>
                                        </div>
                                        <p class="mt-8 text-[0.95rem] leading-7 text-on-surface/62">{{ $announcement['summary'] }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="lg:col-span-7 flex flex-col justify-center">
                                <time class="text-[0.76rem] font-semibold uppercase tracking-[0.24em] text-outline">{{ $announcement['date_display'] }}</time>
                                <h2 class="mt-5 font-headline text-[2rem] leading-tight tracking-[-0.04em] text-on-surface sm:text-[3rem]">{{ $announcement['title'] }}</h2>
                                <p class="mt-6 max-w-3xl text-[1rem] leading-8 text-on-surface/68">{{ $announcement['content_preview'] }}</p>
                                <a href="{{ $announcement['url'] }}" class="mt-8 inline-flex items-center gap-2 text-[0.82rem] font-semibold uppercase tracking-[0.2em] text-primary no-underline transition-transform hover:translate-x-1">
                                    Read Article
                                    <span class="material-symbols-outlined text-[1rem]" aria-hidden="true">arrow_forward</span>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </section>

                <div class="mt-20">
                    {{ $pagination->appends($filters)->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

