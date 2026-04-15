<?php

namespace Tests\Feature\Frontend;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Services\Admin\HeroSlideService;
use App\Services\Frontend\AnnouncementService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery\MockInterface;
use Tests\TestCase;

class FrontendProductScopeTest extends TestCase
{
    use DatabaseTransactions;

    # Mock 非 Product 相關依賴，避免 announcements / hero_slides 表缺失問題
    protected function setUp(): void
    {
        parent::setUp();

        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchSystemAnnouncement')->andReturn(null);
            $mock->shouldReceive('fetchHomepageAnnouncements')->andReturn([]);
        });

        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchActiveSlides')->andReturn([]);
        });
    }

    # 建立上架商品 + 主圖，回傳 Product
    protected function createOnlineProduct(array $attrs = [], bool $featured = false): Product
    {
        $factory = Product::factory();

        if ($featured) {
            $factory = $factory->featured();
        }

        $product = $factory->create($attrs);

        ProductImage::factory()->primary()->create([
            'product_id' => $product->id,
        ]);

        return $product;
    }

    # --- 列表：只顯示上架 + 生效商品 ---

    public function test_list_shows_only_online_and_active_products(): void
    {
        $this->createOnlineProduct(['name' => 'Visible Product']);
        Product::factory()->offline()->create(['name' => 'Offline Product']);
        Product::factory()->upcoming()->create(['name' => 'Upcoming Product']);
        Product::factory()->expired()->create(['name' => 'Expired Product']);

        $response = $this->get('/product');

        $response->assertOk();
        $response->assertSee('Visible Product');
        $response->assertDontSee('Offline Product');
        $response->assertDontSee('Upcoming Product');
        $response->assertDontSee('Expired Product');
    }

    public function test_list_shows_product_with_null_end_at(): void
    {
        $this->createOnlineProduct([
            'name' => 'Perpetual Product',
            'end_at' => null,
        ]);

        $response = $this->get('/product');

        $response->assertOk();
        $response->assertSee('Perpetual Product');
    }

    # --- 內頁：正常 ---

    public function test_detail_returns_200_for_online_active_product(): void
    {
        $product = $this->createOnlineProduct(['name' => 'Detail Product']);

        $response = $this->get('/product/' . $product->id);

        $response->assertOk();
        $response->assertSee('Detail Product');
    }

    # --- 內頁：404 情境 ---

    public function test_detail_returns_404_for_offline_product(): void
    {
        $product = Product::factory()->offline()->create();
        ProductImage::factory()->primary()->create(['product_id' => $product->id]);

        $this->get('/product/' . $product->id)->assertNotFound();
    }

    public function test_detail_returns_404_for_upcoming_product(): void
    {
        $product = Product::factory()->upcoming()->create();
        ProductImage::factory()->primary()->create(['product_id' => $product->id]);

        $this->get('/product/' . $product->id)->assertNotFound();
    }

    public function test_detail_returns_404_for_expired_product(): void
    {
        $product = Product::factory()->expired()->create();
        ProductImage::factory()->primary()->create(['product_id' => $product->id]);

        $this->get('/product/' . $product->id)->assertNotFound();
    }

    public function test_detail_returns_404_for_nonexistent_id(): void
    {
        $this->get('/product/99999')->assertNotFound();
    }

    # --- 搜尋防護 ---

    public function test_list_keyword_percent_does_not_match_all(): void
    {
        $this->createOnlineProduct(['name' => 'Normal Chair']);
        $this->createOnlineProduct(['name' => '100% Organic']);

        $response = $this->get('/product?keyword=' . urlencode('%'));

        $response->assertOk();
        $response->assertDontSee('Normal Chair');
        $response->assertSee('100% Organic');
    }

    public function test_list_invalid_date_from_is_ignored(): void
    {
        $this->createOnlineProduct(['name' => 'Any Product']);

        $response = $this->get('/product?date_from=not-a-date');

        $response->assertOk();
        $response->assertSee('Any Product');
    }

    # --- 白名單：回應不含管理欄位 ---

    public function test_detail_response_does_not_contain_created_by(): void
    {
        $product = $this->createOnlineProduct(['created_by' => 42, 'updated_by' => 42]);

        $response = $this->get('/product/' . $product->id);

        $response->assertOk();
        $content = $response->getContent();
        $this->assertStringNotContainsString('"created_by"', $content);
        $this->assertStringNotContainsString('"updated_by"', $content);
    }

    # --- 首頁：只顯示主打 + 上架生效 ---

    public function test_homepage_featured_shows_only_featured_online_products(): void
    {
        $this->createOnlineProduct(['name' => 'Featured Active'], true);
        Product::factory()->featured()->offline()->create(['name' => 'Featured Offline']);
        Product::factory()->featured()->upcoming()->create(['name' => 'Featured Upcoming']);
        $this->createOnlineProduct(['name' => 'Non Featured Active']);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Featured Active');
        $response->assertDontSee('Featured Offline');
        $response->assertDontSee('Featured Upcoming');
        $response->assertDontSee('Non Featured Active');
    }
}
