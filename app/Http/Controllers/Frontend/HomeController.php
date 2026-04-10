<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Admin\HeroSlideService;
use App\Services\Frontend\AnnouncementService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    # 建構元
    public function __construct(
        protected AnnouncementService $announcementService,
        protected HeroSlideService $heroSlideService
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

        $products = [
            [
                'name' => 'Alabaster Vessel',
                'category' => '雕塑器物',
                'price' => 'NT$ 7,800',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCj_dcP9g8nD0U1J4ybEf0bjBnnbtLuZlcM7vGaUwT7HwIahtu7tA_J6YEKhGzdP0XjPdqBPCmqkAym7VGtKcDyN4AIoN00-64hBA5G0u8AG3CQ8FVKoIThikW4dVhxRcA8fWXPOLvd8sHeCy8dHU16tc-SnrfiHWqgh_RuQJky_saQlp34DQltoMkEN83g_HKKdzsjcev0sG147vsgT0JP4Axf1MXBQJptXix4rOt8i4_5Ia0K1GtrrobMKCEaTI0S5TtLi6UVwWE',
                'image_alt' => '手工陶器花器放置於木質桌面，呈現溫潤有機的材質細節。',
            ],
            [
                'name' => 'The Heirloom Armchair',
                'category' => '單椅與坐感',
                'price' => 'NT$ 58,000',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCheCzPcCHnW9cE8bBPBC_mVA-BG5wEdzl7LRANEFYBNyw7QtHD7Tf22K-tOC4Q-lSWWvjkYHAs-nLFvLeTZWfbFT11pFx56U6cu0uDFn7eXXIzHD_InrQDH4q9Lhiaii3J77LxB1_ox98G7zLNGaKuoiU9ZLzV8cXvCcIjhu0aT6gZCvSEXTzWgNoo-qC-TO_xYXNxjXgXxAc7AkVdpfFFh2QxWqeYsSFfNXYjlitt3gE6Wn317l-4cnFDOZ8-WPFs2r3CnvBJ05I',
                'image_alt' => '奶油色單椅置於溫暖白牆前，展現極簡高級的休憩感。',
            ],
            [
                'name' => 'Luminary No. 4',
                'category' => '燈光設計',
                'price' => 'NT$ 12,600',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAgwWJwAxS3AoGfCVaWxQWsIPqBfJ1SpuY75b66XE8nvAXmkiSw7u1nCopJ7sGFdr3XqUYy61lfujCrDSkfYX9ENIWMmsI-7cZ1gG0m7vEtCBH0pXwSg2Qh5XwgOxMb1oH5hYKXDb95iLYl0hAuXYwyaJUWwnDJaVeA2wNGdO8iRLR7EX9kTBC0t2SQ3ntkptfsb9ezPheAXQ_0Qa-rCV2cawl7pJgAc6f4yErCVKlRUhW--cf-PkxrP04yjxcel6ZqvWCBMsVnqrU',
                'image_alt' => '黃銅桌燈在柔和光線下散發靜謐氛圍。',
            ],
            [
                'name' => 'Raw Linen Suite',
                'category' => '寢具織品',
                'price' => 'NT$ 9,200',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBYTT_CmdEOpQYv9RAOHJCtk464nt6_DApZtQMx0BTftS30P5EFNsNO8YhvE4kORbQJGID6dHv-RdA43yFOb76U-reQJeMiEAB-MyXTlkM7N-WRNaA4imC1xgQvGCz76K8d3iXcB4C3d2BLe59ui8YOwSNvOYQUE0BPXS52KFHVT3SYkRQR9z36SfRFVDX5CjPFK43puew23iDFm9unjhRdDkWpMpCw5WtChi7pJhSU46kxlM5luV2g_Nnh5TSMWmP8mU71vJj48gc',
                'image_alt' => '層疊的天然亞麻寢具以沙色與鼠尾草色系呈現柔軟觸感。',
            ],
            [
                'name' => 'Abstract No. 4',
                'category' => '牆面藝術',
                'price' => 'NT$ 31,000',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBQj_gC0SlewKR6JrFjY_8Q1OMWHl4NWDK5uoMtnad0Cb1TXNxkMveZ8439hO8g3UmD-i5mitvjTcBtgKlFp8X5giOmKEF9IRSPsFo1peU7DJV9MXBBgGAN9O7zMd3uDv-EoQmCqG_UOLFlRibAbCDZ_Wt26OCMTfKdnWKHT-XCidXwpsUBhBFjP4lTj-MTIW1p91sg8YDY5SRWFwRW4mpXpSl_cRPhB2QBZ9FeWyremQl9aSUW46fgF0sWvd8ukURYTBLxM7uEtPw',
                'image_alt' => '中性色抽象畫作懸掛在礦物塗料牆面上，帶有畫廊感。',
            ],
            [
                'name' => 'Stone Ritual Set',
                'category' => '浴室器件',
                'price' => 'NT$ 5,600',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCsqWBqwICQwLYHf3imEcxPvxTKgG6jlQvYubL3p3ItvPwr1SHY_EpWAk5YY2BB2FqRwxvaolI52ycFM-XGIHJnP3qeMtXUuDINz4n9IG48XXLUsPNA2lPiHtH2PJ6hnq4fwPlPRJanhix4_fVG7K8OOrCzx3KMH3YvcVI6bzSwFBLyTtre_0-xpJgkokQPeDH10Qw6i-sWva1QS2cV8SWJ-SDcS0aH_Q_bniJYh-LHV1J9o0ZrLkLzDdzirivzydV6ZWaKAWVn_4g',
                'image_alt' => '石材盥洗用品與白色毛巾組合出靜謐的沐浴儀式感。',
            ],
        ];

        $navItems = [
            ['label' => '首頁', 'url' => '/#top'],
            ['label' => '精選商品', 'url' => '/#products'],
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


