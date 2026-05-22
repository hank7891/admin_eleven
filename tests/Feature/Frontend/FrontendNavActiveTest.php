<?php

namespace Tests\Feature\Frontend;

use App\Services\Admin\HeroSlideService;
use App\Services\Frontend\AnnouncementService;
use App\Services\Frontend\ProductService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\MockInterface;
use Tests\TestCase;

class FrontendNavActiveTest extends TestCase
{
    public function test_home_page_marks_home_nav_item_active(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchActiveSlides')->once()->andReturn([]);
        });

        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchHomepageAnnouncements')->once()->andReturn([]);
            $mock->shouldReceive('fetchSystemAnnouncement')->once()->andReturn(null);
        });

        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchHomepageFeatured')->once()->andReturn([]);
        });

        $response = $this->get('/');
        $response->assertOk();

        $this->assertMatchesRegularExpression('/href="\/#top"\s+class="[^"]*is-active[^"]*"/', $response->getContent());
    }

    public function test_product_page_marks_product_nav_item_active(): void
    {
        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchPaginatedData')
                ->once()
                ->andReturn([
                    'data' => [],
                    'pagination' => new LengthAwarePaginator([], 0, 12),
                    'filters' => [],
                ]);

            $mock->shouldReceive('fetchFilterOptions')
                ->once()
                ->andReturn([
                    'categories' => [],
                    'tags' => [],
                ]);
        });

        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchSystemAnnouncement')->once()->andReturn(null);
        });

        $response = $this->get('/product');
        $response->assertOk();

        $this->assertMatchesRegularExpression('/href="' . preg_quote(url('product'), '/') . '"\s+class="[^"]*is-active[^"]*"/', $response->getContent());
    }

    public function test_product_detail_page_marks_product_nav_item_active(): void
    {
        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchDetailByID')
                ->once()
                ->andReturn([
                    'id' => 1,
                    'name' => '商品測試',
                    'tagline' => '',
                    'price_display' => 'NT$ 1,000',
                    'description_html' => '描述',
                    'category_id' => 1,
                    'category_name' => '分類',
                    'tags' => [],
                    'images' => [
                        ['id' => 11, 'image_url' => 'https://example.com/1.jpg', 'image_alt' => '圖一', 'is_primary' => 1],
                    ],
                ]);

            $mock->shouldReceive('fetchRelatedByCategory')
                ->once()
                ->andReturn([]);
        });

        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchSystemAnnouncement')->once()->andReturn(null);
        });

        $response = $this->get('/product/1');
        $response->assertOk();

        $this->assertMatchesRegularExpression('/href="' . preg_quote(url('product'), '/') . '"\s+class="[^"]*is-active[^"]*"/', $response->getContent());
    }

    public function test_announcement_page_marks_announcement_nav_item_active(): void
    {
        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchPaginatedData')
                ->once()
                ->andReturn([
                    'data' => [],
                    'pagination' => new LengthAwarePaginator([], 0, 10),
                    'filters' => [],
                ]);

            $mock->shouldReceive('fetchSystemAnnouncement')->once()->andReturn(null);
        });

        $response = $this->get('/announcement');
        $response->assertOk();

        $this->assertMatchesRegularExpression('/href="' . preg_quote(url('announcement'), '/') . '"\s+class="[^"]*is-active[^"]*"/', $response->getContent());
    }
}

