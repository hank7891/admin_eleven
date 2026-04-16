<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\AnnouncementService;

class FrontendController extends Controller
{
    public function __construct(
        protected AnnouncementService $announcementService
    ) {
    }

    # 前台共用導覽列
    protected function buildNavItems(?string $activeKey = null): array
    {
        $items = [
            ['key' => 'home', 'label' => '首頁', 'url' => '/#top'],
            ['key' => 'product', 'label' => '精選商品', 'url' => url('product')],
            ['key' => 'announcement', 'label' => '最新公告', 'url' => url('announcement')],
            ['key' => 'member', 'label' => '會員專區', 'url' => '/#member'],
            ['key' => 'about', 'label' => '關於我們', 'url' => '/#footer'],
        ];

        return array_map(function (array $item) use ($activeKey) {
            $item['is_active'] = $activeKey === ($item['key'] ?? null);

            return $item;
        }, $items);
    }

    # 前台共用頁尾
    protected function buildFooterColumns(): array
    {
        return [
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
    }

    # 全站公告橫幅
    protected function buildAlertBanner(): ?array
    {
        return $this->announcementService->fetchSystemAnnouncement();
    }
}
