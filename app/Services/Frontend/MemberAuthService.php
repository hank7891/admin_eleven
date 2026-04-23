<?php

namespace App\Services\Frontend;

use App\Models\Member;
use App\Repositories\Frontend\MemberRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberAuthService
{
    # 建構元
    public function __construct(protected MemberRepository $repository)
    {
    }

    /**
     * 會員註冊
     */
    public function register(array $data): array
    {
        $payload = [
            'email' => strtolower(trim(strip_tags((string) ($data['email'] ?? '')))),
            'name' => mb_substr(trim(strip_tags((string) ($data['name'] ?? ''))), 0, 100),
            'password' => Hash::make((string) ($data['password'] ?? '')),
            'status_key' => MEMBER_STATUS_ACTIVE,
            'gender_key' => (string) GENDER_UNSPECIFIED,
            'registered_ip' => request()->ip(),
        ];

        DB::beginTransaction();
        try {
            $member = $this->repository->create($payload);
            DB::commit();

            return [
                'id' => (int) $member->id,
                'email' => (string) $member->email,
                'name' => (string) $member->name,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * 依 email 查詢會員
     */
    public function findByEmail(string $email): ?Member
    {
        return $this->repository->findByEmail($email);
    }

    /**
     * 寫入會員登入 session
     */
    public function login(Member $member): void
    {
        session([
            MEMBER_AUTH_SESSION => [
                'id' => (int) $member->id,
                'email' => (string) $member->email,
                'name' => (string) $member->name,
            ],
        ]);
    }

    /**
     * 登出
     */
    public function logout(): void
    {
        session()->forget(MEMBER_AUTH_SESSION);
        session()->forget(MEMBER_MESSAGE_SESSION);
    }

    /**
     * 是否已登入
     */
    public function isLoggedIn(): bool
    {
        return !empty(session(MEMBER_AUTH_SESSION));
    }

    /**
     * 取得目前登入會員
     */
    public function currentMember(): ?array
    {
        $member = session(MEMBER_AUTH_SESSION);

        return is_array($member) ? $member : null;
    }
}


