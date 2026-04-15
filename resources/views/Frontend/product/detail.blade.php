@extends('Frontend-share.layout')

@section('meta_description', e($data['meta_description'] ?? ''))

@section('content')
    <section class="px-4 pb-16 pt-32 sm:px-6 lg:px-8 lg:pb-24 lg:pt-40">
        <div class="mx-auto max-w-7xl">
            <a href="{{ url('product') }}" class="inline-flex items-center gap-2 text-[0.9rem] font-medium text-on-surface/62 no-underline transition-colors hover:text-primary">
                <span class="material-symbols-outlined text-[1.1rem]">arrow_back</span>
                返回商品列表
            </a>

            <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-[1fr_0.9fr] lg:gap-14">
                <div>
                    @php($mainImage = collect($data['images'] ?? [])->firstWhere('is_primary', 1) ?? (($data['images'] ?? [])[0] ?? null))
                    @if (!empty($mainImage))
                        <img src="{{ $mainImage['image_url'] }}" alt="{{ $mainImage['image_alt'] ?: $data['name'] }}" class="w-full rounded-[1.4rem] object-cover aspect-[4/5] shadow-[0_24px_56px_-40px_rgba(26,28,25,0.28)]">
                    @endif

                    @if (!empty($data['images']) && count($data['images']) > 1)
                        <div class="mt-5 grid grid-cols-5 gap-3">
                            @foreach ($data['images'] as $image)
                                <img src="{{ $image['image_url'] }}" alt="{{ $image['image_alt'] ?: $data['name'] }}" class="aspect-square w-full rounded-xl object-cover {{ ($image['is_primary'] ?? 0) === 1 ? 'ring-2 ring-primary' : '' }}">
                            @endforeach
                        </div>
                    @endif
                </div>

                <div>
                    <p class="text-[0.76rem] font-semibold uppercase tracking-[0.24em] text-primary">Product Detail</p>
                    <h1 class="mt-4 font-headline text-[2.5rem] leading-[1.1] tracking-[-0.04em] text-on-surface sm:text-[3.4rem]">{{ $data['name'] }}</h1>
                    @if (!empty($data['tagline']))
                        <p class="mt-5 text-[1.05rem] leading-8 text-on-surface/68">{{ $data['tagline'] }}</p>
                    @endif

                    <p class="mt-8 text-[1.25rem] font-semibold text-primary">{{ $data['price_display'] }}</p>

                    <div class="mt-8 space-y-3 text-[0.92rem] text-on-surface/72">
                        <p><span class="font-semibold text-on-surface">類別：</span>{{ $data['category_name'] }}</p>
                        @if (!empty($data['tags']))
                            <p><span class="font-semibold text-on-surface">標籤：</span>{{ implode(' / ', $data['tags']) }}</p>
                        @endif
                    </div>

                    <article class="mt-10 rounded-[1.2rem] bg-surface-container-low p-6 shadow-[0_20px_40px_-32px_rgba(26,28,25,0.28)]">
                        <h2 class="text-[0.8rem] font-semibold uppercase tracking-[0.22em] text-on-surface/54">Description</h2>
                        <div class="mt-4 text-[1rem] leading-8 text-on-surface/74">{!! $data['description_html'] !!}</div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    @if (!empty($relatedProducts))
        <section class="border-t border-outline-variant/25 bg-surface-container-low/55 px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <div class="mx-auto max-w-7xl">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="font-headline text-[2rem] tracking-[-0.04em] text-on-surface sm:text-[3rem]">同類別延伸</h2>
                        <p class="mt-3 text-[1rem] text-on-surface/64">探索更多同類別商品。</p>
                    </div>
                    <a href="{{ url('product') }}" class="inline-flex items-center gap-2 border-b border-outline pb-1 text-[0.85rem] font-semibold uppercase tracking-[0.18em] text-on-surface transition-colors hover:text-primary no-underline">
                        View All Products
                        <span class="material-symbols-outlined text-[1rem]">arrow_outward</span>
                    </a>
                </div>

                <div class="mt-12 grid grid-cols-1 gap-8 lg:grid-cols-3">
                    @foreach ($relatedProducts as $product)
                        <a href="{{ $product['url'] }}" class="group rounded-[1.3rem] bg-surface-container-lowest p-6 no-underline shadow-[0_24px_50px_-36px_rgba(26,28,25,0.24)] transition-transform duration-500 hover:-translate-y-1">
                            <img src="{{ $product['image_url'] }}" alt="{{ $product['image_alt'] }}" class="aspect-[4/5] w-full rounded-[1rem] object-cover">
                            <h3 class="mt-5 font-headline text-[1.45rem] leading-tight tracking-[-0.03em] text-on-surface transition-colors group-hover:text-primary">{{ $product['name'] }}</h3>
                            <p class="mt-3 text-[0.9rem] text-on-surface/62">{{ $product['category_name'] }}</p>
                            <p class="mt-2 text-[0.95rem] font-medium text-primary">{{ $product['price_display'] }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection

