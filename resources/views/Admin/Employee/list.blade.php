@extends('Admin-share/index')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp

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
                        <h1 class="m-0">會員管理</h1>
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
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>NAME</th>
                                        <th>建立時間</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data as $user)
                                            <tr>
                                                <td>{{$user['id']}}</td>
                                                <td>{{$user['name']}}</td>
                                                <td>{{$user['created_at']}}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>ID</th>
                                        <th>NAME</th>
                                        <th>建立時間</th>
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
