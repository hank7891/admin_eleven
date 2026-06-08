<?php

namespace Tests\Feature\Admin;

use App\Models\AdminLoginLog;
use App\Models\MemberLoginLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoginLogStatusToneTest extends TestCase
{
    use DatabaseTransactions;

    private function adminSession(array $allowedUrls): array
    {
        return [
            ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
            ADMIN_PERMISSION_SESSION => [1],
            'admin_allowed_urls' => $allowedUrls,
        ];
    }

    private function insertAdminLoginLog(array $overrides = []): int
    {
        $data = array_merge([
            'employee_id' => 1,
            'account' => 'admin-tone@example.com',
            'employee_name' => '後台 Tone 測試',
            'action' => LOGIN_LOG_ACTION_LOGIN,
            'status' => LOGIN_LOG_STATUS_SUCCESS,
            'fail_reason' => null,
            'ip_address' => '127.0.0.10',
            'operated_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides);

        return (int) DB::table('admin_login_logs')->insertGetId($data);
    }

    private function insertMemberLoginLog(array $overrides = []): int
    {
        $data = array_merge([
            'member_id' => 1,
            'account' => 'member-tone@example.com',
            'member_name' => '會員 Tone 測試',
            'action' => MEMBER_LOGIN_LOG_ACTION_LOGIN,
            'status' => MEMBER_LOGIN_LOG_STATUS_SUCCESS,
            'fail_reason' => null,
            'ip_address' => '127.0.0.20',
            'user_agent' => 'Tone Test Agent',
            'operated_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ], $overrides);

        return (int) DB::table('member_login_logs')->insertGetId($data);
    }

    public function test_admin_login_log_status_tone_matches_success_and_fail_on_list_and_detail(): void
    {
        $successId = $this->insertAdminLoginLog([
            'account' => 'admin-success-tone@example.com',
            'employee_name' => '後台成功 Tone',
            'status' => LOGIN_LOG_STATUS_SUCCESS,
        ]);
        $failId = $this->insertAdminLoginLog([
            'account' => 'admin-fail-tone@example.com',
            'employee_name' => '後台失敗 Tone',
            'status' => LOGIN_LOG_STATUS_FAIL,
            'fail_reason' => '密碼錯誤',
        ]);

        $this->assertSame('success', AdminLoginLog::findOrFail($successId)->status_tone);
        $this->assertSame('danger', AdminLoginLog::findOrFail($failId)->status_tone);

        $session = $this->adminSession(['/admin/admin.login-log/list']);
        $listResponse = $this
            ->withSession($session)
            ->get('/admin/admin.login-log/list?operator_keyword=admin-');

        $listResponse->assertOk();
        $listResponse->assertSee('admin-success-tone@example.com');
        $listResponse->assertSee('admin-fail-tone@example.com');
        $listResponse->assertSee('admin-badge-success', false);
        $listResponse->assertSee('admin-badge-danger', false);

        $successDetail = $this
            ->withSession($session)
            ->get('/admin/admin.login-log/detail/' . $successId);
        $successDetail->assertOk();
        $successDetail->assertSee('admin-badge-success', false);

        $failDetail = $this
            ->withSession($session)
            ->get('/admin/admin.login-log/detail/' . $failId);
        $failDetail->assertOk();
        $failDetail->assertSee('admin-badge-danger', false);
    }

    public function test_member_login_log_status_tone_matches_success_and_fail_on_list_and_detail(): void
    {
        $successId = $this->insertMemberLoginLog([
            'account' => 'member-success-tone@example.com',
            'member_name' => '會員成功 Tone',
            'status' => MEMBER_LOGIN_LOG_STATUS_SUCCESS,
        ]);
        $failId = $this->insertMemberLoginLog([
            'account' => 'member-fail-tone@example.com',
            'member_name' => '會員失敗 Tone',
            'status' => MEMBER_LOGIN_LOG_STATUS_FAIL,
            'fail_reason' => '登入失敗',
        ]);

        $this->assertSame('success', MemberLoginLog::findOrFail($successId)->status_tone);
        $this->assertSame('danger', MemberLoginLog::findOrFail($failId)->status_tone);

        $session = $this->adminSession(['/admin/member.login-log/list']);
        $listResponse = $this
            ->withSession($session)
            ->get('/admin/member.login-log/list?member_keyword=member-');

        $listResponse->assertOk();
        $listResponse->assertSee('member-success-tone@example.com');
        $listResponse->assertSee('member-fail-tone@example.com');
        $listResponse->assertSee('admin-badge-success', false);
        $listResponse->assertSee('admin-badge-danger', false);

        $successDetail = $this
            ->withSession($session)
            ->get('/admin/member.login-log/detail/' . $successId);
        $successDetail->assertOk();
        $successDetail->assertSee('admin-badge-success', false);

        $failDetail = $this
            ->withSession($session)
            ->get('/admin/member.login-log/detail/' . $failId);
        $failDetail->assertOk();
        $failDetail->assertSee('admin-badge-danger', false);
    }
}
