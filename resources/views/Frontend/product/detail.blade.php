@extends('layouts.frontend')

@section('fe-active', 'product')

@section('meta_description', e($data['meta_description'] ?? ''))

@section('content')
    <section class="fe-section fe-product-detail">
        <div class="fe-container">
            <nav class="fe-crumbs" aria-label="麵包屑">
                <a href="{{ url('/') }}">首頁</a>
                <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                <a href="{{ url('product') }}">商品</a>
                <span class="material-symbols-outlined" aria-hidden="true">chevron_right</span>
                <span aria-current="page">{{ $data['name'] }}</span>
            </nav>

            <div class="fe-product-hero">
                {{-- Gallery --}}
                <div class="fe-product-gallery">
                    @php($mainImage = collect($data['images'] ?? [])->firstWhere('is_primary', 1) ?? (($data['images'] ?? [])[0] ?? null))
                    @if (!empty($mainImage))
                        <button type="button" class="fe-product-main-btn" data-open-product-image-dialog aria-label="開啟商品大圖">
                            <img
                                id="productMainImage"
                                src="{{ $mainImage['image_url'] }}"
                                alt="{{ $mainImage['image_alt'] ?: $data['name'] }}"
                                class="fe-product-main-img"
                                data-main-image
                            >
                        </button>
                    @else
                        <div class="fe-product-main-empty" role="img" aria-label="尚無商品圖片">
                            <span class="material-symbols-outlined" aria-hidden="true">image</span>
                            <p class="fe-body">目前尚無商品圖片</p>
                        </div>
                    @endif

                    @if (!empty($data['images']) && count($data['images']) > 1)
                        <div class="fe-thumb-row" role="tablist" aria-label="商品圖片切換">
                            @foreach ($data['images'] as $index => $image)
                                @php($isActive = ($image['is_primary'] ?? 0) === 1)
                                <button
                                    type="button"
                                    class="fe-thumb {{ $isActive ? 'is-active' : '' }}"
                                    data-thumb
                                    data-index="{{ $index }}"
                                    data-image-url="{{ $image['image_url'] }}"
                                    data-image-alt="{{ $image['image_alt'] ?: $data['name'] }}"
                                    role="tab"
                                    aria-selected="{{ $isActive ? 'true' : 'false' }}"
                                    aria-label="切換商品圖片 {{ $index + 1 }}"
                                >
                                    <img src="{{ $image['image_url'] }}" alt="{{ $image['image_alt'] ?: $data['name'] }}">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    @if (!empty($mainImage))
                        <dialog id="productImageDialog" class="fe-product-dialog">
                            <div class="fe-product-dialog-inner">
                                <button type="button" class="fe-product-dialog-close" data-close-product-image-dialog aria-label="關閉商品大圖">
                                    <span class="material-symbols-outlined" aria-hidden="true">close</span>
                                </button>
                                <img id="productDialogImage" src="{{ $mainImage['image_url'] ?? '' }}" alt="{{ $mainImage['image_alt'] ?? ($data['name'] ?? '') }}" class="fe-product-dialog-img">
                            </div>
                        </dialog>
                    @endif
                </div>

                {{-- Meta --}}
                <div class="fe-product-meta-stack">
                    <div>
                        <span class="fe-eyebrow">Product Detail</span>
                        <h1 class="fe-h1 fe-product-title">{{ $data['name'] }}</h1>
                        @if (!empty($data['tagline']))
                            <p class="fe-body fe-product-tagline">{{ $data['tagline'] }}</p>
                        @endif
                    </div>

                    <div>
                        <span class="fe-sr-only">售價</span>
                        <span class="fe-price-large">{{ $data['price_display'] }}</span>
                    </div>

                    <dl class="fe-product-meta-list">
                        <dt class="fe-meta">類別</dt>
                        <dd>{{ $data['category_name'] }}</dd>
                        @if (!empty($data['tags']))
                            <dt class="fe-meta">標籤</dt>
                            <dd>{{ implode(' / ', $data['tags']) }}</dd>
                        @endif
                    </dl>

                    <article class="fe-product-description">
                        <h2 class="fe-eyebrow">Description</h2>
                        <div class="fe-product-description-body">{!! $data['description_html'] !!}</div>
                    </article>
                </div>
            </div>
        </div>
    </section>

    @if (!empty($relatedProducts))
        <section class="fe-section fe-section-soft" aria-label="相關商品">
            <div class="fe-container">
                <div class="fe-section-head">
                    <div>
                        <span class="fe-eyebrow">You may also like</span>
                        <h2 class="fe-h2 fe-section-head-title">相關商品</h2>
                    </div>
                    <a href="{{ url('product') }}" class="fe-link-arrow">
                        View All Products
                        <span class="material-symbols-outlined" aria-hidden="true">arrow_outward</span>
                    </a>
                </div>

                <div class="fe-product-grid">
                    @foreach ($relatedProducts as $product)
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
@endsection
