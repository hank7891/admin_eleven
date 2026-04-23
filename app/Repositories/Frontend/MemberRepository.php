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
}

