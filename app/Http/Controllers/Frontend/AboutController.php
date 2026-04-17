<?php

namespace App\Http\Controllers\Frontend;

use App\Services\Frontend\AboutService;
use App\Services\Frontend\AnnouncementService;
use Illuminate\Contracts\View\View;

class AboutController extends FrontendController
{
    # 建構元
    public function __construct(
        protected AnnouncementService $announcementService,
        protected AboutService $aboutService
    ) {
        parent::__construct($announcementService);
    }

    # 前台關於我們頁
    public function index(): View
    {
        $aboutData = $this->aboutService->fetch();

        return view('Frontend.about', [
            'pageTitle' => 'About Us | Aura & Heirloom',
            'navItems' => $this->buildNavItems('about'),
            'footerColumns' => $this->buildFooterColumns(),
            'alertBanner' => $this->buildAlertBanner(),
            'about' => $aboutData,
        ]);
    }
}


