<?php

namespace App\Repositories\Admin;

use App\Models\HeroSlide;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class HeroSlideRepository
{
    # 建構元
    public function __construct(protected HeroSlide $model)
    {
    }

    /**
     * 取得分頁資料（含篩選）
     */
    public function fetchPaginatedData(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model::query();

        # 關鍵字搜尋
        if (!empty($filters['keyword'])) {
            $query->where(function ($builder) use ($filters) {
                $builder->where('title', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('eyebrow', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['keyword'] . '%');
            });
        }

        # 狀態篩選
        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->ordered()->paginate($perPage);
    }

    /**
     * 依照 ID 取得資料
     */
    public function fetchDataByID(int $id): array
    {
        $slide = $this->model::find($id);

        return !empty($slide) ? $slide->toArray() : [];
    }

    /**
     * 新增資料
     */
    public function addData(array $data): object
    {
        return $this->model::create($data);
    }

    /**
     * 修改資料
     * @throws \Exception
     */
    public function updateData(int $id, array $data): object
    {
        $slide = $this->model::find($id);

        if (empty($slide)) {
            throw new \Exception('修改資料取得錯誤！');
        }

        $slide->update($data);

        return $slide;
    }

    /**
     * 刪除資料
     * @throws \Exception
     */
    public function deleteData(int $id): bool
    {
        $slide = $this->model::find($id);

        if (empty($slide)) {
            throw new \Exception('刪除資料取得錯誤！');
        }

        return $slide->delete();
    }

    /**
     * 取得前台生效輪播
     */
    public function fetchActiveSlides(): Collection
    {
        return $this->model::query()
            ->active()
            ->inEffect()
            ->ordered()
            ->get();
    }
}

