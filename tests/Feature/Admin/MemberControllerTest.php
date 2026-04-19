<?php

namespace Tests\Feature\Admin;

use App\Services\Admin\AdminLogService;
use App\Services\Admin\MemberService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Mockery\MockInterface;
use Tests\TestCase;

class MemberControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_member_list_page_renders_successfully(): void
    {
        $this->mock(MemberService::class, function (MockInterface $mock) {
            $mock->shouldReceive('fetchPaginatedData')
                ->once()
                ->andReturn([
                    'data' => [
                        [
                            'id' => 1,
                            'email' => 'member@example.com',
                            'name' => '會員測試',
                            'phone' => '0912345678',
                            'gender_display' => '男',
                            'status_display' => '正常',
                            'status_key' => MEMBER_STATUS_ACTIVE,
                            'last_login_at' => '尚未登入',
                            'created_at_display' => '2026-04-17 00:00',
                        ],
                    ],
                    'pagination' => new \Illuminate\Pagination\LengthAwarePaginator([['id' => 1]], 1, 20),
                    'filters' => [],
                ]);
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/member/list'],
            ])
            ->get('/admin/member/list');

        $response->assertOk();
        $response->assertSee('會員管理');
        $response->assertSee('member@example.com');
    }

    public function test_member_create_validates_required_fields(): void
    {
        $this->mock(MemberService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('addData');
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/member/list'],
            ])
            ->post('/admin/member/edit', [
                'id' => 0,
                'email' => '',
                'name' => '',
            ]);

        $response->assertRedirect('/admin/member/edit/0');
    }

    public function test_member_create_validation_failure_keeps_create_mode_in_session_without_password(): void
    {
        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/member/list'],
            ])
            ->post('/admin/member/edit', [
                'id' => 0,
                'email' => 'foo@example.com',
                'name' => '',
                'password' => '12345678',
                'password_confirmation' => '12345678',
                'status_key' => MEMBER_STATUS_ACTIVE,
            ]);

        $response->assertRedirect('/admin/member/edit/0');
        $response->assertSessionHas('member_edit_post', function ($post) {
            return (string) ($post['id'] ?? '') === '0'
                && !array_key_exists('password', $post)
                && !array_key_exists('password_confirmation', $post);
        });
    }

    public function test_member_create_records_admin_log(): void
    {
        $this->mock(MemberService::class, function (MockInterface $mock) {
            $mock->shouldReceive('addData')
                ->once()
                ->andReturn([
                    'id' => 33,
                    'name' => '新會員',
                ]);
        });

        $this->mock(AdminLogService::class, function (MockInterface $mock) {
            $mock->shouldReceive('recordSimple')->once();
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/member/list'],
            ])
            ->post('/admin/member/edit', [
                'id' => 0,
                'email' => 'newmember@example.com',
                'name' => '新會員',
                'password' => '12345678',
                'password_confirmation' => '12345678',
                'status_key' => MEMBER_STATUS_ACTIVE,
            ]);

        $response->assertRedirect('/admin/member/edit/33');
    }

    public function test_member_update_records_change_log(): void
    {
        $this->mock(MemberService::class, function (MockInterface $mock) {
            $mock->shouldReceive('getForEdit')
                ->once()
                ->with(8)
                ->andReturn([
                    'id' => 8,
                    'name' => '舊名稱',
                    'status_key' => MEMBER_STATUS_ACTIVE,
                ]);

            $mock->shouldReceive('updateData')
                ->once()
                ->withArgs(function (int $id, array $data) {
                    return $id === 8 && ($data['name'] ?? null) === '新名稱';
                })
                ->andReturn([
                    'id' => 8,
                    'name' => '新名稱',
                    'status_key' => MEMBER_STATUS_INACTIVE,
                ]);
        });

        $this->mock(AdminLogService::class, function (MockInterface $mock) {
            $mock->shouldReceive('recordUpdate')->once();
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/member/list'],
            ])
            ->post('/admin/member/edit', [
                'id' => 8,
                'name' => '新名稱',
                'status_key' => MEMBER_STATUS_INACTIVE,
                'gender_key' => (string) GENDER_UNSPECIFIED,
            ]);

        $response->assertRedirect('/admin/member/edit/8');
    }

    public function test_member_reset_password_shows_plaintext_once_message(): void
    {
        $this->mock(MemberService::class, function (MockInterface $mock) {
            $mock->shouldReceive('resetPassword')
                ->once()
                ->with(9)
                ->andReturn([
                    'id' => 9,
                    'name' => '會員 A',
                    'password' => 'Abc12345',
                ]);
        });

        $this->mock(AdminLogService::class, function (MockInterface $mock) {
            $mock->shouldReceive('recordSimple')->once();
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/member/list'],
            ])
            ->post('/admin/member/resetPassword/9');

        $response->assertRedirect('/admin/member/edit/9');
        $response->assertSessionHas(ADMIN_MESSAGE_SESSION);
    }

    public function test_member_list_redirects_to_login_when_not_logged_in(): void
    {
        $response = $this->get('/admin/member/list');

        $response->assertRedirect('/admin/login');
    }

    public function test_member_list_rejects_when_no_permission(): void
    {
        $this->mock(MemberService::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('fetchPaginatedData');
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [],
                'admin_allowed_urls' => [],
            ])
            ->get('/admin/member/list');

        $response->assertStatus(302);
        $this->assertNotEquals('/admin/member/list', $response->headers->get('Location'));
    }

    public function test_member_create_persists_member_with_hashed_password_using_real_service(): void
    {
        $this->mock(AdminLogService::class, function (MockInterface $mock) {
            $mock->shouldReceive('recordSimple')->once();
        });

        $response = $this
            ->withSession([
                ADMIN_AUTH_SESSION => ['id' => 1, 'name' => 'Tester'],
                ADMIN_PERMISSION_SESSION => [1],
                'admin_allowed_urls' => ['/admin/member/list'],
            ])
            ->post('/admin/member/edit', [
                'id' => 0,
                'email' => 'NewMember@Example.com',
                'name' => '整合測試會員',
                'password' => 'Abcd1234',
                'password_confirmation' => 'Abcd1234',
                'status_key' => MEMBER_STATUS_ACTIVE,
                'gender_key' => (string) GENDER_UNSPECIFIED,
            ]);

        $response->assertStatus(302);

        $member = DB::table('member')->where('email', 'newmember@example.com')->first();

        $this->assertNotNull($member);
        $this->assertTrue(Hash::check('Abcd1234', (string) $member->password));
    }
}

