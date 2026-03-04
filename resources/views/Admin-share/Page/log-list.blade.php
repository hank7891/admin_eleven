@extends('Admin-share/index')
@section('content')
    <!-- DataTables -->
    <link rel="stylesheet" href='<?= asset("admin-layout/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css") ?>'>
    <link rel="stylesheet" href='<?= asset("admin-layout/plugins/datatables-responsive/css/responsive.bootstrap4.min.css") ?>'>
    <link rel="stylesheet" href='<?= asset("admin-layout/plugins/datatables-buttons/css/buttons.bootstrap4.min.css") ?>'>

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{$pageTitle}}</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="<?= asset('admin/') ?>">Home</a></li>
                            <li class="breadcrumb-item active">{{$pageTitle}}</li>
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
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">日誌列表</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="mailbox-controls mb-3">
                                    <button type="button" class="btn btn-default btn-sm" onclick="location.reload()">
                                        <i class="fas fa-sync-alt"></i> 重新整理
                                    </button>
                                </div>

                                <table id="logsTable" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th style="width: 10px;">#</th>
                                        <th style="width: 80px;">詳情</th>
                                        @foreach($fields as $filed_key => $field)
                                            <th>{{$filed_key}}</th>
                                        @endforeach
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $key => $value)
                                            <tr>
                                                <td>{{$key + 1}}</td>
                                                <td>
                                                    <a class="btn btn-primary btn-xs" href={{$editUrl . $value['id']}} title="查看詳情">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                                @foreach($fields as $filed_key => $field)
                                                    <td>{{$value[$field] ?? '--'}}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th>詳情</th>
                                        @foreach($fields as $filed_key => $field)
                                            <th>{{$filed_key}}</th>
                                        @endforeach
                                    </tr>
                                    </tfoot>
                                </table>

                                <!-- 分頁 -->
                                @if(isset($pagination) && $pagination->hasPages())
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $pagination->links('pagination::bootstrap-4') }}
                                    </div>
                                @endif
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>

    <script>
        $(function () {
            $("#logsTable").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "paging": false,
                "searching": true,
                "ordering": true,
            });
        });
    </script>
@endsection

