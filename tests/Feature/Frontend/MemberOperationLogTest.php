<?php

namespace Tests\Feature\Frontend;

use App\Services\Frontend\MemberLogService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MemberOperationLogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_record_update_with_empty_diff_does_not_create_log(): void
    {
        $service = app(MemberLogService::class);
        $this->startSession();
        session([
            MEMBER_AUTH_SESSION => [
                'id' => 5001,
                'name' => '測試會員',
                'email' => 'test@example.com',
            ],
        ]);

        $beforeCount = DB::table('member_operation_logs')->count();

        $request = Request::create('/member/profile', 'POST');
        $result = $service->recordUpdate(
            $request,
            'member_profile',
            5001,
            '測試會員',
            ['name' => '測試會員'],
            ['name' => '測試會員'],
            ['name']
        );

        $this->assertNull($result);
        $this->assertSame($beforeCount, DB::table('member_operation_logs')->count());
    }

    public function test_record_filters_sensitive_fields_and_uses_member_config(): void
    {
        config([
            'admin_log.sensitive_fields' => [],
            'member_log.sensitive_fields' => ['password', 'current_password', 'new_password', 'token', 'secret'],
        ]);

        $service = app(MemberLogService::class);
        $this->startSession();
        session([
            MEMBER_AUTH_SESSION => [
                'id' => 5002,
                'name' => '敏感欄位會員',
                'email' => 'sensitive@example.com',
            ],
        ]);

        $request = Request::create('/member/profile', 'POST', [
            'member_id' => 9999,
        ]);

        $service->record(
            $request,
            'member_profile',
            'update',
            5002,
            '敏感欄位會員',
            [
                'name' => ['old' => 'A', 'new' => 'B'],
                'password' => ['old' => 'x', 'new' => 'y'],
                'current_password' => ['old' => 'x', 'new' => 'y'],
                'new_password' => ['old' => 'x', 'new' => 'y'],
                'token' => ['old' => 'x', 'new' => 'y'],
                'secret' => ['old' => 'x', 'new' => 'y'],
            ]
        );

        $log = DB::table('member_operation_logs')->orderByDesc('id')->first();
        $this->assertSame(5002, (int) $log->member_id);

        $changes = json_decode((string) $log->changes, true);
        $this->assertArrayHasKey('name', $changes);
        $this->assertArrayNotHasKey('password', $changes);
        $this->assertArrayNotHasKey('current_password', $changes);
        $this->assertArrayNotHasKey('new_password', $changes);
        $this->assertArrayNotHasKey('token', $changes);
        $this->assertArrayNotHasKey('secret', $changes);
    }
}
