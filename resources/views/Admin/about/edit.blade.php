@extends('layouts.admin')

@section('title-suffix', ' · 關於我們')

@section('content')
    <x-admin.page-head
        title="關於我們設定"
        subtitle="維護前台 About 頁的 Hero、故事、使命願景與聯絡資訊"
        :breadcrumbs="[['label' => '首頁', 'url' => 'admin/'], ['label' => '關於我們']]"
    >
        <x-slot:actions>
            <button type="submit" form="about-edit-form" class="admin-btn admin-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">save</span>
                <span>儲存設定</span>
            </button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.card title="編輯紀錄">
        <p class="admin-text-sm">最後編輯者：{{ ($data['updater_name'] ?? '') ?: '尚無資料' }}</p>
        <p class="admin-text-sm admin-text-mute">最後更新時間：{{ ($data['updated_at_display'] ?? '') ?: '尚無資料' }}</p>
    </x-admin.card>

    <x-admin.form-error :errors="$errors" />

    <form id="about-edit-form" action="{{ asset('admin/about/edit') }}" method="POST" enctype="multipart/form-data" class="admin-stack admin-stack-lg">
        @csrf

        <x-admin.card title="Hero 區塊">
            <div class="admin-form-row">
                <x-admin.input
                    name="hero_title"
                    label="主標題"
                    :value="old('hero_title', $data['hero_title'] ?? '')"
                    required
                    :error="$errors->first('hero_title')"
                    class="admin-field-full"
                />
                <x-admin.input
                    name="hero_subtitle"
                    label="副標題"
                    :value="old('hero_subtitle', $data['hero_subtitle'] ?? '')"
                    class="admin-field-full"
                />
                <div class="admin-field admin-field-full">
                    <label class="admin-label" for="hero_image">Hero 圖片</label>
                    <input id="hero_image" type="file" name="hero_image" accept="image/*" class="admin-file-input">
                    <p class="admin-help">支援 jpg / jpeg / png / gif / webp，最大 {{ round((config('upload.image.max_size', 5120)) / 1024, 2) }}MB</p>
                    <p class="admin-help">建議尺寸：1200 x 1500（4:5），可避免前台顯示裁切或留白。</p>
                    <input type="hidden" name="remove_hero_image" value="0">
                    <label class="admin-checkbox-row">
                        <input type="checkbox" name="remove_hero_image" value="1" class="admin-checkbox">
                        <span class="admin-checkbox-label">移除目前圖片</span>
                    </label>
                </div>
                @if (!empty($data['hero_image_url']))
                    <div class="admin-field-full">
                        <div class="admin-image-preview-wrap">
                            <img src="{{ $data['hero_image_url'] }}" alt="Hero 目前圖片" class="admin-image-preview">
                        </div>
                    </div>
                @endif
            </div>
        </x-admin.card>

        <x-admin.card title="品牌故事">
            <x-admin.input
                name="story_title"
                label="標題"
                :value="old('story_title', $data['story_title'] ?? '')"
                required
                :error="$errors->first('story_title')"
            />
            <x-admin.textarea
                name="story_content"
                label="內容"
                :value="old('story_content', $data['story_content'] ?? '')"
                :rows="6"
                required
                :error="$errors->first('story_content')"
            />
        </x-admin.card>

        <div class="admin-form-mv-grid">
            <x-admin.card title="使命">
                <x-admin.input
                    name="mission_title"
                    label="標題（可留空）"
                    :value="old('mission_title', $data['mission_title'] ?? '')"
                />
                <x-admin.textarea
                    name="mission_content"
                    label="內容（可留空）"
                    :value="old('mission_content', $data['mission_content'] ?? '')"
                    :rows="5"
                />
            </x-admin.card>

            <x-admin.card title="願景">
                <x-admin.input
                    name="vision_title"
                    label="標題（可留空）"
                    :value="old('vision_title', $data['vision_title'] ?? '')"
                />
                <x-admin.textarea
                    name="vision_content"
                    label="內容（可留空）"
                    :value="old('vision_content', $data['vision_content'] ?? '')"
                    :rows="5"
                />
            </x-admin.card>
        </div>

        <x-admin.card title="聯絡資訊與 SEO">
            <div class="admin-form-row">
                <x-admin.input
                    name="contact_email"
                    type="email"
                    label="聯絡 Email"
                    :value="old('contact_email', $data['contact_email'] ?? '')"
                />
                <x-admin.input
                    name="contact_phone"
                    label="聯絡電話"
                    :value="old('contact_phone', $data['contact_phone'] ?? '')"
                />
                <x-admin.input
                    name="contact_address"
                    label="聯絡地址"
                    :value="old('contact_address', $data['contact_address'] ?? '')"
                    class="admin-field-full"
                />
                <x-admin.textarea
                    name="meta_description"
                    label="Meta Description"
                    :value="old('meta_description', $data['meta_description'] ?? '')"
                    :rows="3"
                    class="admin-field-full"
                />
            </div>
        </x-admin.card>
    </form>
@endsection
