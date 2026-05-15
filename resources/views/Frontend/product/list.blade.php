@extends('layouts.frontend')

@section('fe-active', 'product')

@section('content')
    @php($selectedTagIds = array_map('intval', $filters['tag_ids'] ?? []))

    <section class="fe-page-head">
        <div class="fe-container">
            <span class="fe-eyebrow">All Products</span>
            <h1 class="fe-h1 fe-page-title">Products</h1>
            <p class="fe-body-lg fe-page-lead">
                探索目前已上架且生效中的所有商品，支援關鍵字、上架日期、類別與標籤篩選。
            </p>
        </div>
    </section>

    <section class="fe-section">
        <div class="fe-container">

            <form method="GET" action="{{ url('product') }}" class="fe-filter-form" role="search" aria-label="商品篩選">
                <div class="fe-filter-grid">
                    <div class="fe-form-field fe-filter-keyword">
                        <label for="filter-keyword" class="fe-form-label">Keyword</label>
                        <input id="filter-keyword" type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" placeholder="搜尋商品名稱或標語" class="fe-input">
                    </div>
                    <div class="fe-form-field">
                        <label for="filter-date-from" class="fe-form-label">From</label>
                        <input id="filter-date-from" type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="fe-input">
                    </div>
                    <div class="fe-form-field">
                        <label for="filter-date-to" class="fe-form-label">To</label>
                        <input id="filter-date-to" type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="fe-input">
                    </div>
                    <div class="fe-form-field">
                        <label for="filter-category" class="fe-form-label">Category</label>
                        <select id="filter-category" name="category_id" class="fe-input">
                            <option value="">全部類別</option>
                            @foreach (($filterOptions['categories'] ?? []) as $category)
                                <option value="{{ $category['id'] }}" {{ (string) ($filters['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' }}>{{ $category['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if (!empty($filterOptions['tags']))
                    <div class="fe-form-field fe-filter-tags">
                        <span class="fe-form-label" id="filter-tag-label">Tags</span>
                        <div class="fe-chip-row" role="group" aria-labelledby="filter-tag-label">
                            @foreach ($filterOptions['tags'] as $tag)
                                @php($tagId = (int) ($tag['id'] ?? 0))
                                @php($isActive = in_array($tagId, $selectedTagIds, true))
                                <label data-tag-chip class="fe-chip {{ $isActive ? 'is-active' : '' }}" aria-pressed="{{ $isActive ? 'true' : 'false' }}">
                                    <input data-tag-checkbox type="checkbox" name="tag_ids[]" value="{{ $tagId }}" class="fe-sr-only" {{ $isActive ? 'checked' : '' }}>
                                    <span>{{ $tag['name'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="fe-filter-actions">
                    <button type="submit" class="fe-btn fe-btn-primary">Filter</button>
                    <a href="{{ url('product') }}" class="fe-btn fe-btn-ghost">Clear</a>
                </div>
            </form>

            @if (empty($products))
                <div class="fe-empty-state">
                    <span class="material-symbols-outlined" aria-hidden="true">package_2</span>
                    <p class="fe-body">目前沒有符合條件的商品。</p>
                    <a href="{{ url('product') }}" class="fe-link-arrow">清除篩選 <span class="material-symbols-outlined" aria-hidden="true">arrow_outward</span></a>
                </div>
            @else
                <div class="fe-product-grid">
                    @foreach ($products as $product)
                        <article class="fe-product">
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
                                        @if (!empty($product['start_at_display']))
                                            <p class="fe-product-date">{{ $product['start_at_display'] }}</p>
                                        @endif
                                        <h2 class="fe-product-name">{{ $product['name'] }}</h2>
                                        <p class="fe-product-cat">{{ $product['category_name'] }}</p>
                                        @if (!empty($product['tag_names']))
                                            <p class="fe-product-tags">{{ implode(' / ', $product['tag_names']) }}</p>
                                        @endif
                                    </div>
                                    <p class="fe-product-price">{{ $product['price_display'] }}</p>
                                </div>
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
