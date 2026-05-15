@extends('layouts.admin')

@php($isCreate = empty($data['id']))

@section('title-suffix', $isCreate ? ' · 新增輪播' : ' · 編輯輪播')

@section('content')
    <x-admin.page-head
        title="{{ $isCreate ? '新增輪播' : '編輯輪播' }}"
        subtitle="維護首頁主視覺輪播圖片、文案與點擊連結設定"
        :breadcrumbs="[
            ['label' => '首頁', 'url' => 'admin/'],
            ['label' => '輪播管理', 'url' => 'admin/hero-slide/list'],
            ['label' => $isCreate ? '新增' : '編輯'],
        ]"
    >
        <x-slot:actions>
            <a href="{{ asset('admin/hero-slide/list') }}" class="admin-btn admin-btn-outline">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                <span>返回列表</span>
            </a>
            <button type="submit" form="hero-slide-edit-form" class="admin-btn admin-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">save</span>
                <span>儲存</span>
            </button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.form-error :errors="$errors" />

    <form id="hero-slide-edit-form" action="{{ asset('admin/hero-slide/edit') }}" method="POST" enctype="multipart/form-data" class="admin-form-grid">
        @csrf
        <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

        <div class="admin-form-main admin-stack admin-stack-lg">
            <x-admin.card title="輪播內容">
                <div class="admin-stack">
                    <div class="admin-field">
                        <label class="admin-label" for="image">輪播圖片 <span class="admin-required" aria-hidden="true">*</span></label>
                        <label for="image" data-upload-dropzone class="admin-dropzone">
                            <span class="material-symbols-outlined" aria-hidden="true">upload</span>
                            <div>
                                <p class="admin-strong">點擊上傳或拖曳圖片至此</p>
                                <p class="admin-help">建議尺寸 1920x1080（16:9 橫式），支援 jpg / jpeg / png / gif / webp，最大 {{ round((config('upload.image.max_size', 5120)) / 1024, 2) }}MB</p>
                            </div>
                        </label>
                        <input id="image" type="file" name="image" accept="image/*" class="admin-sr-only">

                        <div class="admin-image-preview-wrap">
                            <img id="imagePreview" src="{{ $data['image_url'] ?? '' }}" alt="輪播預覽" class="admin-image-preview {{ empty($data['image_url']) ? 'is-hidden' : '' }}">
                            <div id="imagePlaceholder" class="admin-image-placeholder {{ empty($data['image_url']) ? '' : 'is-hidden' }}">
                                <span class="material-symbols-outlined" aria-hidden="true">image</span>
                                <p class="admin-help">16:9 預覽比例</p>
                            </div>
                        </div>
                    </div>

                    <div class="admin-form-row">
                        <x-admin.input
                            name="eyebrow"
                            label="眉標"
                            :value="old('eyebrow', $data['eyebrow'] ?? '')"
                            placeholder="例如 Spring / Summer 2026"
                        />
                        <x-admin.input
                            name="image_alt"
                            label="圖片替代文字"
                            :value="old('image_alt', $data['image_alt'] ?? '')"
                            placeholder="描述圖片內容，提升可存取性"
                        />
                        <x-admin.input
                            name="title"
                            label="主標語"
                            :value="old('title', $data['title'] ?? '')"
                            placeholder="請輸入首頁主標語"
                            required
                            :error="$errors->first('title')"
                            class="admin-field-full"
                        />
                        <x-admin.textarea
                            name="description"
                            label="說明文字"
                            :value="old('description', $data['description'] ?? '')"
                            :rows="4"
                            placeholder="補充主視覺說明，最多 500 字"
                            class="admin-field-full"
                        />
                    </div>
                </div>
            </x-admin.card>

            <x-admin.card title="連結與排序設定">
                <div class="admin-form-row">
                    <x-admin.input
                        name="target_url"
                        type="url"
                        label="輪播點擊連結"
                        :value="old('target_url', $data['target_url'] ?? '')"
                        placeholder="https://example.com 或 /product"
                        hint="前台點擊輪播圖片將導向此連結，留空則不啟用點擊導頁"
                        class="admin-field-full"
                    />
                    <div class="admin-field">
                        <label class="admin-label" for="admin-input-sort_order">排序 <span class="admin-required" aria-hidden="true">*</span></label>
                        <input id="admin-input-sort_order" type="number" name="sort_order" value="{{ old('sort_order', $data['sort_order'] ?? 0) }}" required aria-required="true" min="0" class="admin-input">
                        <p class="admin-help">數字越小越前面</p>
                        @if ($errors->first('sort_order'))
                            <p class="admin-help is-error" role="alert">{{ $errors->first('sort_order') }}</p>
                        @endif
                    </div>
                    @if ($isCreate)
                        <div class="admin-field">
                            <label class="admin-label">狀態 <span class="admin-required" aria-hidden="true">*</span></label>
                            <input type="hidden" name="is_active" value="{{ STATUS_INACTIVE }}">
                            <input type="text" value="{{ config('constants.status.' . STATUS_INACTIVE) }}" class="admin-input is-readonly" readonly>
                            <p class="admin-help">新增後才可於編輯模式切換為啟用</p>
                        </div>
                    @else
                        <x-admin.select
                            name="is_active"
                            label="狀態"
                            :options="config('constants.status')"
                            :value="old('is_active', $data['is_active'] ?? STATUS_ACTIVE)"
                            required
                        />
                    @endif
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
                        hint="留空表示永久顯示"
                    />
                </div>
            </x-admin.card>
        </div>

        <div class="admin-form-aside admin-stack admin-stack-lg">
            <x-admin.card title="操作">
                <p class="admin-help">儲存後會寫入操作日誌。</p>
            </x-admin.card>
        </div>
    </form>

    @if (!empty($data['id']))
        <form action="{{ asset('admin/hero-slide/delete/' . $data['id']) }}" method="POST" class="admin-form-delete" onsubmit="return confirm('確定要刪除此輪播嗎？')">
            @csrf
            <x-admin.card title="危險區">
                <p class="admin-help">刪除後無法復原。</p>
                <button type="submit" class="admin-btn admin-btn-danger">
                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                    <span>刪除此輪播</span>
                </button>
            </x-admin.card>
        </form>
    @endif
