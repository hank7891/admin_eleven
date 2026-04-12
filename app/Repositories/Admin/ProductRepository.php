<?php

namespace App\Repositories\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductRepository
{
    public function __construct(
        protected Product $model,
        protected ProductImage $imageModel
    ) {
    }

    public function fetchPaginatedData(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model::query()->with(['category', 'tags', 'primaryImage']);

        if (!empty($filters['keyword'])) {
            $query->where(function (Builder $builder) use ($filters) {
                $builder->where('name', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('tagline', 'like', '%' . $filters['keyword'] . '%');
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (!empty($filters['tag_id'])) {
            $tagId = (int) $filters['tag_id'];
            $query->whereHas('tags', function (Builder $builder) use ($tagId) {
                $builder->where('product_tags.id', $tagId);
            });
        }

        if (isset($filters['status_key']) && $filters['status_key'] !== '') {
            $query->where('status_key', (string) $filters['status_key']);
        }

        if (isset($filters['is_featured']) && $filters['is_featured'] !== '') {
            $query->where('is_featured', (int) $filters['is_featured']);
        }

        if (!empty($filters['period_state'])) {
            $now = now();

            if ($filters['period_state'] === 'upcoming') {
                $query->where('start_at', '>', $now);
            }

            if ($filters['period_state'] === 'live') {
                $query->where('start_at', '<=', $now)
                    ->where(function (Builder $builder) use ($now) {
                        $builder->whereNull('end_at')->orWhere('end_at', '>=', $now);
                    });
            }

            if ($filters['period_state'] === 'expired') {
                $query->whereNotNull('end_at')->where('end_at', '<', $now);
            }
        }

        return $query->ordered()->paginate($perPage);
    }

    public function fetchDataByID(int $id): array
    {
        $data = $this->model::with(['category', 'tags', 'images'])->find($id);

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
            throw new \Exception('修改商品資料取得錯誤！');
        }

        $record->update($data);

        return $record;
    }

    public function deleteData(int $id): bool
    {
        $record = $this->model::find($id);

        if (empty($record)) {
            throw new \Exception('刪除商品資料取得錯誤！');
        }

        return $record->delete();
    }

    public function fetchImageCollectionByProductID(int $productId): Collection
    {
        return $this->imageModel::query()
            ->where('product_id', $productId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    public function fetchImageByID(int $id): ?ProductImage
    {
        return $this->imageModel::find($id);
    }

    public function addImageData(array $data): object
    {
        return $this->imageModel::create($data);
    }

    public function updateImageData(int $id, array $data): object
    {
        $record = $this->imageModel::find($id);

        if (empty($record)) {
            throw new \Exception('修改商品圖片資料取得錯誤！');
        }

        $record->update($data);

        return $record;
    }

    public function deleteImagesByIDs(array $ids): int
    {
        return $this->imageModel::query()->whereIn('id', $ids)->delete();
    }

    public function resetPrimaryImage(int $productId): int
    {
        return $this->imageModel::query()
            ->where('product_id', $productId)
            ->update(['is_primary' => 0]);
    }

    public function setPrimaryImage(int $productId, int $id): int
    {
        return $this->imageModel::query()
            ->where('product_id', $productId)
            ->where('id', $id)
            ->update(['is_primary' => 1]);
    }

    public function syncTags(int $productId, array $tagIds): void
    {
        $this->model::find($productId)?->tags()->sync($tagIds);
    }

    public function bulkUpdateStatus(array $ids, int $statusKey, ?int $updatedBy = null): int
    {
        $payload = [
            'status_key' => (string) $statusKey,
            'updated_at' => now(),
        ];

        if (!empty($updatedBy)) {
            $payload['updated_by'] = $updatedBy;
        }

        return $this->model::query()->whereIn('id', $ids)->update($payload);
    }

    /**
     * 前台首頁主打商品
     */
    public function fetchFrontendFeatured(int $limit = 6): Collection
    {
        return $this->applyFrontendOnlineScope($this->model::query())
            ->with(['category', 'tags', 'primaryImage'])
            ->where('is_featured', PRODUCT_FEATURED_ON)
            ->ordered()
            ->limit($limit)
            ->get();
    }

    /**
     * 前台商品列表（含篩選）
     */
    public function fetchFrontendPaginatedData(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->applyFrontendOnlineScope($this->model::query())
            ->with(['category', 'tags', 'primaryImage']);

        if (!empty($filters['keyword'])) {
            $keyword = addcslashes((string) $filters['keyword'], '%_\\');
            $query->where(function (Builder $builder) use ($keyword) {
                $builder->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('tagline', 'like', '%' . $keyword . '%');
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        if (!empty($filters['tag_id'])) {
            $tagId = (int) $filters['tag_id'];
            $query->whereHas('tags', function (Builder $builder) use ($tagId) {
                $builder->where('product_tags.id', $tagId);
            });
        }

        if (!empty($filters['date_from'])) {
            $query->where('start_at', '>=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $query->where('start_at', '<=', $filters['date_to'] . ' 23:59:59');
        }

        return $query->ordered()->paginate($perPage);
    }

    /**
     * 前台商品內頁
     */
    public function fetchFrontendDataByID(int $id): ?Product
    {
        return $this->applyFrontendOnlineScope($this->model::query())
            ->with(['category', 'tags', 'images'])
            ->where('id', $id)
            ->first();
    }

    /**
     * 前台同類別商品
     */
    public function fetchFrontendRelated(int $excludeId, ?int $categoryId, int $limit = 3): Collection
    {
        if (empty($categoryId)) {
            return new Collection();
        }

        return $this->applyFrontendOnlineScope($this->model::query())
            ->with(['category', 'tags', 'primaryImage'])
            ->where('id', '!=', $excludeId)
            ->where('category_id', $categoryId)
            ->ordered()
            ->limit($limit)
            ->get();
    }

    /**
     * 前台顯示條件（上架且在有效時段）
     */
    protected function applyFrontendOnlineScope(Builder $query): Builder
    {
        return $query
            ->where('status_key', (string) PRODUCT_STATUS_ONLINE)
            ->where('start_at', '<=', now())
            ->where(function (Builder $builder) {
                $builder->whereNull('end_at')->orWhere('end_at', '>=', now());
            });
    }
}

