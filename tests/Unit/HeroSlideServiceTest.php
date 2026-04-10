<?php

namespace Tests\Unit;

use App\Models\HeroSlide;
use App\Repositories\Admin\HeroSlideRepository;
use App\Services\Admin\HeroSlideService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class HeroSlideServiceTest extends TestCase
{
    /**
     * 後台列表資料應產生摘要欄位。
     */
    public function test_fetch_paginated_data_generates_description_preview(): void
    {
        $slide = new HeroSlide();
        $slide->id = 1;
        $slide->title = '首頁輪播一';
        $slide->description = '這是一段很長很長的輪播說明文字，用來測試列表頁是否會自動產生摘要欄位。';
        $slide->sort_order = 1;
        $slide->is_active = STATUS_ACTIVE;
        $slide->start_at = now();
        $slide->end_at = null;
        $slide->image_path = 'uploads/image/2026/04/test.jpg';

        $paginator = new LengthAwarePaginator([$slide], 1, 20);

        $repository = Mockery::mock(HeroSlideRepository::class);
        $repository->shouldReceive('fetchPaginatedData')
            ->once()
            ->andReturn($paginator);

        $service = new HeroSlideService($repository);
        $result = $service->fetchPaginatedData();

        $this->assertArrayHasKey('description_preview', $result['data'][0]);
        $this->assertNotSame('', $result['data'][0]['description_preview']);
    }
}


