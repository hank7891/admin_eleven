@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">登入日誌詳情</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?= asset('admin/') ?>">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?= asset('admin/admin.login-log/list') ?>">登入日誌</a></li>
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
                                        <div class="form-control-static">{{ $data['id'] }}</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">登入帳號</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">{{ $data['account'] }}</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">帳號姓名</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">{{ $data['employee_name'] }}</div>
                                    </div>
                                </div>

                                @if($data['employee_id'])
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">帳號 ID</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">{{ $data['employee_id'] }}</div>
                                    </div>
                                </div>
                                @endif

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">操作類型</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">
                                            <span class="badge
                                                @if($data['action'] === 'login') badge-info
                                                @else badge-secondary
                                                @endif
                                            ">{{ $data['action_display'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">狀態</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">
                                            <span class="badge
                                                @if($data['status'] == 1) badge-success
                                                @else badge-danger
                                                @endif
                                            ">{{ $data['status_display'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($data['fail_reason'])
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">失敗原因</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static text-danger">{{ $data['fail_reason'] }}</div>
                                    </div>
                                </div>
                                @endif

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">IP 位址</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">{{ $data['ip_address'] }}</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">操作時間</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">{{ $data['operated_at'] }}</div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">記錄建立時間</label>
                                    <div class="col-sm-9">
                                        <div class="form-control-static">{{ $data['created_at'] }}</div>
                                    </div>
                                </div>
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
                                <a href="<?= asset('admin/admin.login-log/list') ?>" class="btn btn-secondary btn-block">
                                    <i class="fas fa-arrow-left"></i> 返回列表
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
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
    </style>
@endsection
