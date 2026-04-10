<?php

namespace Tests\Feature\Admin;

use App\Services\Admin\AdminLogService;
use App\Services\Admin\HeroSlideService;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\MockInterface;
use Tests\TestCase;

class HeroSlideControllerTest extends TestCase
{
    /**
     * 輪播列表頁可正常顯示。
     */
    public function test_hero_slide_list_page_renders_successfully(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchPaginatedData')
                ->once()
                ->andReturn([
                    'data' => [
                        [
                            'id' => 1,
                            'title' => '首頁輪播一',
                            'eyebrow' => 'Spring / Summer 2026',
                            'description_preview' => '輪播摘要',
                            'sort_order' => 1,
                            'is_active' => STATUS_ACTIVE,
                            'is_active_display' => '啟用',
                            'status_badge_class' => 'bg-emerald-50 text-emerald-600',
                            'start_at_display' => '2026-04-09 10:00',
                            'end_at_display' => '永久',
                            'image_url' => 'https://example.com/hero.jpg',
                            'image_alt' => '輪播圖片',
                            'primary_cta_label' => '探索本季精選',
                            'secondary_cta_label' => '閱讀品牌日誌',
                        ],
                    ],
                    'pagination' => new LengthAwarePaginator([['id' => 1]], 1, 20),
                ]);
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/hero-slide/list'],
            ])
            ->get('/admin/hero-slide/list');

        $response->assertOk();
        $response->assertSee('輪播管理');
        $response->assertSee('首頁輪播一');
    }

    /**
     * 快速開關可回傳成功 JSON。
     */
    public function test_toggle_active_returns_success_json(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('toggleActive')
                ->once()
                ->with(1)
                ->andReturn([
                    'id' => 1,
                    'is_active' => STATUS_INACTIVE,
                    'is_active_display' => '停用',
                ]);
        });

        $this->mock(AdminLogService::class, function (MockInterface $mock) {
            $mock->shouldReceive('recordSimple')
                ->once();
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/hero-slide/list'],
            ])
            ->post('/admin/hero-slide/toggle-active/1');

        $response->assertOk();
        $response->assertJson([
            'status' => true,
            'message' => '狀態更新成功',
            'data' => [
                'id' => 1,
                'is_active' => STATUS_INACTIVE,
                'is_active_display' => '停用',
            ],
        ]);
    }

    /**
     * 編輯輪播時允許站內相對 CTA 連結。
     */
    public function test_edit_do_allows_relative_cta_url(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchDataByID')
                ->once()
                ->with(1)
                ->andReturn([
                    'id' => 1,
                    'title' => '首頁輪播一',
                    'image_path' => 'uploads/image/2026/04/example.jpg',
                ]);

            $mock->shouldReceive('updateData')
                ->once()
                ->withArgs(function (int $id, array $data) {
                    return $id === 1
                        && ($data['primary_cta_url'] ?? null) === '/#products'
                        && ($data['secondary_cta_url'] ?? null) === '/#journal';
                })
                ->andReturn(1);
        });

        $this->mock(AdminLogService::class, function (MockInterface $mock) {
            $mock->shouldReceive('recordUpdate')->once();
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/hero-slide/list'],
            ])
            ->post('/admin/hero-slide/edit', [
                'id' => 1,
                'image_alt' => '輪播圖片',
                'eyebrow' => 'Spring / Summer 2026',
                'title' => '首頁輪播一',
                'description' => '輪播描述',
                'primary_cta_label' => '探索本季精選',
                'primary_cta_url' => '/#products',
                'secondary_cta_label' => '閱讀品牌日誌',
                'secondary_cta_url' => '/#journal',
                'sort_order' => 1,
                'is_active' => STATUS_ACTIVE,
                'start_at' => '2026-04-09T10:00',
                'end_at' => '',
            ]);

        $response->assertRedirect('/admin/hero-slide/edit/1');
    }

    /**
     * 編輯輪播時應阻擋危險的 javascript 連結。
     */
    public function test_edit_do_rejects_javascript_cta_url(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('updateData');
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/hero-slide/list'],
            ])
            ->post('/admin/hero-slide/edit', [
                'id' => 1,
                'image_alt' => '輪播圖片',
                'eyebrow' => 'Spring / Summer 2026',
                'title' => '首頁輪播一',
                'description' => '輪播描述',
                'primary_cta_label' => '探索本季精選',
                'primary_cta_url' => 'javascript:alert(1)',
                'secondary_cta_label' => '',
                'secondary_cta_url' => '',
                'sort_order' => 1,
                'is_active' => STATUS_ACTIVE,
                'start_at' => '2026-04-09T10:00',
                'end_at' => '',
            ]);

        $response->assertRedirect('/admin/hero-slide/edit/1');
    }

    /**
     * 編輯輪播時 CTA 文字與連結需成對填寫。
     */
    public function test_edit_do_requires_paired_cta_fields(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('updateData');
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/hero-slide/list'],
            ])
            ->post('/admin/hero-slide/edit', [
                'id' => 1,
                'image_alt' => '輪播圖片',
                'eyebrow' => 'Spring / Summer 2026',
                'title' => '首頁輪播一',
                'description' => '輪播描述',
                'primary_cta_label' => '探索本季精選',
                'primary_cta_url' => '',
                'secondary_cta_label' => '',
                'secondary_cta_url' => '',
                'sort_order' => 1,
                'is_active' => STATUS_ACTIVE,
                'start_at' => '2026-04-09T10:00',
                'end_at' => '',
            ]);

        $response->assertRedirect('/admin/hero-slide/edit/1');
    }
}

