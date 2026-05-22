<?php

namespace Tests\Feature\Frontend;

use App\Services\Frontend\AboutService;
use App\Services\Frontend\AnnouncementService;
use Mockery\MockInterface;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    public function test_about_page_renders_successfully(): void
    {
        $this->mock(AboutService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetch')
                ->once()
                ->andReturn([
                    'hero_title' => 'Aura & Heirloom',
                    'hero_subtitle' => '為日常留一個慢下來的位置',
                    'hero_image_url' => null,
                    'story' => [
                        'title' => '品牌故事',
                        'content' => '第一段' . PHP_EOL . '第二段',
                    ],
                    'mission' => null,
                    'vision' => [
                        'title' => '願景標題',
                        'content' => '願景內容',
                    ],
                    'contact' => null,
                    'meta_description' => 'About meta description',
                ]);
        });

        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchSystemAnnouncement')->once()->andReturn(null);
        });

        $response = $this->get('/about');

        $response->assertOk();
        $response->assertSee('Aura &amp; Heirloom', false);
        $response->assertSee('品牌故事');
        $response->assertSee('願景標題');
        $response->assertDontSee('聯絡我們');
        $response->assertSee('meta name="description" content="About meta description"', false);
    }

    public function test_about_page_marks_about_nav_item_active(): void
    {
        $this->mock(AboutService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetch')
                ->once()
                ->andReturn([
                    'hero_title' => '主標',
                    'hero_subtitle' => null,
                    'hero_image_url' => null,
                    'story' => [
                        'title' => '故事',
                        'content' => '內容',
                    ],
                    'mission' => null,
                    'vision' => null,
                    'contact' => null,
                    'meta_description' => null,
                ]);
        });

        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchSystemAnnouncement')->once()->andReturn(null);
        });

        $response = $this->get('/about');
        $response->assertOk();

        $this->assertMatchesRegularExpression(
            '/href="' . preg_quote(url('about'), '/') . '"\s+class="[^"]*is-active[^"]*"/',
            $response->getContent()
        );
    }
}

