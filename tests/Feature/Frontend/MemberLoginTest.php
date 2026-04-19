<?php

namespace Tests\Feature\Frontend;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MemberLoginTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/member/login');

        $response->assertOk();
        $response->assertSee('會員登入');
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
    }

    public function test_logged_in_member_cannot_access_login_page(): void
    {
        $response = $this->withSession([
            MEMBER_AUTH_SESSION => [
                'id' => 999,
                'email' => 'member@example.com',
                'name' => '已登入會員',
            ],
        ])->get('/member/login');

        $response->assertRedirect('/');
    }

    public function test_login_success_updates_session_and_login_stamp_with_log(): void
    {
        $memberId = DB::table('member')->insertGetId([
            'email' => 'member-login@example.com',
            'password' => Hash::make('Abcd1234'),
            'name' => '登入會員',
            'gender_key' => (string) GENDER_UNSPECIFIED,
            'status_key' => MEMBER_STATUS_ACTIVE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $beforeAdminLoginCount = DB::table('admin_login_logs')->count();

        $response = $this->post('/member/login', [
            'email' => 'member-login@example.com',
            'password' => 'Abcd1234',
        ]);

        $response->assertRedirect('member/profile');
        $response->assertSessionHas(MEMBER_AUTH_SESSION, function ($auth) use ($memberId) {
            return (int) ($auth['id'] ?? 0) === $memberId
                && ($auth['email'] ?? '') === 'member-login@example.com';
        });

        $updated = DB::table('member')->where('id', $memberId)->first();
        $this->assertNotNull($updated->last_login_at);
        $this->assertNotEmpty($updated->last_login_ip);

        $latestLog = DB::table('member_login_logs')->orderByDesc('id')->first();
        $this->assertSame($memberId, (int) $latestLog->member_id);
        $this->assertSame(MEMBER_LOGIN_LOG_ACTION_LOGIN, $latestLog->action);
        $this->assertSame(MEMBER_LOGIN_LOG_STATUS_SUCCESS, (int) $latestLog->status);

        $this->assertSame($beforeAdminLoginCount, DB::table('admin_login_logs')->count());
    }

    public function test_login_fail_cases_write_unified_fail_message_log(): void
    {
        DB::table('member')->insert([
            'email' => 'inactive-member@example.com',
            'password' => Hash::make('Abcd1234'),
            'name' => '停用會員',
            'gender_key' => (string) GENDER_UNSPECIFIED,
            'status_key' => MEMBER_STATUS_INACTIVE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cases = [
            ['email' => 'inactive-member@example.com', 'password' => 'wrong-password'],
            ['email' => 'not-exists@example.com', 'password' => 'Abcd1234'],
            ['email' => 'inactive-member@example.com', 'password' => 'Abcd1234'],
        ];

        $beforeFailMaxId = (int) DB::table('member_login_logs')
            ->where('status', MEMBER_LOGIN_LOG_STATUS_FAIL)
            ->max('id');

        foreach ($cases as $case) {
            $response = $this->from('/member/login')->post('/member/login', $case);
            $response->assertRedirect('/member/login');
            $response->assertSessionHasErrors('login');
        }

        # 僅比對本次測試新增的 fail logs，避免被先前殘留資料干擾
        $logs = DB::table('member_login_logs')
            ->where('status', MEMBER_LOGIN_LOG_STATUS_FAIL)
            ->where('id', '>', $beforeFailMaxId)
            ->orderBy('id')
            ->get();

        $this->assertSame(3, $logs->count());
        foreach ($logs as $log) {
            $this->assertSame(MEMBER_LOGIN_LOG_ACTION_LOGIN, $log->action);
            $this->assertSame('電子信箱或密碼錯誤，或此帳號已停用', $log->fail_reason);
        }
    }

    public function test_login_validation_failure_does_not_store_password_in_session(): void
    {
        $response = $this->from('/member/login')->post('/member/login', [
            'email' => 'bad-email',
            'password' => 'Abcd1234',
        ]);

        $response->assertRedirect('/member/login');
        $response->assertSessionHasErrors();
        $response->assertSessionHas('member_login_post', function ($post) {
            return !array_key_exists('password', $post)
                && ($post['email'] ?? '') === 'bad-email';
        });
    }

    public function test_logout_writes_logout_log(): void
    {
        $memberId = DB::table('member')->insertGetId([
            'email' => 'logout-member@example.com',
            'password' => Hash::make('Abcd1234'),
            'name' => '登出會員',
            'gender_key' => (string) GENDER_UNSPECIFIED,
            'status_key' => MEMBER_STATUS_ACTIVE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withSession([
            MEMBER_AUTH_SESSION => [
                'id' => $memberId,
                'email' => 'logout-member@example.com',
                'name' => '登出會員',
                'avatar_url' => null,
            ],
        ])->post('/member/logout');

        $response->assertRedirect('/');
        $response->assertSessionMissing(MEMBER_AUTH_SESSION);

        $latestLog = DB::table('member_login_logs')->orderByDesc('id')->first();
        $this->assertSame(MEMBER_LOGIN_LOG_ACTION_LOGOUT, $latestLog->action);
        $this->assertSame($memberId, (int) $latestLog->member_id);
        $this->assertSame(MEMBER_LOGIN_LOG_STATUS_SUCCESS, (int) $latestLog->status);
    }

    public function test_guest_cannot_call_member_logout(): void
    {
        $response = $this->post('/member/logout');

        $response->assertRedirect('member/login');
    }
}

