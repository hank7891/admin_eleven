<?php

namespace Tests\Feature\Frontend;

use App\Services\Frontend\AnnouncementService;
use App\Services\Frontend\ProductService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\MockInterface;
use Tests\TestCase;

class ProductPageTest extends TestCase
{
    public function test_product_list_page_renders_successfully(): void
    {
        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchPaginatedData')
                ->once()
                ->andReturn([
                    'data' => [
                        [
                            'id' => 1,
                            'name' => '前台商品測試',
                            'category_name' => '客廳家具',
                            'price_display' => 'NT$ 9,800',
                            'image_url' => 'https://example.com/product.jpg',
                            'image_alt' => '商品圖片',
                            'url' => url('product/1'),
                            'tag_names' => ['新品'],
                            'start_at_display' => '2026.04.12',
                        ],
                    ],
                    'pagination' => new LengthAwarePaginator([['id' => 1]], 1, 12),
                    'filters' => ['keyword' => '', 'date_from' => '', 'date_to' => '', 'category_id' => '', 'tag_id' => ''],
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
        $response->assertSee('Products');
        $response->assertSee('前台商品測試');
    }

    public function test_product_detail_page_renders_successfully(): void
    {
        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchDetailByID')
                ->once()
                ->with(1)
                ->andReturn([
                    'id' => 1,
                    'name' => '前台商品內頁測試',
                    'tagline' => '商品標語',
                    'price_display' => 'NT$ 9,800',
                    'description_html' => nl2br(e('第一行' . PHP_EOL . '第二行')),
                    'category_id' => 3,
                    'category_name' => '客廳家具',
                    'tags' => ['新品'],
                    'images' => [
                        ['id' => 10, 'image_url' => 'https://example.com/p1.jpg', 'image_alt' => '圖1', 'is_primary' => 1],
                    ],
                ]);

            $mock->shouldReceive('fetchRelatedByCategory')
                ->once()
                ->with(1, 3)
                ->andReturn([
                    [
                        'id' => 2,
                        'name' => '相關商品',
                        'category_name' => '客廳家具',
                        'price_display' => 'NT$ 12,000',
                        'image_url' => 'https://example.com/p2.jpg',
                        'image_alt' => '圖2',
                        'url' => url('product/2'),
                    ],
                ]);
        });

        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchSystemAnnouncement')->once()->andReturn(null);
        });

        $response = $this->get('/product/1');

        $response->assertOk();
        $response->assertSee('前台商品內頁測試');
        $response->assertSee('商品標語');
        $response->assertSee('相關商品');
    }
}

