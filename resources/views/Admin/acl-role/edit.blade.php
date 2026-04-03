@extends('Admin-share/index')
@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite('resources/css/stitch.css')

    <div class="content-wrapper stitch-page">
        <div class="p-6 lg:p-10 space-y-8">
            {{-- 頁面標題區 --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <nav class="flex items-center gap-2 text-[0.75rem] text-outline-variant mb-1 uppercase tracking-widest font-semibold">
                        <a href="{{ asset('admin/') }}" class="hover:text-primary transition-colors">首頁</a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <a href="{{ asset('admin/acl.role/list') }}" class="hover:text-primary transition-colors">角色管理</a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="text-primary">{{ empty($data['id']) ? '新增' : '編輯' }}</span>
                    </nav>
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">{{ empty($data['id']) ? '新增角色' : '編輯角色' }}</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">{{ empty($data['id']) ? '建立新的系統角色' : '修改角色資料與選單權限' }}</p>
                </div>
            </div>

            <form action="{{ asset('admin/acl.role/edit') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

                <div class="flex flex-col lg:flex-row gap-6">
                    {{-- 左側：表單 --}}
                    <div class="w-full lg:w-2/3 space-y-6">
                        {{-- 基本資料 --}}
                        <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border-t-4 border-t-primary">
                            <div class="p-6 border-b border-outline-variant/20">
                                <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">badge</span>
                                    基本資料
                                </h3>
                            </div>
                            <div class="p-8">
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">角色名稱 <span class="text-error">*</span></label>
                                    <input type="text" name="role_name" placeholder="請輸入角色名稱" value="{{ $data['role_name'] ?? '' }}"
                                           class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                </div>
                            </div>
                        </div>

                        {{-- 選單權限 --}}
                        <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border-t-4 border-t-secondary">
                            <div class="p-6 border-b border-outline-variant/20 flex items-center justify-between">
                                <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline flex items-center gap-2">
                                    <span class="material-symbols-outlined text-secondary">shield_person</span>
                                    選單權限
                                </h3>
                                <div class="flex gap-2">
                                    <button type="button" id="btnSelectAll" class="flex items-center gap-1 px-3 py-1.5 text-[0.75rem] font-semibold text-primary bg-primary/5 hover:bg-primary/10 rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">done_all</span>
                                        全選
                                    </button>
                                    <button type="button" id="btnDeselectAll" class="flex items-center gap-1 px-3 py-1.5 text-[0.75rem] font-semibold text-outline hover:bg-surface-container-low rounded-lg transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">remove_done</span>
                                        清除
                                    </button>
                                </div>
                            </div>
                            <div>
                                @foreach ($menuTree ?? [] as $group)
                                    <div class="border-b border-outline-variant/10 last:border-b-0">
                                        {{-- 群組標題列 --}}
                                        <div class="flex items-center gap-3 px-6 py-4 bg-surface-container-low/50">
                                            <input type="checkbox"
                                                   class="group-checkbox w-5 h-5 rounded text-primary focus:ring-primary/20 bg-surface-container-high border-none cursor-pointer"
                                                   id="group_{{ $loop->index }}"
                                                   data-group="{{ $loop->index }}">
                                            <label for="group_{{ $loop->index }}" class="flex items-center gap-2 text-[0.875rem] font-semibold text-on-surface cursor-pointer flex-1">
                                                <i class="{{ $group['item_icon'] ?? 'fas fa-folder' }}" style="color: var(--color-primary);"></i>
                                                {{ $group['item_name'] }}
                                            </label>
                                            <span class="text-[0.75rem] font-medium text-outline bg-surface-container px-2 py-0.5 rounded-full" data-group="{{ $loop->index }}">
                                                <span class="checked-count">0</span> / {{ count($group['details']) }}
                                            </span>
                                        </div>
                                        {{-- 子選單項目 --}}
                                        <div class="px-6 py-4">
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                @foreach ($group['details'] as $detail)
                                                    <label class="flex items-center gap-3 p-3 rounded-lg border border-surface-container hover:bg-surface-container-low transition-colors cursor-pointer">
                                                        <input type="checkbox"
                                                               class="menu-checkbox group-{{ $loop->parent->index }} w-4 h-4 rounded text-primary focus:ring-primary/20 bg-surface-container-high border-none"
                                                               name="menu_ids[]"
                                                               value="{{ $detail['id'] }}"
                                                               id="menu_{{ $detail['id'] }}"
                                                               {{ in_array($detail['id'], $data['menu_ids'] ?? []) ? 'checked' : '' }}>
                                                        <span class="text-[0.8125rem] font-medium text-on-surface-variant">{{ $detail['name'] }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                @if(empty($menuTree))
                                    <div class="p-6">
                                        <div class="flex items-center gap-2 p-4 bg-surface-container-low rounded-xl text-[0.875rem] text-outline">
                                            <span class="material-symbols-outlined text-[18px]">info</span>
                                            尚無選單可設定，請先至選單管理新增選單
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 右側：操作面板 --}}
                    <div class="w-full lg:w-1/3">
                        <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden sticky top-4 p-6 space-y-4">
                            <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">bolt</span>
                                操作面板
                            </h3>
                            <button type="submit" class="w-full py-3 bg-gradient-to-r from-[#667eea] to-[#764ba2] text-white rounded-xl font-bold text-[0.875rem] flex items-center justify-center gap-2 shadow-lg shadow-indigo-500/20 hover:scale-[1.02] active:scale-95 transition-all duration-200">
                                <span class="material-symbols-outlined text-[20px]">save</span>
                                儲存
                            </button>
                            <a href="{{ asset('admin/acl.role/list') }}" class="w-full flex items-center justify-center gap-2 text-on-surface-variant hover:text-primary text-[0.875rem] font-medium transition-colors py-2 no-underline">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                返回列表
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    $(function() {
        # 更新群組計數與 checkbox 狀態
        function updateGroupState(groupIndex) {
            var total = $('.group-' + groupIndex).length;
            var checked = $('.group-' + groupIndex + ':checked').length;

            $('#group_' + groupIndex).prop('checked', checked === total && total > 0);
            $('#group_' + groupIndex).prop('indeterminate', checked > 0 && checked < total);

            $('[data-group="' + groupIndex + '"] .checked-count').text(checked);
        }

        # 群組 checkbox 全選/取消
        $('.group-checkbox').on('change', function() {
            var groupIndex = $(this).data('group');
            $('.group-' + groupIndex).prop('checked', this.checked);
            updateGroupState(groupIndex);
        });

        # 子選單 checkbox 變更
        $('.menu-checkbox').on('change', function() {
            var groupClass = this.className.split(' ').find(function(c) { return c.startsWith('group-'); });
            if (groupClass) {
                updateGroupState(groupClass.replace('group-', ''));
            }
        });

        # 全選按鈕
        $('#btnSelectAll').on('click', function() {
            $('.menu-checkbox').prop('checked', true);
            $('.group-checkbox').each(function() { updateGroupState($(this).data('group')); });
        });

        # 清除按鈕
        $('#btnDeselectAll').on('click', function() {
            $('.menu-checkbox').prop('checked', false);
            $('.group-checkbox').each(function() { updateGroupState($(this).data('group')); });
        });

        # 初始化所有群組狀態
        $('.group-checkbox').each(function() { updateGroupState($(this).data('group')); });
    });
    </script>
@endsection
