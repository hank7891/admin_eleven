@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '商品管理']]" />
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">商品管理</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">管理商品基本資訊、圖片、上下架與檔期設定</p>
                </div>
                <a href="{{ asset('admin/product/edit/0') }}" class="btn-primary px-6 py-2.5 rounded-xl flex items-center gap-2 no-underline">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    <span class="text-[0.875rem] font-semibold">新增商品</span>
                </a>
            </div>

            <form method="GET" action="{{ url('admin/product/list') }}">
                <div class="bg-surface-container-lowest rounded-xl p-6 shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)]">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 items-end">
                        <div class="md:col-span-2">
                            <label class="text-[0.8rem] font-semibold text-outline">關鍵字</label>
                            <input name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="mt-2 w-full bg-surface-container-low rounded-xl border-none px-4 py-3" placeholder="搜尋商品名稱/標語" type="text" />
                        </div>
                        <div>
                            <label class="text-[0.8rem] font-semibold text-outline">類別</label>
                            <select name="category_id" class="mt-2 w-full bg-surface-container-low rounded-xl border-none px-4 py-3">
                                <option value="">全部類別</option>
                                @foreach (($filterOptions['categories'] ?? []) as $category)
                                    <option value="{{ $category['id'] }}" {{ (string) ($filters['category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' }}>{{ $category['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[0.8rem] font-semibold text-outline">標籤</label>
                            <select name="tag_id" class="mt-2 w-full bg-surface-container-low rounded-xl border-none px-4 py-3">
                                <option value="">全部標籤</option>
                                @foreach (($filterOptions['tags'] ?? []) as $tag)
                                    <option value="{{ $tag['id'] }}" {{ (string) ($filters['tag_id'] ?? '') === (string) $tag['id'] ? 'selected' : '' }}>{{ $tag['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[0.8rem] font-semibold text-outline">狀態</label>
                            <select name="status_key" class="mt-2 w-full bg-surface-container-low rounded-xl border-none px-4 py-3">
                                <option value="">全部狀態</option>
                                @foreach (($filterOptions['statuses'] ?? []) as $key => $label)
                                    <option value="{{ $key }}" {{ (string) ($filters['status_key'] ?? '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[0.8rem] font-semibold text-outline">主打</label>
                            <select name="is_featured" class="mt-2 w-full bg-surface-container-low rounded-xl border-none px-4 py-3">
                                <option value="">全部</option>
                                @foreach (($filterOptions['featured_options'] ?? []) as $key => $label)
                                    <option value="{{ $key }}" {{ (string) ($filters['is_featured'] ?? '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-[0.8rem] font-semibold text-outline">時間狀態</label>
                            <select name="period_state" class="mt-2 w-full bg-surface-container-low rounded-xl border-none px-4 py-3">
                                <option value="">全部</option>
                                @foreach (($filterOptions['period_states'] ?? []) as $key => $label)
                                    <option value="{{ $key }}" {{ (string) ($filters['period_state'] ?? '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-4 flex justify-end gap-3">
                            <button type="submit" class="btn-primary px-7 py-3 rounded-xl">搜尋</button>
                            <a href="{{ url('admin/product/list') }}" class="bg-surface-container-high px-5 py-3 rounded-xl text-on-surface no-underline">清除</a>
                        </div>
                    </div>
                </div>
            </form>

            <form method="POST" action="{{ url('admin/product/bulk-status') }}" onsubmit="return confirm('確定要批次更新所選商品狀態嗎？');">
                @csrf
                @foreach (($filters ?? []) as $key => $value)
                    @if ($value !== '' && $value !== null)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach

                <div class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)]">
                    <div class="p-4 border-b border-outline-variant/15 flex items-center gap-3">
                        <select name="status_key" class="bg-surface-container-low rounded-lg border-none px-3 py-2">
                            <option value="{{ PRODUCT_STATUS_ONLINE }}">批次上架</option>
                            <option value="{{ PRODUCT_STATUS_OFFLINE }}">批次下架</option>
                        </select>
                        <button type="submit" class="btn-primary px-4 py-2 rounded-lg">執行</button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="table-header-row">
                                    <th class="px-4 py-3 text-left"><input type="checkbox" id="checkAll"></th>
                                    <th class="px-4 py-3 text-left">操作</th>
                                    <th class="px-4 py-3 text-left">商品</th>
                                    <th class="px-4 py-3 text-left">價格</th>
                                    <th class="px-4 py-3 text-left">類別/標籤</th>
                                    <th class="px-4 py-3 text-left">主打</th>
                                    <th class="px-4 py-3 text-left">狀態</th>
                                    <th class="px-4 py-3 text-left">檔期</th>
                                    <th class="px-4 py-3 text-left">更新時間</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/10">
                                @forelse ($data as $row)
                                    <tr>
                                        <td class="px-4 py-4"><input type="checkbox" name="ids[]" value="{{ $row['id'] }}" class="row-checkbox"></td>
                                        <td class="px-4 py-4">
                                            <a href="{{ asset('admin/product/edit/' . $row['id']) }}" class="text-primary no-underline">編輯</a>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center gap-3">
                                                @if (!empty($row['primary_image_url']))
                                                    <img src="{{ $row['primary_image_url'] }}" alt="{{ $row['name'] }}" class="w-12 h-12 rounded-lg object-cover">
                                                @endif
                                                <div>
                                                    <div class="font-semibold">{{ $row['name'] }}</div>
                                                    <div class="text-xs text-outline">{{ $row['tagline'] ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">{{ $row['price_display'] }}</td>
                                        <td class="px-4 py-4">
                                            <div>{{ $row['category_name'] }}</div>
                                            <div class="text-xs text-outline">{{ implode(' / ', $row['tag_names'] ?? []) }}</div>
                                        </td>
                                        <td class="px-4 py-4">{{ $row['is_featured_display'] }}</td>
                                        <td class="px-4 py-4">
                                            <span class="inline-flex px-2 py-1 rounded-full text-xs {{ $row['status_badge_class'] ?? '' }}">{{ $row['status_display'] }}</span>
                                        </td>
                                        <td class="px-4 py-4">{{ $row['period_display'] }}</td>
                                        <td class="px-4 py-4">{{ $row['updated_at_display'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-4 py-8 text-center text-outline" colspan="9">尚無商品資料</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (!empty($data))
                        <x-stitch-pagination :paginator="$pagination" :filters="$filters" />
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('checkAll')?.addEventListener('change', function () {
            document.querySelectorAll('.row-checkbox').forEach((node) => {
                node.checked = this.checked;
            });
        });
    </script>
@endpush

