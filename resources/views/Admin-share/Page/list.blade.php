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
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Dashboard v1</li>
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
                                <h3 class="card-title">列表資訊</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="mailbox-controls">
                                    <!-- Check all button -->
                                    <a class="btn btn-default btn-sm" href={{$editUrl . '0'}}>
                                        <i class="fas fa-plus"></i>
                                    </a>

                                    <button type="button" class="btn btn-default btn-sm">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th style="width: 10px;">#</th>
                                        <th style="width: 60px;"></th>
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
                                                    <a class="btn btn-primary" href={{$editUrl . $value['id']}}>編輯</a>
                                                </td>
                                                @foreach($fields as $filed_key => $field)
                                                    <td>{{$value[$field] ?? ''}}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>#</th>
                                        <th></th>
                                        @foreach($fields as $filed_key => $field)
                                            <th>{{$filed_key}}</th>
                                        @endforeach
                                    </tr>
                                    </tfoot>
                                </table>
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
            $("#example1").DataTable({
                "responsive": true, "lengthChange": false, "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>

    <!-- DataTables  & Plugins -->
    <script src='<?= asset("admin-layout/plugins/datatables/jquery.dataTables.min.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/datatables-responsive/js/dataTables.responsive.min.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/datatables-responsive/js/responsive.bootstrap4.min.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/datatables-buttons/js/dataTables.buttons.min.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/datatables-buttons/js/buttons.bootstrap4.min.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/jszip/jszip.min.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/pdfmake/pdfmake.min.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/pdfmake/vfs_fonts.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/datatables-buttons/js/buttons.html5.min.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/datatables-buttons/js/buttons.print.min.js") ?>'></script>
    <script src='<?= asset("admin-layout/plugins/datatables-buttons/js/buttons.colVis.min.js") ?>'></script>
@stop
