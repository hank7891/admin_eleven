@extends('layouts.admin')

@section('title-suffix', empty($data['id']) ? ' · 新增商品' : ' · 編輯商品')

@section('content')
    <x-admin.page-head
        title="{{ empty($data['id']) ? '新增商品' : '編輯商品' }}"
        subtitle="管理商品名稱、圖片、價格與檔期"
        :breadcrumbs="[
            ['label' => '首頁', 'url' => 'admin/'],
            ['label' => '商品管理', 'url' => 'admin/product/list'],
            ['label' => empty($data['id']) ? '新增' : '編輯'],
        ]"
    >
        <x-slot:actions>
            <a href="{{ asset('admin/product/list') }}" class="admin-btn admin-btn-outline">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                <span>返回列表</span>
            </a>
            <button type="submit" form="productEditForm" class="admin-btn admin-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">save</span>
                <span>儲存</span>
            </button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.form-error :errors="$errors" />

    <form id="productEditForm" action="{{ asset('admin/product/edit') }}" method="POST" enctype="multipart/form-data" class="admin-form-grid">
        @csrf
        <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

        {{-- 左欄：主要資訊 --}}
        <div class="admin-form-main admin-stack admin-stack-lg">
            <x-admin.card title="基本資訊">
                <div class="admin-stack">
                    <x-admin.input
                        name="name"
                        label="商品名稱"
                        :value="old('name', $data['name'] ?? '')"
                        required
                        :error="$errors->first('name')"
                    />
                    <x-admin.input
                        name="tagline"
                        label="標語"
                        :value="old('tagline', $data['tagline'] ?? '')"
                        :error="$errors->first('tagline')"
                    />
                    <div class="admin-form-row">
                        <x-admin.input
                            name="price"
                            type="number"
                            label="價格（台幣）"
                            :value="old('price', $data['price'] ?? 0)"
                            required
                            :error="$errors->first('price')"
                            min="0"
                        />
                        <x-admin.input
                            name="sort_order"
                            type="number"
                            label="排序"
                            :value="old('sort_order', $data['sort_order'] ?? 0)"
                            :error="$errors->first('sort_order')"
                            min="0"
                        />
                    </div>
                    <x-admin.textarea
                        name="description"
                        label="商品描述"
                        :value="old('description', $data['description'] ?? '')"
                        :rows="6"
                        required
                        :error="$errors->first('description')"
                    />
                </div>
            </x-admin.card>

            <x-admin.card title="商品圖片">
                <div class="admin-stack">
                    <div class="admin-form-row">
                        <x-admin.input
                            name="primary_new_index"
                            type="number"
                            label="新圖主圖 index"
                            placeholder="0 開始"
                            min="0"
                        />
                        <div class="admin-field">
                            <label class="admin-label" for="images-input">新增圖片（最多 {{ PRODUCT_MAX_IMAGES }} 張）</label>
                            <input id="images-input" type="file" name="images[]" multiple accept="image/*" class="admin-file-input">
                        </div>
                    </div>

                    @if (!empty($data['images']))
                        <div class="admin-product-images">
                            @foreach ($data['images'] as $image)
                                <div class="admin-product-image-row">
                                    <img src="{{ $image['image_url'] }}" alt="{{ $image['image_alt'] ?? '' }}" class="admin-product-image-thumb">
                                    <div class="admin-product-image-meta">
                                        <p class="admin-text-sm">圖片 ID：{{ $image['id'] }}</p>
                                        <input type="hidden" name="kept_ids[]" value="{{ $image['id'] }}">
                                        <label class="admin-checkbox-row">
                                            <input type="radio" name="primary_id" value="{{ $image['id'] }}" {{ (int) ($image['is_primary'] ?? 0) === 1 ? 'checked' : '' }}>
                                            <span class="admin-checkbox-label admin-text-sm">設為主圖</span>
                                        </label>
                                        <label class="admin-checkbox-row">
                                            <input type="checkbox" name="deleted_ids[]" value="{{ $image['id'] }}" class="admin-checkbox">
                                            <span class="admin-checkbox-label admin-text-sm">刪除此圖</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </x-admin.card>

            <x-admin.card title="檔期">
                <div class="admin-form-row">
                    <x-admin.input
                        name="start_at"
                        type="datetime-local"
                        label="開始時間"
                        :value="old('start_at', $data['start_at_input'] ?? '')"
                        required
                        :error="$errors->first('start_at')"
                    />
                    <x-admin.input
                        name="end_at"
                        type="datetime-local"
                        label="結束時間"
                        :value="old('end_at', $data['end_at_input'] ?? '')"
                        hint="留空代表永久"
                        :error="$errors->first('end_at')"
                    />
                </div>
            </x-admin.card>
        </div>

        {{-- 右欄：分類 / 狀態 / 操作 --}}
        <div class="admin-form-aside admin-stack admin-stack-lg">
            <x-admin.card title="上下架狀態">
                @if (empty($data['id']))
                    <p class="admin-help">新增後才可於編輯模式切換為啟用</p>
                    <input type="hidden" name="is_active" value="{{ STATUS_INACTIVE }}">
                    <input type="hidden" name="status_key" value="{{ PRODUCT_STATUS_OFFLINE }}">
                @else
                    <x-admin.select
                        name="status_key"
                        label="狀態"
                        :options="$options['statuses'] ?? []"
                        :value="old('status_key', $data['status_key'] ?? PRODUCT_STATUS_OFFLINE)"
                        :error="$errors->first('status_key')"
                    />
                    <input type="hidden" name="is_active" value="{{ $data['is_active'] ?? STATUS_INACTIVE }}">
                @endif
            </x-admin.card>

            <x-admin.card title="分類與標籤">
                <x-admin.select
                    name="category_id"
                    label="類別"
                    :options="collect($options['categories'] ?? [])->pluck('name', 'id')->all()"
                    :value="old('category_id', $data['category_id'] ?? '')"
                    placeholder="未分類"
                />
                <x-admin.select
                    name="is_featured"
                    label="主打狀態"
                    :options="$options['featured_options'] ?? []"
                    :value="old('is_featured', $data['is_featured'] ?? PRODUCT_FEATURED_OFF)"
                />

                <div class="admin-field">
                    <span class="admin-label">標籤（可複選）</span>
                    @if (!empty($options['tags']))
                        <div class="admin-tag-row">
                            @foreach ($options['tags'] as $tag)
                                <label class="admin-checkbox-row admin-checkbox-pill">
                                    <input type="checkbox" name="tag_ids[]" value="{{ $tag['id'] }}" {{ in_array((int) $tag['id'], $data['tag_ids'] ?? [], true) ? 'checked' : '' }}>
                                    <span class="admin-checkbox-label">{{ $tag['name'] }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <a href="{{ url('admin/product.tag/list') }}" class="admin-link">尚未建立標籤，前往管理</a>
                    @endif
                </div>
            </x-admin.card>

            @if (!empty($data['id']))
                <x-admin.card title="危險區">
                    <p class="admin-help">刪除後無法復原。</p>
                </x-admin.card>
            @endif
        </div>
    </form>

    @if (!empty($data['id']))
        <form action="{{ asset('admin/product/delete/' . $data['id']) }}" method="POST" class="admin-form-delete" onsubmit="return confirm('確定要刪除此商品嗎？')">
            @csrf
            <button type="submit" class="admin-btn admin-btn-danger">
                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                <span>刪除商品</span>
            </button>
        </form>
    @endif
@endsection
