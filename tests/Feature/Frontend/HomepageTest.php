<?php

namespace Tests\Feature\Frontend;

use Tests\TestCase;

class HomepageTest extends TestCase
{
    /**
     * 首頁可正常顯示主要區塊。
     */
    public function test_homepage_renders_successfully_with_key_sections(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Aura & Heirloom');
        $response->assertSee('The Journal');
        $response->assertSee('Selected Works');
        $response->assertSee('會員專區');
    }
}

