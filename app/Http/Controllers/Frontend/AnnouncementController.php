<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\AnnouncementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    # 建構元
    public function __construct(protected AnnouncementService $service)
    {
    }

    /**
     * 前台公告列表
     */
    public function list(Request $request): View
    {
        $filters = $request->only(['keyword', 'date_from', 'date_to']);
        $result = $this->service->fetchPaginatedData($filters);

        return view('Frontend/announcement/list', [
            'pageTitle' => 'The Journal | Aura & Heirloom',
            'navItems' => $this->buildNavItems(),
            'footerColumns' => $this->buildFooterColumns(),
            'alertBanner' => $this->service->fetchSystemAnnouncement(),
            'announcements' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $result['filters'],
        ]);
    }

    /**
     * 前台公告內頁
     */
    public function detail(int $id): View
    {
        $data = $this->service->fetchDetailByID($id);

        return view('Frontend/announcement/detail', [
            'pageTitle' => $data['title'] . ' | Aura & Heirloom',
            'navItems' => $this->buildNavItems(),
            'footerColumns' => $this->buildFooterColumns(),
            'alertBanner' => $this->service->fetchSystemAnnouncement(),
            'data' => $data,
            'moreAnnouncements' => $this->service->fetchMoreAnnouncements($id),
        ]);
    }

    /**
     * 前台導覽資料
     */
    protected function buildNavItems(): array
    {
        return [
            ['label' => '首頁', 'url' => '/#top'],
            ['label' => '精選商品', 'url' => '/#products'],
            ['label' => '最新公告', 'url' => url('announcement')],
            ['label' => '會員專區', 'url' => '/#member'],
            ['label' => '關於我們', 'url' => '/#footer'],
        ];
    }

    /**
     * 前台頁尾資料
     */
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
}


