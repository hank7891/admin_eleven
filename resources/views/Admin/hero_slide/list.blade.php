@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '輪播管理']]" />
                    <h2 class="text-[1.5rem] font-bold text-on-surface tracking-tight font-headline">輪播管理</h2>
                    <p class="text-[0.8125rem] text-outline mt-1">維護首頁 Hero 輪播圖片、標語、排序與顯示時段</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ asset('admin/hero-slide/edit/0') }}" class="btn-primary px-6 py-2.5 rounded-xl flex items-center gap-2 hover:scale-105 active:scale-95 transition-all duration-200 no-underline">
                        <span class="material-symbols-outlined text-[20px]">add</span>
                        <span class="text-[0.875rem] font-semibold">新增輪播</span>
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ url('admin/hero-slide/list') }}">
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px]">slideshow</span>
                                關鍵字
                            </label>
                            <input name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem] focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/30 transition-all placeholder:text-outline-variant" placeholder="搜尋眉標 / 標題 / 說明" type="text" />
                        </div>
                        <div class="space-y-2">
                            <label class="text-[0.8125rem] font-semibold uppercase tracking-widest text-outline">狀態</label>
                            <select name="is_active" class="w-full bg-surface-container-low border-none rounded-xl px-4 py-3 text-[0.875rem] focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary/30 transition-all appearance-none cursor-pointer">
                                <option value="">全部狀態</option>
                                @foreach ($statusOptions as $key => $label)
                                    <option value="{{ $key }}" {{ (string) ($filters['is_active'] ?? '') === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 btn-primary py-3 rounded-xl font-semibold text-[0.875rem] active:scale-95 transition-all">搜尋</button>
                            <a href="{{ url('admin/hero-slide/list') }}" class="px-5 bg-surface-container-high text-on-surface py-3 rounded-xl font-semibold text-[0.875rem] hover:bg-surface-container-highest transition-colors active:scale-95 no-underline flex items-center">清除</a>
                        </div>
                    </div>
                </div>
            </form>

            @if (empty($data))
                <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] p-12 text-center">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant/40 mb-3 block">slideshow</span>
                    <p class="text-outline text-[0.875rem]">尚無輪播資料</p>
                </div>
            @else
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                    @foreach ($data as $row)
                        <div class="bg-surface-container-lowest rounded-xl shadow-[0_24px_40px_-4px_rgba(23,28,31,0.06)] overflow-hidden border border-outline-variant/10">
                            <div class="grid grid-cols-1 md:grid-cols-[220px_1fr] gap-0">
                                <div class="bg-surface-container-low p-4 flex items-center justify-center">
                                    @if (!empty($row['image_url']))
                                        <img src="{{ $row['image_url'] }}" alt="{{ $row['image_alt'] ?: $row['title'] }}" class="w-full h-52 object-cover rounded-xl">
                                    @else
                                        <div class="w-full h-52 rounded-xl bg-surface-container-high flex items-center justify-center text-outline-variant">
                                            <span class="material-symbols-outlined text-[32px]">image</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-6 space-y-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-[0.75rem] uppercase tracking-[0.2em] text-primary font-semibold">{{ $row['eyebrow'] ?: '未設定眉標' }}</p>
                                            <h3 class="mt-2 text-[1.125rem] font-bold text-on-surface">{{ $row['title'] }}</h3>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.75rem] font-bold {{ $row['status_badge_class'] ?? 'bg-slate-100 text-slate-500' }}">{{ $row['is_active_display'] }}</span>
                                    </div>

                                    <p class="text-[0.875rem] text-outline">{{ $row['description_preview'] ?: '尚無說明文字' }}</p>

                                    <div class="grid grid-cols-2 gap-4 text-[0.8125rem]">
                                        <div>
                                            <div class="text-outline-variant">排序</div>
                                            <div class="font-semibold text-on-surface">{{ $row['sort_order'] }}</div>
                                        </div>
                                        <div>
                                            <div class="text-outline-variant">開始時間</div>
                                            <div class="font-semibold text-on-surface">{{ $row['start_at_display'] }}</div>
                                        </div>
                                        <div>
                                            <div class="text-outline-variant">結束時間</div>
                                            <div class="font-semibold text-on-surface">{{ $row['end_at_display'] }}</div>
                                        </div>
                                        <div>
                                            <div class="text-outline-variant">圖片點擊連結</div>
                                            <div class="font-semibold text-on-surface">{{ !empty($row['target_url']) ? '已設定' : '未設定' }}</div>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-3 pt-2">
                                        <a href="{{ asset('admin/hero-slide/edit/' . $row['id']) }}" class="inline-flex items-center gap-1.5 text-primary hover:bg-primary/5 px-3 py-2 rounded-lg transition-colors font-semibold text-[0.875rem] no-underline">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                            編輯
                                        </a>
                                        <button type="button" class="toggle-active-btn inline-flex items-center gap-1.5 px-3 py-2 rounded-lg font-semibold text-[0.875rem] {{ (int) ($row['is_active'] ?? STATUS_INACTIVE) === STATUS_ACTIVE ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} transition-colors" data-id="{{ $row['id'] }}">
                                            <span class="material-symbols-outlined text-[18px]">toggle_on</span>
                                            {{ (int) ($row['is_active'] ?? STATUS_INACTIVE) === STATUS_ACTIVE ? '停用' : '啟用' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <x-stitch-pagination :paginator="$pagination" :filters="$filters" />
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        $(function () {
            $('.toggle-active-btn').on('click', function () {
                const button = $(this);
                const id = button.data('id');
                const originalText = button.text().trim();

                button.prop('disabled', true);

                $.ajax({
                    url: `{{ url('admin/hero-slide/toggle-active') }}/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function () {
                        window.location.reload();
                    },
                    error: function (xhr) {
                        alert(xhr.responseJSON?.message || '狀態更新失敗');
                        button.prop('disabled', false).text(originalText);
                    }
                });
            });
        });
    </script>
    @endpush
@stop

