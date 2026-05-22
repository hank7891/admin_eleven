<?php

namespace App\Repositories\Admin;

use App\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MemberRepository
{
    # 建構元
    public function __construct(protected Member $model)
    {
    }

    /**
     * 取得後台分頁資料
     */
    public function fetchPaginatedData(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model::query();

        # 關鍵字（姓名/Email）
        if (!empty($filters['keyword'])) {
            $keyword = trim((string) $filters['keyword']);
            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            });
        }

        # 狀態篩選
        if (!empty($filters['status_key'])) {
            $query->where('status_key', (string) $filters['status_key']);
        }

        # 註冊日期區間篩選
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * 依 ID 取得資料
     */
    public function findById(int $id): ?Member
    {
        return $this->model::find($id);
    }

    /**
     * 新增會員
     */
    public function create(array $data): Member
    {
        $member = new $this->model;
        $member->fill($data);

        # password 不在 fillable，需在 save 前 forceFill
        if (isset($data['password'])) {
            $member->forceFill(['password' => $data['password']]);
        }

        $member->save();

        return $member;
    }

    /**
     * 更新會員
     */
    public function update(int $id, array $data): Member
    {
        $member = $this->model::find($id);

        if (empty($member)) {
            throw new \Exception('查無此會員資料！ #001');
        }

        $payload = $data;

        if (isset($payload['password'])) {
            unset($payload['password']);
        }

        if (!empty($payload)) {
            $member->update($payload);
        }

        if (isset($data['password'])) {
            $member->forceFill(['password' => $data['password']])->save();
        }

        return $member->fresh();
    }

    /**
     * 計算指定年月的新註冊會員數（Dashboard KPI 用）
     */
    public function countNewInMonth(int $year, int $month): int
    {
        return $this->model::query()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
    }

    /**
     * 全表會員總數（Dashboard 系統概況用）
     */
    public function countTotal(): int
    {
        return $this->model::query()->count();
    }

    /**
     * 啟用中會員數（Dashboard 系統概況用）
     */
    public function countActive(): int
    {
        return $this->model::query()
            ->where('status_key', MEMBER_STATUS_ACTIVE)
            ->count();
    }
}

