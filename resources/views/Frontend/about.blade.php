@extends('Frontend-share.layout')

@section('meta_description', e($about['meta_description'] ?? ''))

@section('content')
    <section class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-16 px-6 py-16 md:grid-cols-2 md:px-12 md:py-32" aria-labelledby="aboutHeroTitle">
        <div class="space-y-8">
            <span class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-primary">About Aura &amp; Heirloom</span>
            <h1 id="aboutHeroTitle" class="font-headline text-5xl leading-tight md:text-7xl">
                {{ $about['hero_title'] ?? '' }}
            </h1>
            @if (!empty($about['hero_subtitle']))
                <p class="max-w-lg text-lg leading-relaxed text-on-surface/72 md:text-xl">
                    {{ $about['hero_subtitle'] }}
                </p>
            @endif
        </div>

        <div class="relative">
            @if (!empty($about['hero_image_url']))
                <div class="aspect-[4/5] overflow-hidden rounded-xl shadow-2xl">
                    <img
                        src="{{ $about['hero_image_url'] }}"
                        alt="關於我們主視覺"
                        class="h-full w-full object-cover transition-transform duration-700 hover:scale-105"
                    >
                </div>
            @else
                <div class="flex aspect-[4/5] items-center justify-center rounded-xl bg-surface-container-high shadow-2xl">
                    <div class="text-center text-outline">
                        <span class="material-symbols-outlined text-[2.3rem]" aria-hidden="true">image</span>
                        <p class="mt-2 text-[0.88rem]">About Hero Image</p>
                    </div>
                </div>
            @endif
            <div class="pointer-events-none absolute -bottom-8 -left-8 -z-10 h-48 w-48 rounded-full bg-primary-container/10 blur-3xl"></div>
        </div>
    </section>

    <section class="bg-surface-container-low px-6 py-24 md:px-12 md:py-32" aria-labelledby="aboutStoryTitle">
        <div class="mx-auto max-w-4xl space-y-12 text-center">
            <div class="space-y-4">
                <span class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-on-surface/60">Our Story</span>
                <h2 id="aboutStoryTitle" class="font-headline text-4xl md:text-5xl">{{ $about['story']['title'] ?? '' }}</h2>
            </div>
            <div class="mx-auto max-w-3xl text-left md:text-center">
                <p class="whitespace-pre-line text-lg leading-relaxed text-on-surface/72">
                    {{ $about['story']['content'] ?? '' }}
                </p>
            </div>
        </div>
    </section>

    @if (!empty($about['mission']) || !empty($about['vision']))
        <section class="px-6 py-24 md:px-12 md:py-32" aria-label="品牌理念">
            <div class="mx-auto max-w-7xl">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 md:gap-0">
                    @if (!empty($about['mission']))
                        <article class="flex flex-col justify-center space-y-6 bg-surface-container-lowest p-12 md:border-r md:border-outline-variant/20 md:p-20">
                            <span class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-primary">Mission</span>
                            <h2 class="font-headline text-3xl italic">{{ $about['mission']['title'] }}</h2>
                            <p class="whitespace-pre-line leading-relaxed text-on-surface/70">{{ $about['mission']['content'] }}</p>
                        </article>
                    @endif

                    @if (!empty($about['vision']))
                        <article class="flex flex-col justify-center space-y-6 bg-surface-container p-12 md:p-20">
                            <span class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-primary">Vision</span>
                            <h2 class="font-headline text-3xl italic">{{ $about['vision']['title'] }}</h2>
                            <p class="whitespace-pre-line leading-relaxed text-on-surface/70">{{ $about['vision']['content'] }}</p>
                        </article>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if (!empty($about['contact']))
        <section class="bg-surface px-6 py-24 md:px-12 md:py-32" aria-labelledby="aboutContactTitle">
            <div class="mx-auto max-w-3xl space-y-16 text-center">
                <div class="space-y-4">
                    <span class="text-[0.72rem] font-semibold uppercase tracking-[0.2em] text-on-surface/60">Contact</span>
                    <h2 id="aboutContactTitle" class="font-headline text-4xl md:text-5xl">聯絡我們</h2>
                </div>

                <div class="grid grid-cols-1 gap-10 text-sm leading-relaxed tracking-wide md:grid-cols-3">
                    @if (!empty($about['contact']['email']))
                        <div class="space-y-2">
                            <p class="font-semibold uppercase tracking-tight text-primary">Email</p>
                            <a href="mailto:{{ $about['contact']['email'] }}" class="text-on-surface/70 no-underline transition-colors hover:text-primary">{{ $about['contact']['email'] }}</a>
                        </div>
                    @endif

                    @if (!empty($about['contact']['phone']))
                        <div class="space-y-2">
                            <p class="font-semibold uppercase tracking-tight text-primary">Phone</p>
                            <p class="text-on-surface/70">{{ $about['contact']['phone'] }}</p>
                        </div>
                    @endif

                    @if (!empty($about['contact']['address']))
                        <div class="space-y-2">
                            <p class="font-semibold uppercase tracking-tight text-primary">Studio</p>
                            <p class="text-on-surface/70">{{ $about['contact']['address'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif
@endsection



