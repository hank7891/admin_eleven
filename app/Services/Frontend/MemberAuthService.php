<?php

namespace App\Services\Frontend;

use App\Models\Member;
use App\Repositories\Frontend\MemberRepository;
use App\Services\Share\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberAuthService
{
    # 建構元
    public function __construct(
        protected MemberRepository $repository,
        protected FileUploadService $fileUploadService
    ) {
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
     * 依 ID 查詢會員
     */
    public function findById(int $id): ?Member
    {
        return $this->repository->findById($id);
    }

    /**
     * 登入驗證
     */
    public function attempt(string $email, string $password): ?Member
    {
        $email = strtolower(trim($email));
        $member = $this->repository->findByEmail($email);

        # email 不存在時仍做一次 hash 檢查，降低時間差
        if (empty($member)) {
            Hash::check((string) $password, Hash::make('member-auth-dummy-password'));

            return null;
        }

        if (!Hash::check((string) $password, (string) $member->password)) {
            return null;
        }

        if ((string) $member->status_key !== MEMBER_STATUS_ACTIVE) {
            return null;
        }

        return $member;
    }

    /**
     * 寫入會員登入 session
     */
    public function login(Member $member): void
    {
        # 先重生 session id，避免 session fixation
        session()->regenerate();

        session([
            MEMBER_AUTH_SESSION => [
                'id' => (int) $member->id,
                'email' => (string) $member->email,
                'name' => (string) $member->name,
                'avatar_url' => $member->avatar_url,
            ],
        ]);

        $this->repository->touchLoginStamp((int) $member->id, request()->ip() ?? '0.0.0.0');
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

    /**
     * 更新會員基本資料
     */
    public function updateProfile(int $memberId, array $data, ?UploadedFile $avatar = null): array
    {
        $member = $this->repository->findById($memberId);

        if (empty($member)) {
            throw new \Exception('會員資料不存在');
        }

        $name = mb_substr(trim(strip_tags((string) ($data['name'] ?? ''))), 0, 100);
        $phoneRaw = trim(strip_tags((string) ($data['phone'] ?? '')));
        $birthdayRaw = trim((string) ($data['birthday'] ?? ''));
        $genderKey = (string) ($data['gender_key'] ?? $member->gender_key);

        $phone = $phoneRaw;
        if ($phoneRaw !== '') {
            if (!preg_match('/^[0-9+()\\-\s]{6,30}$/', $phoneRaw)) {
                throw new \InvalidArgumentException('手機格式錯誤');
            }

            $phone = preg_replace('/\s+/', '', $phoneRaw) ?? $phoneRaw;
        }

        $allowedGenderKeys = array_map('strval', array_keys(config('constants.gender', [])));
        if (!in_array($genderKey, $allowedGenderKeys, true)) {
            throw new \InvalidArgumentException('性別格式錯誤');
        }

        $birthday = null;
        if ($birthdayRaw !== '') {
            $birthdayDate = \DateTime::createFromFormat('Y-m-d', $birthdayRaw);
            if (!$birthdayDate || $birthdayDate->format('Y-m-d') !== $birthdayRaw) {
                throw new \InvalidArgumentException('生日格式錯誤');
            }

            $birthday = $birthdayRaw;
        }

        $oldAvatarPath = (string) ($member->avatar_path ?? '');
        $newAvatarPath = null;

        DB::beginTransaction();
        try {
            if (!empty($avatar)) {
                $newAvatarPath = $this->fileUploadService->upload($avatar, 'member_avatar');
            }

            $updatedMember = $this->repository->updateProfile($memberId, [
                'name' => $name,
                'phone' => $phone !== '' ? $phone : null,
                'birthday' => $birthday,
                'gender_key' => $genderKey,
                'avatar_path' => $newAvatarPath ?? $oldAvatarPath,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            if (!empty($newAvatarPath)) {
                $this->fileUploadService->delete($newAvatarPath);
            }

            throw $e;
        }

        # 成功後再刪舊檔，避免更新失敗造成檔案遺失
        if (!empty($newAvatarPath) && str_starts_with($oldAvatarPath, 'uploads/member_avatar/')) {
            $this->fileUploadService->delete($oldAvatarPath);
        }

        $updatedMember = $updatedMember->fresh();

        return [
            'id' => (int) $updatedMember->id,
            'email' => (string) $updatedMember->email,
            'name' => (string) $updatedMember->name,
            'phone' => (string) ($updatedMember->phone ?? ''),
            'birthday' => !empty($updatedMember->birthday)
                ? $updatedMember->birthday->format('Y-m-d')
                : null,
            'gender_key' => (string) $updatedMember->gender_key,
            'avatar_path' => (string) ($updatedMember->avatar_path ?? ''),
            'avatar_url' => $updatedMember->avatar_url,
        ];
    }

    /**
     * 變更會員密碼
     */
    public function changePassword(int $memberId, string $currentPassword, string $newPassword): bool
    {
        $member = $this->repository->findById($memberId);

        if (empty($member)) {
            throw new \Exception('會員資料不存在');
        }

        if (!Hash::check($currentPassword, (string) $member->password)) {
            throw new \InvalidArgumentException('目前密碼錯誤');
        }

        $this->repository->updatePassword($memberId, Hash::make($newPassword));

        return true;
    }
}


