<?php

namespace App\Repositories\Admin;

use App\Models\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductCategoryRepository
{
    public function __construct(protected ProductCategory $model)
    {
    }

    public function fetchPaginatedData(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model::query()->withCount('products');

        if (!empty($filters['keyword'])) {
            $query->where('name', 'like', '%' . $filters['keyword'] . '%');
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('sort_order')->orderByDesc('id')->paginate($perPage);
    }

    public function fetchEnabledData(): array
    {
        return $this->model::query()
            ->where('is_active', STATUS_ACTIVE)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    public function fetchDataByID(int $id): array
    {
        $data = $this->model::query()->withCount('products')->find($id);

        return !empty($data) ? $data->toArray() : [];
    }

    public function addData(array $data): object
    {
        return $this->model::create($data);
    }

    public function updateData(int $id, array $data): object
    {
        $record = $this->model::find($id);

        if (empty($record)) {
            throw new \Exception('修改商品類別資料取得錯誤！');
        }

        $record->update($data);

        return $record;
    }

    public function deleteData(int $id): bool
    {
        $record = $this->model::find($id);

        if (empty($record)) {
            throw new \Exception('刪除商品類別資料取得錯誤！');
        }

        return $record->delete();
    }
}


