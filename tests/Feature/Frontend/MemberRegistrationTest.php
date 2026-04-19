<?php

namespace Tests\Feature\Frontend;

use App\Services\Frontend\AnnouncementService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mockery\MockInterface;
use Tests\TestCase;

class MemberRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_register_page_renders_successfully(): void
    {
        $this->mock(AnnouncementService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchSystemAnnouncement')->once()->andReturn(null);
        });

        $response = $this->get('/member/register');

        $response->assertOk();
        $response->assertSee('加入會員');
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('href="' . url('member/login') . '"', false);
    }

    public function test_register_success_creates_member_and_auto_login(): void
    {
        $beforeLoginLogCount = DB::table('member_login_logs')->count();
        $beforeOperationLogCount = DB::table('member_operation_logs')->count();

        $response = $this->post('/member/register', [
            'email' => 'NewMember@Example.com',
            'name' => '前台會員',
            'password' => 'Abcd1234',
            'password_confirmation' => 'Abcd1234',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHas(MEMBER_AUTH_SESSION);
        $response->assertSessionHas(MEMBER_MESSAGE_SESSION);

        $member = DB::table('member')->where('email', 'newmember@example.com')->first();
        $this->assertNotNull($member);
        $this->assertSame('前台會員', $member->name);
        $this->assertSame(MEMBER_STATUS_ACTIVE, $member->status_key);
        $this->assertTrue(Hash::check('Abcd1234', (string) $member->password));

        $this->assertSame($beforeLoginLogCount + 1, DB::table('member_login_logs')->count());
        $this->assertSame($beforeOperationLogCount + 1, DB::table('member_operation_logs')->count());

        $latestLoginLog = DB::table('member_login_logs')->orderByDesc('id')->first();
        $this->assertSame(MEMBER_LOGIN_LOG_ACTION_REGISTER, $latestLoginLog->action);
        $this->assertSame(MEMBER_LOGIN_LOG_STATUS_SUCCESS, (int) $latestLoginLog->status);

        $latestOperationLog = DB::table('member_operation_logs')->orderByDesc('id')->first();
        $this->assertSame('member_profile', $latestOperationLog->module);
        $this->assertSame('create', $latestOperationLog->action);
    }

    public function test_register_validation_failure_redirects_back_without_password_in_session(): void
    {
        $response = $this->from('/member/register')->post('/member/register', [
            'email' => 'bad-format',
            'name' => '',
            'password' => '12345678',
            'password_confirmation' => '87654321',
        ]);

        $response->assertRedirect('/member/register');
        $response->assertSessionHasErrors();
        $response->assertSessionHas('member_register_post', function ($post) {
            return !array_key_exists('password', $post)
                && !array_key_exists('password_confirmation', $post)
                && ($post['email'] ?? '') === 'bad-format';
        });
    }

    public function test_register_fails_when_email_is_duplicate(): void
    {
        DB::table('member')->insert([
            'email' => 'duplicate@example.com',
            'password' => Hash::make('Abcd1234'),
            'name' => '原會員',
            'gender_key' => (string) GENDER_UNSPECIFIED,
            'status_key' => MEMBER_STATUS_ACTIVE,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->from('/member/register')->post('/member/register', [
            'email' => 'duplicate@example.com',
            'name' => '新會員',
            'password' => 'Abcd1234',
            'password_confirmation' => 'Abcd1234',
        ]);

        $response->assertRedirect('/member/register');
        $response->assertSessionHasErrors();
    }

    public function test_logged_in_member_cannot_access_register_page(): void
    {
        $response = $this
            ->withSession([
                MEMBER_AUTH_SESSION => [
                    'id' => 999,
                    'email' => 'member@example.com',
                    'name' => '已登入會員',
                ],
            ])
            ->get('/member/register');

        $response->assertRedirect('/');
    }

    public function test_member_logout_clears_auth_session(): void
    {
        $response = $this
            ->withSession([
                MEMBER_AUTH_SESSION => [
                    'id' => 100,
                    'email' => 'member@example.com',
                    'name' => '測試會員',
                ],
            ])
            ->post('/member/logout');

        $response->assertRedirect('/');
        $response->assertSessionMissing(MEMBER_AUTH_SESSION);
    }

    public function test_guest_cannot_call_member_logout(): void
    {
        $response = $this->post('/member/logout');

        $response->assertRedirect('member/login');
    }
}

