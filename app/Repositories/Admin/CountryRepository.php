<?php

namespace App\Repositories\Admin;

use App\Models\Country;

class CountryRepository
{
    # 建構元
    public function __construct(protected Country $model)
    {
    }

    /**
     * 取得所有資料
     * @return array
     */
    public function fetchAllData(): array
    {
        return $this->model::orderBy('id', 'desc')->get()->toArray();
    }

    /**
     * 取得分頁資料（含篩選）
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function fetchPaginatedData(array $filters = [], int $perPage = 20): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = $this->model::query();

        # 國名篩選
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        # 國家代碼篩選
        if (!empty($filters['country_code'])) {
            $query->where('country_code', 'like', '%' . strtoupper($filters['country_code']) . '%');
        }

        # 狀態篩選
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * 依照 id 取得資料
     * @param int $id
     * @return array
     */
    public function fetchDataByID(int $id): array
    {
        $country = $this->model::find($id);
        return !empty($country) ? $country->toArray() : [];
    }

    /**
     * 新增資料
     * @param array $data
     * @return object
     */
    public function addData(array $data): object
    {
        return $this->model::create($data);
    }

    /**
     * 修改資料
     * @param int $id
     * @param array $data
     * @return object
     * @throws \Exception
     */
    public function updateData(int $id, array $data): object
    {
        $country = $this->model::find($id);

        if (empty($country)) {
            throw new \Exception('修改資料取得錯誤！');
        }

        $country->update($data);
        return $country;
    }

    /**
     * 刪除資料
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteData(int $id): bool
    {
        $country = $this->model::find($id);

        if (empty($country)) {
            throw new \Exception('刪除資料取得錯誤！');
        }

        return $country->delete();
    }
}

