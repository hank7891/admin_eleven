@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            {{-- 頁面標題區 --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '會員管理', 'url' => 'admin/employee/list'], ['label' => empty($data['id']) ? '新增' : '編輯']]" />
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">{{ empty($data['id']) ? '新增會員' : '編輯會員' }}</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">{{ empty($data['id']) ? '建立新的系統使用者帳號' : '修改會員資料與角色設定' }}</p>
                </div>
            </div>

            <form action="{{ asset('admin/employee/edit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

                <div class="flex flex-col lg:flex-row gap-6">
                    {{-- 左側：表單 --}}
                    <div class="w-full lg:w-2/3 space-y-6">
                        {{-- 基本資料 --}}
                        <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border-t-4 border-t-primary">
                            <div class="p-6 border-b border-outline-variant/20">
                                <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary">person</span>
                                    基本資料
                                </h3>
                            </div>
                            <div class="p-8 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ changePassword: {{ !empty($data['change_password']) ? 'true' : 'false' }} }">
                                    <div class="space-y-1.5">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">帳號 @if (($data['id'] ?? 0) === 0)<span class="text-error">*</span>@endif</label>
                                        @if (($data['id'] ?? 0) > 0)
                                            <input type="text" readonly value="{{ $data['account'] ?? '--' }}"
                                                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 text-[0.875rem] text-outline cursor-not-allowed">
                                        @else
                                            <input type="text" name="account" placeholder="請輸入帳號" value="{{ $data['account'] ?? '' }}"
                                                   class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                        @endif
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">姓名 <span class="text-error">*</span></label>
                                        <input type="text" name="name" placeholder="請輸入姓名" value="{{ $data['name'] ?? '' }}"
                                               class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">性別</label>
                                        <select name="gender"
                                                class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                            @foreach (config('constants.gender') as $key => $label)
                                                <option value="{{ $key }}" {{ ($data['gender'] ?? 0) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">生日</label>
                                        <input type="date" name="birthday"
                                               value="{{ isset($data['birthday']) && $data['birthday'] instanceof \Carbon\Carbon ? $data['birthday']->format('Y-m-d') : ($data['birthday'] ?? '') }}"
                                               class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">電話</label>
                                        <input type="text" name="phone" placeholder="請輸入電話號碼" value="{{ $data['phone'] ?? '' }}"
                                               class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                    </div>
                                    <div class="space-y-1.5 md:col-span-2">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">帳號狀態</label>
                                        <select name="is_active"
                                                class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all">
                                            @foreach (config('constants.status') as $key => $label)
                                                <option value="{{ $key }}" {{ ($data['is_active'] ?? 1) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if (($data['id'] ?? 0) > 0)
                                        <div class="md:col-span-2 space-y-4 rounded-xl border border-outline-variant/20 bg-surface-container-low p-5">
                                            <input type="hidden" name="change_password" :value="changePassword ? 1 : 0">
                                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                                <div>
                                                    <p class="text-[0.875rem] font-medium text-on-surface-variant">密碼</p>
                                                    <p class="text-[0.75rem] text-outline">目前密碼已設定，如需修改請點擊右側按鈕。</p>
                                                </div>
                                                <button type="button" @click="changePassword = !changePassword" class="inline-flex items-center justify-center gap-2 rounded-lg bg-surface-container-high px-4 py-2 text-[0.875rem] font-semibold text-on-surface hover:bg-surface-container transition-colors">
                                                    <span class="material-symbols-outlined text-[18px]" x-text="changePassword ? 'lock_reset' : 'password'"></span>
                                                    <span x-text="changePassword ? '取消變更密碼' : '變更密碼'"></span>
                                                </button>
                                            </div>

                                            <div x-show="changePassword" x-collapse class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div class="space-y-1.5">
                                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">新密碼</label>
                                                    <input type="password" name="password" autocomplete="new-password"
                                                           placeholder="留空則不更新密碼"
                                                           class="w-full bg-surface-container-highest border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                                </div>
                                                <div class="space-y-1.5">
                                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">確認新密碼</label>
                                                    <input type="password" name="password_confirmation" autocomplete="new-password"
                                                           placeholder="請再次輸入新密碼"
                                                           class="w-full bg-surface-container-highest border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="space-y-1.5">
                                            <label class="block text-[0.875rem] font-medium text-on-surface-variant">密碼 <span class="text-error">*</span></label>
                                            <input type="password" name="password" autocomplete="new-password"
                                                   placeholder="請輸入密碼"
                                                   class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                        </div>
                                        <div class="space-y-1.5">
                                            <label class="block text-[0.875rem] font-medium text-on-surface-variant">確認密碼 <span class="text-error">*</span></label>
                                            <input type="password" name="password_confirmation" autocomplete="new-password"
                                                   placeholder="請再次輸入密碼"
                                                   class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all placeholder:text-outline-variant/60">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- 角色指派 --}}
                        <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border-t-4 border-t-secondary">
                            <div class="p-6 border-b border-outline-variant/20">
                                <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline flex items-center gap-2">
                                    <span class="material-symbols-outlined text-secondary">admin_panel_settings</span>
                                    角色指派
                                </h3>
                            </div>
                            <div class="p-8">
                                @if(!empty($roles))
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        @foreach ($roles as $role)
                                            <label class="relative flex items-center gap-3 p-3 rounded-lg border border-surface-container hover:bg-surface-container-low transition-colors cursor-pointer">
                                                <input type="checkbox"
                                                       name="role_ids[]"
                                                       value="{{ $role['id'] }}"
                                                       id="role_{{ $role['id'] }}"
                                                       class="stitch-checkbox stitch-checkbox--md"
                                                       {{ in_array($role['id'], $data['role_ids'] ?? []) ? 'checked' : '' }}>
                                                <span class="text-[0.875rem] font-medium text-on-surface">{{ $role['role_name'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 p-4 bg-surface-container-low rounded-xl text-[0.875rem] text-outline">
                                        <span class="material-symbols-outlined text-[18px]">info</span>
                                        尚無角色可指派，請先至角色管理新增角色
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- 大頭照上傳 --}}
                        <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border-t-4 border-t-tertiary">
                            <div class="p-6 border-b border-outline-variant/20">
                                <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline flex items-center gap-2">
                                    <span class="material-symbols-outlined text-tertiary">photo_camera</span>
                                    大頭照
                                </h3>
                            </div>
                            <div class="p-8">
                                <div class="flex flex-col md:flex-row items-center gap-10">
                                    <div class="relative">
                                        @if (!empty($data['avatar']))
                                            <div class="w-[120px] h-[120px] rounded-full border-4 border-surface-container overflow-hidden bg-surface-container-low shadow-inner">
                                                <img src="{{ asset('storage/' . $data['avatar']) }}" alt="大頭照" class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="w-[120px] h-[120px] rounded-full border-4 border-surface-container bg-surface-container-low flex items-center justify-center shadow-inner">
                                                <span class="material-symbols-outlined text-[2.5rem] text-outline-variant">person</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 space-y-4 text-center md:text-left">
                                        <label class="cursor-pointer bg-surface-container-high hover:bg-surface-container text-on-surface-variant px-6 py-2.5 rounded-lg text-[0.875rem] font-semibold transition-all inline-flex items-center justify-center gap-2">
                                            <span class="material-symbols-outlined">upload_file</span>
                                            選擇檔案
                                            <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.gif,.webp" class="hidden">
                                        </label>
                                        <div class="space-y-1">
                                            <p class="text-[0.75rem] text-on-surface-variant flex items-center justify-center md:justify-start gap-1">
                                                <span class="material-symbols-outlined text-[14px]">info</span>
                                                支援 JPG、PNG、GIF、WebP，檔案大小不超過 2MB
                                            </p>
                                        </div>
                                    </div>
                                </div>
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
                            <button type="submit" class="w-full py-3 btn-primary rounded-xl font-bold text-[0.875rem] flex items-center justify-center gap-2 hover:scale-[1.02] active:scale-95 transition-all duration-200">
                                <span class="material-symbols-outlined text-[20px]">save</span>
                                儲存
                            </button>
                            <a href="{{ asset('admin/employee/list') }}" class="w-full flex items-center justify-center gap-2 text-on-surface-variant hover:text-primary text-[0.875rem] font-medium transition-colors py-2 no-underline">
                                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                                返回列表
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
