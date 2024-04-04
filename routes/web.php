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
});

Route::prefix('share')->group(function () {
    Route::get('getMessage/{type}', [Controllers\Share\MessageController::class, 'getMessage']);
});