@endsection

@push('scripts')
    <script>
        (function () {
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const imagePlaceholder = document.getElementById('imagePlaceholder');
            const dropzone = document.querySelector('[data-upload-dropzone]');

            if (!imageInput || !imagePreview || !imagePlaceholder || !dropzone) return;

            const showPreview = (file) => {
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function (loadEvent) {
                    imagePreview.src = loadEvent.target?.result || '';
                    imagePreview.classList.remove('is-hidden');
                    imagePlaceholder.classList.add('is-hidden');
                };
                reader.readAsDataURL(file);
            };

            imageInput.addEventListener('change', (event) => {
                const file = event.target.files?.[0];
                if (file) showPreview(file);
            });

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName) => {
                dropzone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                });
            });

            ['dragenter', 'dragover'].forEach((eventName) => {
                dropzone.addEventListener(eventName, () => dropzone.classList.add('is-dragover'));
            });

            ['dragleave', 'drop'].forEach((eventName) => {
                dropzone.addEventListener(eventName, () => dropzone.classList.remove('is-dragover'));
            });

            dropzone.addEventListener('drop', (event) => {
                const file = event.dataTransfer?.files?.[0];
                if (!file || !file.type.startsWith('image/')) return;
                const transfer = new DataTransfer();
                transfer.items.add(file);
                imageInput.files = transfer.files;
                imageInput.dispatchEvent(new Event('change', { bubbles: true }));
            });
        })();
    </script>
@endpush
