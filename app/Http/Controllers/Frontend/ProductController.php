<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\Frontend\AnnouncementService;
use App\Services\Frontend\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service,
        protected AnnouncementService $announcementService
    ) {
    }

    /**
     * 前台商品列表
     */
    public function list(Request $request): View
    {
        $filters = $request->only(['keyword', 'date_from', 'date_to', 'category_id', 'tag_id']);
        $result = $this->service->fetchPaginatedData($filters);

        return view('Frontend/product/list', [
            'pageTitle' => '全部商品 | Aura & Heirloom',
            'navItems' => $this->buildNavItems(),
            'footerColumns' => $this->buildFooterColumns(),
            'alertBanner' => $this->announcementService->fetchSystemAnnouncement(),
            'products' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $result['filters'],
            'filterOptions' => $this->service->fetchFilterOptions(),
        ]);
    }

    /**
     * 前台商品內頁
     */
    public function detail(int $id): View
    {
        $data = $this->service->fetchDetailByID($id);

        return view('Frontend/product/detail', [
            'pageTitle' => $data['name'] . ' | Aura & Heirloom',
            'navItems' => $this->buildNavItems(),
            'footerColumns' => $this->buildFooterColumns(),
            'alertBanner' => $this->announcementService->fetchSystemAnnouncement(),
            'data' => $data,
            'relatedProducts' => $this->service->fetchRelatedByCategory($id, $data['category_id'] ?? null),
        ]);
    }

    protected function buildNavItems(): array
    {
        return [
            ['label' => '首頁', 'url' => '/#top'],
            ['label' => '精選商品', 'url' => url('product')],
            ['label' => '最新公告', 'url' => url('announcement')],
            ['label' => '會員專區', 'url' => '/#member'],
            ['label' => '關於我們', 'url' => '/#footer'],
        ];
    }

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

