<?php

namespace Tests\Feature\Frontend;

use App\Services\Frontend\AnnouncementService;
use App\Services\Frontend\ProductService;
use Mockery\MockInterface;
use Tests\TestCase;

class FrontendProductDetailTest extends TestCase
{
    public function test_product_detail_renders_gallery_data_attributes_and_dialog(): void
    {
        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchDetailByID')
                ->once()
                ->andReturn([
                    'id' => 1,
                    'name' => '商品內頁測試',
                    'tagline' => '標語',
                    'price_display' => 'NT$ 1,200',
                    'description_html' => '描述',
                    'category_id' => 2,
                    'category_name' => '分類',
                    'tags' => ['標籤一'],
                    'images' => [
                        ['id' => 10, 'image_url' => 'https://example.com/p1.jpg', 'image_alt' => '圖1', 'is_primary' => 1],
                        ['id' => 11, 'image_url' => 'https://example.com/p2.jpg', 'image_alt' => '圖2', 'is_primary' => 0],
                    ],
                ]);

            $mock->shouldReceive('fetchRelatedByCategory')
                ->once()
                ->with(1, 2)
                ->andReturn([]);
        });

        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchSystemAnnouncement')->once()->andReturn(null);
        });

        $response = $this->get('/product/1');

        $response->assertOk();
        $response->assertSee('id="productMainImage"', false);
        $response->assertSee('data-main-image', false);
        $response->assertSee('data-open-product-image-dialog', false);
        $response->assertSee('id="productImageDialog"', false);
        $response->assertSee('id="productDialogImage"', false);
        $response->assertSee('data-thumb', false);
        $response->assertSee('data-image-url="https://example.com/p2.jpg"', false);
        $response->assertSee('data-image-alt="圖2"', false);
    }

    public function test_product_detail_without_images_shows_placeholder_and_no_dialog(): void
    {
        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchDetailByID')
                ->once()
                ->andReturn([
                    'id' => 2,
                    'name' => '無圖商品',
                    'tagline' => '',
                    'price_display' => 'NT$ 900',
                    'description_html' => '描述',
                    'category_id' => 2,
                    'category_name' => '分類',
                    'tags' => [],
                    'images' => [],
                ]);

            $mock->shouldReceive('fetchRelatedByCategory')
                ->once()
                ->with(2, 2)
                ->andReturn([]);
        });

        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchSystemAnnouncement')->once()->andReturn(null);
        });

        $response = $this->get('/product/2');

        $response->assertOk();
        $response->assertSee('目前尚無商品圖片');
        $response->assertDontSee('id="productImageDialog"', false);
        $response->assertDontSee('data-open-product-image-dialog', false);
    }
}


