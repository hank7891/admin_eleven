@extends('layouts.admin')

@php($memberId = (int) ($data['id'] ?? 0))

@section('title-suffix', $memberId === 0 ? ' · 新增會員' : ' · 編輯會員')

@section('content')
    <x-admin.page-head
        title="{{ $memberId === 0 ? '新增會員' : '編輯會員' }}"
        subtitle="管理會員基本資料、狀態與重設密碼"
        :breadcrumbs="[
            ['label' => '首頁', 'url' => 'admin/'],
            ['label' => '會員管理', 'url' => 'admin/member/list'],
            ['label' => $memberId === 0 ? '新增' : '編輯'],
        ]"
    >
        <x-slot:actions>
            <a href="{{ asset('admin/member/list') }}" class="admin-btn admin-btn-outline">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                <span>返回列表</span>
            </a>
            <button type="submit" form="memberEditForm" class="admin-btn admin-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">save</span>
                <span>儲存</span>
            </button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.form-error :errors="$errors" />

    <form id="memberEditForm" action="{{ asset('admin/member/edit') }}" method="POST" enctype="multipart/form-data" class="admin-form-grid">
        @csrf
        <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

        <div class="admin-form-main admin-stack admin-stack-lg">
            <x-admin.card title="基本資料">
                <div class="admin-form-row">
                    <div class="admin-field admin-field-full">
                        <label class="admin-label" for="member-email">Email @if ($memberId === 0)<span class="admin-required" aria-hidden="true">*</span>@endif</label>
                        @if ($memberId > 0)
                            <input id="member-email" type="text" readonly value="{{ $data['email'] ?? '' }}" class="admin-input is-readonly">
                        @else
                            <input id="member-email" type="email" name="email" required aria-required="true" value="{{ old('email', $data['email'] ?? '') }}" class="admin-input" placeholder="請輸入 Email">
                        @endif
                    </div>

                    <x-admin.input
                        name="name"
                        label="姓名"
                        :value="old('name', $data['name'] ?? '')"
                        required
                        :error="$errors->first('name')"
                        class="admin-field-full"
                    />

                    @if ($memberId === 0)
                        <x-admin.input
                            name="password"
                            type="password"
                            label="密碼"
                            required
                            hint="儲存失敗時需重新輸入密碼"
                            :error="$errors->first('password')"
                        />
                        <x-admin.input
                            name="password_confirmation"
                            type="password"
                            label="確認密碼"
                            required
                        />
                    @endif

                    <x-admin.input
                        name="phone"
                        label="電話"
                        :value="old('phone', $data['phone'] ?? '')"
                    />
                    <x-admin.input
                        name="birthday"
                        type="date"
                        label="生日"
                        :value="old('birthday', $data['birthday'] ?? '')"
                    />
                    <x-admin.select
                        name="gender_key"
                        label="性別"
                        :options="$genderOptions ?? []"
                        :value="old('gender_key', $data['gender_key'] ?? GENDER_UNSPECIFIED)"
                    />
                    <x-admin.select
                        name="status_key"
                        label="狀態"
                        :options="$statusOptions ?? []"
                        :value="old('status_key', $data['status_key'] ?? 'active')"
                        required
                    />
                </div>

                <div class="admin-field">
                    <label class="admin-label" for="member-avatar">大頭照</label>
                    <input id="member-avatar" type="file" name="avatar" accept=".jpg,.jpeg,.png,.gif,.webp" class="admin-file-input">
                    <p class="admin-help">建議尺寸：800 × 800（1:1），可避免顯示裁切變形。</p>
                </div>

                @if (!empty($data['avatar_url']))
                    <div class="admin-avatar-preview">
                        <img src="{{ $data['avatar_url'] }}" alt="會員大頭照">
                    </div>
                @endif
            </x-admin.card>

            @if ($memberId > 0)
                <x-admin.card title="帳號資訊（唯讀）">
                    <dl class="admin-meta-list">
                        <dt>註冊 IP</dt><dd>{{ $data['registered_ip'] ?: '--' }}</dd>
                        <dt>最後登入時間</dt><dd>{{ $data['last_login_at'] ?: '尚未登入' }}</dd>
                        <dt>最後登入 IP</dt><dd>{{ $data['last_login_ip'] ?: '--' }}</dd>
                        <dt>Email 驗證</dt><dd>{{ $data['email_verified_at'] ?: '未驗證' }}</dd>
                        <dt>加入時間</dt><dd>{{ $data['created_at_display'] ?: '--' }}</dd>
                    </dl>
                </x-admin.card>
            @endif
        </div>

        <div class="admin-form-aside admin-stack admin-stack-lg">
            <x-admin.card title="操作">
                <p class="admin-help">儲存後會寫入操作日誌。</p>
            </x-admin.card>
        </div>
    </form>

    @if ($memberId > 0)
        <form action="{{ asset('admin/member/resetPassword/' . $data['id']) }}" method="POST" class="admin-form-delete" onsubmit="return confirm('確定要重設此會員密碼嗎？');">
            @csrf
            <x-admin.card title="重設密碼">
                <p class="admin-help">系統會隨機產生新密碼並寫入操作日誌；密碼不會於回應中顯示。</p>
                <button type="submit" class="admin-btn admin-btn-danger">
                    <span class="material-symbols-outlined" aria-hidden="true">lock_reset</span>
                    <span>重設密碼</span>
                </button>
            </x-admin.card>
        </form>
    @endif
@endsection
