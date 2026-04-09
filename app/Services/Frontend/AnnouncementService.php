<?php

namespace App\Services\Frontend;

use App\Repositories\Admin\AnnouncementRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class AnnouncementService
{
    # 建構元
    public function __construct(protected AnnouncementRepository $repository)
    {
    }

    /**
     * 取得全系統公告
     */
    public function fetchSystemAnnouncement(): ?array
    {
        $announcement = Cache::remember('frontend:announcement:system', now()->addMinutes(5), function () {
            return $this->repository->fetchCurrentSystemAnnouncement();
        });

        if (empty($announcement)) {
            return null;
        }

        $data = $this->formatItem($announcement->toArray(), true);

        # 全系統公告 Blade 使用 message 欄位顯示
        $data['message'] = $data['content'] ?? '';

        return $data;
    }

    /**
     * 取得首頁公告
     */
    public function fetchHomepageAnnouncements(int $limit = 5): array
    {
        $announcements = Cache::remember('frontend:announcement:home_latest', now()->addMinutes(5), function () use ($limit) {
            return $this->repository->fetchLatestGeneralAnnouncements($limit);
        });

        $data = [];
        foreach ($announcements as $announcement) {
            $data[] = $this->formatItem($announcement->toArray(), true);
        }

        return $data;
    }

    /**
     * 取得前台列表分頁資料
     */
    public function fetchPaginatedData(array $filters = [], int $perPage = 10): array
    {
        $filters = $this->normalizeFilters($filters);
        $paginator = $this->repository->fetchFrontendPaginatedData($filters, $perPage);

        $data = [];
        foreach ($paginator->items() as $announcement) {
            $data[] = $this->formatItem($announcement->toArray(), true);
        }

        return [
            'data' => $data,
            'pagination' => $paginator,
            'filters' => $filters,
        ];
    }

    /**
     * 取得公告詳情
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function fetchDetailByID(int $id): array
    {
        $announcement = $this->repository->fetchFrontendDataByID($id);

        if (empty($announcement)) {
            abort(404);
        }

        return $this->formatItem($announcement->toArray());
    }

    /**
     * 取得更多文章
     */
    public function fetchMoreAnnouncements(int $excludeId, int $limit = 3): array
    {
        $collection = $this->repository->fetchMoreFrontendAnnouncements($excludeId, $limit);
        $data = [];

        foreach ($collection as $announcement) {
            $data[] = $this->formatItem($announcement->toArray(), true);
        }

        return $data;
    }

    /**
     * 正規化篩選條件
     */
    protected function normalizeFilters(array $filters): array
    {
        foreach (['keyword', 'date_from', 'date_to'] as $key) {
            if (isset($filters[$key])) {
                $filters[$key] = trim((string) $filters[$key]);
            }
        }

        return $filters;
    }

    /**
     * 格式化前台顯示資料
     */
    protected function formatItem(array $item, bool $forList = false): array
    {
        $item['date_display'] = !empty($item['start_at'])
            ? Carbon::parse($item['start_at'])->translatedFormat('M d, Y')
            : '';
        $item['date_full_display'] = !empty($item['start_at'])
            ? Carbon::parse($item['start_at'])->format('Y.m.d H:i')
            : '';
        $item['summary'] = $item['summary'] ?: mb_strimwidth((string) ($item['content'] ?? ''), 0, 120, '...');
        $item['url'] = url('announcement/' . $item['id']);
        $item['content_lines'] = preg_split("/(\r\n|\r|\n)/", (string) ($item['content'] ?? '')) ?: [];

        if ($forList) {
            $item['content_preview'] = mb_strimwidth((string) ($item['summary'] ?? ''), 0, 160, '...');
        }

        return $item;
    }
}

