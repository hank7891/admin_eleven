<?php

namespace Tests\Feature\Frontend;

use App\Services\Admin\HeroSlideService;
use App\Services\Frontend\AnnouncementService;
use App\Services\Frontend\ProductService;
use Mockery\MockInterface;
use Tests\TestCase;

class HomepageCarouselTest extends TestCase
{
    /**
     * 首頁可依資料契約顯示動態輪播。
     */
    public function test_homepage_uses_dynamic_hero_slides(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchActiveSlides')
                ->once()
                ->andReturn([
                    [
                        'image' => 'https://example.com/hero-slide.jpg',
                        'image_alt' => '動態輪播圖片',
                        'target_url' => '/product',
                    ],
                ]);
        });

        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchHomepageAnnouncements')
                ->once()
                ->andReturn([]);

            $mock->shouldReceive('fetchSystemAnnouncement')
                ->once()
                ->andReturn(null);
        });

        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchHomepageFeatured')->once()->andReturn([]);
        });

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="heroSlideLink"', false);
        $response->assertSee('href="/product"', false);
        $response->assertDontSee('id="heroPrimaryCta"', false);
        $response->assertDontSee('id="heroSecondaryCta"', false);
        $response->assertSee('data-hero-image', false);
        $response->assertDontSee('id="heroTitle"', false);
        $response->assertDontSee('id="heroDescription"', false);
        $response->assertDontSee('id="heroEyebrow"', false);
        $response->assertSee('id="heroLiveRegion"', false);
        $response->assertSee('data-hero-carousel', false);
        $response->assertSee('tabindex="0"', false);
    }

    /**
     * 輪播連結留空時，前台應停用圖片點擊。
     */
    public function test_homepage_disables_slide_click_when_target_url_is_empty(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchActiveSlides')
                ->once()
                ->andReturn([
                    [
                        'image' => 'https://example.com/hero-slide.jpg',
                        'image_alt' => '動態輪播圖片',
                        'target_url' => '',
                    ],
                ]);
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
        $response->assertSee('id="heroSlideLink"', false);
        $response->assertSee('data-link-disabled="1"', false);
    }

    /**
     * 輪播連結填寫後，前台應啟用圖片點擊。
     */
    public function test_homepage_enables_slide_click_when_target_url_exists(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchActiveSlides')
                ->once()
                ->andReturn([
                    [
                        'image' => 'https://example.com/hero-slide.jpg',
                        'image_alt' => '動態輪播圖片',
                        'target_url' => 'https://example.com/product/1',
                    ],
                ]);
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
        $response->assertSee('href="https://example.com/product/1"', false);
        $response->assertSee('data-link-disabled="0"', false);
    }
}

