@extends('Admin-share/index')
@section('content')
    <!-- DataTables -->
    <link rel="stylesheet" href='<?= asset("admin-layout/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css") ?>'>
    <link rel="stylesheet" href='<?= asset("admin-layout/plugins/datatables-responsive/css/responsive.bootstrap4.min.css") ?>'>

    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">登入日誌</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?= asset('admin/') ?>">Home</a></li>
                            <li class="breadcrumb-item active">登入日誌</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                {{-- 搜尋篩選 --}}
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-search mr-1"></i>搜尋條件</h3>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ asset('admin/admin.login-log/list') }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>操作者名稱 / 帳號</label>
                                        <input type="text"
                                               class="form-control"
                                               name="operator_keyword"
                                               placeholder="模糊搜尋"
                                               value="{{ $filters['operator_keyword'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>IP 位址</label>
                                        <input type="text"
                                               class="form-control"
                                               name="ip_address"
                                               placeholder="模糊搜尋"
                                               value="{{ $filters['ip_address'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>操作</label>
                                        <select class="form-control" name="action">
                                            <option value="">全部</option>
                                            @foreach ($actionOptions ?? [] as $key => $label)
                                                <option value="{{ $key }}" {{ ($filters['action'] ?? '') === $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>狀態</label>
                                        <select class="form-control" name="status">
                                            <option value="">全部</option>
                                            @foreach ($statusOptions ?? [] as $key => $label)
                                                <option value="{{ $key }}" {{ (!is_null($filters['status'] ?? null) && $filters['status'] !== '' && (int)$filters['status'] === $key) ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>開始時間</label>
                                        <input type="date"
                                               class="form-control"
                                               name="date_from"
                                               value="{{ $filters['date_from'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>結束時間</label>
                                        <input type="date"
                                               class="form-control"
                                               name="date_to"
                                               value="{{ $filters['date_to'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search mr-1"></i>搜尋
                                    </button>
                                    <a href="{{ asset('admin/admin.login-log/list') }}" class="btn btn-default ml-2">
                                        <i class="fas fa-eraser mr-1"></i>清除
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 資料列表 --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">列表資訊</h3>
                    </div>
                    <div class="card-body">
                        @if (!($hasFilter ?? false))
                            {{-- 尚未搜尋 --}}
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-search fa-3x mb-3 d-block"></i>
                                <p>請輸入搜尋條件後查詢</p>
                            </div>
                        @elseif (empty($data))
                            {{-- 搜尋無結果 --}}
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                <p>查無符合條件的資料</p>
                            </div>
                        @else
                            <table id="logTable" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 10px;">#</th>
                                    <th style="width: 60px;"></th>
                                    <th>ID</th>
                                    <th>帳號</th>
                                    <th>姓名</th>
                                    <th>操作</th>
                                    <th>狀態</th>
                                    <th>IP 位址</th>
                                    <th>操作時間</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($data as $key => $row)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" href="{{ asset('admin/admin.login-log/detail/' . $row['id']) }}">詳情</a>
                                        </td>
                                        <td>{{ $row['id'] }}</td>
                                        <td>{{ $row['account'] }}</td>
                                        <td>{{ $row['employee_name'] }}</td>
                                        <td>{{ $row['action_display'] }}</td>
                                        <td>
                                            @if ($row['status'] == 1)
                                                <span class="badge badge-success">{{ $row['status_display'] }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ $row['status_display'] }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $row['ip_address'] }}</td>
                                        <td>{{ $row['operated_at'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                            {{-- 分頁 --}}
                            @if (isset($pagination) && $pagination->hasPages())
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $pagination->appends($filters)->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
        </section>
    </div>

    <!-- DataTables -->
    <script src='<?= asset("admin-layout/plugins/datatables/jquery.dataTables.min.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/datatables-responsive/js/dataTables.responsive.min.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/datatables-responsive/js/responsive.bootstrap4.min.js") ?>'></script>
    <script>
        $(function () {
            $('#logTable').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
@stop
