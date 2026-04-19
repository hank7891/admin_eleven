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
     * 依 ID 查詢會員
     */
    public function findById(int $id): ?Member
    {
        return $this->model::find($id);
    }

    /**
     * 更新會員基本資料
     */
    public function updateProfile(int $id, array $data): Member
    {
        $member = $this->findById($id);

        if (empty($member)) {
            throw new \Exception('會員資料不存在');
        }

        $member->fill($data);
        $member->save();

        return $member;
    }

    /**
     * 更新會員密碼
     */
    public function updatePassword(int $id, string $passwordHash): Member
    {
        $member = $this->findById($id);

        if (empty($member)) {
            throw new \Exception('會員資料不存在');
        }

        $member->forceFill(['password' => $passwordHash]);
        $member->save();

        return $member;
    }

    /**
     * 更新最後登入資訊
     */
    public function touchLoginStamp(int $id, string $ipAddress): void
    {
        $this->model::where('id', $id)->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
            'updated_at' => now(),
        ]);
    }
}

