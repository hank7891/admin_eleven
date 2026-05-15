@extends('layouts.admin')

@section('title-suffix', empty($data['id']) ? ' · 新增角色' : ' · 編輯角色')

@section('content')
    <x-admin.page-head
        title="{{ empty($data['id']) ? '新增角色' : '編輯角色' }}"
        subtitle="{{ empty($data['id']) ? '建立新的系統角色' : '修改角色資料與選單權限' }}"
        :breadcrumbs="[
            ['label' => '首頁', 'url' => 'admin/'],
            ['label' => '角色管理', 'url' => 'admin/acl.role/list'],
            ['label' => empty($data['id']) ? '新增' : '編輯'],
        ]"
    >
        <x-slot:actions>
            <a href="{{ asset('admin/acl.role/list') }}" class="admin-btn admin-btn-outline">
                <span class="material-symbols-outlined" aria-hidden="true">arrow_back</span>
                <span>返回列表</span>
            </a>
            <button type="submit" form="aclRoleEditForm" class="admin-btn admin-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">save</span>
                <span>儲存</span>
            </button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.form-error :errors="$errors" />

    <form id="aclRoleEditForm" action="{{ asset('admin/acl.role/edit') }}" method="POST" class="admin-stack admin-stack-lg">
        @csrf
        <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

        <x-admin.card title="基本資料">
            <x-admin.input
                name="role_name"
                label="角色名稱"
                :value="old('role_name', $data['role_name'] ?? '')"
                placeholder="請輸入角色名稱"
                required
                :error="$errors->first('role_name')"
            />
        </x-admin.card>

        <x-admin.card title="選單權限">
            <x-slot:head>
                <div class="admin-flex admin-gap-sm">
                    <button type="button" id="btnSelectAll" class="admin-btn admin-btn-muted admin-btn-sm">
                        <span class="material-symbols-outlined" aria-hidden="true">done_all</span>
                        <span>全選</span>
                    </button>
                    <button type="button" id="btnDeselectAll" class="admin-btn admin-btn-outline admin-btn-sm">
                        <span class="material-symbols-outlined" aria-hidden="true">remove_done</span>
                        <span>清除</span>
                    </button>
                </div>
            </x-slot:head>

            @if (!empty($menuTree))
                <div class="admin-acl-tree">
                    @foreach ($menuTree as $groupIndex => $group)
                        <div class="admin-acl-group">
                            <div class="admin-acl-group-head">
                                <input
                                    type="checkbox"
                                    class="admin-checkbox group-checkbox"
                                    id="group_{{ $groupIndex }}"
                                    data-group="{{ $groupIndex }}"
                                >
                                <label for="group_{{ $groupIndex }}" class="admin-acl-group-label">
                                    <i class="{{ $group['item_icon'] ?? 'fas fa-folder' }}" style="color: var(--color-primary);"></i>
                                    <span>{{ $group['item_name'] }}</span>
                                </label>
                                <span class="admin-badge admin-badge-neutral" data-group="{{ $groupIndex }}">
                                    <span class="checked-count">0</span> / {{ count($group['details']) }}
                                </span>
                            </div>
                            <div class="admin-acl-group-body">
                                @foreach ($group['details'] as $detail)
                                    <label class="admin-checkbox-row admin-acl-item">
                                        <input
                                            type="checkbox"
                                            class="admin-checkbox menu-checkbox group-{{ $groupIndex }}"
                                            name="menu_ids[]"
                                            value="{{ $detail['id'] }}"
                                            {{ in_array($detail['id'], $data['menu_ids'] ?? []) ? 'checked' : '' }}
                                        >
                                        <span class="admin-checkbox-label">{{ $detail['name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <x-admin.empty-state icon="info" title="尚無選單可設定" description="請先至選單管理新增選單。" />
            @endif
        </x-admin.card>
    </form>
@endsection

@push('scripts')
    <script>
        (function () {
            const updateGroupState = (groupIndex) => {
                const items = document.querySelectorAll('.group-' + groupIndex + '.menu-checkbox');
                const checked = Array.from(items).filter((c) => c.checked).length;
                const total = items.length;

                const groupCheckbox = document.getElementById('group_' + groupIndex);
                if (groupCheckbox) {
                    groupCheckbox.checked = checked === total && total > 0;
                    groupCheckbox.indeterminate = checked > 0 && checked < total;
                }

                const counter = document.querySelector('[data-group="' + groupIndex + '"] .checked-count');
                if (counter) counter.textContent = checked;
            };

            document.querySelectorAll('.group-checkbox').forEach((groupCheckbox) => {
                groupCheckbox.addEventListener('change', () => {
                    const groupIndex = groupCheckbox.dataset.group;
                    document.querySelectorAll('.group-' + groupIndex + '.menu-checkbox').forEach((c) => {
                        c.checked = groupCheckbox.checked;
                    });
                    updateGroupState(groupIndex);
                });
            });

            document.querySelectorAll('.menu-checkbox').forEach((c) => {
                c.addEventListener('change', () => {
                    const groupClass = Array.from(c.classList).find((cls) => cls.startsWith('group-'));
                    if (groupClass) updateGroupState(groupClass.replace('group-', ''));
                });
            });

            document.getElementById('btnSelectAll')?.addEventListener('click', () => {
                document.querySelectorAll('.menu-checkbox').forEach((c) => (c.checked = true));
                document.querySelectorAll('.group-checkbox').forEach((g) => updateGroupState(g.dataset.group));
            });

            document.getElementById('btnDeselectAll')?.addEventListener('click', () => {
                document.querySelectorAll('.menu-checkbox').forEach((c) => (c.checked = false));
                document.querySelectorAll('.group-checkbox').forEach((g) => updateGroupState(g.dataset.group));
            });

            document.querySelectorAll('.group-checkbox').forEach((g) => updateGroupState(g.dataset.group));
        })();
    </script>
@endpush
