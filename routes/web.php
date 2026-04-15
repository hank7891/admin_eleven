<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Middleware;

Route::get('/', [Controllers\Frontend\HomeController::class, 'index']);
Route::get('product', [Controllers\Frontend\ProductController::class, 'list'])
    ->middleware('throttle:60,1');
Route::get('product/{id}', [Controllers\Frontend\ProductController::class, 'detail'])
    ->whereNumber('id')
    ->middleware('throttle:120,1');
Route::get('announcement', [Controllers\Frontend\AnnouncementController::class, 'list'])
    ->middleware('throttle:60,1');
Route::get('announcement/{id}', [Controllers\Frontend\AnnouncementController::class, 'detail'])
    ->whereNumber('id')
    ->middleware('throttle:120,1');

Route::get('test', [Controllers\TestController::class, 'index'])->middleware(Middleware\Test::class);

Route::prefix('admin')->group(function () {
    # 白名單路由（只需登入，不檢查選單權限）
    Route::get('/', [Controllers\Admin\IndexController::class, 'index'])->middleware(Middleware\AdminIsLogin::class);
    Route::get('login', [Controllers\Admin\IndexController::class, 'login'])->middleware(Middleware\AdminNotLogin::class);
    Route::post('login', [Controllers\Admin\IndexController::class, 'loginDo'])->middleware(Middleware\AdminNotLogin::class);
    Route::get('logout', [Controllers\Admin\IndexController::class, 'logout'])->middleware(Middleware\AdminIsLogin::class);
    Route::get('notice', [Controllers\Admin\IndexController::class, 'notice']);
    Route::get('select-role', [Controllers\Admin\IndexController::class, 'selectRole'])->middleware(Middleware\AdminIsLogin::class);
    Route::post('select-role', [Controllers\Admin\IndexController::class, 'selectRoleDo'])->middleware(Middleware\AdminIsLogin::class);

    # 需權限檢查的路由
    Route::middleware([Middleware\AdminIsLogin::class, Middleware\AdminCheckPermission::class])->group(function () {
        # 角色管理
        Route::prefix('acl.role')->group(function () {
            Route::get('list', [Controllers\Admin\AclRoleController::class, 'list']);
            Route::get('edit/{id}', [Controllers\Admin\AclRoleController::class, 'edit']);
            Route::post('edit', [Controllers\Admin\AclRoleController::class, 'editDo']);
        });

        # 帳號管理
        Route::prefix('employee')->group(function () {
            Route::get('list', [Controllers\Admin\EmployeeController::class, 'list']);
            Route::get('edit/{id}', [Controllers\Admin\EmployeeController::class, 'edit']);
            Route::post('edit', [Controllers\Admin\EmployeeController::class, 'editDo']);
        });

        # 國別管理
        Route::prefix('country')->group(function () {
            Route::get('list', [Controllers\Admin\CountryController::class, 'list']);
            Route::get('edit/{id}', [Controllers\Admin\CountryController::class, 'edit']);
            Route::post('edit', [Controllers\Admin\CountryController::class, 'editDo']);
            Route::post('delete/{id}', [Controllers\Admin\CountryController::class, 'delete']);
        });

        # 公告管理
        Route::prefix('announcement')->group(function () {
            Route::get('list', [Controllers\Admin\AnnouncementController::class, 'list']);
            Route::get('edit/{id}', [Controllers\Admin\AnnouncementController::class, 'edit']);
            Route::post('edit', [Controllers\Admin\AnnouncementController::class, 'editDo']);
            Route::post('delete/{id}', [Controllers\Admin\AnnouncementController::class, 'delete']);
        });

        # 首頁輪播管理
        Route::prefix('hero-slide')->group(function () {
            Route::get('list', [Controllers\Admin\HeroSlideController::class, 'list']);
            Route::get('edit/{id}', [Controllers\Admin\HeroSlideController::class, 'edit']);
            Route::post('edit', [Controllers\Admin\HeroSlideController::class, 'editDo']);
            Route::post('delete/{id}', [Controllers\Admin\HeroSlideController::class, 'delete']);
            Route::post('toggle-active/{id}', [Controllers\Admin\HeroSlideController::class, 'toggleActive']);
        });

        # 商品管理
        Route::prefix('product')->group(function () {
            Route::get('list', [Controllers\Admin\ProductController::class, 'list']);
            Route::get('edit/{id}', [Controllers\Admin\ProductController::class, 'edit']);
            Route::post('edit', [Controllers\Admin\ProductController::class, 'editDo']);
            Route::post('delete/{id}', [Controllers\Admin\ProductController::class, 'delete'])
                ->middleware('throttle:30,1');
            Route::post('bulk-status', [Controllers\Admin\ProductController::class, 'bulkStatus'])
                ->middleware('throttle:30,1');
        });

        # 商品類別管理
        Route::prefix('product.category')->group(function () {
            Route::get('list', [Controllers\Admin\ProductCategoryController::class, 'list']);
            Route::get('edit/{id}', [Controllers\Admin\ProductCategoryController::class, 'edit']);
            Route::post('edit', [Controllers\Admin\ProductCategoryController::class, 'editDo']);
            Route::post('delete/{id}', [Controllers\Admin\ProductCategoryController::class, 'delete']);
        });

        # 商品標籤管理
        Route::prefix('product.tag')->group(function () {
            Route::get('list', [Controllers\Admin\ProductTagController::class, 'list']);
            Route::get('edit/{id}', [Controllers\Admin\ProductTagController::class, 'edit']);
            Route::post('edit', [Controllers\Admin\ProductTagController::class, 'editDo']);
            Route::post('delete/{id}', [Controllers\Admin\ProductTagController::class, 'delete']);
        });

        # 遊戲 - 貪食蛇
        Route::prefix('game.snake')->group(function () {
            Route::get('/', [Controllers\Admin\GameSnakeController::class, 'index']);
        });

        # 操作日誌
        Route::prefix('admin.log')->group(function () {
            Route::get('list', [Controllers\Admin\AdminLogController::class, 'list']);
            Route::get('detail/{id}', [Controllers\Admin\AdminLogController::class, 'detail']);
        });

        # 選單管理
        Route::prefix('admin.menu')->group(function () {
            Route::get('list', [Controllers\Admin\AdminMenuController::class, 'list']);
            Route::get('edit/{id}', [Controllers\Admin\AdminMenuController::class, 'edit']);
            Route::post('edit', [Controllers\Admin\AdminMenuController::class, 'editDo']);
            Route::post('delete/{id}', [Controllers\Admin\AdminMenuController::class, 'delete']);
        });

        # 登入日誌
        Route::prefix('admin.login-log')->group(function () {
            Route::get('list', [Controllers\Admin\AdminLoginLogController::class, 'list']);
            Route::get('detail/{id}', [Controllers\Admin\AdminLoginLogController::class, 'detail']);
        });
    });
});

Route::prefix('share')->group(function () {
    Route::get('getMessage/{type}', [Controllers\Share\MessageController::class, 'getMessage']);
});
