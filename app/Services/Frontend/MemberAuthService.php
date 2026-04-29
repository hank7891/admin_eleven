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
    /**
     * 避免帳號枚舉的固定假密碼 hash
     */
    private const DUMMY_PASSWORD_HASH = '$2y$12$VwwlYQ8m0QK/dQz2jM0u8eZAYxH/uQAWwwY/TRFhnQWvU9mFvyeOa';

    # 建構元
    public function __construct(
        protected MemberRepository $repository,
        protected FileUploadService $fileUploadService
    )
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
     * 依 ID 查詢會員
     */
    public function findById(int $memberId): ?Member
    {
        return $this->repository->findById($memberId);
    }

    /**
     * 驗證會員帳號密碼
     */
    public function attempt(string $email, string $password): ?Member
    {
        $email = strtolower(trim($email));
        $member = $this->repository->findByEmail($email);

        if (empty($member)) {
            Hash::check($password, self::DUMMY_PASSWORD_HASH);

            return null;
        }

        if ((string) $member->status_key !== (string) MEMBER_STATUS_ACTIVE) {
            Hash::check($password, (string) $member->password);

            return null;
        }

        if (!Hash::check($password, (string) $member->password)) {
            return null;
        }

        return $member;
    }

    /**
     * 寫入會員登入 session
     */
    public function login(Member $member): void
    {
        session()->regenerate();
        $this->repository->touchLoginStamp((int) $member->id, request()->ip());

        session([
            MEMBER_AUTH_SESSION => [
                'id' => (int) $member->id,
                'email' => (string) $member->email,
                'name' => (string) $member->name,
                'avatar_url' => $member->avatar_url,
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

    /**
     * 更新會員基本資料
     */
    public function updateProfile(int $memberId, array $data, ?UploadedFile $avatar = null): array
    {
        $member = $this->repository->findById($memberId);

        if (empty($member)) {
            throw new \Exception('會員資料不存在');
        }

        $genderOptions = array_map('strval', array_keys(config('constants.gender', [])));
        $genderKey = (string) ($data['gender_key'] ?? GENDER_UNSPECIFIED);

        if (!in_array($genderKey, $genderOptions, true)) {
            throw new \InvalidArgumentException('性別格式錯誤');
        }

        $phone = preg_replace('/[^0-9]/', '', (string) ($data['phone'] ?? ''));
        $phone = $phone !== '' ? $phone : null;

        if (!empty($phone) && !preg_match('/^[0-9]{6,30}$/', $phone)) {
            throw new \InvalidArgumentException('手機格式錯誤');
        }

        $birthday = null;
        $birthdayInput = trim((string) ($data['birthday'] ?? ''));
        if ($birthdayInput !== '') {
            $birthdayDate = \DateTime::createFromFormat('Y-m-d', $birthdayInput);

            if ($birthdayDate === false || $birthdayDate->format('Y-m-d') !== $birthdayInput) {
                throw new \InvalidArgumentException('生日格式錯誤');
            }

            $birthday = $birthdayInput;
        }

        $payload = [
            'name' => mb_substr(trim(strip_tags((string) ($data['name'] ?? ''))), 0, 100),
            'phone' => $phone,
            'birthday' => $birthday,
            'gender_key' => $genderKey,
        ];

        $newAvatarPath = null;
        $oldAvatarPath = (string) ($member->avatar_path ?? '');

        DB::beginTransaction();
        try {
            if (!empty($avatar)) {
                $newAvatarPath = $this->fileUploadService->upload($avatar, 'member_avatar');
                $payload['avatar_path'] = $newAvatarPath;
            }

            $updatedMember = $this->repository->updateProfile($memberId, $payload);

            if (empty($updatedMember)) {
                throw new \Exception('會員資料更新失敗');
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            if (!empty($newAvatarPath)) {
                $this->fileUploadService->delete($newAvatarPath);
            }

            throw $e;
        }

        if (!empty($newAvatarPath) && !empty($oldAvatarPath) && str_starts_with($oldAvatarPath, 'uploads/member_avatar/')) {
            try {
                $this->fileUploadService->delete($oldAvatarPath);
            } catch (\Exception $e) {
                report($e);
            }
        }

        return [
            'id' => (int) $updatedMember->id,
            'email' => (string) $updatedMember->email,
            'name' => (string) $updatedMember->name,
            'phone' => (string) ($updatedMember->phone ?? ''),
            'birthday' => !empty($updatedMember->birthday) ? $updatedMember->birthday->format('Y-m-d') : null,
            'gender_key' => (string) $updatedMember->gender_key,
            'avatar_path' => (string) ($updatedMember->avatar_path ?? ''),
            'avatar_url' => $updatedMember->avatar_url,
        ];
    }

    /**
     * 變更會員密碼
     */
    public function changePassword(int $memberId, string $currentPassword, string $newPassword): void
    {
        $member = $this->repository->findById($memberId);

        if (empty($member)) {
            throw new \Exception('會員資料不存在');
        }

        if (!Hash::check($currentPassword, (string) $member->password)) {
            throw new \InvalidArgumentException('目前密碼輸入錯誤');
        }

        $this->repository->updatePassword($memberId, Hash::make($newPassword));
    }
}


