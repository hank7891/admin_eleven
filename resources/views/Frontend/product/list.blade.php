@extends('Frontend-share.layout')

@section('content')
    <section class="px-4 pb-16 pt-32 sm:px-6 lg:px-8 lg:pb-24 lg:pt-40">
        <div class="mx-auto max-w-7xl">
            <header class="mb-14 lg:mb-20">
                <p class="text-[0.76rem] font-semibold uppercase tracking-[0.24em] text-primary">All Products</p>
                <h1 class="mt-4 font-headline text-[2.8rem] tracking-[-0.05em] text-on-surface sm:text-[4.8rem]">Products</h1>
                <p class="mt-6 max-w-2xl text-[1rem] leading-8 text-on-surface/70 sm:text-[1.08rem]">
                    探索目前已上架且生效中的所有商品，支援關鍵字、上架日期、類別與標籤篩選。
                </p>
            </header>

            <section class="mb-16 rounded-[1.4rem] bg-surface-container-low p-6 shadow-[0_20px_40px_-32px_rgba(26,28,25,0.28)] sm:p-8">
                <form method="GET" action="{{ url('product') }}" class="grid grid-cols-1 gap-5 md:grid-cols-6 md:items-end">
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-on-surface/54">Keyword</label>
                        <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="搜尋商品名稱或標語" class="w-full rounded-xl border-none bg-surface-container px-4 py-3.5 text-[0.95rem] text-on-surface placeholder:text-outline/70 focus:ring-2 focus:ring-primary/30" />
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-on-surface/54">From</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full rounded-xl border-none bg-surface-container px-4 py-3.5 text-[0.95rem] text-on-surface focus:ring-2 focus:ring-primary/30" />
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-on-surface/54">To</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full rounded-xl border-none bg-surface-container px-4 py-3.5 text-[0.95rem] text-on-surface focus:ring-2 focus:ring-primary/30" />
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-on-surface/54">Category</label>
                        <select name="category_id" class="w-full rounded-xl border-none bg-surface-container px-4 py-3.5 text-[0.95rem] text-on-surface focus:ring-2 focus:ring-primary/30">
                            <option value="">全部類別</option>
                            @foreach (($filterOptions['categories'] ?? []) as $category)
                                <option value="{{ $category['id'] }}" {{ (string) ($filters['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' }}>{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-on-surface/54">Tag</label>
                        <select name="tag_id" class="w-full rounded-xl border-none bg-surface-container px-4 py-3.5 text-[0.95rem] text-on-surface focus:ring-2 focus:ring-primary/30">
                            <option value="">全部標籤</option>
                            @foreach (($filterOptions['tags'] ?? []) as $tag)
                                <option value="{{ $tag['id'] }}" {{ (string) ($filters['tag_id'] ?? '') === (string) $tag['id'] ? 'selected' : '' }}>{{ $tag['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-6 flex gap-3 justify-end">
                        <button type="submit" class="frontend-btn-primary inline-flex min-h-12 items-center justify-center rounded-xl px-6 py-3 text-[0.82rem] font-semibold uppercase tracking-[0.18em]">Filter</button>
                        <a href="{{ url('product') }}" class="frontend-btn-ghost inline-flex min-h-12 items-center justify-center rounded-xl px-6 py-3 text-[0.82rem] font-semibold uppercase tracking-[0.18em] no-underline">Clear</a>
                    </div>
                </form>
            </section>

            @if (empty($products))
                <div class="rounded-[1.5rem] border border-outline-variant/35 bg-surface-container-lowest px-8 py-20 text-center shadow-[0_24px_48px_-36px_rgba(26,28,25,0.24)]">
                    <span class="material-symbols-outlined text-[3rem] text-outline-variant/60">package_2</span>
                    <p class="mt-4 text-[1rem] text-on-surface/68">目前沒有符合條件的商品。</p>
                    <a href="{{ url('product') }}" class="mt-6 inline-flex items-center gap-2 border-b border-outline pb-1 text-[0.85rem] font-semibold uppercase tracking-[0.18em] text-on-surface transition-colors hover:text-primary no-underline">
                        清除篩選
                    </a>
                </div>
            @else
                <section class="grid grid-cols-1 gap-x-8 gap-y-12 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($products as $product)
                        <article class="frontend-product-card group">
                            <a href="{{ $product['url'] }}" class="block no-underline" aria-label="查看更多商品：{{ $product['name'] }}">
                                <div class="overflow-hidden rounded-[1.35rem] bg-surface-container-low shadow-[0_24px_56px_-40px_rgba(26,28,25,0.28)]">
                                    <img src="{{ $product['image_url'] }}" alt="{{ $product['image_alt'] }}" class="aspect-[4/5] w-full object-cover transition-transform duration-700 group-hover:scale-[1.04]" loading="lazy">
                                </div>
                                <div class="mt-5 flex items-start justify-between gap-4 px-2">
                                    <div>
                                        <p class="text-[0.72rem] font-semibold uppercase tracking-[0.24em] text-secondary">{{ $product['start_at_display'] ?? '' }}</p>
                                        <h2 class="mt-2 font-headline text-[1.3rem] tracking-[-0.02em] text-on-surface">{{ $product['name'] }}</h2>
                                        <p class="mt-2 text-[0.75rem] font-semibold uppercase tracking-[0.22em] text-on-surface/52">{{ $product['category_name'] }}</p>
                                        @if (!empty($product['tag_names']))
                                            <p class="mt-3 text-[0.82rem] text-on-surface/62">{{ implode(' / ', $product['tag_names']) }}</p>
                                        @endif
                                    </div>
                                    <p class="pt-1 text-[0.96rem] font-medium text-primary">{{ $product['price_display'] }}</p>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </section>

                <div class="mt-16">
                    {{ $pagination->appends($filters)->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection


