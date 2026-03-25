@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{(empty($data['id'])) ? '角色新增' : '角色編輯'}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?= asset('admin/') ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?= asset('admin/acl.role/list') ?>">角色管理</a></li>
                            <li class="breadcrumb-item active">{{(empty($data['id'])) ? '新增' : '編輯'}}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <form action={{asset('admin/acl.role/edit')}} method="POST">
                    @csrf
                    <input type="hidden" name="id" value={{ $data['id'] ?? 0 }}>

                    {{-- 基本資料 --}}
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">基本資料</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="inputRoleName">角色名稱</label>
                                <input type="text"
                                       class="form-control"
                                       id="inputRoleName"
                                       name="role_name"
                                       placeholder="請輸入角色名稱"
                                       value="{{$data['role_name'] ?? ''}}">
                            </div>
                        </div>
                    </div>

                    {{-- 選單權限 --}}
                    <div class="card card-outline card-primary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-shield-alt mr-1"></i>選單權限</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" id="btnSelectAll" title="全選">
                                    <i class="fas fa-check-double"></i> 全選
                                </button>
                                <button type="button" class="btn btn-tool" id="btnDeselectAll" title="取消全選">
                                    <i class="fas fa-times"></i> 清除
                                </button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @foreach ($menuTree ?? [] as $group)
                                <div class="permission-group {{ !$loop->last ? 'border-bottom' : '' }}">
                                    {{-- 群組標題列 --}}
                                    <div class="d-flex align-items-center px-3 py-2 bg-light">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input group-checkbox"
                                                   type="checkbox"
                                                   id="group_{{ $loop->index }}"
                                                   data-group="{{ $loop->index }}">
                                            <label class="custom-control-label font-weight-bold" for="group_{{ $loop->index }}">
                                                <i class="{{ $group['item_icon'] ?? 'fas fa-folder' }} mr-1 text-primary"></i>
                                                {{ $group['item_name'] }}
                                            </label>
                                        </div>
                                        <span class="badge badge-light ml-auto group-count" data-group="{{ $loop->index }}">
                                            <span class="checked-count">0</span> / {{ count($group['details']) }}
                                        </span>
                                    </div>
                                    {{-- 子選單項目 --}}
                                    <div class="px-3 py-2">
                                        <div class="row">
                                            @foreach ($group['details'] as $detail)
                                                <div class="col-lg-3 col-md-4 col-sm-6">
                                                    <div class="custom-control custom-checkbox mb-2">
                                                        <input class="custom-control-input menu-checkbox group-{{ $loop->parent->index }}"
                                                               type="checkbox"
                                                               name="menu_ids[]"
                                                               value="{{ $detail['id'] }}"
                                                               id="menu_{{ $detail['id'] }}"
                                                               {{ in_array($detail['id'], $data['menu_ids'] ?? []) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="menu_{{ $detail['id'] }}">
                                                            <i class="{{ $detail['icon'] ?? 'far fa-circle' }} mr-1 text-muted"></i>
                                                            {{ $detail['name'] }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if(empty($menuTree))
                                <div class="p-3 text-center text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>尚無選單可設定，請先至選單管理新增選單
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 送出 --}}
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>儲存
                        </button>
                        <a href="{{ asset('admin/acl.role/list') }}" class="btn btn-default ml-2">
                            <i class="fas fa-arrow-left mr-1"></i>返回列表
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <script>
    $(function() {
        // 更新群組計數與 checkbox 狀態
        function updateGroupState(groupIndex) {
            var total = $('.group-' + groupIndex).length;
            var checked = $('.group-' + groupIndex + ':checked').length;

            $('#group_' + groupIndex).prop('checked', checked === total && total > 0);
            $('#group_' + groupIndex).prop('indeterminate', checked > 0 && checked < total);

            // 更新計數 badge
            $('.group-count[data-group="' + groupIndex + '"] .checked-count').text(checked);
        }

        // 群組 checkbox 全選/取消
        $('.group-checkbox').on('change', function() {
            var groupIndex = $(this).data('group');
            $('.group-' + groupIndex).prop('checked', this.checked);
            updateGroupState(groupIndex);
        });

        // 子選單 checkbox 變更
        $('.menu-checkbox').on('change', function() {
            var groupClass = this.className.split(' ').find(function(c) { return c.startsWith('group-'); });
            if (groupClass) {
                updateGroupState(groupClass.replace('group-', ''));
            }
        });

        // 全選按鈕
        $('#btnSelectAll').on('click', function() {
            $('.menu-checkbox').prop('checked', true);
            $('.group-checkbox').each(function() { updateGroupState($(this).data('group')); });
        });

        // 清除按鈕
        $('#btnDeselectAll').on('click', function() {
            $('.menu-checkbox').prop('checked', false);
            $('.group-checkbox').each(function() { updateGroupState($(this).data('group')); });
        });

        // 初始化所有群組狀態
        $('.group-checkbox').each(function() { updateGroupState($(this).data('group')); });
    });
    </script>
@stop
