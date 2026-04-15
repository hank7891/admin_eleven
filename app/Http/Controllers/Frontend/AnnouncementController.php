<?php

namespace App\Http\Controllers\Frontend;

use App\Services\Frontend\AnnouncementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AnnouncementController extends FrontendController
{
    # 建構元
    public function __construct(protected AnnouncementService $announcementService)
    {
        parent::__construct($announcementService);
    }

    # 前台公告列表
    public function list(Request $request): View
    {
        $filters = $request->only(['keyword', 'date_from', 'date_to']);
        $result = $this->announcementService->fetchPaginatedData($filters);

        return view('Frontend/announcement/list', [
            'pageTitle' => 'The Journal | Aura & Heirloom',
            'navItems' => $this->buildNavItems(),
            'footerColumns' => $this->buildFooterColumns(),
            'alertBanner' => $this->buildAlertBanner(),
            'announcements' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $result['filters'],
        ]);
    }

    # 前台公告內頁
    public function detail(int $id): View
    {
        $data = $this->announcementService->fetchDetailByID($id);

        return view('Frontend/announcement/detail', [
            'pageTitle' => $data['title'] . ' | Aura & Heirloom',
            'navItems' => $this->buildNavItems(),
            'footerColumns' => $this->buildFooterColumns(),
            'alertBanner' => $this->buildAlertBanner(),
            'data' => $data,
            'moreAnnouncements' => $this->announcementService->fetchMoreAnnouncements($id),
        ]);
    }
}
