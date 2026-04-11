<?php

namespace Tests\Feature\Admin;

use App\Repositories\Admin\ProductCategoryRepository;
use App\Repositories\Admin\ProductRepository;
use App\Repositories\Admin\ProductTagRepository;
use App\Services\Admin\AdminLogService;
use App\Services\Admin\ProductCategoryService;
use App\Services\Admin\ProductService;
use App\Services\Share\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use Tests\TestCase;

class ProductTest extends TestCase
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
                            'name' => '測試商品',
                            'tagline' => '測試標語',
                            'price_display' => 'NT$ 1,200',
                            'category_name' => '沙發',
                            'tag_names' => ['布料'],
                            'is_featured_display' => '主打',
                            'status_display' => '上架',
                            'status_badge_class' => 'bg-emerald-50 text-emerald-600',
                            'period_display' => '2026-04-11 10:00 ~ 永久',
                            'updated_at_display' => '2026-04-11 10:00',
                            'primary_image_url' => '',
                        ],
                    ],
                    'pagination' => new LengthAwarePaginator([['id' => 1]], 1, 20),
                ]);

            $mock->shouldReceive('fetchFilterOptions')
                ->once()
                ->andReturn([
                    'categories' => [],
                    'tags' => [],
                    'statuses' => [PRODUCT_STATUS_ONLINE => '上架', PRODUCT_STATUS_OFFLINE => '下架'],
                    'featured_options' => [PRODUCT_FEATURED_ON => '主打', PRODUCT_FEATURED_OFF => '一般'],
                    'period_states' => ['live' => '進行中'],
                ]);
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/product/list'],
            ])
            ->get('/admin/product/list');

        $response->assertOk();
        $response->assertSee('商品管理');
        $response->assertSee('測試商品');
    }

    public function test_product_bulk_status_updates_successfully(): void
    {
        DB::table('products')->whereIn('id', [1, 2])->delete();

        DB::table('products')->insert([
            [
                'id' => 1,
                'name' => '測試商品 1',
                'price' => 100,
                'description' => '測試描述',
                'status_key' => (string) PRODUCT_STATUS_OFFLINE,
                'start_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => '測試商品 2',
                'price' => 200,
                'description' => '測試描述',
                'status_key' => (string) PRODUCT_STATUS_OFFLINE,
                'start_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldReceive('bulkUpdateStatus')
                ->once()
                ->with([1, 2], PRODUCT_STATUS_ONLINE)
                ->andReturn(2);
        });

        $this->mock(AdminLogService::class, function (MockInterface $mock) {
            $mock->shouldReceive('recordSimple')->once();
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/product/list'],
            ])
            ->post('/admin/product/bulk-status', [
                'ids' => [1, 2],
                'status_key' => (string) PRODUCT_STATUS_ONLINE,
            ]);

        $response->assertRedirect('/admin/product/list?');

        DB::table('products')->whereIn('id', [1, 2])->delete();
    }

    public function test_product_create_validates_images_required(): void
    {
        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('addData');
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/product/list'],
            ])
            ->post('/admin/product/edit', [
                'id' => 0,
                'name' => '測試商品',
                'price' => 100,
                'description' => '描述',
                'sort_order' => 1,
                'is_featured' => PRODUCT_FEATURED_OFF,
                'status_key' => PRODUCT_STATUS_ONLINE,
                'start_at' => '2026-04-11T10:00',
                'end_at' => '',
            ]);

        $response->assertRedirect('/admin/product/edit/0');
    }

    public function test_product_add_forces_status_key_to_offline(): void
    {
        session([ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester']]);

        $file = UploadedFile::fake()->image('test.jpg');

        $this->mock(FileUploadService::class, function (MockInterface $mock) {
            $mock->shouldReceive('upload')->once()->andReturn('uploads/image/test.jpg');
        });

        $capturedPayload = null;
        $this->mock(ProductRepository::class, function (MockInterface $mock) use (&$capturedPayload) {
            $mock->shouldReceive('addData')
                ->once()
                ->andReturnUsing(function ($payload) use (&$capturedPayload) {
                    $capturedPayload = $payload;
                    return (object) ['id' => 99];
                });
            $mock->shouldReceive('addImageData')->once()->andReturn((object) ['id' => 1]);
            $mock->shouldReceive('syncTags')->once();
        });

        $this->mock(ProductCategoryRepository::class);
        $this->mock(ProductTagRepository::class);

        $service = app(ProductService::class);
        $id = $service->addData(
            [
                'name' => '測試商品',
                'price' => 100,
                'description' => '描述',
                'status_key' => (string) PRODUCT_STATUS_ONLINE,
                'start_at' => '2026-04-11T10:00',
            ],
            [$file],
            []
        );

        $this->assertSame(99, $id);
        $this->assertNotNull($capturedPayload);
        $this->assertSame((string) PRODUCT_STATUS_OFFLINE, (string) $capturedPayload['status_key']);
    }

    public function test_product_add_rejects_zero_images(): void
    {
        session([ADMIN_AUTH_SESSION => ['id' => 1]]);

        $this->mock(FileUploadService::class);
        $this->mock(ProductRepository::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('addData');
        });
        $this->mock(ProductCategoryRepository::class);
        $this->mock(ProductTagRepository::class);

        $service = app(ProductService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('商品圖片張數需介於 1 ~ ' . PRODUCT_MAX_IMAGES);

        $service->addData(
            [
                'name' => '測試',
                'price' => 100,
                'description' => '描述',
                'start_at' => '2026-04-11T10:00',
            ],
            [],
            []
        );
    }

    public function test_product_add_rejects_more_than_max_images(): void
    {
        session([ADMIN_AUTH_SESSION => ['id' => 1]]);

        $files = array_map(
            fn ($i) => UploadedFile::fake()->image("test{$i}.jpg"),
            range(1, PRODUCT_MAX_IMAGES + 1)
        );

        $this->mock(FileUploadService::class);
        $this->mock(ProductRepository::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('addData');
        });
        $this->mock(ProductCategoryRepository::class);
        $this->mock(ProductTagRepository::class);

        $service = app(ProductService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('商品圖片張數需介於 1 ~ ' . PRODUCT_MAX_IMAGES);

        $service->addData(
            [
                'name' => '測試',
                'price' => 100,
                'description' => '描述',
                'start_at' => '2026-04-11T10:00',
            ],
            $files,
            []
        );
    }

    public function test_product_update_rejects_cross_product_primary_id(): void
    {
        session([ADMIN_AUTH_SESSION => ['id' => 1]]);

        $this->mock(FileUploadService::class);
        $this->mock(ProductRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchDataByID')
                ->with(10)
                ->andReturn([
                    'id' => 10,
                    'name' => '商品 A',
                    'tagline' => null,
                    'price' => 100,
                    'description' => '描述',
                    'category_id' => null,
                    'category' => null,
                    'tags' => [],
                    'images' => [
                        ['id' => 1, 'image_path' => 'uploads/a1.jpg', 'is_primary' => 1, 'sort_order' => 1],
                        ['id' => 2, 'image_path' => 'uploads/a2.jpg', 'is_primary' => 0, 'sort_order' => 2],
                    ],
                    'status_key' => (string) PRODUCT_STATUS_OFFLINE,
                    'is_featured' => PRODUCT_FEATURED_OFF,
                    'sort_order' => 0,
                    'start_at' => '2026-04-11 10:00:00',
                    'end_at' => null,
                    'updated_at' => '2026-04-11 10:00:00',
                ]);
            $mock->shouldReceive('updateData')->zeroOrMoreTimes();
            $mock->shouldNotReceive('setPrimaryImage');
            $mock->shouldNotReceive('syncTags');
        });

        $this->mock(ProductCategoryRepository::class);
        $this->mock(ProductTagRepository::class);

        $service = app(ProductService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('標題圖片設定錯誤');

        $service->updateData(
            10,
            [
                'name' => '商品 A',
                'price' => 100,
                'description' => '描述',
                'start_at' => '2026-04-11T10:00',
            ],
            [],
            [
                'kept_ids' => [1, 2],
                'primary_id' => 999,
            ]
        );
    }

    public function test_product_bulk_update_rejects_empty_ids(): void
    {
        $this->mock(FileUploadService::class);
        $this->mock(ProductRepository::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('bulkUpdateStatus');
        });
        $this->mock(ProductCategoryRepository::class);
        $this->mock(ProductTagRepository::class);

        $service = app(ProductService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('請至少選擇一筆商品');

        $service->bulkUpdateStatus([], PRODUCT_STATUS_ONLINE);
    }

    public function test_product_category_delete_rejected_when_referenced_by_products(): void
    {
        $this->mock(ProductCategoryRepository::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchDataByID')
                ->with(5)
                ->once()
                ->andReturn([
                    'id' => 5,
                    'name' => '熱銷',
                    'sort_order' => 0,
                    'is_active' => STATUS_ACTIVE,
                    'created_at' => '2026-04-11 10:00:00',
                    'updated_at' => '2026-04-11 10:00:00',
                    'products_count' => 3,
                ]);
            $mock->shouldNotReceive('deleteData');
        });

        $service = app(ProductCategoryService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('此類別已有商品使用中');

        $service->deleteData(5);
    }

    public function test_unauthorized_admin_redirected_from_product_list(): void
    {
        $this->mock(ProductService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('fetchPaginatedData');
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [],
                'admin_allowed_urls' => [],
            ])
            ->get('/admin/product/list');

        $response->assertStatus(302);
        $this->assertNotEquals('/admin/product/list', $response->headers->get('Location'));
    }
}
