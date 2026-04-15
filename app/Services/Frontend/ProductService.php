<?php

namespace App\Services\Frontend;

use App\Repositories\Admin\ProductCategoryRepository;
use App\Repositories\Admin\ProductRepository;
use App\Repositories\Admin\ProductTagRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function __construct(
        protected ProductRepository $repository,
        protected ProductCategoryRepository $categoryRepository,
        protected ProductTagRepository $tagRepository
    ) {
    }

    /**
     * 首頁主打商品
     */
    public function fetchHomepageFeatured(int $limit = 6): array
    {
        $items = Cache::remember('frontend:product:home_featured:' . $limit, now()->addMinutes(5), function () use ($limit) {
            return $this->repository->fetchFrontendFeatured($limit);
        });

        $data = [];
        foreach ($items as $item) {
            $data[] = $this->formatItem($item->toArray());
        }

        return $data;
    }

    /**
     * 前台商品列表
     */
    public function fetchPaginatedData(array $filters = [], int $perPage = 12): array
    {
        $filters = $this->normalizeFilters($filters);
        $paginator = $this->repository->fetchFrontendPaginatedData($filters, $perPage);

        $data = [];
        foreach ($paginator->items() as $item) {
            $formatted = $this->formatItem($item->toArray());
            $formatted['tag_names'] = array_values(array_map(function ($tag) {
                return $tag['name'] ?? '';
            }, $item->tags->toArray()));
            $formatted['start_at_display'] = !empty($item->start_at)
                ? Carbon::parse($item->start_at)->format('Y.m.d')
                : '';
            $data[] = $formatted;
        }

        return [
            'data' => $data,
            'pagination' => $paginator,
            'filters' => array_filter($filters, fn ($v) => $v !== ''),
        ];
    }

    /**
     * 前台商品內頁
     */
    public function fetchDetailByID(int $id): array
    {
        $product = $this->repository->fetchFrontendDataByID($id);

        if (empty($product)) {
            abort(404);
        }

        $item = $product->toArray();
        $data = [
            'id' => $item['id'],
            'name' => $item['name'] ?? '',
            'tagline' => $item['tagline'] ?? '',
            'price_display' => 'NT$ ' . number_format((int) ($item['price'] ?? 0)),
            'description_html' => nl2br(e((string) ($item['description'] ?? ''))),
            'category_id' => $item['category_id'] ?? null,
            'category_name' => $item['category']['name'] ?? '未分類',
            'tags' => array_values(array_map(function ($tag) {
                return $tag['name'] ?? '';
            }, $item['tags'] ?? [])),
            'images' => $this->formatImages($item['images'] ?? []),
        ];

        $primaryImage = collect($data['images'])->firstWhere('is_primary', 1) ?? ($data['images'][0] ?? []);
        $description = (string) ($item['description'] ?? '');
        $data['primary_image_url'] = $primaryImage['image_url'] ?? '';
        $data['meta_description'] = !empty($item['tagline'])
            ? (string) $item['tagline']
            : mb_strimwidth($description, 0, 160, '...');

        return $data;
    }

    /**
     * 同類別其他商品
     */
    public function fetchRelatedByCategory(int $excludeId, ?int $categoryId, int $limit = 3): array
    {
        $items = $this->repository->fetchFrontendRelated($excludeId, $categoryId, $limit);

        $data = [];
        foreach ($items as $item) {
            $data[] = $this->formatItem($item->toArray());
        }

        return $data;
    }

    /**
     * 列表篩選選項
     */
    public function fetchFilterOptions(): array
    {
        return [
            'categories' => $this->categoryRepository->fetchEnabledData(),
            'tags' => $this->tagRepository->fetchEnabledData(),
        ];
    }

    protected function normalizeFilters(array $filters): array
    {
        $filters = [
            'keyword' => trim((string) ($filters['keyword'] ?? '')),
            'date_from' => trim((string) ($filters['date_from'] ?? '')),
            'date_to' => trim((string) ($filters['date_to'] ?? '')),
            'category_id' => trim((string) ($filters['category_id'] ?? '')),
            'tag_id' => trim((string) ($filters['tag_id'] ?? '')),
        ];

        foreach (['date_from', 'date_to'] as $field) {
            if ($filters[$field] === '') {
                continue;
            }

            try {
                $parsed = Carbon::createFromFormat('Y-m-d', $filters[$field]);
                if ($parsed->format('Y-m-d') !== $filters[$field]) {
                    $filters[$field] = '';
                }
            } catch (\Exception $e) {
                $filters[$field] = '';
            }
        }

        return $filters;
    }

    protected function formatItem(array $item): array
    {
        $primaryImage = $item['primary_image'] ?? [];
        $imagePath = (string) ($primaryImage['image_path'] ?? '');

        return [
            'id' => (int) ($item['id'] ?? 0),
            'name' => (string) ($item['name'] ?? ''),
            'tagline' => (string) ($item['tagline'] ?? ''),
            'category_name' => (string) ($item['category']['name'] ?? '未分類'),
            'price_display' => 'NT$ ' . number_format((int) ($item['price'] ?? 0)),
            'image_url' => $imagePath !== '' ? Storage::url($imagePath) : 'https://placehold.co/720x900?text=No+Image',
            'image_alt' => (string) (($primaryImage['image_alt'] ?? '') ?: ($item['name'] ?? '商品圖片')),
            'url' => url('product/' . (int) ($item['id'] ?? 0)),
        ];
    }

    protected function formatImages(array $images): array
    {
        $data = [];

        foreach ($images as $image) {
            $path = (string) ($image['image_path'] ?? '');
            $data[] = [
                'id' => (int) ($image['id'] ?? 0),
                'image_url' => $path !== '' ? Storage::url($path) : 'https://placehold.co/720x900?text=No+Image',
                'image_alt' => (string) ($image['image_alt'] ?? ''),
                'is_primary' => (int) ($image['is_primary'] ?? 0),
            ];
        }

        return $data;
    }
}

