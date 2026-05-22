<?php

namespace App\Services\Admin;

use App\Repositories\Admin\MemberRepository;
use App\Services\Share\FileUploadService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberService
{
    # 建構元
    public function __construct(
        protected MemberRepository $repository,
        protected FileUploadService $uploadService
    ) {
    }

    /**
     * 後台列表資料
     */
    public function fetchPaginatedData(array $filters = [], int $perPage = 20): array
    {
        $normalizedFilters = $this->normalizeFilters($filters);
        $paginator = $this->repository->fetchPaginatedData($normalizedFilters, $perPage);

        $data = [];
        foreach ($paginator->items() as $member) {
            $row = $member->toArray();
            $row['gender_display'] = $member->gender_display;
            $row['status_display'] = $member->status_display;
            $row['last_login_at'] = !empty($row['last_login_at'])
                ? Carbon::parse($row['last_login_at'])->format('Y-m-d H:i')
                : '尚未登入';
            $row['created_at_display'] = !empty($row['created_at'])
                ? Carbon::parse($row['created_at'])->format('Y-m-d H:i')
                : '';

            $data[] = $row;
        }

        return [
            'data' => $data,
            'pagination' => $paginator,
            'filters' => $normalizedFilters,
        ];
    }

    /**
     * 編輯頁資料
     */
    public function getForEdit(int $id): array
    {
        if ($id === 0) {
            return [
                'id' => 0,
                'email' => '',
                'name' => '',
                'phone' => '',
                'birthday' => '',
                'gender_key' => (string) GENDER_UNSPECIFIED,
                'status_key' => MEMBER_STATUS_ACTIVE,
                'avatar_path' => null,
                'avatar_url' => '',
                'registered_ip' => '',
                'last_login_at' => '',
                'last_login_ip' => '',
                'email_verified_at' => '',
                'created_at_display' => '',
            ];
        }

        $member = $this->repository->findById($id);

        if (empty($member)) {
            throw new \Exception('查無此會員資料！ #002');
        }

        return [
            'id' => (int) $member->id,
            'email' => (string) $member->email,
            'name' => (string) $member->name,
            'phone' => (string) ($member->phone ?? ''),
            'birthday' => !empty($member->birthday) ? Carbon::parse($member->birthday)->format('Y-m-d') : '',
            'gender_key' => (string) ($member->gender_key ?? GENDER_UNSPECIFIED),
            'status_key' => (string) ($member->status_key ?? MEMBER_STATUS_ACTIVE),
            'avatar_path' => $member->avatar_path,
            'avatar_url' => (string) ($member->avatar_url ?? ''),
            'registered_ip' => (string) ($member->registered_ip ?? ''),
            'last_login_at' => !empty($member->last_login_at) ? Carbon::parse($member->last_login_at)->format('Y-m-d H:i:s') : '',
            'last_login_ip' => (string) ($member->last_login_ip ?? ''),
            'email_verified_at' => !empty($member->email_verified_at) ? Carbon::parse($member->email_verified_at)->format('Y-m-d H:i:s') : '',
            'created_at_display' => !empty($member->created_at) ? Carbon::parse($member->created_at)->format('Y-m-d H:i:s') : '',
        ];
    }

    /**
     * 新增會員
     */
    public function addData(array $data, ?UploadedFile $avatar = null): array
    {
        $payload = $this->normalizePayload($data, false);

        # 密碼需 hash 後儲存
        $payload['password'] = Hash::make((string) ($data['password'] ?? ''));
        $payload['registered_ip'] = request()->ip();

        $newAvatarPath = null;

        DB::beginTransaction();
        try {
            if (!empty($avatar)) {
                $newAvatarPath = $this->uploadService->upload($avatar, 'image');
                $payload['avatar_path'] = $newAvatarPath;
            }

            $member = $this->repository->create($payload);

            DB::commit();

            return $member->toArray();
        } catch (\Exception $e) {
            DB::rollBack();

            if (!empty($newAvatarPath)) {
                $this->uploadService->delete($newAvatarPath);
            }

            throw $e;
        }
    }

    /**
     * 更新會員
     */
    public function updateData(int $id, array $data, ?UploadedFile $avatar = null): array
    {
        $member = $this->repository->findById($id);

        if (empty($member)) {
            throw new \Exception('查無此會員資料！ #003');
        }

        $payload = $this->normalizePayload($data, true);

        $newAvatarPath = null;
        $oldAvatarPathToDelete = null;

        DB::beginTransaction();
        try {
            if (!empty($avatar)) {
                $newAvatarPath = $this->uploadService->upload($avatar, 'image');
                $payload['avatar_path'] = $newAvatarPath;

                if (!empty($member->avatar_path) && $member->avatar_path !== $newAvatarPath) {
                    $oldAvatarPathToDelete = (string) $member->avatar_path;
                }
            }

            $updated = $this->repository->update($id, $payload);

            DB::commit();

            if (!empty($oldAvatarPathToDelete)) {
                $this->uploadService->delete($oldAvatarPathToDelete);
            }

            return $updated->toArray();
        } catch (\Exception $e) {
            DB::rollBack();

            if (!empty($newAvatarPath)) {
                $this->uploadService->delete($newAvatarPath);
            }

            throw $e;
        }
    }

    /**
     * 重設密碼
     */
    public function resetPassword(int $id): array
    {
        $member = $this->repository->findById($id);

        if (empty($member)) {
            throw new \Exception('查無此會員資料！ #004');
        }

        $plainPassword = Str::password(10, true, true, false, false);
        $this->repository->update($id, ['password' => Hash::make($plainPassword)]);

        return [
            'id' => (int) $member->id,
            'name' => (string) $member->name,
            'password' => $plainPassword,
        ];
    }

    /**
     * 正規化搜尋條件
     */
    protected function normalizeFilters(array $filters): array
    {
        return [
            'keyword' => trim((string) ($filters['keyword'] ?? '')),
            'status_key' => (string) ($filters['status_key'] ?? ''),
            'date_from' => trim((string) ($filters['date_from'] ?? '')),
            'date_to' => trim((string) ($filters['date_to'] ?? '')),
        ];
    }

    /**
     * 正規化儲存資料
     */
    protected function normalizePayload(array $data, bool $isUpdate): array
    {
        $payload = [
            'name' => mb_substr(trim(strip_tags((string) ($data['name'] ?? ''))), 0, 100),
            'phone' => $this->nullableString($data['phone'] ?? null, 30),
            'birthday' => $this->nullableString($data['birthday'] ?? null, 10),
            'gender_key' => (string) ($data['gender_key'] ?? GENDER_UNSPECIFIED),
            'status_key' => (string) ($data['status_key'] ?? MEMBER_STATUS_ACTIVE),
        ];

        if (!$isUpdate) {
            $payload['email'] = strtolower(trim((string) ($data['email'] ?? '')));
        }

        return $payload;
    }

    /**
     * 轉換空字串為 null
     */
    protected function nullableString(?string $value, int $maxLength): ?string
    {
        $value = mb_substr(trim(strip_tags((string) $value)), 0, $maxLength);

        return $value === '' ? null : $value;
    }
}

