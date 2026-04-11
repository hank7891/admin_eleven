<?php

namespace App\Services\Admin;

use App\Repositories\Admin\ProductCategoryRepository;
use Carbon\Carbon;

class ProductCategoryService
{
    public function __construct(
        protected ProductCategoryRepository $repository
    ) {
    }

    public function fetchPaginatedData(array $filters = [], int $perPage = 20): array
    {
        $filters = $this->normalizeFilters($filters);
        $paginator = $this->repository->fetchPaginatedData($filters, $perPage);

        $data = [];
        foreach ($paginator->items() as $item) {
            $data[] = $this->formatData($item->toArray());
        }

        return [
            'data' => $data,
            'pagination' => $paginator,
        ];
    }

    public function fetchEnabledData(): array
    {
        $data = $this->repository->fetchEnabledData();

        foreach ($data as &$item) {
            $item = $this->formatData($item);
        }

        return $data;
    }

    public function fetchDataByID(int $id): array
    {
        $data = $this->repository->fetchDataByID($id);

        if (empty($data)) {
            throw new \Exception('查無此商品類別資料！');
        }

        return $this->formatData($data);
    }

    public function addData(array $data): int
    {
        $data = $this->normalizePayload($data, false);
        $record = $this->repository->addData($data);

        if (empty($record->id)) {
            throw new \Exception('新增商品類別資料失敗！');
        }

        return $record->id;
    }

    public function updateData(int $id, array $data): int
    {
        $data = $this->normalizePayload($data, true);
        $this->repository->updateData($id, $data);

        return $id;
    }

    public function deleteData(int $id): bool
    {
        $data = $this->fetchDataByID($id);

        if (($data['products_count'] ?? 0) > 0) {
            throw new \Exception('此類別已有商品使用中，無法刪除！');
        }

        return $this->repository->deleteData($id);
    }

    protected function normalizeFilters(array $filters): array
    {
        if (isset($filters['keyword'])) {
            $filters['keyword'] = trim((string) $filters['keyword']);
        }

        return $filters;
    }

    protected function normalizePayload(array $data, bool $isUpdate): array
    {
        $data['name'] = trim(strip_tags((string) ($data['name'] ?? '')));
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = (int) ($data['is_active'] ?? STATUS_ACTIVE);

        $employee = session(ADMIN_AUTH_SESSION);
        if (!empty($employee['id'])) {
            if (!$isUpdate) {
                $data['created_by'] = $employee['id'];
            }
            $data['updated_by'] = $employee['id'];
        }

        unset($data['id']);

        return $data;
    }

    protected function formatData(array $data): array
    {
        $data['created_at_display'] = !empty($data['created_at'])
            ? Carbon::parse($data['created_at'])->format('Y-m-d H:i')
            : '';
        $data['updated_at_display'] = !empty($data['updated_at'])
            ? Carbon::parse($data['updated_at'])->format('Y-m-d H:i')
            : '';
        $data['is_active_display'] = config('constants.status.' . ((int) ($data['is_active'] ?? STATUS_INACTIVE)));
        $data['products_count'] = $data['products_count'] ?? 0;

        return $data;
    }
}

