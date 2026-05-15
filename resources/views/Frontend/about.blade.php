@extends('layouts.frontend')

@section('fe-active', 'about')

@section('meta_description', e($about['meta_description'] ?? ''))

@section('content')
    <section class="fe-about-hero" aria-labelledby="aboutHeroTitle">
        <div class="fe-container fe-about-hero-grid">
            <div class="fe-about-hero-text">
                <span class="fe-eyebrow">About Aura &amp; Heirloom</span>
                <h1 id="aboutHeroTitle" class="fe-h1 fe-about-hero-title">
                    {{ $about['hero_title'] ?? '' }}
                </h1>
                @if (!empty($about['hero_subtitle']))
                    <p class="fe-body-lg fe-about-hero-lead">{{ $about['hero_subtitle'] }}</p>
                @endif
            </div>

            <div class="fe-about-hero-media">
                @if (!empty($about['hero_image_url']))
                    <div class="fe-about-hero-frame">
                        <img src="{{ $about['hero_image_url'] }}" alt="關於我們主視覺" loading="lazy">
                    </div>
                @else
                    <div class="fe-about-hero-frame is-empty" role="img" aria-label="About Hero Image">
                        <span class="material-symbols-outlined" aria-hidden="true">image</span>
                        <p class="fe-meta">About Hero Image</p>
                    </div>
                @endif
                <div class="fe-about-hero-glow" aria-hidden="true"></div>
            </div>
        </div>
    </section>

    <section class="fe-about-section fe-section-soft" aria-labelledby="aboutStoryTitle">
        <div class="fe-container-narrow fe-about-section-inner">
            <div class="fe-about-section-head">
                <span class="fe-eyebrow">Our Story</span>
                <h2 id="aboutStoryTitle" class="fe-h2">{{ $about['story']['title'] ?? '品牌故事' }}</h2>
            </div>
            <p class="fe-body fe-about-story-content">{{ $about['story']['content'] ?? '' }}</p>
        </div>
    </section>

    @if (!empty($about['mission']) || !empty($about['vision']))
        <section class="fe-about-mv" aria-label="品牌理念">
            <div class="fe-container">
                <div class="fe-about-mv-grid">
                    @if (!empty($about['mission']))
                        <article class="fe-about-mv-card">
                            <span class="fe-eyebrow">Mission</span>
                            <h2 class="fe-h3 fe-about-mv-title">{{ $about['mission']['title'] }}</h2>
                            <p class="fe-body">{{ $about['mission']['content'] }}</p>
                        </article>
                    @endif

                    @if (!empty($about['vision']))
                        <article class="fe-about-mv-card is-alt">
                            <span class="fe-eyebrow">Vision</span>
                            <h2 class="fe-h3 fe-about-mv-title">{{ $about['vision']['title'] ?? '願景標題' }}</h2>
                            <p class="fe-body">{{ $about['vision']['content'] }}</p>
                        </article>
                    @endif
                </div>
            </div>
        </section>
    @endif

    @if (!empty($about['contact']))
        <section class="fe-about-section fe-section-soft" aria-labelledby="aboutContactTitle">
            <div class="fe-container-narrow fe-about-section-inner">
                <div class="fe-about-section-head">
                    <span class="fe-eyebrow">Contact</span>
                    <h2 id="aboutContactTitle" class="fe-h2">聯絡我們</h2>
                </div>

                <div class="fe-about-contact-grid">
                    @if (!empty($about['contact']['email']))
                        <div>
                            <p class="fe-eyebrow">Email</p>
                            <a href="mailto:{{ $about['contact']['email'] }}" class="fe-body fe-about-contact-link">{{ $about['contact']['email'] }}</a>
                        </div>
                    @endif

                    @if (!empty($about['contact']['phone']))
                        <div>
                            <p class="fe-eyebrow">Phone</p>
                            <p class="fe-body">{{ $about['contact']['phone'] }}</p>
                        </div>
                    @endif

                    @if (!empty($about['contact']['address']))
                        <div>
                            <p class="fe-eyebrow">Studio</p>
                            <p class="fe-body">{{ $about['contact']['address'] }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif
@endsection
