<?php

namespace Tests\Feature\Admin;

use App\Services\Admin\AboutPageService;
use App\Services\Admin\AdminLogService;
use Mockery\MockInterface;
use Tests\TestCase;

class AboutControllerTest extends TestCase
{
    public function test_about_edit_page_renders_successfully(): void
    {
        $this->mock(AboutPageService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getForEdit')
                ->once()
                ->andReturn([
                    'id' => 1,
                    'hero_title' => 'Aura & Heirloom',
                    'hero_subtitle' => '副標題',
                    'hero_image_path' => null,
                    'hero_image_url' => '',
                    'story_title' => '品牌故事',
                    'story_content' => '故事內容',
                    'mission_title' => null,
                    'mission_content' => null,
                    'vision_title' => null,
                    'vision_content' => null,
                    'contact_email' => null,
                    'contact_phone' => null,
                    'contact_address' => null,
                    'meta_description' => null,
                    'updated_at_display' => '',
                    'updater_name' => '',
                ]);
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/about/edit'],
            ])
            ->get('/admin/about/edit');

        $response->assertOk();
        $response->assertSee('關於我們設定');
    }

    public function test_about_edit_requires_hero_title(): void
    {
        $this->mock(AboutPageService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('update');
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/about/edit'],
            ])
            ->post('/admin/about/edit', [
                'hero_title' => '',
                'story_title' => '品牌故事',
                'story_content' => '內容',
            ]);

        $response->assertRedirect('/admin/about/edit');
    }

    public function test_about_edit_updates_and_records_admin_log(): void
    {
        $this->mock(AboutPageService::class, function (MockInterface $mock) {
            $mock->shouldReceive('update')
                ->once()
                ->andReturn([
                    'old' => [
                        'id' => 1,
                        'hero_title' => '舊主標',
                    ],
                    'new' => [
                        'id' => 1,
                        'hero_title' => '新主標',
                    ],
                ]);
        });

        $this->mock(AdminLogService::class, function (MockInterface $mock) {
            $mock->shouldReceive('recordUpdate')->once();
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/about/edit'],
            ])
            ->post('/admin/about/edit', [
                'hero_title' => '新主標',
                'hero_subtitle' => '副標',
                'story_title' => '品牌故事',
                'story_content' => '故事內容',
                'mission_title' => '使命',
                'mission_content' => '使命內容',
                'vision_title' => '願景',
                'vision_content' => '願景內容',
                'contact_email' => 'service@example.com',
                'contact_phone' => '+886-2-1234-5678',
                'contact_address' => '台北市',
                'meta_description' => 'meta',
            ]);

        $response->assertRedirect('/admin/about/edit');
    }
}

