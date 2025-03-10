<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= asset("admin/") ?>" class="brand-link">
        <img src='<?= asset("admin-layout/dist/img/AdminLTELogo.png") ?>' alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">CYO ADMIN</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src='<?= asset("admin-layout/dist/img/user2-160x160.jpg") ?>' class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ $user['name'] ?? '-' }}</a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                @foreach($menu as $item)
                    {{-- 產生表單外誆 --}}
                    <li class="nav-item {{$item['item_open'] ? 'menu-open' : ''}}">
                        @if($item['have_item'])
                            {{-- 產生表單下拉 --}}
                            <a href="#" class="nav-link {{$item['item_open'] ? 'active' : ''}}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    {{$item['item_name']}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                        @endif
                        @foreach($item['details'] as $detail)
                            {{-- 產生表單內容 --}}
                            {!! $item['have_item'] ? "<ul class='nav nav-treeview'>" : '' !!}
                            <li class="nav-item">
                                <a href="{{asset($detail['url'] ?? '#')}}" class="nav-link {{$detail['is_open'] ? 'active' : ''}}">
                                    <i class="nav-icon far fa-image"></i>
                                    <p>
                                        {{$detail['name']}}
                                    </p>
                                </a>
                            </li>
                            {!! $item['have_item'] ? "</ul>" : '' !!}
                        @endforeach
                    </li>
                @endforeach
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
