<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>YoYo Admin | Dashboard</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href='<?= asset("admin-layout/plugins/fontawesome-free/css/all.min.css") ?>'>
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href='<?= asset("admin-layout/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css") ?>'>
    <!-- iCheck -->
    <link rel="stylesheet" href='<?= asset("admin-layout/plugins/icheck-bootstrap/icheck-bootstrap.min.css") ?>'>
    <!-- JQVMap -->
    <link rel="stylesheet" href='<?= asset("admin-layout/plugins/jqvmap/jqvmap.min.css") ?>'>
    <!-- Theme style -->
    <link rel="stylesheet" href='<?= asset("admin-layout/dist/css/adminlte.min.css") ?>'>
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href='<?= asset("admin-layout/plugins/overlayScrollbars/css/OverlayScrollbars.min.css") ?>'>
    <!-- Daterange picker -->
    <link rel="stylesheet" href='<?= asset("admin-layout/plugins/daterangepicker/daterangepicker.css") ?>'>
    <!-- summernote -->
    <link rel="stylesheet" href='<?= asset("admin-layout/plugins/summernote/summernote-bs4.min.css") ?>'>
    <!-- jQuery -->
    <script src='<?= asset("admin-layout/plugins/jquery/jquery.min.js") ?>'></script>
    <!-- jQuery UI 1.11.4 -->
    <script src='<?= asset("admin-layout/plugins/jquery-ui/jquery-ui.min.js") ?>'></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

    @include('Share/message-alert')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <!-- Navbar -->
    @include('Admin-share/navbar')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    @include('Admin-share/main-sidbar')

    <!-- Content Wrapper. Contains page content -->
    @yield('content')
    <!-- /.content-wrapper -->

    @include('Admin-share/footer')
    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src='<?= asset("admin-layout/plugins/bootstrap/js/bootstrap.bundle.min.js") ?>'></script>
<!-- ChartJS -->
<script src='<?= asset("admin-layout/plugins/chart.js/Chart.min.js") ?>'></script>
<!-- Sparkline -->
<script src='<?= asset("admin-layout/plugins/sparklines/sparkline.js") ?>'></script>
<!-- JQVMap -->
<script src='<?= asset("admin-layout/plugins/jqvmap/jquery.vmap.min.js") ?>'></script>
<script src='<?= asset("admin-layout/plugins/jqvmap/maps/jquery.vmap.usa.js") ?>'></script>
<!-- jQuery Knob Chart -->
<script src='<?= asset("admin-layout/plugins/jquery-knob/jquery.knob.min.js") ?>'></script>
<!-- daterangepicker -->
<script src='<?= asset("admin-layout/plugins/moment/moment.min.js") ?>'></script>
<script src='<?= asset("admin-layout/plugins/daterangepicker/daterangepicker.js") ?>'></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src='<?= asset("admin-layout/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js") ?>'></script>
<!-- Summernote -->
<script src='<?= asset("admin-layout/plugins/summernote/summernote-bs4.min.js") ?>'></script>
<!-- overlayScrollbars -->
<script src='<?= asset("admin-layout/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js") ?>'></script>
<!-- AdminLTE App -->
<script src='<?= asset("admin-layout/dist/js/adminlte.js") ?>'></script>
<!-- AdminLTE for demo purposes -->
<script src='<?= asset("admin-layout/dist/js/demo.js") ?>'></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src='<?= asset("admin-layout/dist/js/pages/dashboard.js") ?>'></script>
</body>
</html>
