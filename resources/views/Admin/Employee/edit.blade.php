@extends('Admin-share/index')
@section('content')
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{(empty($data['id'])) ? '資料新增' : '資料編輯'}}</h1>
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
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">基本資料</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form action={{asset('admin/employee/edit')}} method="POST">
                                @csrf
                                <input type="hidden" name="id" value={{ $data['id'] ?? 0 }}>
                                <div class="card-body">
                                    <div class="form-group">
                                        @if ($data['id'] ?? 0 > 0)
                                            <label>帳號: {{$data['account'] ?? '--'}}</label>
                                        @else
                                            <label for="exampleInputAccount">帳號</label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="exampleInputAccount"
                                                   name="account"
                                                   placeholder="Enter account"
                                                   value="">
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputName">姓名</label>
                                        <input type="text"
                                               class="form-control"
                                               id="exampleInputName"
                                               name="name"
                                               placeholder="Enter name"
                                               value="{{$data['name'] ?? ''}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Password</label>
                                        <input type="password"
                                               class="form-control"
                                               id="exampleInputPassword1"
                                               name="password"
                                               placeholder="Password">
                                    </div>
                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>        <!-- /.content -->
    </div>
@stop
