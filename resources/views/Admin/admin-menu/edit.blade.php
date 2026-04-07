@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            {{-- 頁面標題區 --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <nav class="flex items-center gap-2 text-[0.75rem] text-outline-variant mb-1 uppercase tracking-widest font-semibold">
                        <a href="{{ asset('admin/') }}" class="hover:text-primary transition-colors">首頁</a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <a href="{{ asset('admin/admin.menu/list') }}" class="hover:text-primary transition-colors">選單管理</a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="text-primary">{{ empty($data['id']) ? '新增' : '編輯' }}</span>
                    </nav>
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">{{ empty($data['id']) ? '新增選單' : '編輯選單' }}</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">{{ empty($data['id']) ? '建立新的選單項目' : '修改選單設定與排序' }}</p>
                </div>
            </div>

            <form action="{{ asset('admin/admin.menu/edit') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

                <div class="flex flex-col lg:flex-row gap-6">
                    {{-- 左側：表單 --}}
                    <div class="w-full lg:w-2/3">
                        <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border-t-4 border-t-primary">
                            <div class="p-6 border-b border-outline-variant/20">
                                <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">menu</span>
                                    基本資料
                                </h3>
                            </div>
                            <div class="p-8 space-y-6">
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">類型 / 所屬群組</label>
                                    <select id="inputParentId" name="parent_id"
                                            class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                        <option value="0" {{ ($data['parent_id'] ?? 0) == 0 ? 'selected' : '' }}>群組（最上層）</option>
                                        @foreach ($groups as $group)
                                            <option value="{{ $group['id'] }}" {{ ($data['parent_id'] ?? 0) == $group['id'] ? 'selected' : '' }}>
                                                選單項目 → {{ $group['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">名稱 <span class="text-error">*</span></label>
                                    <input type="text" name="name" placeholder="輸入選單名稱" value="{{ $data['name'] ?? '' }}" required
                                           class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                </div>

                                <div class="space-y-1.5" id="urlGroup">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">連結路徑</label>
                                    <input type="text" name="url" placeholder="例如：/admin/employee/list" value="{{ $data['url'] ?? '' }}"
                                           class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                    <p class="text-[0.75rem] text-outline-variant mt-1">群組不需填寫，選單項目必填</p>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">圖示 Class</label>
                                    <input type="text" name="icon" placeholder="例如：fas fa-users" value="{{ $data['icon'] ?? '' }}"
                                           class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                    <p class="text-[0.75rem] text-outline-variant mt-1">
                                        使用 Font Awesome 圖示，留空則自動帶入預設值。
                                        <a href="https://fontawesome.com/v5/search" target="_blank" class="text-primary hover:underline">查詢圖示</a>
                                    </p>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">排序</label>
                                    <input type="number" name="sort_order" placeholder="數字越小越前面" value="{{ $data['sort_order'] ?? 0 }}" min="0" required
                                           class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                </div>

                                <div class="space-y-1.5">
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
                            <a href="{{ asset('admin/admin.menu/list') }}" class="w-full flex items-center justify-center gap-2 text-on-surface-variant hover:text-primary text-[0.875rem] font-medium transition-colors py-2 no-underline">
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
                                <form action="{{ asset('admin/admin.menu/delete/' . $data['id']) }}" method="POST"
                                      onsubmit="return confirm('確定要刪除此選單嗎？')">
                                    @csrf
                                    <button type="submit" class="w-full py-2.5 bg-error text-on-error rounded-xl font-bold text-[0.875rem] flex items-center justify-center gap-2 hover:opacity-90 active:scale-95 transition-all">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                        刪除此選單
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(function () {
            // 根據類型切換 URL 欄位顯示
            function toggleUrlField() {
                var parentId = $('#inputParentId').val();
                if (parentId == '0') {
                    $('#urlGroup').hide();
                } else {
                    $('#urlGroup').show();
                }
            }
            toggleUrlField();
            $('#inputParentId').on('change', toggleUrlField);
        });
    </script>
@endsection
