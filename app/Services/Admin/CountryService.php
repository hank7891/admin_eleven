<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use App\Repositories\Admin\CountryRepository;

class CountryService
{
    # 建構元
    public function __construct(protected CountryRepository $repository)
    {
    }

    /**
     * 取得所有國別資料
     * @return array
     */
    public function fetchAllData(): array
    {
        $data = $this->repository->fetchAllData();

        foreach ($data as &$value) {
            $value = $this->formatData($value);
        }

        return $data;
    }

    /**
     * 取得分頁國別資料
     * @param array $filters
     * @param int $perPage
     * @return array
     */
    public function fetchPaginatedData(array $filters = [], int $perPage = 20): array
    {
        $filters = $this->normalizeFilters($filters);
        $paginator = $this->repository->fetchPaginatedData($filters, $perPage);

        $data = [];
        foreach ($paginator->items() as $country) {
            $data[] = $this->formatData($country->toArray());
        }

        return [
            'data' => $data,
            'pagination' => $paginator,
        ];
    }

    /**
     * 依照 ID 取得國別資料
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function fetchDataByID(int $id): array
    {
        $data = $this->repository->fetchDataByID($id);

        if (empty($data)) {
            throw new \Exception('查無此國別資料！');
        }

        return $this->formatData($data);
    }

    /**
     * 新增國別資料
     * @param array $data
     * @return int
     * @throws \Exception
     */
    public function addData(array $data): int
    {
        $data = $this->normalizePayload($data);
        $country = $this->repository->addData($data);

        if (empty($country->id)) {
            throw new \Exception('新增國別資料失敗！');
        }

        return $country->id;
    }

    /**
     * 修改國別資料
     * @param int $id
     * @param array $data
     * @return int
     * @throws \Exception
     */
    public function updateData(int $id, array $data): int
    {
        $data = $this->normalizePayload($data);
        $this->repository->updateData($id, $data);

        return $id;
    }

    /**
     * 刪除國別資料
     * @param int $id
     * @return bool
     */
    public function deleteData(int $id): bool
    {
        return $this->repository->deleteData($id);
    }

    /**
     * 正規化篩選條件
     * @param array $filters
     * @return array
     */
    protected function normalizeFilters(array $filters): array
    {
        if (!empty($filters['name'])) {
            $filters['name'] = trim($filters['name']);
        }

        if (!empty($filters['country_code'])) {
            $filters['country_code'] = strtoupper(trim($filters['country_code']));
        }

        return $filters;
    }

    /**
     * 正規化儲存資料
     * @param array $data
     * @return array
     */
    protected function normalizePayload(array $data): array
    {
        $data['name'] = trim($data['name'] ?? '');
        $data['country_code'] = strtoupper(trim($data['country_code'] ?? ''));
        $data['abbreviation'] = trim($data['abbreviation'] ?? '');
        $data['abbreviation'] = $data['abbreviation'] === '' ? null : strtoupper($data['abbreviation']);
        $data['is_active'] = (int) ($data['is_active'] ?? STATUS_ACTIVE);

        return $data;
    }

    /**
     * 格式化顯示資料
     * @param array $value
     * @return array
     */
    protected function formatData(array $value): array
    {
        $value['created_at'] = !empty(trim((string) ($value['created_at'] ?? '')))
            ? Carbon::parse($value['created_at'])->format('Y-m-d')
            : '';

        $value['updated_at'] = !empty(trim((string) ($value['updated_at'] ?? '')))
            ? Carbon::parse($value['updated_at'])->format('Y-m-d')
            : '';

        $value['abbreviation'] = $value['abbreviation'] ?? '';
        $value['is_active_display'] = config('constants.status')[$value['is_active'] ?? STATUS_ACTIVE]
            ?? config('constants.status.' . STATUS_INACTIVE);

        return $value;
    }
}


