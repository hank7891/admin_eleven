@extends('layouts.admin')

@php($employeeId = (int) ($data['id'] ?? 0))

@section('title-suffix', $employeeId === 0 ? ' · 新增帳號' : ' · 編輯帳號')

@section('content')
    <x-admin.page-head
        title="{{ $employeeId === 0 ? '新增帳號' : '編輯帳號' }}"
        subtitle="{{ $employeeId === 0 ? '建立新的系統使用者帳號' : '修改帳號資料、角色與密碼' }}"
        :breadcrumbs="[
            ['label' => '首頁', 'url' => 'admin/'],
            ['label' => '帳號管理', 'url' => 'admin/employee/list'],
            ['label' => $employeeId === 0 ? '新增' : '編輯'],
        ]"
    >
        <x-slot:actions>
            <a href="{{ asset('admin/employee/list') }}" class="admin-btn admin-btn-outline">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                <span>返回列表</span>
            </a>
            <button type="submit" form="employeeEditForm" class="admin-btn admin-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">save</span>
                <span>儲存</span>
            </button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.form-error :errors="$errors" />

    <form id="employeeEditForm" action="{{ asset('admin/employee/edit') }}" method="POST" enctype="multipart/form-data" class="admin-form-grid">
        @csrf
        <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

        <div class="admin-form-main admin-stack admin-stack-lg">
            <x-admin.card title="基本資料">
                <div class="admin-form-row">
                    <div class="admin-field">
                        <label class="admin-label" for="employee-account">帳號 @if ($employeeId === 0)<span class="admin-required" aria-hidden="true">*</span>@endif</label>
                        @if ($employeeId > 0)
                            <input id="employee-account" type="text" readonly value="{{ $data['account'] ?? '--' }}" class="admin-input is-readonly">
                        @else
                            <input id="employee-account" type="text" name="account" placeholder="請輸入帳號" value="{{ old('account', $data['account'] ?? '') }}" class="admin-input">
                        @endif
                    </div>
                    <x-admin.input
                        name="name"
                        label="姓名"
                        :value="old('name', $data['name'] ?? '')"
                        required
                        :error="$errors->first('name')"
                    />
                    <x-admin.select
                        name="gender"
                        label="性別"
                        :options="config('constants.gender')"
                        :value="old('gender', $data['gender'] ?? 0)"
                    />
                    <x-admin.input
                        name="birthday"
                        type="date"
                        label="生日"
                        :value="old('birthday', isset($data['birthday']) && $data['birthday'] instanceof \Carbon\Carbon ? $data['birthday']->format('Y-m-d') : ($data['birthday'] ?? ''))"
                    />
                    <x-admin.input
                        name="phone"
                        label="電話"
                        :value="old('phone', $data['phone'] ?? '')"
                        placeholder="請輸入電話號碼"
                    />
                    <x-admin.select
                        name="is_active"
                        label="帳號狀態"
                        :options="config('constants.status')"
                        :value="old('is_active', $data['is_active'] ?? 1)"
                    />
                </div>
            </x-admin.card>

            @if ($employeeId > 0)
                <x-admin.card title="變更密碼">
                    <p class="admin-help">目前密碼已設定，留空則不更新；填寫新密碼後請同步填寫確認密碼。</p>
                    <input type="hidden" name="change_password" value="1">
                    <div class="admin-form-row">
                        <x-admin.input
                            name="password"
                            type="password"
                            label="新密碼"
                            placeholder="留空則不更新密碼"
                            autocomplete="new-password"
                        />
                        <x-admin.input
                            name="password_confirmation"
                            type="password"
                            label="確認新密碼"
                            placeholder="請再次輸入新密碼"
                            autocomplete="new-password"
                        />
                    </div>
                </x-admin.card>
            @else
                <x-admin.card title="密碼">
                    <div class="admin-form-row">
                        <x-admin.input
                            name="password"
                            type="password"
                            label="密碼"
                            placeholder="請輸入密碼"
                            required
                            autocomplete="new-password"
                            :error="$errors->first('password')"
                        />
                        <x-admin.input
                            name="password_confirmation"
                            type="password"
                            label="確認密碼"
                            placeholder="請再次輸入密碼"
                            required
                            autocomplete="new-password"
                        />
                    </div>
                </x-admin.card>
            @endif

            <x-admin.card title="角色指派">
                @if (!empty($roles))
                    <div class="admin-tag-row">
                        @foreach ($roles as $role)
                            <label class="admin-checkbox-row admin-checkbox-pill">
                                <input type="checkbox" name="role_ids[]" value="{{ $role['id'] }}" {{ in_array($role['id'], $data['role_ids'] ?? []) ? 'checked' : '' }}>
                                <span class="admin-checkbox-label">{{ $role['role_name'] }}</span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <p class="admin-help">
                        <span class="material-symbols-outlined" aria-hidden="true">info</span>
                        尚無角色可指派，請先至角色管理新增角色。
                    </p>
                @endif
            </x-admin.card>

            <x-admin.card title="大頭照">
                <div class="admin-stack">
                    @if (!empty($data['avatar']))
                        <div class="admin-avatar-preview">
                            <img src="{{ asset('storage/' . $data['avatar']) }}" alt="大頭照">
                        </div>
                    @endif
                    <div class="admin-field">
                        <label class="admin-label" for="avatar">選擇新的大頭照</label>
                        <input id="avatar" type="file" name="avatar" accept=".jpg,.jpeg,.png,.gif,.webp" class="admin-file-input">
                        <p class="admin-help">支援 JPG、PNG、GIF、WebP，檔案大小不超過 2MB。</p>
                    </div>
                </div>
            </x-admin.card>
        </div>

        <div class="admin-form-aside admin-stack admin-stack-lg">
            <x-admin.card title="操作">
                <p class="admin-help">儲存後會寫入操作日誌。</p>
            </x-admin.card>
        </div>
    </form>
@endsection
