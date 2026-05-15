@extends('layouts.admin')

@section('title-suffix', ' · 商品管理')

@section('content')
    <x-admin.page-head
        title="商品管理"
        subtitle="管理商品基本資訊、圖片、上下架與檔期設定"
        :breadcrumbs="[['label' => '首頁', 'url' => 'admin/'], ['label' => '商品管理']]"
    >
        <x-slot:actions>
            <x-admin.button as="a" :href="url('admin/product/edit/0')" iconLeft="add">新增商品</x-admin.button>
        </x-slot:actions>
    </x-admin.page-head>

    <x-admin.filter-card :action="url('admin/product/list')" title="篩選">
        <x-admin.input
            name="keyword"
            label="關鍵字"
            :value="$filters['keyword'] ?? ''"
            placeholder="搜尋商品名稱 / 標語"
            icon="search"
        />
        <x-admin.select
            name="category_id"
            label="類別"
            :options="collect($filterOptions['categories'] ?? [])->pluck('name', 'id')->all()"
            :value="$filters['category_id'] ?? ''"
            placeholder="全部類別"
        />
        <x-admin.select
            name="tag_id"
            label="標籤"
            :options="collect($filterOptions['tags'] ?? [])->pluck('name', 'id')->all()"
            :value="$filters['tag_id'] ?? ''"
            placeholder="全部標籤"
        />
        <x-admin.select
            name="status_key"
            label="狀態"
            :options="$filterOptions['statuses'] ?? []"
            :value="$filters['status_key'] ?? ''"
            placeholder="全部狀態"
        />
        <x-admin.select
            name="is_featured"
            label="主打"
            :options="$filterOptions['featured_options'] ?? []"
            :value="$filters['is_featured'] ?? ''"
            placeholder="全部"
        />
        <x-admin.select
            name="period_state"
            label="時間狀態"
            :options="$filterOptions['period_states'] ?? []"
            :value="$filters['period_state'] ?? ''"
            placeholder="全部"
        />

        <x-slot:actions>
            <a href="{{ url('admin/product/list') }}" class="admin-btn admin-btn-outline">清除</a>
            <button type="submit" class="admin-btn admin-btn-primary">
                <span class="material-symbols-outlined" aria-hidden="true">search</span>
                <span>搜尋</span>
            </button>
        </x-slot:actions>
    </x-admin.filter-card>

    <form id="bulkStatusForm" method="POST" action="{{ url('admin/product/bulk-status') }}" class="admin-card admin-card-flush" data-bulk-table>
        @csrf
        @foreach (($filters ?? []) as $key => $value)
            @if ($value !== '' && $value !== null && $key !== 'page')
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach

        <div class="admin-bulk-bar">
            <div class="admin-flex admin-gap-sm admin-bulk-bar-left">
                <select name="status_key" id="bulkStatusSelect" class="admin-select admin-select-inline">
                    <option value="{{ PRODUCT_STATUS_ONLINE }}">批次上架</option>
                    <option value="{{ PRODUCT_STATUS_OFFLINE }}">批次下架</option>
                </select>
                <button type="button" id="bulkStatusTrigger" class="admin-btn admin-btn-primary admin-btn-sm">執行</button>
                <span class="admin-text-sm admin-text-mute admin-bulk-counter">
                    已選 <span data-bulk-counter>0</span> 件
                </span>
            </div>
            <div class="admin-text-sm admin-text-mute">共 {{ $pagination->total() ?? 0 }} 件商品</div>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="admin-table-checkbox">
                            <input type="checkbox" class="admin-checkbox" data-bulk-toggle-all id="checkAll" aria-label="全選">
                        </th>
                        <th>操作</th>
                        <th>商品</th>
                        <th>價格</th>
                        <th>類別 / 標籤</th>
                        <th>主打</th>
                        <th>狀態</th>
                        <th>檔期</th>
                        <th>更新時間</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr>
                            <td>
                                <input type="checkbox" class="admin-checkbox row-checkbox" name="ids[]" value="{{ $row['id'] }}" data-bulk-item aria-label="選擇 {{ $row['name'] }}">
                            </td>
                            <td>
                                <a href="{{ asset('admin/product/edit/' . $row['id']) }}" class="admin-link-strong">編輯</a>
                            </td>
                            <td>
                                <div class="admin-flex admin-gap-md admin-product-cell">
                                    @if (!empty($row['primary_image_url']))
                                        <img src="{{ $row['primary_image_url'] }}" alt="{{ $row['name'] }}" class="admin-thumb">
                                    @else
                                        <div class="admin-thumb admin-thumb-empty" aria-hidden="true"></div>
                                    @endif
                                    <div>
                                        <div class="admin-strong">{{ $row['name'] }}</div>
                                        @if (!empty($row['tagline']))
                                            <div class="admin-mute">{{ $row['tagline'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><span class="admin-strong">{{ $row['price_display'] }}</span></td>
                            <td>
                                <div>{{ $row['category_name'] }}</div>
                                @if (!empty($row['tag_names']))
                                    <div class="admin-mute">{{ implode(' / ', $row['tag_names']) }}</div>
                                @endif
                            </td>
                            <td>
                                @if (!empty($row['is_featured']))
                                    <x-admin.badge tone="info">主打</x-admin.badge>
                                @else
                                    <span class="admin-text-mute admin-text-sm">—</span>
                                @endif
                            </td>
                            <td>
                                <x-admin.badge tone="{{ $row['status_tone'] ?? 'neutral' }}">{{ $row['status_display'] }}</x-admin.badge>
                            </td>
                            <td>{{ $row['period_display'] }}</td>
                            <td>{{ $row['updated_at_display'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="admin-table-empty">尚無商品資料</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (!empty($data))
            <x-stitch-pagination :paginator="$pagination" :filters="$filters" />
        @endif
    </form>

    {{-- 批次確認 modal --}}
    <div id="bulkConfirmModal" class="admin-modal" hidden>
        <div class="admin-modal-backdrop" data-modal-close></div>
        <div class="admin-modal-content" role="dialog" aria-modal="true" aria-labelledby="bulkConfirmTitle">
            <h3 id="bulkConfirmTitle" class="admin-section-title">確認批次操作</h3>
            <p id="bulkConfirmMessage" class="admin-text-sm admin-text-mute"></p>
            <div class="admin-modal-actions">
                <button type="button" id="bulkConfirmCancel" class="admin-btn admin-btn-outline">取消</button>
                <button type="button" id="bulkConfirmOk" class="admin-btn admin-btn-primary">確定執行</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const trigger = document.getElementById('bulkStatusTrigger');
            const modal = document.getElementById('bulkConfirmModal');
            const message = document.getElementById('bulkConfirmMessage');
            const cancelBtn = document.getElementById('bulkConfirmCancel');
            const okBtn = document.getElementById('bulkConfirmOk');
            const form = document.getElementById('bulkStatusForm');
            const select = document.getElementById('bulkStatusSelect');

            if (!trigger || !modal || !form || !select) return;

            const closeModal = () => {
                modal.setAttribute('hidden', '');
            };

            const openModal = () => {
                modal.removeAttribute('hidden');
            };

            trigger.addEventListener('click', () => {
                const checked = form.querySelectorAll('[data-bulk-item]:checked');
                if (checked.length === 0) return;
                const action = select.options[select.selectedIndex].text;
                message.textContent = `即將對 ${checked.length} 件商品執行「${action}」，確定要繼續嗎？`;
                openModal();
            });

            cancelBtn?.addEventListener('click', closeModal);
            modal.querySelector('[data-modal-close]')?.addEventListener('click', closeModal);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && !modal.hasAttribute('hidden')) closeModal();
            });
            okBtn?.addEventListener('click', () => { form.submit(); });
        })();
    </script>
@endpush
