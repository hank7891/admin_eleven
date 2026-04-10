@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '輪播管理', 'url' => 'admin/hero-slide/list'], ['label' => empty($data['id']) ? '新增' : '編輯']]" />
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">{{ empty($data['id']) ? '新增輪播' : '編輯輪播' }}</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">維護首頁主視覺輪播圖片、文案與按鈕設定</p>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6">
                <form id="hero-slide-edit-form" action="{{ asset('admin/hero-slide/edit') }}" method="POST" enctype="multipart/form-data" class="w-full lg:w-2/3 space-y-6">
                    @csrf
                    <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

                    <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border-t-4 border-t-primary">
                        <div class="p-6 border-b border-outline-variant/20">
                            <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">slideshow</span>
                                輪播內容
                            </h3>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="space-y-2">
                                <label class="block text-[0.875rem] font-medium text-on-surface-variant">輪播圖片 <span class="text-error">*</span></label>
                                <label for="image" data-upload-dropzone class="block rounded-xl border border-dashed border-outline-variant/40 bg-surface-container-low px-6 py-6 cursor-pointer hover:border-primary/50 transition-colors">
                                    <div class="flex flex-col items-center justify-center gap-3 text-center">
                                        <span class="material-symbols-outlined text-[32px] text-primary">upload</span>
                                        <div>
                                            <p class="text-[0.875rem] font-semibold text-on-surface">點擊上傳或拖曳圖片至此</p>
                                            <p class="mt-1 text-[0.75rem] text-outline-variant">建議尺寸 1200x1500（4:5 直式），支援 jpg / jpeg / png / gif / webp，最大 {{ round((config('upload.image.max_size', 5120)) / 1024, 2) }}MB</p>
                                        </div>
                                    </div>
                                </label>
                                <input id="image" type="file" name="image" accept="image/*" class="hidden">
                                <div class="rounded-xl bg-surface-container-low p-4">
                                    <div class="mx-auto w-full max-w-xs overflow-hidden rounded-lg bg-surface-container-high aspect-[4/5]">
                                        <img id="imagePreview" src="{{ $data['image_url'] ?? '' }}" alt="輪播預覽" class="{{ empty($data['image_url']) ? 'hidden ' : '' }}h-full w-full object-cover">
                                        <div id="imagePlaceholder" class="{{ empty($data['image_url']) ? '' : 'hidden ' }}flex h-full w-full items-center justify-center text-outline-variant">
                                            <div class="text-center">
                                                <span class="material-symbols-outlined text-[32px]">image</span>
                                                <p class="mt-2 text-[0.75rem]">4:5 預覽比例</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">眉標</label>
                                    <input type="text" name="eyebrow" value="{{ $data['eyebrow'] ?? '' }}" placeholder="例如 Spring / Summer 2026" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">圖片替代文字</label>
                                    <input type="text" name="image_alt" value="{{ $data['image_alt'] ?? '' }}" placeholder="描述圖片內容，提升可存取性" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                </div>
                                <div class="space-y-1.5 md:col-span-2">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">主標語 <span class="text-error">*</span></label>
                                    <input type="text" name="title" value="{{ $data['title'] ?? '' }}" placeholder="請輸入首頁主標語" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                </div>
                                <div class="space-y-1.5 md:col-span-2">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">說明文字</label>
                                    <textarea name="description" rows="4" placeholder="補充主視覺說明，最多 500 字" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-3 text-[0.875rem] leading-7 text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">{{ $data['description'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border-t-4 border-t-secondary">
                        <div class="p-6 border-b border-outline-variant/20">
                            <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline flex items-center gap-2">
                                <span class="material-symbols-outlined text-secondary">ads_click</span>
                                按鈕與排序設定
                            </h3>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">主按鈕文字</label>
                                    <input type="text" name="primary_cta_label" value="{{ $data['primary_cta_label'] ?? '' }}" placeholder="例如 探索本季精選" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">主按鈕連結</label>
                                    <input type="url" name="primary_cta_url" value="{{ $data['primary_cta_url'] ?? '' }}" placeholder="https://example.com 或 /#products" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">次按鈕文字</label>
                                    <input type="text" name="secondary_cta_label" value="{{ $data['secondary_cta_label'] ?? '' }}" placeholder="例如 閱讀品牌日誌" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">次按鈕連結</label>
                                    <input type="url" name="secondary_cta_url" value="{{ $data['secondary_cta_url'] ?? '' }}" placeholder="https://example.com 或 /#journal" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">排序 <span class="text-error">*</span></label>
                                    <input type="number" min="0" name="sort_order" value="{{ $data['sort_order'] ?? 0 }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                    <p class="text-[0.75rem] text-outline-variant">數字越小越前面</p>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">狀態 <span class="text-error">*</span></label>
                                    <select name="is_active" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                        @foreach (config('constants.status') as $key => $label)
                                            <option value="{{ $key }}" {{ (string) ($data['is_active'] ?? STATUS_ACTIVE) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">開始時間 <span class="text-error">*</span></label>
                                    <input type="datetime-local" name="start_at" value="{{ $data['start_at_input'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">結束時間</label>
                                    <input type="datetime-local" name="end_at" value="{{ $data['end_at_input'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                    <p class="text-[0.75rem] text-outline-variant">留空表示永久顯示</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="w-full lg:w-1/3 space-y-6">
                    <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden sticky top-4 p-6 space-y-4">
                        <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">bolt</span>
                            操作面板
                        </h3>
                        <button type="submit" form="hero-slide-edit-form" class="w-full py-3 btn-primary rounded-xl font-bold text-[0.875rem] flex items-center justify-center gap-2 hover:scale-[1.02] active:scale-95 transition-all duration-200">
                            <span class="material-symbols-outlined text-[20px]">save</span>
                            儲存
                        </button>
                        <a href="{{ asset('admin/hero-slide/list') }}" class="w-full flex items-center justify-center gap-2 text-on-surface-variant hover:text-primary text-[0.875rem] font-medium transition-colors py-2 no-underline">
                            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                            返回列表
                        </a>
                    </div>

                    @if(!empty($data['id']))
                        <div class="bg-error-container/30 rounded-xl border border-error/20 p-6 space-y-4">
                            <h3 class="text-[0.9375rem] font-semibold text-error flex items-center gap-2">
                                <span class="material-symbols-outlined">warning</span>
                                危險操作
                            </h3>
                            <form action="{{ asset('admin/hero-slide/delete/' . $data['id']) }}" method="POST" onsubmit="return confirm('確定要刪除此輪播嗎？')">
                                @csrf
                                <button type="submit" class="w-full py-2.5 bg-error text-on-error rounded-xl font-bold text-[0.875rem] flex items-center justify-center gap-2 hover:opacity-90 active:scale-95 transition-all">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                    刪除此輪播
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const imagePlaceholder = document.getElementById('imagePlaceholder');
            const dropzone = document.querySelector('[data-upload-dropzone]');

            if (!imageInput || !imagePreview || !imagePlaceholder || !dropzone) {
                return;
            }

            const showPreview = (file) => {
                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (loadEvent) {
                    imagePreview.src = loadEvent.target?.result || '';
                    imagePreview.classList.remove('hidden');
                    imagePlaceholder.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            };

            imageInput.addEventListener('change', function (event) {
                const file = event.target.files?.[0];

                if (!file) {
                    return;
                }

                showPreview(file);
            });

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName) => {
                dropzone.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                });
            });

            ['dragenter', 'dragover'].forEach((eventName) => {
                dropzone.addEventListener(eventName, function () {
                    dropzone.classList.add('is-dragover', 'border-primary', 'bg-primary/5');
                });
            });

            ['dragleave', 'drop'].forEach((eventName) => {
                dropzone.addEventListener(eventName, function () {
                    dropzone.classList.remove('is-dragover', 'border-primary', 'bg-primary/5');
                });
            });

            dropzone.addEventListener('drop', function (event) {
                const file = event.dataTransfer?.files?.[0];

                if (!file || !file.type.startsWith('image/')) {
                    return;
                }

                const transfer = new DataTransfer();
                transfer.items.add(file);
                imageInput.files = transfer.files;
                imageInput.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    </script>
    @endpush
@endsection

