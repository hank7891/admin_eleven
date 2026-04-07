<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Admin\EmployeeRepository;

class EmployeeService
{
    # 建構元
    public function __construct(protected EmployeeRepository $employeeRepository)
    {

    }

    /**
     * 取得所有會員資料
     * @return array
     */
    public function fetchAllData(): array
    {
        $data = $this->employeeRepository->fetchAllData();

        # 資料解析
        foreach ($data as &$value) {
            # 時間資料調整
            $value['created_at'] = !empty(trim($value['created_at']))
                ? Carbon::parse($value['created_at'])->format('Y-m-d')
                : '';

            $value['updated_at'] = !empty(trim($value['updated_at']))
                ? Carbon::parse($value['updated_at'])->format('Y-m-d')
                : '';

            # 生日格式化
            $value['birthday'] = !empty($value['birthday'])
                ? Carbon::parse($value['birthday'])->format('Y-m-d')
                : '';

            # 性別顯示文字
            $value['gender_display'] = config('constants.gender')[$value['gender'] ?? GENDER_UNSPECIFIED] ?? config('constants.gender.' . GENDER_UNSPECIFIED);

            # 啟用狀態顯示文字
            $value['is_active_display'] = config('constants.status')[$value['is_active'] ?? STATUS_ACTIVE] ?? config('constants.status.' . STATUS_INACTIVE);
        }

        return $data;
    }

    /**
     * 取得分頁會員資料
     * @param array $filters
     * @param int $perPage
     * @return array ['data' => array, 'pagination' => LengthAwarePaginator]
     */
    public function fetchPaginatedData(array $filters = [], int $perPage = 20): array
    {
        $paginator = $this->employeeRepository->fetchPaginatedData($filters, $perPage);

        # 資料解析
        $data = [];
        foreach ($paginator->items() as $employee) {
            $value = $employee->toArray();
            $value['role_names'] = $employee->roles->pluck('role_name')->implode(', ') ?: '--';

            $value['created_at'] = !empty(trim($value['created_at']))
                ? Carbon::parse($value['created_at'])->format('Y-m-d')
                : '';

            $value['updated_at'] = !empty(trim($value['updated_at']))
                ? Carbon::parse($value['updated_at'])->format('Y-m-d')
                : '';

            $value['birthday'] = !empty($value['birthday'])
                ? Carbon::parse($value['birthday'])->format('Y-m-d')
                : '';

            $value['gender_display'] = config('constants.gender')[$value['gender'] ?? GENDER_UNSPECIFIED] ?? config('constants.gender.' . GENDER_UNSPECIFIED);
            $value['is_active_display'] = config('constants.status')[$value['is_active'] ?? STATUS_ACTIVE] ?? config('constants.status.' . STATUS_INACTIVE);

            $data[] = $value;
        }

        return [
            'data' => $data,
            'pagination' => $paginator,
        ];
    }

    /**
     * 依照 ID 取得會員資料
     * @param int $id
     *
     * @return array
     * @throws \Exception
     */
    public function fetchDataByID(int $id): array
    {
        $data = $this->employeeRepository->fetchDataByID($id);

        if (empty($data)) {
            throw new \Exception('查無此會員資料！ #001');
        }

        return $data;
    }

    /**
     * 新增會員帳號
     *
     * @param array $data
     *
     * @return int
     * @throws \Exception
     */
    public function addData(array $data): int
    {
        # 資料正規化
        $data = $this->normalizePayload($data, false);

        # 資料新增
        $user = $this->employeeRepository->addData($data);

        # 回傳 id
        if (empty($user->id)) {
            throw new \Exception('新增會員資料失敗！ #001');
        }

        return $user->id;
    }

    /**
     * 修改資料
     * @param int   $id
     * @param array $data
     *
     * @return bool
     * @throws \Exception
     */
    public function updateData(int $id, array $data): int
    {
        # 資料正規化
        $data = $this->normalizePayload($data, true);

        # 資料更新
        $this->employeeRepository->updateData($id, $data);

        return $id;
    }

    /**
     * 同步帳號角色
     * @param int $employeeId
     * @param array $roleIds
     */
    public function syncRoles(int $employeeId, array $roleIds): void
    {
        $this->employeeRepository->syncRoles($employeeId, $roleIds);
    }

    /**
     * 正規化儲存資料
     * @param array $data
     * @param bool $isUpdate
     * @return array
     */
    protected function normalizePayload(array $data, bool $isUpdate): array
    {
        if (isset($data['account'])) {
            $data['account'] = trim((string) $data['account']);
        }

        if (isset($data['name'])) {
            $data['name'] = trim((string) $data['name']);
        }

        if (isset($data['phone'])) {
            $data['phone'] = trim((string) $data['phone']);
            $data['phone'] = $data['phone'] === '' ? null : $data['phone'];
        }

        if (isset($data['birthday']) && trim((string) $data['birthday']) === '') {
            $data['birthday'] = null;
        }

        if (isset($data['password'])) {
            if ($isUpdate && trim((string) $data['password']) === '') {
                unset($data['password']);
            } else {
                $data['password'] = Hash::make((string) $data['password']);
            }
        }

        return $data;
    }
}
