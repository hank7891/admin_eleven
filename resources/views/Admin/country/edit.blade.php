@extends('layouts.admin')

@section('title-suffix', empty($data['id']) ? ' · 新增國別' : ' · 編輯國別')

@section('content')
    <x-admin.page-head
        title="{{ empty($data['id']) ? '新增國別' : '編輯國別' }}"
        subtitle="{{ empty($data['id']) ? '建立新的國別資料' : '修改國別名稱、縮寫與狀態設定' }}"
        :breadcrumbs="[
            ['label' => '首頁', 'url' => 'admin/'],
            ['label' => '國別管理', 'url' => 'admin/country/list'],
            ['label' => empty($data['id']) ? '新增' : '編輯'],
        ]"
    >
        <x-slot:actions>
            <a href="{{ asset('admin/country/list') }}" class="admin-btn admin-btn-outline">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                <span>返回列表</span>
            </a>
            <button type="submit" form="countryEditForm" class="admin-btn admin-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">save</span>
                <span>儲存</span>
            </button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.form-error :errors="$errors" />

    <form id="countryEditForm" action="{{ asset('admin/country/edit') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

        <x-admin.card title="基本資料">
            <div class="admin-form-row">
                <x-admin.input
                    name="name"
                    label="國名"
                    :value="old('name', $data['name'] ?? '')"
                    placeholder="請輸入國家名稱"
                    required
                    :error="$errors->first('name')"
                    class="admin-field-full"
                />
                <x-admin.input
                    name="abbreviation"
                    label="縮寫"
                    :value="old('abbreviation', $data['abbreviation'] ?? '')"
                    placeholder="如 TW、US"
                    hint="非必填，儲存時會自動轉成大寫"
                />
                <x-admin.input
                    name="country_code"
                    label="國家代碼"
                    :value="old('country_code', $data['country_code'] ?? '')"
                    placeholder="唯一代碼，如 TWN"
                    required
                    hint="不可重複，儲存時會自動轉成大寫"
                    :error="$errors->first('country_code')"
                />
                <x-admin.select
                    name="is_active"
                    label="啟用狀態"
                    :options="config('constants.status')"
                    :value="old('is_active', $data['is_active'] ?? STATUS_ACTIVE)"
                    class="admin-field-full"
                />
            </div>
        </x-admin.card>
    </form>

    @if (!empty($data['id']))
        <form action="{{ asset('admin/country/delete/' . $data['id']) }}" method="POST" class="admin-form-delete" onsubmit="return confirm('確定要刪除此國別資料嗎？')">
            @csrf
            <x-admin.card title="危險區">
                <p class="admin-help">刪除後無法復原。</p>
                <button type="submit" class="admin-btn admin-btn-danger">
                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                    <span>刪除此國別</span>
                </button>
            </x-admin.card>
        </form>
    @endif
@endsection
