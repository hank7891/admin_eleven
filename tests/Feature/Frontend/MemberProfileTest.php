<?php

namespace Tests\Feature\Frontend;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MemberProfileTest extends TestCase
{
    use DatabaseTransactions;

    public function test_guest_is_redirected_to_member_login_when_accessing_profile(): void
    {
        $response = $this->get('/member/profile');

        $response->assertRedirect('member/login');
    }

    public function test_profile_page_renders_member_data(): void
    {
        $memberId = $this->createMember('profile-page@example.com', '頁面會員');

        $response = $this->withSession([
            MEMBER_AUTH_SESSION => [
                'id' => $memberId,
                'email' => 'profile-page@example.com',
                'name' => '頁面會員',
                'avatar_url' => null,
            ],
        ])->get('/member/profile');

        $response->assertOk();
        $response->assertSee('會員個人資料');
        $response->assertSee('profile-page@example.com');
        $response->assertSee('頁面會員');
    }

    public function test_profile_update_success_updates_data_and_member_operation_log(): void
    {
        Storage::fake('public');

        $memberId = $this->createMember('profile-update@example.com', '舊姓名');
        $otherMemberId = $this->createMember('other-member@example.com', '其他會員');

        $beforeAdminLogCount = DB::table('admin_logs')->count();

        $response = $this->withSession([
            MEMBER_AUTH_SESSION => [
                'id' => $memberId,
                'email' => 'profile-update@example.com',
                'name' => '舊姓名',
                'avatar_url' => null,
            ],
        ])->post('/member/profile', [
            'id' => $otherMemberId,
            'name' => '新姓名',
            'phone' => '0912 345 678',
            'birthday' => '1995-05-20',
            'gender_key' => (string) GENDER_FEMALE,
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 200, 200),
        ]);

        $response->assertRedirect('member/profile');
        $response->assertSessionHas(MEMBER_AUTH_SESSION, function ($auth) use ($memberId) {
            return (int) ($auth['id'] ?? 0) === $memberId
                && ($auth['name'] ?? '') === '新姓名';
        });

        $member = DB::table('member')->where('id', $memberId)->first();
        $this->assertSame('新姓名', $member->name);
        $this->assertSame('0912345678', $member->phone);
        $this->assertSame('1995-05-20', $member->birthday);
        $this->assertSame((string) GENDER_FEMALE, (string) $member->gender_key);
        $this->assertNotEmpty($member->avatar_path);
        $this->assertMatchesRegularExpression('/^uploads\/member_avatar\/\d{4}\/\d{2}\/.+\.(jpg|jpeg|png|gif|webp)$/', $member->avatar_path);
        Storage::disk('public')->assertExists($member->avatar_path);

        $otherMember = DB::table('member')->where('id', $otherMemberId)->first();
        $this->assertSame('其他會員', $otherMember->name);

        $log = DB::table('member_operation_logs')->orderByDesc('id')->first();
        $this->assertSame($memberId, (int) $log->member_id);
        $this->assertSame('member_profile', $log->module);
        $this->assertSame('update', $log->action);

        $changesRaw = $log->changes;
        $changes = is_array($changesRaw) ? $changesRaw : json_decode((string) $changesRaw, true);
        $this->assertIsArray($changes);
        $this->assertArrayNotHasKey('password', $changes);

        $this->assertSame($beforeAdminLogCount, DB::table('admin_logs')->count());
    }

    public function test_change_password_success_updates_hash_and_writes_log_without_sensitive_changes(): void
    {
        $memberId = $this->createMember('password-success@example.com', '改密碼會員', 'OldPass123');

        $response = $this->withSession([
            MEMBER_AUTH_SESSION => [
                'id' => $memberId,
                'email' => 'password-success@example.com',
                'name' => '改密碼會員',
                'avatar_url' => null,
            ],
        ])->post('/member/profile/password', [
            'current_password' => 'OldPass123',
            'new_password' => 'NewPass123',
            'new_password_confirmation' => 'NewPass123',
        ]);

        $response->assertRedirect('member/profile');
        $response->assertSessionHas(MEMBER_AUTH_SESSION);

        $member = DB::table('member')->where('id', $memberId)->first();
        $this->assertTrue(Hash::check('NewPass123', (string) $member->password));

        $log = DB::table('member_operation_logs')->orderByDesc('id')->first();
        $this->assertSame('member_profile', $log->module);
        $this->assertSame('update', $log->action);
        $this->assertSame('會員變更登入密碼', $log->remarks);

        $changesRaw = $log->changes;
        $changes = is_array($changesRaw) ? $changesRaw : json_decode((string) $changesRaw, true);
        $this->assertIsArray($changes);
        $this->assertArrayNotHasKey('password', $changes);
        $this->assertArrayNotHasKey('new_password', $changes);
        $this->assertArrayNotHasKey('current_password', $changes);
    }

    public function test_change_password_fail_with_wrong_current_password_keeps_old_password_and_no_log_added(): void
    {
        $memberId = $this->createMember('password-fail@example.com', '錯誤密碼會員', 'OldPass123');
        $beforeLogCount = DB::table('member_operation_logs')->count();

        $response = $this->withSession([
            MEMBER_AUTH_SESSION => [
                'id' => $memberId,
                'email' => 'password-fail@example.com',
                'name' => '錯誤密碼會員',
                'avatar_url' => null,
            ],
        ])->from('/member/profile')->post('/member/profile/password', [
            'current_password' => 'wrong-password',
            'new_password' => 'NewPass123',
            'new_password_confirmation' => 'NewPass123',
        ]);

        $response->assertRedirect('/member/profile');
        $response->assertSessionHasErrorsIn('change_password', ['current_password', 'change_password']);

        $member = DB::table('member')->where('id', $memberId)->first();
        $this->assertTrue(Hash::check('OldPass123', (string) $member->password));
        $this->assertSame($beforeLogCount, DB::table('member_operation_logs')->count());
    }

    public function test_profile_shows_last_login_at_when_login_log_exists(): void
    {
        $memberId = $this->createMember('last-login-exists@example.com', '有登入紀錄會員');

        # 寫入兩筆 LOGIN+SUCCESS 紀錄；profile 應顯示最新一筆
        $earlier = '2026-04-25 10:00:00';
        $latest = '2026-04-25 14:30:45';

        DB::table('member_login_logs')->insert([
            [
                'member_id' => $memberId,
                'account' => 'last-login-exists@example.com',
                'member_name' => '有登入紀錄會員',
                'action' => MEMBER_LOGIN_LOG_ACTION_LOGIN,
                'status' => MEMBER_LOGIN_LOG_STATUS_SUCCESS,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'phpunit',
                'operated_at' => $earlier,
                'created_at' => $earlier,
                'updated_at' => $earlier,
            ],
            [
                'member_id' => $memberId,
                'account' => 'last-login-exists@example.com',
                'member_name' => '有登入紀錄會員',
                'action' => MEMBER_LOGIN_LOG_ACTION_LOGIN,
                'status' => MEMBER_LOGIN_LOG_STATUS_SUCCESS,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'phpunit',
                'operated_at' => $latest,
                'created_at' => $latest,
                'updated_at' => $latest,
            ],
        ]);

        $response = $this->withSession([
            MEMBER_AUTH_SESSION => [
                'id' => $memberId,
                'email' => 'last-login-exists@example.com',
                'name' => '有登入紀錄會員',
                'avatar_url' => null,
            ],
        ])->get('/member/profile');

        $response->assertOk();
        $response->assertSee('最後登入時間');
        $response->assertSee($latest);
        # 不應顯示「尚未有登入紀錄」與較早那筆
        $response->assertDontSee('尚未有登入紀錄');
        $response->assertDontSee($earlier);
    }

    public function test_profile_shows_no_login_record_message_when_no_success_login_log(): void
    {
        $memberId = $this->createMember('no-login@example.com', '無登入紀錄會員');

        # 寫入一筆 LOGIN+FAIL 與一筆 REGISTER+SUCCESS；都不應被視為「最後登入」
        DB::table('member_login_logs')->insert([
            [
                'member_id' => null,
                'account' => 'no-login@example.com',
                'member_name' => null,
                'action' => MEMBER_LOGIN_LOG_ACTION_LOGIN,
                'status' => MEMBER_LOGIN_LOG_STATUS_FAIL,
                'fail_reason' => '密碼錯誤',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'phpunit',
                'operated_at' => '2026-04-25 09:00:00',
                'created_at' => '2026-04-25 09:00:00',
                'updated_at' => '2026-04-25 09:00:00',
            ],
            [
                'member_id' => $memberId,
                'account' => 'no-login@example.com',
                'member_name' => '無登入紀錄會員',
                'action' => MEMBER_LOGIN_LOG_ACTION_REGISTER,
                'status' => MEMBER_LOGIN_LOG_STATUS_SUCCESS,
                'fail_reason' => null,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'phpunit',
                'operated_at' => '2026-04-25 09:30:00',
                'created_at' => '2026-04-25 09:30:00',
                'updated_at' => '2026-04-25 09:30:00',
            ],
        ]);

        $response = $this->withSession([
            MEMBER_AUTH_SESSION => [
                'id' => $memberId,
                'email' => 'no-login@example.com',
                'name' => '無登入紀錄會員',
                'avatar_url' => null,
            ],
        ])->get('/member/profile');

        $response->assertOk();
        $response->assertSee('最後登入時間');
        $response->assertSee('尚未有登入紀錄');
        # 不應誤抓 FAIL 或 REGISTER 紀錄的時間
        $response->assertDontSee('2026-04-25 09:00:00');
        $response->assertDontSee('2026-04-25 09:30:00');
    }

    protected function createMember(string $email, string $name, string $password = 'Abcd1234'): int
    {
        return DB::table('member')->insertGetId([
            'email' => $email,
            'password' => Hash::make($password),
            'name' => $name,
            'gender_key' => (string) GENDER_UNSPECIFIED,
            'status_key' => MEMBER_STATUS_ACTIVE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

