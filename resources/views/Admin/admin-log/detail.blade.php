@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">操作日誌詳情</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?= asset('admin/') ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?= asset('admin/admin.log/list') ?>">操作日誌</a></li>
                            <li class="breadcrumb-item active">詳情</li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <!-- 基本信息卡片 -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">基本信息</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">日誌 ID</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">{{$data['id']}}</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">操作者名稱</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">{{$data['operator_name']}}</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">操作者 IP</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">{{$data['ip_address']}}</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">操作模組</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">
                                            <span class="badge badge-info">{{$data['module_display']}}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">操作類型</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">
                                            <span class="badge
                                                @if($data['action'] === 'create') badge-success
                                                @elseif($data['action'] === 'update') badge-warning
                                                @elseif($data['action'] === 'delete') badge-danger
                                                @else badge-secondary
                                                @endif
                                            ">{{$data['action_display']}}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">被操作資源</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">
                                            @if($data['target_id'])
                                                ID: <strong>{{$data['target_id']}}</strong>
                                                @if($data['target_name'])
                                                    - {{$data['target_name']}}
                                                @endif
                                            @else
                                                --
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">操作時間</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">{{$data['operated_at']}}</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">記錄建立時間</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">{{$data['created_at']}}</div>
                                    </div>
                                </div>

                                @if($data['remarks'])
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">備註</label>
                                        <div class="col-sm-9">
                                            <div class="form-control-static">{{$data['remarks']}}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- 修改詳情卡片 -->
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">修改詳情</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                @if(is_array($data['changes']) && count($data['changes']) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr class="table-header">
                                                    <th style="width: 25%;">欄位名稱</th>
                                                    <th style="width: 37.5%;">修改前</th>
                                                    <th style="width: 37.5%;">修改後</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data['changes'] as $field => $change)
                                                    <tr>
                                                        <td><strong>{{$field}}</strong></td>
                                                        <td>
                                                            @if(is_array($change) && isset($change['old']))
                                                                <code class="bg-light p-2 d-block">{{is_array($change['old']) ? json_encode($change['old'], JSON_UNESCAPED_UNICODE) : ($change['old'] ?? '--')}}</code>
                                                            @else
                                                                <code class="bg-light p-2 d-block">{{is_array($change) ? json_encode($change, JSON_UNESCAPED_UNICODE) : ($change ?? '--')}}</code>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if(is_array($change) && isset($change['new']))
                                                                <code class="bg-light p-2 d-block">{{is_array($change['new']) ? json_encode($change['new'], JSON_UNESCAPED_UNICODE) : ($change['new'] ?? '--')}}</code>
                                                            @else
                                                                <code class="bg-light p-2 d-block">{{is_array($change) ? json_encode($change, JSON_UNESCAPED_UNICODE) : ($change ?? '--')}}</code>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-info">
                                        <i class="icon fas fa-info"></i> 無修改詳情
                                    </div>
                                @endif
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>

                    <!-- 操作欄 -->
                    <div class="col-md-4">
                        <div class="card card-outline card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">操作</h3>
                            </div>
                            <div class="card-body">
                                <a href="<?= asset('admin/admin.log/list') ?>" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left"></i> 返回列表
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>        <!-- /.content -->
    </div>

    <style>
        .form-control-static {
            padding-top: 7px;
            padding-bottom: 7px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding-left: 10px;
            background-color: #f9f9f9;
        }

        .table-header {
            background-color: #e8f4f8;
        }

        code {
            word-break: break-all;
            white-space: pre-wrap;
        }
    </style>
@endsection

