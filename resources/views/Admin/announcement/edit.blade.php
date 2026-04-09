@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '公告管理', 'url' => 'admin/announcement/list'], ['label' => empty($data['id']) ? '新增' : '編輯']]" />
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">{{ empty($data['id']) ? '新增公告' : '編輯公告' }}</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">設定公告類型、顯示時段與前台呈現內容</p>
                </div>
            </div>

            <form action="{{ asset('admin/announcement/edit') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="w-full lg:w-2/3 space-y-6">
                        <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border-t-4 border-t-primary">
                            <div class="p-6 border-b border-outline-variant/20">
                                <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">campaign</span>
                                    基本資料
                                </h3>
                            </div>
                            <div class="p-8 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-1.5">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">公告類型 <span class="text-error">*</span></label>
                                        <select name="type" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                            @foreach ($typeOptions as $key => $label)
                                                <option value="{{ $key }}" {{ (string) ($data['type'] ?? '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        <p class="text-[0.75rem] text-outline-variant">全系統公告會顯示於前台最上方橫幅，同時段僅允許一筆。</p>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">狀態 <span class="text-error">*</span></label>
                                        <select name="is_active" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                            @foreach ($statusOptions as $key => $label)
                                                <option value="{{ $key }}" {{ (string) ($data['is_active'] ?? STATUS_ACTIVE) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-1.5 md:col-span-2">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">標題 <span class="text-error">*</span></label>
                                        <input type="text" name="title" placeholder="請輸入公告標題" value="{{ $data['title'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                    </div>
                                    <div class="space-y-1.5 md:col-span-2">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">大綱</label>
                                        <input type="text" name="summary" placeholder="可作為前台列表摘要，最多 500 字" value="{{ $data['summary'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">開始時間 <span class="text-error">*</span></label>
                                        <input type="datetime-local" name="start_at" value="{{ $data['start_at_input'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">結束時間</label>
                                        <input type="datetime-local" name="end_at" value="{{ $data['end_at_input'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                        <p class="text-[0.75rem] text-outline-variant">全系統公告必須設定結束時間；一般公告留空表示永久顯示。</p>
                                    </div>
                                    <div class="space-y-1.5 md:col-span-2">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">內文 <span class="text-error">*</span></label>
                                        <textarea name="content" rows="10" placeholder="請輸入純文字內文" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-3 text-[0.875rem] leading-7 text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">{{ $data['content'] ?? '' }}</textarea>
                                        <p class="text-[0.75rem] text-outline-variant">僅支援純文字，儲存時會自動移除 HTML 標籤。</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:w-1/3 space-y-6">
                        <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden sticky top-4 p-6 space-y-4">
                            <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">bolt</span>
                                操作面板
                            </h3>
                            <button type="submit" class="w-full py-3 btn-primary rounded-xl font-bold text-[0.875rem] flex items-center justify-center gap-2 hover:scale-[1.02] active:scale-95 transition-all duration-200">
                                <span class="material-symbols-outlined text-[20px]">save</span>
                                儲存
                            </button>
                            <a href="{{ asset('admin/announcement/list') }}" class="w-full flex items-center justify-center gap-2 text-on-surface-variant hover:text-primary text-[0.875rem] font-medium transition-colors py-2 no-underline">
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
                                <form action="{{ asset('admin/announcement/delete/' . $data['id']) }}" method="POST" onsubmit="return confirm('確定要刪除此公告嗎？')">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 bg-error text-on-error rounded-xl font-bold text-[0.875rem] flex items-center justify-center gap-2 hover:opacity-90 active:scale-95 transition-all">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                        刪除此公告
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


