<?php

namespace App\Http\Controllers\Frontend;

use App\Services\Frontend\AnnouncementService;
use App\Services\Frontend\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends FrontendController
{
    public function __construct(
        protected ProductService $service,
        protected AnnouncementService $announcementService
    ) {
        parent::__construct($announcementService);
    }

    # 前台商品列表
    public function list(Request $request): View
    {
        $filters = $request->only(['keyword', 'date_from', 'date_to', 'category_id', 'tag_id']);
        $result = $this->service->fetchPaginatedData($filters);

        return view('Frontend/product/list', [
            'pageTitle' => '全部商品 | Aura & Heirloom',
            'navItems' => $this->buildNavItems(),
            'footerColumns' => $this->buildFooterColumns(),
            'alertBanner' => $this->buildAlertBanner(),
            'products' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $result['filters'],
            'filterOptions' => $this->service->fetchFilterOptions(),
        ]);
    }

    # 前台商品內頁
    public function detail(int $id): View
    {
        $data = $this->service->fetchDetailByID($id);

        return view('Frontend/product/detail', [
            'pageTitle' => $data['name'] . ' | Aura & Heirloom',
            'navItems' => $this->buildNavItems(),
            'footerColumns' => $this->buildFooterColumns(),
            'alertBanner' => $this->buildAlertBanner(),
            'data' => $data,
            'relatedProducts' => $this->service->fetchRelatedByCategory($id, $data['category_id'] ?? null),
        ]);
    }
}
