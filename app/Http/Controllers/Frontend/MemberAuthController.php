<?php

namespace App\Http\Controllers\Frontend;

use App\Services\Frontend\AnnouncementService;
use App\Services\Frontend\MemberAuthService;
use App\Services\Frontend\MemberLoginLogService;
use App\Services\Frontend\MemberLogService;
use App\Services\Share\MessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MemberAuthController extends FrontendController
{
    const POST_SESSION = 'member_register_post';
    const LOGIN_POST_SESSION = 'member_login_post';
    const PROFILE_BASIC_POST_SESSION = 'member_profile_post_basic';
    const LOGIN_FAIL_MESSAGE = '電子信箱或密碼錯誤，或此帳號已停用';

    # 建構元
    public function __construct(
        protected AnnouncementService $announcementService,
        protected MemberAuthService $authService,
        protected MemberLoginLogService $memberLoginLogService,
        protected MemberLogService $memberLogService
    ) {
        parent::__construct($announcementService);
    }

    /**
     * 註冊頁
     */
    public function register()
    {
        $post = session(self::POST_SESSION, []);
        session()->forget(self::POST_SESSION);

        return view('Frontend.member.register', [
            'pageTitle' => '加入會員 | Aura & Heirloom',
            'navItems' => $this->buildNavItems('member'),
            'footerColumns' => $this->buildFooterColumns(),
            'alertBanner' => $this->buildAlertBanner(),
            'formData' => $post,
        ]);
    }

    /**
     * 註冊送出
     */
    public function registerDo(Request $request): RedirectResponse
    {
        $input = $request->only(['email', 'name', 'password', 'password_confirmation']);
        $input['email'] = strtolower(trim((string) ($input['email'] ?? '')));

        $validator = Validator::make($input, [
            'email' => ['required', 'email', 'max:255', Rule::unique('member', 'email')],
            'name' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ], [
            'email.required' => '電子信箱為必填欄位',
            'email.email' => '電子信箱格式錯誤',
            'email.unique' => '此電子信箱已註冊',
            'name.required' => '姓名為必填欄位',
            'password.required' => '密碼為必填欄位',
            'password.min' => '密碼至少需要 8 個字元',
            'password.confirmed' => '兩次密碼輸入不一致',
        ]);

        $sessionPost = [
            'email' => (string) ($input['email'] ?? ''),
            'name' => (string) ($input['name'] ?? ''),
        ];

        if ($validator->fails()) {
            session([self::POST_SESSION => $sessionPost]);

            return redirect('member/register')
                ->withErrors($validator)
                ->withInput($sessionPost);
        }

        try {
            $registered = $this->authService->register([
                'email' => $input['email'],
                'name' => (string) ($input['name'] ?? ''),
                'password' => (string) ($input['password'] ?? ''),
            ]);
            $member = $this->authService->findByEmail((string) ($registered['email'] ?? ''));

            if (empty($member)) {
                throw new \Exception('註冊成功但登入失敗，請稍後再試。');
            }

            $this->authService->login($member);

            # 註冊成功寫前台登入/操作日誌，失敗不得中斷主流程
            try {
                $this->memberLoginLogService->recordRegisterSuccess(
                    $request,
                    (int) $member->id,
                    (string) $member->email,
                    (string) $member->name
                );
                $this->memberLogService->recordSimple(
                    $request,
                    'member_profile',
                    'create',
                    (int) $member->id,
                    (string) $member->name,
                    '會員註冊建立資料'
                );
            } catch (\Exception $e) {
                report($e);
            }

            MessageService::setMessage(MEMBER_MESSAGE_SESSION, MessageService::SUCCESS, '註冊成功，歡迎加入！');

            return redirect('/');
        } catch (\Exception $e) {
            report($e);
            session([self::POST_SESSION => $sessionPost]);

            return redirect('member/register')
                ->withInput($sessionPost)
                ->withErrors(['register' => '註冊失敗，請稍後再試或聯絡客服。']);
        }
    }

    /**
     * 登入頁
     */
    public function login()
    {
        $post = session(self::LOGIN_POST_SESSION, []);
        session()->forget(self::LOGIN_POST_SESSION);

        return view('Frontend.member.login', [
            'pageTitle' => '會員登入 | Aura & Heirloom',
            'navItems' => $this->buildNavItems('member'),
            'footerColumns' => $this->buildFooterColumns(),
            'alertBanner' => $this->buildAlertBanner(),
            'formData' => $post,
        ]);
    }

    /**
     * 登入送出
     */
    public function loginDo(Request $request): RedirectResponse
    {
        $input = $request->only(['email', 'password']);
        $input['email'] = strtolower(trim((string) ($input['email'] ?? '')));

        $validator = Validator::make($input, [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => '電子信箱為必填欄位',
            'email.email' => '電子信箱格式錯誤',
            'password.required' => '密碼為必填欄位',
        ]);

        $sessionPost = [
            'email' => (string) ($input['email'] ?? ''),
        ];

        if ($validator->fails()) {
            session([self::LOGIN_POST_SESSION => $sessionPost]);
            $this->recordLoginFailSafe($request, (string) ($sessionPost['email'] ?? ''), self::LOGIN_FAIL_MESSAGE);

            return redirect('member/login')
                ->withErrors($validator)
                ->withInput($sessionPost);
        }

        $member = $this->authService->attempt((string) $input['email'], (string) ($input['password'] ?? ''));

        if (empty($member)) {
            session([self::LOGIN_POST_SESSION => $sessionPost]);
            $this->recordLoginFailSafe($request, (string) ($sessionPost['email'] ?? ''), self::LOGIN_FAIL_MESSAGE);

            return redirect('member/login')
                ->withInput($sessionPost)
                ->withErrors(['login' => self::LOGIN_FAIL_MESSAGE]);
        }

        $this->authService->login($member);
        $this->recordLoginSuccessSafe($request, (int) $member->id, (string) $member->email, (string) $member->name);
        MessageService::setMessage(MEMBER_MESSAGE_SESSION, MessageService::SUCCESS, '歡迎回來');

        return redirect('member/profile');
    }

    /**
     * 個人資料頁
     */
    public function profile(): RedirectResponse|\Illuminate\Contracts\View\View
    {
        $sessionMember = session(MEMBER_AUTH_SESSION, []);
        $memberId = (int) ($sessionMember['id'] ?? 0);

        if ($memberId <= 0) {
            return redirect('member/login');
        }

        $member = $this->authService->findById($memberId);
        if (empty($member)) {
            $this->authService->logout();

            return redirect('member/login')->withErrors(['member' => '會員資料不存在，請重新登入']);
        }

        $postBasic = session(self::PROFILE_BASIC_POST_SESSION, []);
        session()->forget(self::PROFILE_BASIC_POST_SESSION);

        # 取得最新一次成功登入時間（含本次當前 session 的登入；無紀錄為 null）
        $lastLoginAt = $this->memberLoginLogService->getLastLoginAt($memberId);

        $data = [
            'id' => (int) $member->id,
            'email' => (string) $member->email,
            'name' => (string) $member->name,
            'phone' => (string) ($member->phone ?? ''),
            'birthday' => !empty($member->birthday) ? $member->birthday->format('Y-m-d') : null,
            'gender_key' => (string) $member->gender_key,
            'avatar_url' => $member->avatar_url,
            'last_login_at' => $lastLoginAt?->format('Y-m-d H:i:s'),
        ];

        return view('Frontend.member.profile', [
            'pageTitle' => '個人資料 | Aura & Heirloom',
            'navItems' => $this->buildNavItems('member'),
            'footerColumns' => $this->buildFooterColumns(),
            'alertBanner' => $this->buildAlertBanner(),
            'data' => $data,
            'genderOptions' => config('constants.gender', []),
            'postBasic' => $postBasic,
            'postPassword' => [],
        ]);
    }

    /**
     * 更新個人資料
     */
    public function profileDo(Request $request): RedirectResponse
    {
        $memberId = (int) (session(MEMBER_AUTH_SESSION)['id'] ?? 0);
        if ($memberId <= 0) {
            return redirect('member/login');
        }

        $avatarConfig = config('upload.member_avatar', config('upload.image', []));
        $avatarMimes = implode(',', $avatarConfig['mimes'] ?? ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        $avatarMax = (int) ($avatarConfig['max_size'] ?? 5120);

        $input = $request->only(['name', 'phone', 'birthday', 'gender_key']);
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'regex:/^[0-9+()\-\s]{6,30}$/'],
            'birthday' => ['nullable', 'date_format:Y-m-d'],
            'gender_key' => ['required', Rule::in(array_map('strval', array_keys(config('constants.gender', []))))],
            'avatar' => ['nullable', 'file', 'mimes:' . $avatarMimes, 'max:' . $avatarMax],
        ], [
            'name.required' => '姓名為必填欄位',
            'phone.regex' => '手機格式錯誤',
            'birthday.date_format' => '生日格式錯誤',
            'gender_key.required' => '請選擇性別',
            'gender_key.in' => '性別格式錯誤',
            'avatar.mimes' => '大頭照格式不支援',
            'avatar.max' => '大頭照檔案大小超過限制',
        ]);

        $sessionPost = [
            'name' => (string) ($input['name'] ?? ''),
            'phone' => (string) ($input['phone'] ?? ''),
            'birthday' => (string) ($input['birthday'] ?? ''),
            'gender_key' => (string) ($input['gender_key'] ?? ''),
        ];

        if ($validator->fails()) {
            session([self::PROFILE_BASIC_POST_SESSION => $sessionPost]);

            return redirect('member/profile')
                ->withErrors($validator)
                ->withInput($sessionPost);
        }

        $oldMember = $this->authService->findById($memberId);
        if (empty($oldMember)) {
            return redirect('member/login')->withErrors(['member' => '會員資料不存在，請重新登入']);
        }

        $oldData = [
            'name' => (string) $oldMember->name,
            'phone' => (string) ($oldMember->phone ?? ''),
            'birthday' => !empty($oldMember->birthday) ? $oldMember->birthday->format('Y-m-d') : null,
            'gender_key' => (string) $oldMember->gender_key,
            'avatar_path' => (string) ($oldMember->avatar_path ?? ''),
        ];

        try {
            $updated = $this->authService->updateProfile($memberId, $input, $request->file('avatar'));

            # 同步會員 session 顯示資訊，避免 header 顯示舊資料
            session([
                MEMBER_AUTH_SESSION => [
                    'id' => $memberId,
                    'email' => (string) (session(MEMBER_AUTH_SESSION)['email'] ?? $updated['email'] ?? ''),
                    'name' => (string) ($updated['name'] ?? ''),
                    'avatar_url' => $updated['avatar_url'] ?? null,
                ],
            ]);

            try {
                $this->memberLogService->recordUpdate(
                    $request,
                    'member_profile',
                    $memberId,
                    (string) ($updated['name'] ?? ''),
                    $oldData,
                    [
                        'name' => (string) ($updated['name'] ?? ''),
                        'phone' => (string) ($updated['phone'] ?? ''),
                        'birthday' => $updated['birthday'] ?? null,
                        'gender_key' => (string) ($updated['gender_key'] ?? ''),
                        'avatar_path' => (string) ($updated['avatar_path'] ?? ''),
                    ],
                    ['name', 'phone', 'birthday', 'gender_key', 'avatar_path']
                );
            } catch (\Exception $e) {
                report($e);
            }

            MessageService::setMessage(MEMBER_MESSAGE_SESSION, MessageService::SUCCESS, '個人資料已更新');

            return redirect('member/profile');
        } catch (\Exception $e) {
            report($e);
            session([self::PROFILE_BASIC_POST_SESSION => $sessionPost]);

            return redirect('member/profile')
                ->withInput($sessionPost)
                ->withErrors(['profile' => '個人資料更新失敗，請稍後再試']);
        }
    }

    /**
     * 修改密碼
     */
    public function changePasswordDo(Request $request): RedirectResponse
    {
        $memberId = (int) (session(MEMBER_AUTH_SESSION)['id'] ?? 0);
        if ($memberId <= 0) {
            return redirect('member/login');
        }

        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => '目前密碼為必填欄位',
            'new_password.required' => '新密碼為必填欄位',
            'new_password.min' => '新密碼至少需要 8 個字元',
            'new_password.confirmed' => '新密碼與確認密碼不一致',
        ]);

        if ($validator->fails()) {
            return redirect('member/profile')
                ->withErrors($validator, 'change_password');
        }

        try {
            $this->authService->changePassword(
                $memberId,
                (string) $request->input('current_password', ''),
                (string) $request->input('new_password', '')
            );

            try {
                $this->memberLogService->recordSimple(
                    $request,
                    'member_profile',
                    'update',
                    $memberId,
                    (string) (session(MEMBER_AUTH_SESSION)['name'] ?? ''),
                    '會員變更登入密碼'
                );
            } catch (\Exception $e) {
                report($e);
            }

            MessageService::setMessage(MEMBER_MESSAGE_SESSION, MessageService::SUCCESS, '密碼已更新');

            return redirect('member/profile');
        } catch (\InvalidArgumentException $e) {
            return redirect('member/profile')->withErrors([
                'current_password' => $e->getMessage(),
                'change_password' => '密碼更新失敗，請重新輸入',
            ], 'change_password');
        } catch (\Exception $e) {
            report($e);

            return redirect('member/profile')->withErrors([
                'change_password' => '密碼更新失敗，請稍後再試',
            ], 'change_password');
        }
    }

    /**
     * 會員登出
     */
    public function logout(Request $request): RedirectResponse
    {
        $member = session(MEMBER_AUTH_SESSION, []);

        $this->authService->logout();

        if (!empty($member['id'])) {
            $this->recordLogoutSafe(
                $request,
                (int) $member['id'],
                (string) ($member['email'] ?? ''),
                (string) ($member['name'] ?? '')
            );
        }

        MessageService::setMessage(MEMBER_MESSAGE_SESSION, MessageService::SUCCESS, '您已成功登出。');

        return redirect('/');
    }

    /**
     * 非阻斷式記錄登入成功
     */
    protected function recordLoginSuccessSafe(Request $request, int $memberId, string $account, string $memberName): void
    {
        try {
            $this->memberLoginLogService->recordLoginSuccess($request, $memberId, $account, $memberName);
        } catch (\Exception $e) {
            report($e);
        }
    }

    /**
     * 非阻斷式記錄登入失敗
     */
    protected function recordLoginFailSafe(Request $request, string $account, string $failReason): void
    {
        try {
            $this->memberLoginLogService->recordLoginFail($request, $account, $failReason);
        } catch (\Exception $e) {
            report($e);
        }
    }

    /**
     * 非阻斷式記錄登出
     */
    protected function recordLogoutSafe(Request $request, int $memberId, string $account, string $memberName): void
    {
        try {
            $this->memberLoginLogService->recordLogout($request, $memberId, $account, $memberName);
        } catch (\Exception $e) {
            report($e);
        }
    }
}

