<?php

namespace App\Repositories\Frontend;

use App\Models\Member;

class MemberRepository
{
    # 建構元
    public function __construct(protected Member $model)
    {
    }

    /**
     * 依 Email 查詢會員
     */
    public function findByEmail(string $email): ?Member
    {
        return $this->model::where('email', strtolower(trim($email)))->first();
    }

    /**
     * 依 ID 查詢會員
     */
    public function findById(int $memberId): ?Member
    {
        return $this->model::find($memberId);
    }

    /**
     * 建立會員
     */
    public function create(array $data): Member
    {
        $member = new $this->model;
        $member->fill($data);

        if (isset($data['password'])) {
            $member->forceFill(['password' => $data['password']]);
        }

        $member->save();

        return $member;
    }

    /**
     * 更新會員基本資料
     */
    public function updateProfile(int $memberId, array $data): ?Member
    {
        $member = $this->findById($memberId);

        if (empty($member)) {
            return null;
        }

        $member->fill($data);
        $member->save();

        return $member->fresh();
    }

    /**
     * 更新會員密碼
     */
    public function updatePassword(int $memberId, string $password): bool
    {
        $member = $this->findById($memberId);

        if (empty($member)) {
            return false;
        }

        $member->forceFill([
            'password' => $password,
        ]);
        $member->save();

        return true;
    }

    /**
     * 更新最後登入資訊
     */
    public function touchLoginStamp(int $memberId, ?string $ipAddress = null): bool
    {
        $member = $this->findById($memberId);

        if (empty($member)) {
            return false;
        }

        $member->fill([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
        ]);
        $member->save();

        return true;
    }
}

