<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Admin\HeroSlideService;
use App\Services\Frontend\AnnouncementService;
use App\Services\Frontend\ProductService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    # 建構元
    public function __construct(
        protected AnnouncementService $announcementService,
        protected HeroSlideService $heroSlideService,
        protected ProductService $productService
    )
    {
    }

    /**
     * 前台首頁
     */
    public function index(): View
    {
        $slides = $this->heroSlideService->fetchActiveSlides();

        $journalEntries = $this->announcementService->fetchHomepageAnnouncements();

        $products = $this->productService->fetchHomepageFeatured();

        $navItems = [
            ['label' => '首頁', 'url' => '/#top'],
            ['label' => '精選商品', 'url' => url('product')],
            ['label' => '最新公告', 'url' => url('announcement')],
            ['label' => '會員專區', 'url' => '/#member'],
            ['label' => '關於我們', 'url' => '/#footer'],
        ];

        $footerColumns = [
            [
                'title' => 'Collections',
                'links' => ['New Arrivals', 'Living Room', 'Objects & Art', 'Textiles'],
            ],
            [
                'title' => 'Company',
                'links' => ['Our Story', 'The Journal', 'Sustainability', 'Careers'],
            ],
            [
                'title' => 'Support',
                'links' => ['Shipping & Returns', 'Contact Us', 'Privacy Policy', 'Terms of Service'],
            ],
        ];

        $alertBanner = $this->announcementService->fetchSystemAnnouncement();

        return view('Frontend.home', [
            'pageTitle' => 'Aura & Heirloom | 前台形象首頁',
            'navItems' => $navItems,
            'footerColumns' => $footerColumns,
            'alertBanner' => $alertBanner,
            'slides' => $slides,
            'journalEntries' => $journalEntries,
            'products' => $products,
        ]);
    }
}


