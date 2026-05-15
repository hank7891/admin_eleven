@extends('layouts.admin')

@section('title-suffix', empty($data['id']) ? ' · 新增公告' : ' · 編輯公告')

@section('content')
    <x-admin.page-head
        title="{{ empty($data['id']) ? '新增公告' : '編輯公告' }}"
        subtitle="設定公告類型、顯示時段與前台呈現內容"
        :breadcrumbs="[
            ['label' => '首頁', 'url' => 'admin/'],
            ['label' => '公告管理', 'url' => 'admin/announcement/list'],
            ['label' => empty($data['id']) ? '新增' : '編輯'],
        ]"
    >
        <x-slot:actions>
            <a href="{{ asset('admin/announcement/list') }}" class="admin-btn admin-btn-outline">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                <span>返回列表</span>
            </a>
            <button type="submit" form="announcementEditForm" class="admin-btn admin-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">save</span>
                <span>儲存</span>
            </button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.form-error :errors="$errors" />

    <form id="announcementEditForm" action="{{ asset('admin/announcement/edit') }}" method="POST" class="admin-form-grid">
        @csrf
        <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

        <div class="admin-form-main admin-stack admin-stack-lg">
            <x-admin.card title="基本資料">
                <div class="admin-form-row">
                    <x-admin.select
                        name="type"
                        label="公告類型"
                        :options="$typeOptions ?? []"
                        :value="old('type', $data['type'] ?? '')"
                        required
                        hint="全系統公告會顯示於前台最上方橫幅，同時段僅允許一筆。"
                        :error="$errors->first('type')"
                    />
                    <x-admin.select
                        name="is_active"
                        label="狀態"
                        :options="$statusOptions ?? []"
                        :value="old('is_active', $data['is_active'] ?? STATUS_ACTIVE)"
                        required
                        :error="$errors->first('is_active')"
                    />
                    <x-admin.input
                        name="title"
                        label="標題"
                        :value="old('title', $data['title'] ?? '')"
                        placeholder="請輸入公告標題"
                        required
                        :error="$errors->first('title')"
                        class="admin-field-full"
                    />
                    <x-admin.input
                        name="summary"
                        label="大綱"
                        :value="old('summary', $data['summary'] ?? '')"
                        placeholder="可作為前台列表摘要，最多 500 字"
                        :error="$errors->first('summary')"
                        class="admin-field-full"
                    />
                    <x-admin.input
                        name="start_at"
                        type="datetime-local"
                        label="開始時間"
                        :value="old('start_at', $data['start_at_input'] ?? '')"
                        required
                        :error="$errors->first('start_at')"
                    />
                    <x-admin.input
                        name="end_at"
                        type="datetime-local"
                        label="結束時間"
                        :value="old('end_at', $data['end_at_input'] ?? '')"
                        hint="全系統公告必須設定結束時間；一般公告留空表示永久顯示。"
                        :error="$errors->first('end_at')"
                    />
                    <x-admin.textarea
                        name="content"
                        label="內文"
                        :value="old('content', $data['content'] ?? '')"
                        :rows="10"
                        placeholder="請輸入純文字內文"
                        required
                        hint="僅支援純文字，儲存時會自動移除 HTML 標籤。"
                        :error="$errors->first('content')"
                        class="admin-field-full"
                    />
                </div>
            </x-admin.card>
        </div>

        <div class="admin-form-aside admin-stack admin-stack-lg">
            <x-admin.card title="操作">
                <p class="admin-help">儲存後會寫入操作日誌。</p>
            </x-admin.card>
        </div>
    </form>

    @if (!empty($data['id']))
        <form action="{{ asset('admin/announcement/delete/' . $data['id']) }}" method="POST" class="admin-form-delete" onsubmit="return confirm('確定要刪除此公告嗎？')">
            @csrf
            <x-admin.card title="危險區">
                <p class="admin-help">刪除後無法復原。</p>
                <button type="submit" class="admin-btn admin-btn-danger">
                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                    <span>刪除此公告</span>
                </button>
            </x-admin.card>
        </form>
    @endif
@endsection
