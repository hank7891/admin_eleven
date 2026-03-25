@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ empty($data['id']) ? '新增選單' : '編輯選單' }}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?= asset('admin/') ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?= asset('admin/admin.menu/list') ?>">選單管理</a></li>
                            <li class="breadcrumb-item active">{{ empty($data['id']) ? '新增' : '編輯' }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">基本資料</h3>
                            </div>
                            <form action="{{ asset('admin/admin.menu/edit') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $data['id'] ?? 0 }}">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="inputParentId">類型 / 所屬群組</label>
                                        <select class="form-control" id="inputParentId" name="parent_id">
                                            <option value="0" {{ ($data['parent_id'] ?? 0) == 0 ? 'selected' : '' }}>群組（最上層）</option>
                                            @foreach ($groups as $group)
                                                <option value="{{ $group['id'] }}" {{ ($data['parent_id'] ?? 0) == $group['id'] ? 'selected' : '' }}>
                                                    選單項目 → {{ $group['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputName">名稱 <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control"
                                               id="inputName"
                                               name="name"
                                               placeholder="輸入選單名稱"
                                               value="{{ $data['name'] ?? '' }}"
                                               required>
                                    </div>

                                    <div class="form-group" id="urlGroup">
                                        <label for="inputUrl">連結路徑</label>
                                        <input type="text"
                                               class="form-control"
                                               id="inputUrl"
                                               name="url"
                                               placeholder="例如：/admin/employee/list"
                                               value="{{ $data['url'] ?? '' }}">
                                        <small class="form-text text-muted">群組不需填寫，選單項目必填</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputIcon">圖示 Class</label>
                                        <input type="text"
                                               class="form-control"
                                               id="inputIcon"
                                               name="icon"
                                               placeholder="例如：fas fa-users"
                                               value="{{ $data['icon'] ?? '' }}">
                                        <small class="form-text text-muted">
                                            使用 Font Awesome 圖示，留空則自動帶入預設值。
                                            <a href="https://fontawesome.com/v5/search" target="_blank">查詢圖示</a>
                                        </small>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputSortOrder">排序</label>
                                        <input type="number"
                                               class="form-control"
                                               id="inputSortOrder"
                                               name="sort_order"
                                               placeholder="數字越小越前面"
                                               value="{{ $data['sort_order'] ?? 0 }}"
                                               min="0"
                                               required>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputIsActive">啟用狀態</label>
                                        <select class="form-control" id="inputIsActive" name="is_active">
                                            @foreach (config('constants.status') as $key => $label)
                                                <option value="{{ $key }}" {{ ($data['is_active'] ?? STATUS_ACTIVE) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">儲存</button>
                                    <a href="{{ asset('admin/admin.menu/list') }}" class="btn btn-secondary ml-2">返回列表</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- 操作欄 -->
                    @if(!empty($data['id']))
                    <div class="col-md-4">
                        <div class="card card-outline card-danger">
                            <div class="card-header">
                                <h3 class="card-title">危險操作</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ asset('admin/admin.menu/delete/' . $data['id']) }}" method="POST"
                                      onsubmit="return confirm('確定要刪除此選單嗎？')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-block">
                                        <i class="fas fa-trash"></i> 刪除此選單
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    <script>
        $(function () {
            # 根據類型切換 URL 欄位顯示
            function toggleUrlField() {
                var parentId = $('#inputParentId').val();
                if (parentId == '0') {
                    $('#urlGroup').hide();
                } else {
                    $('#urlGroup').show();
                }
            }
            toggleUrlField();
            $('#inputParentId').on('change', toggleUrlField);
        });
    </script>
@endsection
