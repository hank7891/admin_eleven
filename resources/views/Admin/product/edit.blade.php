@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            <div>
                <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '商品管理', 'url' => 'admin/product/list'], ['label' => empty($data['id']) ? '新增' : '編輯']]" />
                <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">{{ empty($data['id']) ? '新增商品' : '編輯商品' }}</h2>
            </div>

            <form action="{{ asset('admin/product/edit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl p-6 space-y-5">
                        <div>
                            <label class="text-sm font-medium">商品名稱 <span class="text-error">*</span></label>
                            <input type="text" name="name" value="{{ $data['name'] ?? '' }}" class="mt-2 w-full bg-surface-container-low rounded-lg border-none px-4 py-3">
                        </div>

                        <div>
                            <label class="text-sm font-medium">標語</label>
                            <input type="text" name="tagline" value="{{ $data['tagline'] ?? '' }}" class="mt-2 w-full bg-surface-container-low rounded-lg border-none px-4 py-3">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium">價格（台幣） <span class="text-error">*</span></label>
                                <input type="number" min="0" name="price" value="{{ $data['price'] ?? 0 }}" class="mt-2 w-full bg-surface-container-low rounded-lg border-none px-4 py-3">
                            </div>
                            <div>
                                <label class="text-sm font-medium">排序</label>
                                <input type="number" min="0" name="sort_order" value="{{ $data['sort_order'] ?? 0 }}" class="mt-2 w-full bg-surface-container-low rounded-lg border-none px-4 py-3">
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium">商品描述 <span class="text-error">*</span></label>
                            <textarea name="description" rows="6" class="mt-2 w-full bg-surface-container-low rounded-lg border-none px-4 py-3">{{ $data['description'] ?? '' }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium">類別</label>
                                <select name="category_id" class="mt-2 w-full bg-surface-container-low rounded-lg border-none px-4 py-3">
                                    <option value="">未分類</option>
                                    @foreach (($options['categories'] ?? []) as $category)
                                        <option value="{{ $category['id'] }}" {{ (string) ($data['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' }}>{{ $category['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="text-sm font-medium">主打狀態</label>
                                <select name="is_featured" class="mt-2 w-full bg-surface-container-low rounded-lg border-none px-4 py-3">
                                    @foreach (($options['featured_options'] ?? []) as $key => $label)
                                        <option value="{{ $key }}" {{ (string) ($data['is_featured'] ?? PRODUCT_FEATURED_OFF) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium">標籤（可複選）</label>
                            <div class="mt-2 flex flex-wrap gap-3">
                                @forelse (($options['tags'] ?? []) as $tag)
                                    <label class="inline-flex items-center gap-2 bg-surface-container-low px-3 py-2 rounded-full text-sm">
                                        <input type="checkbox" name="tag_ids[]" value="{{ $tag['id'] }}" {{ in_array((int) $tag['id'], $data['tag_ids'] ?? [], true) ? 'checked' : '' }}>
                                        <span>{{ $tag['name'] }}</span>
                                    </label>
                                @empty
                                    <a href="{{ url('admin/product.tag/list') }}" class="text-primary no-underline">尚未建立標籤，前往管理</a>
                                @endforelse
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="text-sm font-medium">開始時間 <span class="text-error">*</span></label>
                                <input type="datetime-local" name="start_at" value="{{ $data['start_at_input'] ?? '' }}" class="mt-2 w-full bg-surface-container-low rounded-lg border-none px-4 py-3">
                            </div>
                            <div>
                                <label class="text-sm font-medium">結束時間（留空代表永久）</label>
                                <input type="datetime-local" name="end_at" value="{{ $data['end_at_input'] ?? '' }}" class="mt-2 w-full bg-surface-container-low rounded-lg border-none px-4 py-3">
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium">上下架狀態</label>
                            @if (empty($data['id']))
                                <select name="status_key" class="mt-2 w-full bg-surface-container-low rounded-lg border-none px-4 py-3" disabled>
                                    <option value="{{ PRODUCT_STATUS_OFFLINE }}">下架</option>
                                </select>
                                <input type="hidden" name="status_key" value="{{ PRODUCT_STATUS_OFFLINE }}">
                                <p class="mt-2 text-xs text-outline">儲存後可於編輯頁調整</p>
                            @else
                                <select name="status_key" class="mt-2 w-full bg-surface-container-low rounded-lg border-none px-4 py-3">
                                    @foreach (($options['statuses'] ?? []) as $key => $label)
                                        <option value="{{ $key }}" {{ (string) ($data['status_key'] ?? PRODUCT_STATUS_OFFLINE) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>

                        <div class="border-t border-outline-variant/20 pt-5 space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-sm font-medium">商品圖片（最多 {{ PRODUCT_MAX_IMAGES }} 張）</label>
                                <input type="number" min="0" name="primary_new_index" placeholder="新圖主圖 index" class="w-40 bg-surface-container-low rounded-lg border-none px-3 py-2 text-sm">
                            </div>
                            <input type="file" name="images[]" multiple accept="image/*" class="w-full bg-surface-container-low rounded-lg border-none px-4 py-3">

                            @foreach (($data['images'] ?? []) as $image)
                                <div class="flex items-center gap-3 border border-outline-variant/20 rounded-xl p-3">
                                    <img src="{{ $image['image_url'] }}" alt="{{ $image['image_alt'] ?? '' }}" class="w-16 h-16 rounded-lg object-cover">
                                    <div class="flex-1">
                                        <p class="text-sm">圖片 ID：{{ $image['id'] }}</p>
                                        <input type="hidden" name="kept_ids[]" value="{{ $image['id'] }}">
                                        <label class="text-xs text-outline inline-flex items-center gap-2 mr-4">
                                            <input type="radio" name="primary_id" value="{{ $image['id'] }}" {{ (int) ($image['is_primary'] ?? 0) === 1 ? 'checked' : '' }}>
                                            設為標題圖片
                                        </label>
                                        <label class="text-xs text-outline inline-flex items-center gap-2">
                                            <input type="checkbox" name="deleted_ids[]" value="{{ $image['id'] }}">
                                            刪除此圖
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest rounded-xl p-6 h-fit sticky top-4 space-y-4">
                        <button type="submit" class="w-full btn-primary py-3 rounded-xl">儲存</button>
                        <a href="{{ asset('admin/product/list') }}" class="w-full text-center block py-3 rounded-xl bg-surface-container-high text-on-surface no-underline">返回列表</a>

                        @if (!empty($data['id']))
                            <form action="{{ asset('admin/product/delete/' . $data['id']) }}" method="POST" onsubmit="return confirm('確定要刪除此商品嗎？')">
                                @csrf
                                <button type="submit" class="w-full py-3 rounded-xl bg-error text-on-error">刪除商品</button>
                            </form>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

