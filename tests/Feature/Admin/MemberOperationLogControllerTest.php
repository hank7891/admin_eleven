<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MemberOperationLogControllerTest extends TestCase
{
    use DatabaseTransactions;

    private function adminSession(array $allowedUrls = ['/admin/member.operation-log/list']): array
    {
        return [
            ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
            ADMIN_PERMISSION_SESSION => [1],
            'admin_allowed_urls' => $allowedUrls,
        ];
    }

    private function insertOperationLog(array $overrides = []): int
    {
        $data = array_merge([
            'member_id' => 999,
            'operator_name' => '操作測試會員',
            'ip_address' => '10.0.0.1',
            'module' => 'member_profile',
            'action' => 'update',
            'target_id' => 999,
            'target_name' => '個人資料',
            'changes' => json_encode(['name' => ['old' => 'A', 'new' => 'B']], JSON_UNESCAPED_UNICODE),
            'remarks' => null,
            'operated_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides);

        return (int) DB::table('member_operation_logs')->insertGetId($data);
    }

    public function test_list_redirects_to_login_when_not_logged_in(): void
    {
        $response = $this->get('/admin/member.operation-log/list');

        $response->assertRedirect('/admin/login');
    }

    public function test_list_rejects_when_no_permission(): void
    {
        $response = $this
            ->withSession($this->adminSession(allowedUrls: []))
            ->get('/admin/member.operation-log/list');

        $response->assertStatus(302);
        $this->assertStringNotContainsString('/admin/member.operation-log/list', (string) $response->headers->get('Location'));
    }

    public function test_list_without_filter_shows_prompt(): void
    {
        $response = $this
            ->withSession($this->adminSession())
            ->get('/admin/member.operation-log/list');

        $response->assertOk();
        $response->assertSee('請輸入搜尋條件後查詢');
    }

    public function test_list_with_filter_shows_matching_data(): void
    {
        $this->insertOperationLog([
            'operator_name' => '篩選命中會員',
        ]);

        $response = $this
            ->withSession($this->adminSession())
            ->get('/admin/member.operation-log/list?member_keyword=' . urlencode('篩選命中會員'));

        $response->assertOk();
        $response->assertSee('篩選命中會員');
    }

    public function test_detail_filters_sensitive_fields_from_changes(): void
    {
        # 即使 DB 內有 password 欄位（假設寫入層遺漏），讀取層也要過濾
        $id = $this->insertOperationLog([
            'changes' => json_encode([
                'name' => ['old' => '舊名稱', 'new' => '新名稱'],
                'password' => ['old' => 'secret-old', 'new' => 'secret-new'],
                'token' => ['old' => null, 'new' => 'tok-abc'],
            ], JSON_UNESCAPED_UNICODE),
        ]);

        $response = $this
            ->withSession($this->adminSession([
                '/admin/member.operation-log/list',
                '/admin/member.operation-log/detail/' . $id,
            ]))
            ->get('/admin/member.operation-log/detail/' . $id);

        $response->assertOk();
        $response->assertSee('新名稱');
        $response->assertDontSee('secret-old');
        $response->assertDontSee('secret-new');
        $response->assertDontSee('tok-abc');
    }

    public function test_detail_with_nonexistent_id_redirects_back_with_message(): void
    {
        $response = $this
            ->withSession($this->adminSession())
            ->get('/admin/member.operation-log/detail/99999999');

        $response->assertRedirect('admin/member.operation-log/list');
        $response->assertSessionHas(ADMIN_MESSAGE_SESSION);
    }
}
