@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <div class="p-6 lg:p-10 space-y-8">
            <div>
                <x-breadcrumb :items="[['label' => '首頁', 'url' => 'admin/'], ['label' => '商品標籤管理', 'url' => 'admin/product.tag/list'], ['label' => empty($data['id']) ? '新增' : '編輯']]" />
                <h2 class="text-[1.5rem] font-bold font-headline">{{ empty($data['id']) ? '新增標籤' : '編輯標籤' }}</h2>
            </div>

            <form action="{{ asset('admin/product.tag/edit') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @csrf
                <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">

                <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl p-6 space-y-5">
                    <div>
                        <label class="text-sm">名稱 <span class="text-error">*</span></label>
                        <input name="name" value="{{ $data['name'] ?? '' }}" class="mt-2 w-full bg-surface-container-low rounded-lg border-none px-4 py-3" type="text">
                    </div>
                    <div>
                        <label class="text-sm">狀態</label>
                        <select name="is_active" class="mt-2 w-full bg-surface-container-low rounded-lg border-none px-4 py-3">
                            @foreach (config('constants.status') as $key => $label)
                                <option value="{{ $key }}" {{ (string) ($data['is_active'] ?? STATUS_ACTIVE) === (string) $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="bg-surface-container-lowest rounded-xl p-6 h-fit space-y-4">
                    <button type="submit" class="w-full btn-primary py-3 rounded-xl">儲存</button>
                    <a href="{{ asset('admin/product.tag/list') }}" class="w-full text-center block py-3 rounded-xl bg-surface-container-high text-on-surface no-underline">返回列表</a>

                    @if (!empty($data['id']))
                        <form action="{{ asset('admin/product.tag/delete/' . $data['id']) }}" method="POST" onsubmit="return confirm('確定要刪除此標籤嗎？')">
                            @csrf
                            <button class="w-full py-3 rounded-xl bg-error text-on-error" type="submit">刪除標籤</button>
                        </form>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection

