@extends('layouts.admin')

@section('title-suffix', empty($data['id']) ? ' · 新增類別' : ' · 編輯類別')

@section('content')
    <x-admin.page-head
        title="{{ empty($data['id']) ? '新增類別' : '編輯類別' }}"
        :breadcrumbs="[
            ['label' => '首頁', 'url' => 'admin/'],
            ['label' => '商品類別管理', 'url' => 'admin/product.category/list'],
            ['label' => empty($data['id']) ? '新增' : '編輯'],
        ]"
    >
        <x-slot:actions>
            <a href="{{ asset('admin/product.category/list') }}" class="admin-btn admin-btn-outline">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                <span>返回列表</span>
            </a>
            <button type="submit" form="categoryEditForm" class="admin-btn admin-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">save</span>
                <span>儲存</span>
            </button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.form-error :errors="$errors" />

    <form id="categoryEditForm" action="{{ asset('admin/product.category/edit') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

        <x-admin.card title="類別資訊">
            <div class="admin-stack">
                <x-admin.input
                    name="name"
                    label="名稱"
                    :value="old('name', $data['name'] ?? '')"
                    required
                    :error="$errors->first('name')"
                />
                <x-admin.input
                    name="sort_order"
                    type="number"
                    label="排序"
                    :value="old('sort_order', $data['sort_order'] ?? 0)"
                    min="0"
                />
                <x-admin.select
                    name="is_active"
                    label="狀態"
                    :options="config('constants.status')"
                    :value="old('is_active', $data['is_active'] ?? STATUS_ACTIVE)"
                />
            </div>
        </x-admin.card>
    </form>

    @if (!empty($data['id']))
        <form action="{{ asset('admin/product.category/delete/' . $data['id']) }}" method="POST" class="admin-form-delete" onsubmit="return confirm('確定要刪除此類別嗎？')">
            @csrf
            <x-admin.card title="危險區">
                <p class="admin-help">刪除前請確認此類別已無關聯商品。</p>
                <button type="submit" class="admin-btn admin-btn-danger">
                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                    <span>刪除類別</span>
                </button>
            </x-admin.card>
        </form>
    @endif
@endsection
