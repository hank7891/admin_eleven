<?php

namespace Tests\Feature\Frontend;

use App\Services\Frontend\AnnouncementService;
use Mockery\MockInterface;
use Tests\TestCase;

class AnnouncementPagesTest extends TestCase
{
    /**
     * 公告列表頁可正常顯示動態公告。
     */
    public function test_announcement_list_page_renders_successfully(): void
    {
        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchPaginatedData')
                ->once()
                ->andReturn([
                    'data' => [
                        [
                            'title' => '公告列表測試',
                            'summary' => '公告摘要',
                            'content_preview' => '公告摘要',
                            'date_display' => 'Apr 09, 2026',
                            'url' => url('announcement/1'),
                        ],
                    ],
                    'pagination' => new \Illuminate\Pagination\LengthAwarePaginator([
                        ['id' => 1],
                    ], 1, 10),
                    'filters' => ['keyword' => '', 'date_from' => '', 'date_to' => ''],
                ]);

            $mock->shouldReceive('fetchSystemAnnouncement')
                ->once()
                ->andReturn(null);
        });

        $response = $this->get('/announcement');

        $response->assertOk();
        $response->assertSee('The Journal');
        $response->assertSee('公告列表測試');
    }

    /**
     * 公告內頁可正常顯示詳情與延伸閱讀。
     */
    public function test_announcement_detail_page_renders_successfully(): void
    {
        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchDetailByID')
                ->once()
                ->with(1)
                ->andReturn([
                    'id' => 1,
                    'title' => '公告內頁測試',
                    'summary' => '公告詳細摘要',
                    'date_full_display' => '2026.04.09 10:00',
                    'content_lines' => ['第一段內容', '第二段內容'],
                ]);

            $mock->shouldReceive('fetchSystemAnnouncement')
                ->once()
                ->andReturn(null);

            $mock->shouldReceive('fetchMoreAnnouncements')
                ->once()
                ->with(1)
                ->andReturn([
                    [
                        'title' => '更多公告',
                        'content_preview' => '更多公告摘要',
                        'date_display' => 'Apr 08, 2026',
                        'url' => url('announcement/2'),
                    ],
                ]);
        });

        $response = $this->get('/announcement/1');

        $response->assertOk();
        $response->assertSee('公告內頁測試');
        $response->assertSee('第一段內容');
        $response->assertSee('更多公告');
    }
}


