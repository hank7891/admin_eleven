@extends('Admin-share/index')
@section('content')
    @php($memberId = (int) ($data['id'] ?? 0))
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '會員管理', 'url' => 'admin/member/list'], ['label' => empty($data['id']) ? '新增' : '編輯']]" />
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">{{ empty($data['id']) ? '新增會員' : '編輯會員' }}</h2>
                </div>
            </div>

            <form action="{{ asset('admin/member/edit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="w-full lg:w-2/3 space-y-6">
                        <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border-t-4 border-t-primary">
                            <div class="p-6 border-b border-outline-variant/20">
                                <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline">基本資料</h3>
                            </div>
                            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5 md:col-span-2">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">Email @if ($memberId === 0)<span class="text-error">*</span>@endif</label>
                                    @if ($memberId > 0)
                                        <input type="text" readonly value="{{ $data['email'] ?? '' }}" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg px-4 py-2.5 text-[0.875rem] text-outline cursor-not-allowed">
                                    @else
                                        <input type="email" name="email" required value="{{ $data['email'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface" placeholder="請輸入 Email">
                                    @endif
                                </div>
                                <div class="space-y-1.5 md:col-span-2">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">姓名 <span class="text-error">*</span></label>
                                    <input type="text" name="name" required value="{{ $data['name'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                                </div>
                                @if ($memberId === 0)
                                    <div class="space-y-1.5">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">密碼 <span class="text-error">*</span></label>
                                        <input type="password" name="password" required class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                                        <p class="text-[0.75rem] text-outline">儲存失敗時需重新輸入密碼</p>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="block text-[0.875rem] font-medium text-on-surface-variant">確認密碼 <span class="text-error">*</span></label>
                                        <input type="password" name="password_confirmation" required class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                                    </div>
                                @endif
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">電話</label>
                                    <input type="text" name="phone" value="{{ $data['phone'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">生日</label>
                                    <input type="date" name="birthday" value="{{ $data['birthday'] ?? '' }}" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">性別</label>
                                    <select name="gender_key" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                                        @foreach ($genderOptions as $key => $label)
                                            <option value="{{ $key }}" {{ (string) ($data['gender_key'] ?? GENDER_UNSPECIFIED) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">狀態</label>
                                    <select name="status_key" required class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                                        @foreach ($statusOptions as $key => $label)
                                            <option value="{{ $key }}" {{ (string) ($data['status_key'] ?? 'active') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1.5 md:col-span-2">
                                    <label class="block text-[0.875rem] font-medium text-on-surface-variant">大頭照</label>
                                    <input type="file" name="avatar" accept=".jpg,.jpeg,.png,.gif,.webp" class="w-full bg-surface-container-high border border-transparent rounded-lg px-4 py-2.5 text-[0.875rem] text-on-surface">
                                    <p class="text-[0.75rem] text-outline-variant">建議尺寸：800 x 800（1:1），可避免顯示裁切變形。</p>
                                </div>
                                @if (!empty($data['avatar_url']))
                                    <div class="md:col-span-2">
                                        <div class="inline-flex h-28 w-28 items-center justify-center overflow-hidden rounded-full border-4 border-surface-container bg-surface-container-low shadow-inner">
                                            <img src="{{ $data['avatar_url'] }}" alt="會員大頭照" class="h-full w-full object-cover">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($memberId > 0)
                            <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border-t-4 border-t-secondary">
                                <div class="p-6 border-b border-outline-variant/20">
                                    <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline">帳號資訊（唯讀）</h3>
                                </div>
                                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-4 text-[0.875rem] text-on-surface/75">
                                    <p><span class="font-medium text-on-surface">註冊 IP：</span>{{ $data['registered_ip'] ?: '--' }}</p>
                                    <p><span class="font-medium text-on-surface">最後登入時間：</span>{{ $data['last_login_at'] ?: '尚未登入' }}</p>
                                    <p><span class="font-medium text-on-surface">最後登入 IP：</span>{{ $data['last_login_ip'] ?: '--' }}</p>
                                    <p><span class="font-medium text-on-surface">Email 驗證：</span>{{ $data['email_verified_at'] ?: '未驗證' }}</p>
                                    <p class="md:col-span-2"><span class="font-medium text-on-surface">加入時間：</span>{{ $data['created_at_display'] ?: '--' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="w-full lg:w-1/3 space-y-6">
                        <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden sticky top-4 p-6 space-y-4">
                            <h3 class="text-[0.9375rem] font-semibold text-on-surface font-headline">操作面板</h3>
                            <button type="submit" class="w-full py-3 btn-primary rounded-xl font-bold text-[0.875rem]">儲存</button>
                            <a href="{{ asset('admin/member/list') }}" class="w-full flex items-center justify-center text-on-surface-variant hover:text-primary text-[0.875rem] font-medium no-underline">返回列表</a>
                        </div>

                        @if ($memberId > 0)
                            <div class="bg-error-container/30 rounded-xl border border-error/20 p-6 space-y-4">
                                <h3 class="text-[0.9375rem] font-semibold text-error">重設密碼</h3>
                                <button
                                    type="submit"
                                    formmethod="POST"
                                    formaction="{{ asset('admin/member/resetPassword/' . $data['id']) }}"
                                    onclick="return confirm('確定要重設此會員密碼嗎？');"
                                    class="w-full py-2.5 bg-error text-on-error rounded-xl font-bold text-[0.875rem]"
                                >
                                    重設密碼
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

