<?php

namespace Tests\Feature\Frontend;

use App\Services\Admin\HeroSlideService;
use App\Services\Frontend\AnnouncementService;
use App\Services\Frontend\ProductService;
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
                        'image' => 'https://example.com/hero.jpg',
                        'image_alt' => '首頁輪播圖片',
                        'target_url' => '/product',
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

        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchHomepageFeatured')
                ->once()
                ->andReturn([
                    [
                        'id' => 1,
                        'name' => '首頁商品測試',
                        'category_name' => '燈具',
                        'price_display' => 'NT$ 1,200',
                        'image_url' => 'https://example.com/product.jpg',
                        'image_alt' => '首頁商品圖片',
                        'url' => url('product/1'),
                    ],
                ]);
        });

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Aura & Heirloom');
        $response->assertSee('Selected Works');
        $response->assertSee('會員專區');
        $response->assertSee('id="heroSlideLink"', false);
        $response->assertDontSee('id="heroPrimaryCta"', false);
        $response->assertDontSee('id="heroSecondaryCta"', false);
        $response->assertDontSee('id="heroTitle"', false);
        $response->assertDontSee('id="heroDescription"', false);
        $response->assertDontSee('把生活過成一種有留白的閱讀體驗');
        $response->assertDontSee('最新五筆已生效一般公告。保留足夠呼吸感與節奏，讓資訊像品牌日誌而非訊息牆。');
        $response->assertSee('href="' . url('announcement') . '"', false);
        $response->assertSee('More Articles');
        $response->assertSee('首頁公告測試');
        $response->assertSee('首頁商品測試');
    }

    public function test_home_member_section_shows_login_and_register_cta_for_guest(): void
    {
        $this->mockHomeServices();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('建立會員帳號');
        $response->assertSee('會員登入');
        $response->assertSee('href="' . url('member/register') . '"', false);
        $response->assertSee('href="' . url('member/login') . '"', false);
        $response->assertDontSee('進入會員專區');
    }

    public function test_home_member_section_shows_profile_entry_for_logged_in_member(): void
    {
        $this->mockHomeServices();

        $response = $this->withSession([
            MEMBER_AUTH_SESSION => [
                'id' => 9001,
                'email' => 'member@example.com',
                'name' => '首頁會員',
                'avatar_url' => null,
            ],
        ])->get('/');

        $response->assertOk();
        $response->assertSee('進入會員專區');
        $response->assertSee('會員登出');
        $response->assertSee('href="' . url('member/profile') . '"', false);
        $response->assertSee('action="' . url('member/logout') . '"', false);
        $response->assertDontSee('建立會員帳號');
    }

    protected function mockHomeServices(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchActiveSlides')->andReturn([]);
        });
        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchHomepageAnnouncements')->andReturn([]);
            $mock->shouldReceive('fetchSystemAnnouncement')->andReturn(null);
        });
        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchHomepageFeatured')->andReturn([]);
        });
    }
}

