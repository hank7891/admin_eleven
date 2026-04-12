<?php

namespace App\Services\Admin;

use App\Repositories\Admin\ProductCategoryRepository;
use App\Repositories\Admin\ProductRepository;
use App\Repositories\Admin\ProductTagRepository;
use App\Services\Share\FileUploadService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        protected ProductRepository $repository,
        protected ProductCategoryRepository $categoryRepository,
        protected ProductTagRepository $tagRepository,
        protected FileUploadService $uploadService
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

    public function fetchDataByID(int $id): array
    {
        $data = $this->repository->fetchDataByID($id);

        if (empty($data)) {
            throw new \Exception('查無此商品資料！');
        }

        return $this->formatData($data, true);
    }

    public function fetchFilterOptions(): array
    {
        return [
            'categories' => $this->categoryRepository->fetchEnabledData(),
            'tags' => $this->tagRepository->fetchEnabledData(),
            'statuses' => config('constants.product_status', []),
            'featured_options' => config('constants.product_featured', []),
            'period_states' => config('constants.product_period_state', []),
        ];
    }

    public function addData(array $data, array $uploadedImages = [], array $imageMeta = []): int
    {
        $payload = $this->normalizePayload($data, false);
        $payload['status_key'] = (string) PRODUCT_STATUS_OFFLINE;

        $total = count($uploadedImages);
        if ($total < 1 || $total > PRODUCT_MAX_IMAGES) {
            throw new \Exception('商品圖片張數需介於 1 ~ ' . PRODUCT_MAX_IMAGES . ' 張。');
        }

        $storedPaths = [];

        DB::beginTransaction();
        try {
            $product = $this->repository->addData($payload);
            if (empty($product->id)) {
                throw new \Exception('新增商品失敗！');
            }

            $primaryIndex = isset($imageMeta['primary_new_index']) ? (int) $imageMeta['primary_new_index'] : 0;

            foreach ($uploadedImages as $index => $uploadedImage) {
                if (!$uploadedImage instanceof UploadedFile) {
                    continue;
                }

                $imagePath = $this->uploadService->upload($uploadedImage, 'image');
                $storedPaths[] = $imagePath;

                $this->repository->addImageData([
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'image_alt' => trim(strip_tags((string) ($imageMeta['new_alt'][$index] ?? $payload['name']))),
                    'is_primary' => $index === $primaryIndex ? 1 : 0,
                    'sort_order' => (int) ($imageMeta['new_sort'][$index] ?? $index + 1),
                ]);
            }

            $this->repository->syncTags((int) $product->id, $this->normalizeTagIDs($data['tag_ids'] ?? []));

            DB::commit();

            $this->clearFrontendCache();

            return $product->id;
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($storedPaths as $path) {
                $this->uploadService->delete($path);
            }

            throw $e;
        }
    }

    public function updateData(int $id, array $data, array $uploadedImages = [], array $imageMeta = []): int
    {
        $payload = $this->normalizePayload($data, true);
        $current = $this->fetchDataByID($id);
        $currentImages = $current['images'] ?? [];

        $currentImageIds = array_map('intval', array_column($currentImages, 'id'));

        $keptIds = array_map('intval', $imageMeta['kept_ids'] ?? $currentImageIds);
        $keptIds = array_values(array_intersect($keptIds, $currentImageIds));

        $deletedIds = array_map('intval', $imageMeta['deleted_ids'] ?? []);
        $deletedIds = array_values(array_intersect($deletedIds, $currentImageIds));

        if (!empty($deletedIds)) {
            $keptIds = array_values(array_diff($keptIds, $deletedIds));
        }

        $totalAfter = count($keptIds) + count($uploadedImages);
        if ($totalAfter < 1 || $totalAfter > PRODUCT_MAX_IMAGES) {
            throw new \Exception('商品圖片張數需介於 1 ~ ' . PRODUCT_MAX_IMAGES . ' 張。');
        }

        $storedPaths = [];

        DB::beginTransaction();
        try {
            $this->repository->updateData($id, $payload);

            # 先刪除勾選移除的舊圖資料列
            if (!empty($deletedIds)) {
                foreach ($currentImages as $image) {
                    if (in_array((int) $image['id'], $deletedIds, true)) {
                        $this->uploadService->delete((string) ($image['image_path'] ?? ''));
                    }
                }
                $this->repository->deleteImagesByIDs($deletedIds);
            }

            $newImageIds = [];
            foreach ($uploadedImages as $index => $uploadedImage) {
                if (!$uploadedImage instanceof UploadedFile) {
                    continue;
                }

                $imagePath = $this->uploadService->upload($uploadedImage, 'image');
                $storedPaths[] = $imagePath;

                $newImage = $this->repository->addImageData([
                    'product_id' => $id,
                    'image_path' => $imagePath,
                    'image_alt' => trim(strip_tags((string) ($imageMeta['new_alt'][$index] ?? $payload['name']))),
                    'is_primary' => 0,
                    'sort_order' => (int) ($imageMeta['new_sort'][$index] ?? 999 + $index),
                ]);

                $newImageIds[$index] = (int) $newImage->id;
            }

            # 主圖需明確指定：既有 primary_id 或新圖 primary_new_index
            $primaryId = !empty($imageMeta['primary_id']) ? (int) $imageMeta['primary_id'] : 0;
            if ($primaryId > 0 && !in_array($primaryId, $keptIds, true)) {
                throw new \Exception('標題圖片設定錯誤，請重新指定。');
            }

            if ($primaryId === 0 && isset($imageMeta['primary_new_index']) && $imageMeta['primary_new_index'] !== '') {
                $newIndex = (int) $imageMeta['primary_new_index'];
                $primaryId = $newImageIds[$newIndex] ?? 0;
            }

            if ($primaryId === 0) {
                throw new \Exception('請指定一張標題圖片。');
            }

            $this->repository->resetPrimaryImage($id);
            if ($this->repository->setPrimaryImage($id, $primaryId) === 0) {
                throw new \Exception('標題圖片設定錯誤，請重新指定。');
            }

            $this->repository->syncTags($id, $this->normalizeTagIDs($data['tag_ids'] ?? []));

            DB::commit();

            $this->clearFrontendCache();

            return $id;
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($storedPaths as $path) {
                $this->uploadService->delete($path);
            }

            throw $e;
        }
    }

    public function deleteData(int $id): bool
    {
        $data = $this->fetchDataByID($id);

        DB::beginTransaction();
        try {
            foreach (($data['images'] ?? []) as $image) {
                $this->uploadService->delete((string) ($image['image_path'] ?? ''));
            }

            $result = $this->repository->deleteData($id);
            DB::commit();

            $this->clearFrontendCache();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function bulkUpdateStatus(array $ids, int $statusKey): int
    {
        $validStatuses = array_map('strval', array_keys(config('constants.product_status', [])));
        if (!in_array((string) $statusKey, $validStatuses, true)) {
            throw new \Exception('無效的商品狀態。');
        }

        if (empty($ids)) {
            throw new \Exception('請至少選擇一筆商品。');
        }

        $ids = array_values(array_unique(array_map('intval', $ids)));

        $affected = $this->repository->bulkUpdateStatus(
            $ids,
            $statusKey,
            session(ADMIN_AUTH_SESSION)['id'] ?? null
        );

        $this->clearFrontendCache();

        return $affected;
    }

    protected function normalizeFilters(array $filters): array
    {
        foreach (['keyword', 'period_state', 'status_key', 'category_id', 'tag_id', 'is_featured'] as $field) {
            if (isset($filters[$field])) {
                $filters[$field] = is_string($filters[$field]) ? trim($filters[$field]) : $filters[$field];
            }
        }

        return $filters;
    }

    protected function normalizePayload(array $data, bool $isUpdate): array
    {
        $data['name'] = trim(strip_tags((string) ($data['name'] ?? '')));
        $data['tagline'] = trim(strip_tags((string) ($data['tagline'] ?? '')));
        $data['tagline'] = $data['tagline'] === '' ? null : $data['tagline'];
        $data['description'] = trim(strip_tags((string) ($data['description'] ?? '')));
        $data['price'] = (int) ($data['price'] ?? 0);
        $data['category_id'] = !empty($data['category_id']) ? (int) $data['category_id'] : null;
        $data['status_key'] = (string) ($data['status_key'] ?? PRODUCT_STATUS_OFFLINE);
        $data['is_featured'] = (int) ($data['is_featured'] ?? PRODUCT_FEATURED_OFF);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['start_at'] = Carbon::createFromFormat('Y-m-d\TH:i', (string) $data['start_at'])->format('Y-m-d H:i:s');
        $data['end_at'] = !empty($data['end_at'])
            ? Carbon::createFromFormat('Y-m-d\TH:i', (string) $data['end_at'])->format('Y-m-d H:i:s')
            : null;

        $employee = session(ADMIN_AUTH_SESSION);
        if (!empty($employee['id'])) {
            if (!$isUpdate) {
                $data['created_by'] = $employee['id'];
            }
            $data['updated_by'] = $employee['id'];
        }

        unset($data['id']);

        return Arr::only($data, [
            'name',
            'tagline',
            'price',
            'description',
            'category_id',
            'status_key',
            'is_featured',
            'sort_order',
            'start_at',
            'end_at',
            'created_by',
            'updated_by',
        ]);
    }

    protected function normalizeTagIDs(array $tagIds): array
    {
        return array_values(array_unique(array_filter(array_map('intval', $tagIds), function ($id) {
            return $id > 0;
        })));
    }

    protected function formatData(array $data, bool $includeDetail = false): array
    {
        $data['category_name'] = $data['category']['name'] ?? '未分類';
        $data['tag_names'] = array_values(array_map(function ($item) {
            return $item['name'] ?? '';
        }, $data['tags'] ?? []));

        $primaryImage = $data['primary_image'] ?? null;
        $data['primary_image_url'] = !empty($primaryImage['image_path']) ? Storage::url($primaryImage['image_path']) : '';
        $data['price_display'] = 'NT$ ' . number_format((int) ($data['price'] ?? 0));
        $data['is_featured_display'] = config('constants.product_featured.' . ((int) ($data['is_featured'] ?? PRODUCT_FEATURED_OFF)));
        $data['status_display'] = config('constants.product_status.' . ((string) ($data['status_key'] ?? PRODUCT_STATUS_OFFLINE)));
        $data['status_badge_class'] = ((string) ($data['status_key'] ?? PRODUCT_STATUS_OFFLINE) === (string) PRODUCT_STATUS_ONLINE)
            ? 'bg-emerald-50 text-emerald-600'
            : 'bg-slate-100 text-slate-500';

        $startAt = !empty($data['start_at']) ? Carbon::parse($data['start_at']) : null;
        $endAt = !empty($data['end_at']) ? Carbon::parse($data['end_at']) : null;
        $now = now();
        if (!empty($startAt) && $startAt->gt($now)) {
            $data['period_state'] = 'upcoming';
        } elseif (!empty($endAt) && $endAt->lt($now)) {
            $data['period_state'] = 'expired';
        } else {
            $data['period_state'] = 'live';
        }

        $data['period_display'] = ($startAt ? $startAt->format('Y-m-d H:i') : '-') . ' ~ ' . ($endAt ? $endAt->format('Y-m-d H:i') : '永久');
        $data['updated_at_display'] = !empty($data['updated_at']) ? Carbon::parse($data['updated_at'])->format('Y-m-d H:i') : '';
        $data['start_at_input'] = $startAt ? $startAt->format('Y-m-d\TH:i') : '';
        $data['end_at_input'] = $endAt ? $endAt->format('Y-m-d\TH:i') : '';

        if ($includeDetail) {
            $data['tag_ids'] = array_values(array_map(function ($item) {
                return (int) ($item['id'] ?? 0);
            }, $data['tags'] ?? []));
            $data['images'] = $this->formatImages($data['images'] ?? []);
        }

        return $data;
    }

    protected function formatImages(array $images): array
    {
        foreach ($images as &$image) {
            $image['image_url'] = !empty($image['image_path']) ? Storage::url($image['image_path']) : '';
        }

        return $images;
    }

    public function clearFrontendCache(): void
    {
        Cache::forget('frontend:product:home_featured');
    }
}


