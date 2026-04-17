<?php

namespace Tests\Feature\Admin;

use App\Services\Share\MessageService;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PostTooLargeExceptionTest extends TestCase
{
    public function test_admin_post_too_large_redirects_back_with_message(): void
    {
        $uri = '/admin/test-post-too-large-web';

        Route::post($uri, function () {
            throw new PostTooLargeException('payload too large');
        });

        $response = $this
            ->withHeader('referer', '/admin/about/edit')
            ->post($uri);

        $response->assertRedirect('/admin/about/edit?upload_error=too_large');
        $response->assertSessionHas(ADMIN_MESSAGE_SESSION, function ($messages) {
            if (!is_array($messages) || empty($messages[0])) {
                return false;
            }

            return ($messages[0]['type'] ?? null) === MessageService::DANGER
                && str_contains((string) ($messages[0]['message'] ?? ''), '上傳檔案過大');
        });
    }

    public function test_admin_post_too_large_returns_413_for_json_request(): void
    {
        $uri = '/admin/test-post-too-large-json';

        Route::post($uri, function () {
            throw new PostTooLargeException('payload too large');
        });

        $response = $this->postJson($uri);

        $response->assertStatus(413);
        $response->assertJson([
            'status' => false,
        ]);
        $response->assertJsonPath('message', function ($message) {
            return str_contains((string) $message, '上傳檔案過大');
        });
    }

    public function test_admin_post_too_large_without_referer_redirects_to_current_admin_path(): void
    {
        $uri = '/admin/test-post-too-large-no-referer';

        Route::post($uri, function () {
            throw new PostTooLargeException('payload too large');
        });

        $response = $this->post($uri);

        $response->assertRedirect($uri . '?upload_error=too_large');
        $response->assertSessionHas(ADMIN_MESSAGE_SESSION);
    }
}



