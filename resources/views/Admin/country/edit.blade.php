@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            {{-- 頁面標題區 --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '國別管理', 'url' => 'admin/country/list'], ['label' => empty($data['id']) ? '新增' : '編輯']]" />
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">{{ empty($data['id']) ? '新增國別' : '編輯國別' }}</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">{{ empty($data['id']) ? '建立新的國別資料' : '修改國別名稱、縮寫與狀態設定' }}</p>
                </div>
            </div>

            <form action="{{ asset('admin/country/edit') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

                <div class="flex flex-col lg:flex-row gap-6">
                    {{-- 左側：表單 --}}
                    <div class="w-full lg:w-2/3">
                        <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border-t-4 border-t-primary">
                            <div class="p-6 border-b border-outline-variant/20">
                                <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">public</span>
                                    基本資料
                                </h3>
                            </div>
                            <div class="p-8 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-1.5 md:col-span-2">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">國名 <span class="text-error">*</span></label>
                                        <input type="text" name="name" placeholder="請輸入國家名稱" value="{{ $data['name'] ?? '' }}"
                                               class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">縮寫</label>
                                        <input type="text" name="abbreviation" placeholder="如 TW、US" value="{{ $data['abbreviation'] ?? '' }}"
                                               class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] uppercase text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                        <p class="text-[0.75rem] text-outline-variant">非必填，儲存時會自動轉成大寫</p>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">國家代碼 <span class="text-error">*</span></label>
                                        <input type="text" name="country_code" placeholder="唯一代碼，如 TWN" value="{{ $data['country_code'] ?? '' }}"
                                               class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] uppercase text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                        <p class="text-[0.75rem] text-outline-variant">不可重複，儲存時會自動轉成大寫</p>
                                    </div>
                                    <div class="space-y-1.5 md:col-span-2">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">啟用狀態</label>
                                        <select name="is_active"
                                                class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                            @foreach (config('constants.status') as $key => $label)
                                                <option value="{{ $key }}" {{ ($data['is_active'] ?? STATUS_ACTIVE) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 右側：操作面板 --}}
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
                            <a href="{{ asset('admin/country/list') }}" class="w-full flex items-center justify-center gap-2 text-on-surface-variant hover:text-primary text-[0.875rem] font-medium transition-colors py-2 no-underline">
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
                                <form action="{{ asset('admin/country/delete/' . $data['id']) }}" method="POST"
                                      onsubmit="return confirm('確定要刪除此國別資料嗎？')">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 bg-error text-on-error rounded-xl font-bold text-[0.875rem] flex items-center justify-center gap-2 hover:opacity-90 active:scale-95 transition-all">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                        刪除此國別
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



