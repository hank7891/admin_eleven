<?php

namespace Tests\Feature\Frontend;

use App\Services\Admin\HeroSlideService;
use App\Services\Frontend\AnnouncementService;
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
                        'eyebrow' => 'Spring / Summer 2026',
                        'title' => '動態輪播標題',
                        'description' => '動態輪播說明文字',
                        'image' => 'https://example.com/hero-slide.jpg',
                        'image_alt' => '動態輪播圖片',
                        'primary_cta' => [
                            'label' => '探索本季精選',
                            'url' => '/#products',
                        ],
                        'secondary_cta' => [
                            'label' => '閱讀品牌日誌',
                            'url' => '/#journal',
                        ],
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

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('動態輪播標題');
        $response->assertSee('動態輪播說明文字');
        $response->assertSee('Spring / Summer 2026');
        $response->assertSee('id="heroLiveRegion"', false);
        $response->assertSee('data-hero-carousel', false);
        $response->assertSee('tabindex="0"', false);
    }

    /**
     * CTA 僅有文字沒有連結時，首屏按鈕應隱藏。
     */
    public function test_homepage_hides_cta_when_only_label_exists(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchActiveSlides')
                ->once()
                ->andReturn([
                    [
                        'eyebrow' => 'Spring / Summer 2026',
                        'title' => '動態輪播標題',
                        'description' => '動態輪播說明文字',
                        'image' => 'https://example.com/hero-slide.jpg',
                        'image_alt' => '動態輪播圖片',
                        'primary_cta' => [
                            'label' => '探索本季精選',
                            'url' => '',
                        ],
                        'secondary_cta' => [
                            'label' => '',
                            'url' => '',
                        ],
                    ],
                ]);
        });

        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchHomepageAnnouncements')->once()->andReturn([]);
            $mock->shouldReceive('fetchSystemAnnouncement')->once()->andReturn(null);
        });

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="heroPrimaryCta"', false);
        $response->assertSee('frontend-btn-primary hidden', false);
        $response->assertDontSee('frontend-btn-primary hidden inline-flex', false);
    }

    /**
     * CTA 填齊時按鈕應顯示（inline-flex）。
     */
    public function test_homepage_shows_cta_when_fully_filled(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchActiveSlides')
                ->once()
                ->andReturn([
                    [
                        'eyebrow' => 'Spring / Summer 2026',
                        'title' => '動態輪播標題',
                        'description' => '動態輪播說明文字',
                        'image' => 'https://example.com/hero-slide.jpg',
                        'image_alt' => '動態輪播圖片',
                        'primary_cta' => [
                            'label' => '探索本季精選',
                            'url' => '/#products',
                        ],
                        'secondary_cta' => [
                            'label' => '閱讀品牌日誌',
                            'url' => '/#journal',
                        ],
                    ],
                ]);
        });

        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchHomepageAnnouncements')->once()->andReturn([]);
            $mock->shouldReceive('fetchSystemAnnouncement')->once()->andReturn(null);
        });

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('frontend-btn-primary inline-flex', false);
        $response->assertDontSee('frontend-btn-primary hidden', false);
    }

    /**
     * 舊資料 secondary CTA 僅填一半時仍應隱藏。
     */
    public function test_homepage_hides_secondary_cta_when_incomplete(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchActiveSlides')
                ->once()
                ->andReturn([
                    [
                        'eyebrow' => 'Spring / Summer 2026',
                        'title' => '動態輪播標題',
                        'description' => '動態輪播說明文字',
                        'image' => 'https://example.com/hero-slide.jpg',
                        'image_alt' => '動態輪播圖片',
                        'primary_cta' => [
                            'label' => '探索本季精選',
                            'url' => '/#products',
                        ],
                        'secondary_cta' => [
                            'label' => '閱讀品牌日誌',
                            'url' => '',
                        ],
                    ],
                ]);
        });

        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchHomepageAnnouncements')->once()->andReturn([]);
            $mock->shouldReceive('fetchSystemAnnouncement')->once()->andReturn(null);
        });

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('id="heroSecondaryCta"', false);
        $response->assertSee('frontend-btn-ghost hidden', false);
        $response->assertDontSee('frontend-btn-ghost hidden inline-flex', false);
    }
}

