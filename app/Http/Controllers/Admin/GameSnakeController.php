<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GameSnakeController extends Controller
{
    /** @var \App\Services\Share\SettingService */
    protected $settingService;

    # 建構元
    public function __construct()
    {
        $this->settingService = app('setting');
    }

    /**
     * 貪食蛇遊戲頁面
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function index()
    {
        $this->settingService->setSetData('pageTitle', '貪食蛇小遊戲');

        return view('admin/game/snake', $this->settingService->fetchSetData());
    }
}

