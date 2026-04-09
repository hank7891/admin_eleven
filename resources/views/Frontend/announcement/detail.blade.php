@extends('Frontend-share.layout')

@section('content')
    <section class="px-4 pb-16 pt-32 sm:px-6 lg:px-8 lg:pb-24 lg:pt-40">
        <div class="mx-auto max-w-5xl">
            <a href="{{ url('announcement') }}" class="inline-flex items-center gap-2 text-[0.9rem] font-medium text-on-surface/62 no-underline transition-colors hover:text-primary">
                <span class="material-symbols-outlined text-[1.1rem]">arrow_back</span>
                返回公告列表
            </a>

            <header class="mx-auto mt-10 max-w-4xl text-center">
                <span class="text-[0.76rem] font-semibold uppercase tracking-[0.24em] text-primary">Announcement</span>
                <h1 class="mt-6 font-headline text-[2.8rem] leading-[1.12] tracking-[-0.05em] text-on-surface sm:text-[4.8rem]">{{ $data['title'] }}</h1>
                <div class="mt-7 flex flex-col items-center justify-center gap-2 text-[0.78rem] font-semibold uppercase tracking-[0.22em] text-on-surface/46 sm:flex-row sm:gap-4">
                    <span>{{ $data['date_full_display'] }}</span>
                    <span class="hidden sm:block">•</span>
                    <span>Aura Editorial</span>
                </div>
                @if (!empty($data['summary']))
                    <p class="mx-auto mt-8 max-w-3xl text-[1.05rem] leading-8 text-on-surface/68">{{ $data['summary'] }}</p>
                @endif
            </header>

            <article class="mx-auto mt-16 max-w-3xl">
                <div class="rounded-[1.6rem] bg-surface-container-low p-8 shadow-[0_30px_56px_-40px_rgba(26,28,25,0.22)] sm:p-10 lg:p-14">
                    <div class="space-y-8 text-[1.02rem] leading-9 text-on-surface/74">
                        @foreach ($data['content_lines'] as $line)
                            @if (trim($line) !== '')
                                <p class="whitespace-pre-line">{{ $line }}</p>
                            @endif
                        @endforeach
                    </div>
                </div>
            </article>
        </div>
    </section>

    @if (!empty($moreAnnouncements))
        <section class="border-t border-outline-variant/25 bg-surface-container-low/55 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="font-headline text-[2rem] tracking-[-0.04em] text-on-surface sm:text-[3rem]">More Latest News</h2>
                        <p class="mt-3 text-[1rem] text-on-surface/64">延伸閱讀更多已生效的最新公告內容。</p>
                    </div>
                    <a href="{{ url('announcement') }}" class="inline-flex items-center gap-2 border-b border-outline pb-1 text-[0.85rem] font-semibold uppercase tracking-[0.18em] text-on-surface transition-colors hover:text-primary no-underline">
                        View All Entries
                        <span class="material-symbols-outlined text-[1rem]">arrow_outward</span>
                    </a>
                </div>

                <div class="mt-12 grid grid-cols-1 gap-8 lg:grid-cols-3">
                    @foreach ($moreAnnouncements as $announcement)
                        <a href="{{ $announcement['url'] }}" class="group rounded-[1.3rem] bg-surface-container-lowest p-6 no-underline shadow-[0_24px_50px_-36px_rgba(26,28,25,0.24)] transition-transform duration-500 hover:-translate-y-1">
                            <span class="text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-outline">{{ $announcement['date_display'] }}</span>
                            <h3 class="mt-5 font-headline text-[1.45rem] leading-tight tracking-[-0.03em] text-on-surface transition-colors group-hover:text-primary">{{ $announcement['title'] }}</h3>
                            <p class="mt-4 text-[0.96rem] leading-7 text-on-surface/62">{{ $announcement['content_preview'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

