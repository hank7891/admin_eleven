@extends('layouts.admin')

@section('title-suffix', empty($data['id']) ? ' · 新增選單' : ' · 編輯選單')

@section('content')
    <x-admin.page-head
        title="{{ empty($data['id']) ? '新增選單' : '編輯選單' }}"
        subtitle="{{ empty($data['id']) ? '建立新的選單項目' : '修改選單設定與排序' }}"
        :breadcrumbs="[
            ['label' => '首頁', 'url' => 'admin/'],
            ['label' => '選單管理', 'url' => 'admin/admin.menu/list'],
            ['label' => empty($data['id']) ? '新增' : '編輯'],
        ]"
    >
        <x-slot:actions>
            <a href="{{ asset('admin/admin.menu/list') }}" class="admin-btn admin-btn-outline">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                <span>返回列表</span>
            </a>
            <button type="submit" form="adminMenuEditForm" class="admin-btn admin-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">save</span>
                <span>儲存</span>
            </button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.form-error :errors="$errors" />

    <form id="adminMenuEditForm" action="{{ asset('admin/admin.menu/edit') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

        <x-admin.card title="基本資料">
            <div class="admin-stack">
                <div class="admin-field">
                    <label class="admin-label" for="inputParentId">類型 / 所屬群組</label>
                    <select id="inputParentId" name="parent_id" class="admin-select">
                        <option value="0" {{ ($data['parent_id'] ?? 0) == 0 ? 'selected' : '' }}>群組（最上層）</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group['id'] }}" {{ ($data['parent_id'] ?? 0) == $group['id'] ? 'selected' : '' }}>
                                選單項目 → {{ $group['name'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <x-admin.input
                    name="name"
                    label="名稱"
                    :value="old('name', $data['name'] ?? '')"
                    placeholder="輸入選單名稱"
                    required
                    :error="$errors->first('name')"
                />

                <div id="urlGroup">
                    <x-admin.input
                        name="url"
                        label="連結路徑"
                        :value="old('url', $data['url'] ?? '')"
                        placeholder="例如：/admin/employee/list"
                        hint="群組不需填寫，選單項目必填"
                    />
                </div>

                <x-admin.input
                    name="icon"
                    label="圖示 Class"
                    :value="old('icon', $data['icon'] ?? '')"
                    placeholder="例如：fas fa-users"
                    hint="使用 Font Awesome 圖示，留空則自動帶入預設值。"
                />

                <x-admin.input
                    name="sort_order"
                    type="number"
                    label="排序"
                    :value="old('sort_order', $data['sort_order'] ?? 0)"
                    placeholder="數字越小越前面"
                    required
                    min="0"
                />

                <x-admin.select
                    name="is_active"
                    label="啟用狀態"
                    :options="config('constants.status')"
                    :value="old('is_active', $data['is_active'] ?? STATUS_ACTIVE)"
                />
            </div>
        </x-admin.card>
    </form>

    @if (!empty($data['id']))
        <form action="{{ asset('admin/admin.menu/delete/' . $data['id']) }}" method="POST" class="admin-form-delete" onsubmit="return confirm('確定要刪除此選單嗎？')">
            @csrf
            <x-admin.card title="危險區">
                <p class="admin-help">刪除前請確認此選單已無相依資料。</p>
                <button type="submit" class="admin-btn admin-btn-danger">
                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                    <span>刪除此選單</span>
                </button>
            </x-admin.card>
        </form>
    @endif
@endsection

@push('scripts')
    <script>
        (function () {
            const parentSelect = document.getElementById('inputParentId');
            const urlGroup = document.getElementById('urlGroup');
            if (!parentSelect || !urlGroup) return;

            const toggleUrlField = () => {
                urlGroup.style.display = parentSelect.value === '0' ? 'none' : '';
            };
            toggleUrlField();
            parentSelect.addEventListener('change', toggleUrlField);
        })();
    </script>
@endpush
