<?php

namespace Tests\Feature;

use App\Services\Admin\HeroSlideService;
use App\Services\Frontend\AnnouncementService;
use Mockery\MockInterface;
// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->mock(HeroSlideService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchActiveSlides')
                ->once()
                ->andReturn([]);
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

        $response->assertStatus(200);
    }
}
