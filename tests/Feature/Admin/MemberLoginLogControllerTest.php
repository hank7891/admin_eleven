<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MemberLoginLogControllerTest extends TestCase
{
    use DatabaseTransactions;

    private function adminSession(array $allowedUrls = ['/admin/member.login-log/list']): array
    {
        return [
            ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
            ADMIN_PERMISSION_SESSION => [1],
            'admin_allowed_urls' => $allowedUrls,
        ];
    }

    private function insertLoginLog(array $overrides = []): int
    {
        $data = array_merge([
            'member_id' => 999,
            'account' => 'member999@example.com',
            'member_name' => '日誌測試會員',
            'action' => MEMBER_LOGIN_LOG_ACTION_LOGIN,
            'status' => MEMBER_LOGIN_LOG_STATUS_SUCCESS,
            'fail_reason' => null,
            'ip_address' => '10.0.0.1',
            'user_agent' => 'Mozilla/5.0 Test',
            'operated_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides);

        return (int) DB::table('member_login_logs')->insertGetId($data);
    }

    public function test_list_redirects_to_login_when_not_logged_in(): void
    {
        $response = $this->get('/admin/member.login-log/list');

        $response->assertRedirect('/admin/login');
    }

    public function test_list_rejects_when_no_permission(): void
    {
        $response = $this
            ->withSession($this->adminSession(allowedUrls: []))
            ->get('/admin/member.login-log/list');

        $response->assertStatus(302);
        $this->assertStringNotContainsString('/admin/member.login-log/list', (string) $response->headers->get('Location'));
    }

    public function test_list_without_filter_shows_prompt(): void
    {
        $response = $this
            ->withSession($this->adminSession())
            ->get('/admin/member.login-log/list');

        $response->assertOk();
        $response->assertSee('請輸入搜尋條件後查詢');
    }

    public function test_list_with_filter_shows_matching_data(): void
    {
        $this->insertLoginLog([
            'account' => 'filtermatch@example.com',
            'member_name' => '符合篩選會員',
        ]);

        $response = $this
            ->withSession($this->adminSession())
            ->get('/admin/member.login-log/list?member_keyword=filtermatch');

        $response->assertOk();
        $response->assertSee('filtermatch@example.com');
        $response->assertSee('符合篩選會員');
    }

    public function test_detail_renders_user_agent_with_xss_escape(): void
    {
        $id = $this->insertLoginLog([
            'user_agent' => '<script>alert("xss")</script>Mozilla',
        ]);

        $response = $this
            ->withSession($this->adminSession([
                '/admin/member.login-log/list',
                '/admin/member.login-log/detail/' . $id,
            ]))
            ->get('/admin/member.login-log/detail/' . $id);

        $response->assertOk();
        # Blade {{ }} escapes <script>
        $response->assertDontSee('<script>alert("xss")</script>', false);
        $response->assertSee('&lt;script&gt;', false);
    }

    public function test_detail_with_nonexistent_id_redirects_back_with_message(): void
    {
        $response = $this
            ->withSession($this->adminSession())
            ->get('/admin/member.login-log/detail/99999999');

        $response->assertRedirect('admin/member.login-log/list');
        $response->assertSessionHas(ADMIN_MESSAGE_SESSION);
    }
}
