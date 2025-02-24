<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Middleware;

Route::get('/', function () {
    return view('welcome');
});

Route::get('test', [Controllers\TestController::class, 'index'])->middleware(Middleware\Test::class);

Route::prefix('admin')->group(function () {
    Route::get('/', [Controllers\Admin\IndexController::class, 'index'])->middleware(Middleware\AdminIsLogin::class);
    Route::get('login', [Controllers\Admin\IndexController::class, 'login'])->middleware(Middleware\AdminNotLogin::class);
    Route::post('login', [Controllers\Admin\IndexController::class, 'loginDo'])->middleware(Middleware\AdminNotLogin::class);
    Route::get('logout', [Controllers\Admin\IndexController::class, 'logout'])->middleware(Middleware\AdminIsLogin::class);

    # 角色管理
    Route::prefix('acl.role')->group(function () {
        Route::get('list', [Controllers\Admin\AclRoleController::class, 'list'])->middleware(Middleware\AdminIsLogin::class);
        Route::get('edit/{id}', [Controllers\Admin\AclRoleController::class, 'edit'])->middleware(Middleware\AdminIsLogin::class);
        Route::post('edit', [Controllers\Admin\AclRoleController::class, 'editDo'])->middleware(Middleware\AdminIsLogin::class);
    });

    # 帳號管理
    Route::prefix('employee')->group(function () {
        Route::get('list', [Controllers\Admin\EmployeeController::class, 'list'])->middleware(Middleware\AdminIsLogin::class);
        Route::get('edit/{id}', [Controllers\Admin\EmployeeController::class, 'edit'])->middleware(Middleware\AdminIsLogin::class);
        Route::post('edit', [Controllers\Admin\EmployeeController::class, 'editDo'])->middleware(Middleware\AdminIsLogin::class);
    });
});

Route::prefix('share')->group(function () {
    Route::get('getMessage/{type}', [Controllers\Share\MessageController::class, 'getMessage']);
});