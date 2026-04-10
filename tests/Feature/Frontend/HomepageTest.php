<?php

namespace Tests\Feature\Frontend;

use App\Services\Admin\HeroSlideService;
use App\Services\Frontend\AnnouncementService;
use Mockery\MockInterface;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    /**
     * 首頁可正常顯示主要區塊。
     */
    public function test_homepage_renders_successfully_with_key_sections(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchActiveSlides')
                ->once()
                ->andReturn([
                    [
                        'eyebrow' => 'Spring / Summer 2026',
                        'title' => '首頁輪播測試',
                        'description' => '這是一則首頁輪播描述。',
                        'image' => 'https://example.com/hero.jpg',
                        'image_alt' => '首頁輪播圖片',
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
                ->andReturn([
                    [
                        'title' => '首頁公告測試',
                        'date_display' => 'Apr 09, 2026',
                        'content_preview' => '這是一則首頁公告摘要。',
                        'url' => url('announcement/1'),
                    ],
                ]);

            $mock->shouldReceive('fetchSystemAnnouncement')
                ->once()
                ->andReturn([
                    'title' => '系統公告',
                    'message' => '這是一則系統公告',
                    'link_label' => '前往公告列表',
                    'link_url' => url('announcement'),
                ]);
        });

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Aura & Heirloom');
        $response->assertSee('The Journal');
        $response->assertSee('Selected Works');
        $response->assertSee('會員專區');
        $response->assertSee('首頁輪播測試');
        $response->assertSee('首頁公告測試');
    }
}

