<?php

namespace App\Http\Controllers\Frontend;

use App\Services\Admin\HeroSlideService;
use App\Services\Frontend\AnnouncementService;
use App\Services\Frontend\ProductService;
use Illuminate\Contracts\View\View;

class HomeController extends FrontendController
{
    # 建構元
    public function __construct(
        protected AnnouncementService $announcementService,
        protected HeroSlideService $heroSlideService,
        protected ProductService $productService
    ) {
        parent::__construct($announcementService);
    }

    # 前台首頁
    public function index(): View
    {
        return view('Frontend.home', [
            'pageTitle' => 'Aura & Heirloom | 前台形象首頁',
            'navItems' => $this->buildNavItems(),
            'footerColumns' => $this->buildFooterColumns(),
            'alertBanner' => $this->buildAlertBanner(),
            'slides' => $this->heroSlideService->fetchActiveSlides(),
            'journalEntries' => $this->announcementService->fetchHomepageAnnouncements(),
            'products' => $this->productService->fetchHomepageFeatured(),
        ]);
    }
}
