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
                            <form action={{asset('admin/employee/edit')}} method="POST" enctype="multipart/form-data">
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
                                    <div class="form-group">
                                        <label for="inputGender">性別</label>
                                        <select class="form-control" id="inputGender" name="gender">
                                            @foreach (config('constants.gender') as $key => $label)
                                                <option value="{{ $key }}" {{ ($data['gender'] ?? 0) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputBirthday">生日</label>
                                        <input type="date"
                                               class="form-control"
                                               id="inputBirthday"
                                               name="birthday"
                                               value="{{ isset($data['birthday']) && $data['birthday'] instanceof \Carbon\Carbon ? $data['birthday']->format('Y-m-d') : ($data['birthday'] ?? '') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="inputPhone">電話</label>
                                        <input type="text"
                                               class="form-control"
                                               id="inputPhone"
                                               name="phone"
                                               placeholder="Enter phone"
                                               value="{{$data['phone'] ?? ''}}">
                                    </div>
                                    <div class="form-group">
                                        <label for="inputIsActive">帳號狀態</label>
                                        <select class="form-control" id="inputIsActive" name="is_active">
                                            @foreach (config('constants.status') as $key => $label)
                                                <option value="{{ $key }}" {{ ($data['is_active'] ?? 1) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputAvatar">大頭照</label>
                                        @if (!empty($data['avatar']))
                                            <div class="mb-2">
                                                <img src="{{ asset('storage/' . $data['avatar']) }}" alt="大頭照" style="max-width: 150px; max-height: 150px;">
                                            </div>
                                        @endif
                                        <input type="file"
                                               class="form-control-file"
                                               id="inputAvatar"
                                               name="avatar"
                                               accept=".jpg,.jpeg,.png,.gif,.webp">
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
