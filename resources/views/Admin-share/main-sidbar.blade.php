<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= asset("admin/") ?>" class="brand-link">
        <img src='<?= asset("admin-layout/dist/img/AdminLTELogo.png") ?>' alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">CYO ADMIN</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        @php
            # 判斷是否有大頭照且為圖片格式
            $avatarExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $avatarPath = $user['avatar'] ?? '';
            $avatarExt = strtolower(pathinfo($avatarPath, PATHINFO_EXTENSION));
            $hasAvatar = !empty($avatarPath) && in_array($avatarExt, $avatarExtensions);
            $avatarUrl = $hasAvatar
                ? asset('storage/' . $avatarPath)
                : asset('admin-layout/dist/img/user2-160x160.jpg');
        @endphp
        @php
            $currentRole = session(ADMIN_ROLE_SESSION);
            $roles = $user['roles'] ?? [];
        @endphp
        {{-- 使用者資訊面板：角色 + 頭像姓名 --}}
        <div class="user-panel mt-3 pb-3 mb-3">
            @if(!empty($currentRole))
                <div class="d-flex align-items-center mb-2" style="padding-left: 8px;">
                    @if(count($roles) > 1)
                        <a href="{{ url('admin/select-role') }}" class="text-sm text-light" title="點擊切換角色">
                            <i class="fas fa-user-tag mr-1"></i>{{ $currentRole['name'] }}
                            <i class="fas fa-sync-alt ml-1" style="font-size: 0.7rem;"></i>
                        </a>
                    @else
                        <span class="text-sm text-light">
                            <i class="fas fa-user-tag mr-1"></i>{{ $currentRole['name'] }}
                        </span>
                    @endif
                </div>
            @endif
            <div class="d-flex align-items-center">
                <div class="image">
                    <img src="{{ $avatarUrl }}" class="img-circle elevation-2" alt="User Image">
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ $user['name'] ?? '-' }}</a>
                </div>
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
                    {{-- 產生選單群組 --}}
                    <li class="nav-item {{ $item['item_open'] ? 'menu-open' : '' }}">
                        @if($item['have_item'])
                            {{-- 群組標題 --}}
                            <a href="#" class="nav-link {{ $item['item_open'] ? 'active' : '' }}">
                                <i class="nav-icon {{ $item['item_icon'] ?? 'fas fa-folder' }}"></i>
                                <p>
                                    {{ $item['item_name'] }}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                        @endif
                        @foreach($item['details'] as $detail)
                            {{-- 選單項目 --}}
                            {!! $item['have_item'] ? "<ul class='nav nav-treeview'>" : '' !!}
                            <li class="nav-item">
                                <a href="{{ asset($detail['url'] ?? '#') }}" class="nav-link {{ $detail['is_open'] ? 'active' : '' }}">
                                    <i class="nav-icon {{ $detail['icon'] ?? 'far fa-circle' }}"></i>
                                    <p>{{ $detail['name'] }}</p>
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
