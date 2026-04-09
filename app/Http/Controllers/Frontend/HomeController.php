<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\AnnouncementService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    # 建構元
    public function __construct(protected AnnouncementService $announcementService)
    {
    }

    /**
     * 前台首頁
     */
    public function index(): View
    {
        $slides = [
            [
                'eyebrow' => 'Spring / Summer 2026',
                'title' => '以留白與質地，安放日常的節奏',
                'description' => '從器物、織品到家具，以柔和編排呈現一種「想待在這裡」的生活感。',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBRAwWD2hQyaFjmrY9eHr-khCMhLx9nSK55uYvxMGTxmvnIjC9lPdph5mmYYGnUcCy7kwJ11jJosbwKgQYLUFpJO5a4t2k-lptrgAiH2rPUM7yUJ673gU6ZebVqVANz9_Sxp1dDSQBDk_toeDGuZIEDFmmbqc_uom_g4KhNzvWut5NKIVEPuprK8cJj238qN3Oqm7C-VFZ1dH7bTtLlFS-ICA52X36JzY0UlMylk_Bf8SSaP70EkjiZGtk2yBtrvLKrWyt2UEiyN2A',
                'image_alt' => '陽光灑落的高級簡約客廳，米白沙發與石材茶几營造安定與留白感。',
                'primary_cta' => [
                    'label' => '探索本季精選',
                    'url' => '/#products',
                ],
                'secondary_cta' => [
                    'label' => '閱讀最新公告',
                    'url' => '/#journal',
                ],
            ],
            [
                'eyebrow' => 'Editorial Living',
                'title' => '讓家的氣味與光影，都有被珍藏的理由',
                'description' => '在材質、比例與沉靜色調之間，尋回更鬆弛、更有呼吸感的空間秩序。',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAISwqPJM-7zw2HDoxIIYQO3QYzkQPsJqOKRT0cZLkNQaHaRL7_k6NGy07wbGh3dK5KylqdvzIf9lBG-T_sb0PcVvf2BLgj0FInkuaGePTbFNcvtL-Ku-7774cZiDrn6mU0czZF-5vTPGMGoMzfIl8MGgFBoLx8M0bkSsrV4srgp7DUGWCe0VEy4SsujJxzD9WoaKAgmo9pmWVsUHq-B7wrDi3Ho3WP2thU1jPrfjijDR9x791TvbqrGpr6zvPqLBH7TjCRbPF408w',
                'image_alt' => '米色調生活空間搭配自然光影，呈現安靜而時尚的居家場景。',
                'primary_cta' => [
                    'label' => '查看設計靈感',
                    'url' => '/#journal',
                ],
                'secondary_cta' => [
                    'label' => '會員專屬邀請',
                    'url' => '/#member',
                ],
            ],
            [
                'eyebrow' => 'Craft & Heritage',
                'title' => '每一件作品，都為日常留下更長久的溫度',
                'description' => '我們關注器物背後的工藝與故事，讓生活用品也成為可傳承的風景。',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAnShC2M7xfr6TBoGgCKJjF17gQNbAqtOPDjlctdBj02JJGyMkbm3GgiqBEtf_PjiFrbbxaxwq8IH6b1kvWW7pk5Z3DxuvXPl7JdB30XI8L2hBsGTksN6qAtQ1GCeNynd6Q138KZLQUmdpzWuzjnpfy6A7U_vC6r7Eqhnazlr6YacW0EBxIAH91WXLxZoS3q-YzfGImRX2N5gmhZ2ASoFcqRRXUOsOq1VJ5zogPbxo2eLOYZInulcR9YC2REtYPjUnW2c88qf2rYqA',
                'image_alt' => '帶有柔和光線的人物生活照，傳遞品牌會員與風格生活的連結感。',
                'primary_cta' => [
                    'label' => '瀏覽作品選集',
                    'url' => '/#products',
                ],
                'secondary_cta' => [
                    'label' => '加入內圈會員',
                    'url' => '/#member',
                ],
            ],
        ];

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


