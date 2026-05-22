<?php

namespace App\Services\Admin;

use App\Repositories\Admin\AdminLogRepository;
use App\Repositories\Admin\AdminLoginLogRepository;
use App\Repositories\Admin\AnnouncementRepository;
use App\Repositories\Admin\HeroSlideRepository;
use App\Repositories\Admin\MemberRepository;
use App\Repositories\Admin\ProductRepository;

class DashboardService
{
    # 建構元
    public function __construct(
        protected MemberRepository $memberRepository,
        protected ProductRepository $productRepository,
        protected AnnouncementRepository $announcementRepository,
        protected HeroSlideRepository $heroSlideRepository,
        protected AdminLoginLogRepository $adminLoginLogRepository,
        protected AdminLogRepository $adminLogRepository,
    ) {
    }

    /**
     * 取得後台 Dashboard 三組顯示資料
     * 回傳結構詳見 docs/tasks/20260518-dashboard-kpi-refactor/INTERFACE_CONTRACT.md
     */
    public function getDashboardData(): array
    {
        return [
            'kpi'         => $this->buildKpi(),
            'recentLogs'  => $this->buildRecentLogs(),
            'systemStats' => $this->buildSystemStats(),
        ];
    }

    /**
     * KPI 4 卡資料
     */
    protected function buildKpi(): array
    {
        $now = now();

        return [
            'member_new_month'    => $this->memberRepository->countNewInMonth($now->year, $now->month),
            'product_active'      => $this->productRepository->countOnline(),
            'announcement_unread' => $this->announcementRepository->countActive(),
            'admin_login_today'   => $this->adminLoginLogRepository->countSuccessOnDate($now->toDateString()),
        ];
    }

    /**
     * 近期操作日誌（最多 5 筆，UI 顯示用結構）
     */
    protected function buildRecentLogs(): array
    {
        return $this->adminLogRepository->fetchRecent(5)
            ->map(function ($log) {
                $tone = match ($log->action) {
                    'create' => 'success',
                    'update' => 'info',
                    'delete' => 'danger',
                    default  => 'neutral',
                };
                $icon = match ($log->action) {
                    'create' => 'add_circle',
                    'update' => 'edit_note',
                    'delete' => 'delete',
                    default  => 'history',
                };

                return [
                    'title'  => ($log->operator_name ?? '系統') . ' · ' . ($log->remarks ?? $log->module),
                    'meta'   => optional($log->operated_at)->format('Y/m/d H:i') . ' · ' . $log->module . ' · ' . ($log->ip_address ?? '--'),
                    'action' => $log->action,
                    'icon'   => $icon,
                    'tone'   => $tone,
                ];
            })
            ->toArray();
    }

    /**
     * 系統概況 4 條
     */
    protected function buildSystemStats(): array
    {
        $productTotal   = $this->productRepository->countTotal();
        $productOnline  = $this->productRepository->countOnline();
        $announceTotal  = $this->announcementRepository->countTotal();
        $announceActive = $this->announcementRepository->countActive();
        $heroTotal      = $this->heroSlideRepository->countTotal();
        $heroActive     = $this->heroSlideRepository->countActive();
        $memberTotal    = $this->memberRepository->countTotal();
        $memberActive   = $this->memberRepository->countActive();

        return [
            [
                'label'   => '商品（上架 / 全部）',
                'value'   => $productOnline . ' / ' . $productTotal,
                'percent' => $productTotal > 0 ? (int) round($productOnline / $productTotal * 100) : 0,
            ],
            [
                'label'   => '公告（已公開 / 全部）',
                'value'   => $announceActive . ' / ' . $announceTotal,
                'percent' => $announceTotal > 0 ? (int) round($announceActive / $announceTotal * 100) : 0,
                'color'   => '#8f7859',
            ],
            [
                'label'   => '輪播（啟用 / 全部）',
                'value'   => $heroActive . ' / ' . $heroTotal,
                'percent' => $heroTotal > 0 ? (int) round($heroActive / $heroTotal * 100) : 0,
                'color'   => '#6b5680',
            ],
            [
                'label'   => '會員（啟用 / 全部）',
                'value'   => $memberActive . ' / ' . $memberTotal,
                'percent' => $memberTotal > 0 ? (int) round($memberActive / $memberTotal * 100) : 0,
                'color'   => '#2f7d54',
            ],
        ];
    }
}
