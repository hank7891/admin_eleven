@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '關於我們'] ]" />
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">關於我們設定</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">維護前台 About 頁的 Hero、故事、使命願景與聯絡資訊</p>
                </div>
            </div>

            <div class="bg-surface-container-low rounded-xl border border-outline-variant/20 p-5 text-[0.82rem] text-on-surface/75">
                <p>最後編輯者：{{ $data['updater_name'] ?? '' ?: '尚無資料' }}</p>
                <p class="mt-1">最後更新時間：{{ $data['updated_at_display'] ?? '' ?: '尚無資料' }}</p>
            </div>

            <form id="about-edit-form" action="{{ asset('admin/about/edit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <fieldset class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border-t-4 border-t-primary">
                    <legend class="sr-only">Hero 區塊</legend>
                    <div class="p-6 border-b border-outline-variant/20">
                        <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline">Hero 區塊</h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="block text-[0.875rem] font-medium text-on-surface-variant">主標題 <span class="text-error">*</span></label>
                            <input type="text" name="hero_title" value="{{ $data['hero_title'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="block text-[0.875rem] font-medium text-on-surface-variant">副標題</label>
                            <input type="text" name="hero_subtitle" value="{{ $data['hero_subtitle'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="block text-[0.875rem] font-medium text-on-surface-variant">Hero 圖片</label>
                            <input type="file" name="hero_image" accept="image/*" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                            <p class="text-[0.75rem] text-outline-variant">支援 jpg / jpeg / png / gif / webp，最大 {{ round((config('upload.image.max_size', 5120)) / 1024, 2) }}MB</p>
                            <p class="text-[0.75rem] text-outline-variant">建議尺寸：1200 x 1500（4:5），可避免前台顯示裁切或留白。</p>
                            <input type="hidden" name="remove_hero_image" value="0">
                            <label class="inline-flex items-center gap-2 text-[0.8rem] text-on-surface/75">
                                <input type="checkbox" name="remove_hero_image" value="1" class="rounded border-outline-variant/50">
                                移除目前圖片
                            </label>
                        </div>
                        @if (!empty($data['hero_image_url']))
                            <div class="md:col-span-2 rounded-xl overflow-hidden bg-surface-container-low border border-outline-variant/25 max-w-xl">
                                <img src="{{ $data['hero_image_url'] }}" alt="Hero 目前圖片" class="w-full h-auto object-cover">
                            </div>
                        @endif
                    </div>
                </fieldset>

                <fieldset class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden">
                    <legend class="sr-only">品牌故事</legend>
                    <div class="p-6 border-b border-outline-variant/20">
                        <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline">品牌故事</h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 gap-6">
                        <div class="space-y-1.5">
                            <label class="block text-[0.875rem] font-medium text-on-surface-variant">標題 <span class="text-error">*</span></label>
                            <input type="text" name="story_title" value="{{ $data['story_title'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[0.875rem] font-medium text-on-surface-variant">內容 <span class="text-error">*</span></label>
                            <textarea name="story_content" rows="6" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-3 text-[0.875rem] text-on-surface leading-7">{{ $data['story_content'] ?? '' }}</textarea>
                        </div>
                    </div>
                </fieldset>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <fieldset class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden">
                        <legend class="sr-only">使命</legend>
                        <div class="p-6 border-b border-outline-variant/20">
                            <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline">使命</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <input type="text" name="mission_title" value="{{ $data['mission_title'] ?? '' }}" placeholder="標題（可留空）" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                            <textarea name="mission_content" rows="5" placeholder="內容（可留空）" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-3 text-[0.875rem] text-on-surface leading-7">{{ $data['mission_content'] ?? '' }}</textarea>
                        </div>
                    </fieldset>

                    <fieldset class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden">
                        <legend class="sr-only">願景</legend>
                        <div class="p-6 border-b border-outline-variant/20">
                            <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline">願景</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <input type="text" name="vision_title" value="{{ $data['vision_title'] ?? '' }}" placeholder="標題（可留空）" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                            <textarea name="vision_content" rows="5" placeholder="內容（可留空）" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-3 text-[0.875rem] text-on-surface leading-7">{{ $data['vision_content'] ?? '' }}</textarea>
                        </div>
                    </fieldset>
                </div>

                <fieldset class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden">
                    <legend class="sr-only">聯絡資訊與 SEO</legend>
                    <div class="p-6 border-b border-outline-variant/20">
                        <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline">聯絡資訊與 SEO</h3>
                    </div>
                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="block text-[0.875rem] font-medium text-on-surface-variant">聯絡 Email</label>
                            <input type="email" name="contact_email" value="{{ $data['contact_email'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[0.875rem] font-medium text-on-surface-variant">聯絡電話</label>
                            <input type="text" name="contact_phone" value="{{ $data['contact_phone'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="block text-[0.875rem] font-medium text-on-surface-variant">聯絡地址</label>
                            <input type="text" name="contact_address" value="{{ $data['contact_address'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="block text-[0.875rem] font-medium text-on-surface-variant">Meta Description</label>
                            <textarea name="meta_description" rows="3" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-3 text-[0.875rem] text-on-surface">{{ $data['meta_description'] ?? '' }}</textarea>
                        </div>
                    </div>
                </fieldset>

                <div class="flex items-center justify-end">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl btn-primary px-8 py-3 text-[0.875rem] font-bold">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        儲存設定
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection


