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
        $response->assertSessionHas(MEMBER_MESSAGE_SESSION, function ($messages) {
            return is_array($messages)
                && collect($messages)->contains(fn ($m) => ($m['message'] ?? '') === '個人資料已更新');
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

    public function test_profile_success_flash_message_is_visible_after_redirect(): void
    {
        Storage::fake('public');
        $memberId = $this->createMember('profile-flash@example.com', '提示會員');

        $this->withSession([
            MEMBER_AUTH_SESSION => [
                'id' => $memberId,
                'email' => 'profile-flash@example.com',
                'name' => '提示會員',
                'avatar_url' => null,
            ],
        ])->followingRedirects()->post('/member/profile', [
            'name' => '提示會員新',
            'phone' => '',
            'birthday' => '',
            'gender_key' => (string) GENDER_UNSPECIFIED,
        ])->assertOk()->assertSee('個人資料已更新');
    }

    public function test_profile_update_rejects_oversized_avatar_with_chinese_error_and_flash(): void
    {
        Storage::fake('public');
        $memberId = $this->createMember('profile-oversized@example.com', '大檔會員');

        $oversized = UploadedFile::fake()->create('huge.gif', 6144, 'image/gif'); // 6MB，超過 5MB max

        $response = $this->withSession([
            MEMBER_AUTH_SESSION => [
                'id' => $memberId,
                'email' => 'profile-oversized@example.com',
                'name' => '大檔會員',
                'avatar_url' => null,
            ],
        ])->from('/member/profile')->post('/member/profile', [
            'name' => '大檔會員',
            'phone' => '',
            'birthday' => '',
            'gender_key' => (string) GENDER_UNSPECIFIED,
            'avatar' => $oversized,
        ]);

        $response->assertRedirect('/member/profile');
        $response->assertSessionHasErrors(['avatar']);
        $response->assertSessionHas(MEMBER_MESSAGE_SESSION, function ($messages) {
            return is_array($messages)
                && collect($messages)->contains(function ($m) {
                    return ($m['type'] ?? '') === 'danger'
                        && str_contains((string) ($m['message'] ?? ''), '大頭照')
                        && str_contains((string) ($m['message'] ?? ''), 'MB');
                });
        });

        $member = DB::table('member')->where('id', $memberId)->first();
        $this->assertEmpty($member->avatar_path);
    }

    public function test_profile_oversized_avatar_message_is_visible_after_redirect(): void
    {
        Storage::fake('public');
        $memberId = $this->createMember('profile-oversized-flash@example.com', '大檔提示會員');

        $oversized = UploadedFile::fake()->create('huge.gif', 6144, 'image/gif');

        $this->withSession([
            MEMBER_AUTH_SESSION => [
                'id' => $memberId,
                'email' => 'profile-oversized-flash@example.com',
                'name' => '大檔提示會員',
                'avatar_url' => null,
            ],
        ])->followingRedirects()->post('/member/profile', [
            'name' => '大檔提示會員',
            'phone' => '',
            'birthday' => '',
            'gender_key' => (string) GENDER_UNSPECIFIED,
            'avatar' => $oversized,
        ])->assertOk()->assertSee('大頭照檔案大小超過');
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


