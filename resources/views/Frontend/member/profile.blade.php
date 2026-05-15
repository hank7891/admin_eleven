@extends('layouts.frontend')

@section('fe-active', '')

@section('content')
    <section class="fe-section fe-profile">
        <div class="fe-container">
            <header class="fe-profile-head">
                <span class="fe-eyebrow is-muted">The Inner Circle</span>
                <h1 class="fe-h1 fe-profile-title">會員個人資料</h1>
                <p class="fe-body-lg fe-profile-lead">在會員專區可以更新個人資料、頭像與登入密碼。</p>
            </header>

            <div class="fe-profile-grid">
                {{-- Side: avatar + meta + menu --}}
                <aside class="fe-profile-side">
                    @if (!empty($data['avatar_url']))
                        <img src="{{ $data['avatar_url'] }}" alt="會員頭像" class="fe-avatar-lg fe-avatar-img">
                    @else
                        <div class="fe-avatar-lg" aria-hidden="true">{{ mb_substr($data['name'] ?? '會員', 0, 1) }}</div>
                    @endif
                    <h2 class="fe-profile-side-name">{{ $data['name'] ?? '' }}</h2>
                    <p class="fe-meta fe-profile-side-email">{{ $data['email'] ?? '' }}</p>

                    <div class="fe-profile-side-meta">
                        <p class="fe-meta">最後登入時間</p>
                        @if (!empty($data['last_login_at']))
                            <p class="fe-body fe-profile-side-login">{{ $data['last_login_at'] }}</p>
                        @else
                            <p class="fe-body fe-profile-side-login is-muted">尚未有登入紀錄</p>
                        @endif
                    </div>

                    <form method="POST" action="{{ url('member/logout') }}" class="fe-profile-side-logout">
                        @csrf
                        <button type="submit" class="fe-btn fe-btn-ghost">
                            <span class="material-symbols-outlined" aria-hidden="true">logout</span>
                            會員登出
                        </button>
                    </form>
                </aside>

                {{-- Main: forms --}}
                <div class="fe-profile-main">
                    <section class="fe-profile-section">
                        <div class="fe-profile-section-head">
                            <h2 class="fe-h3">基本資料</h2>
                            <p class="fe-meta">這些資訊會用於通知與顯示。</p>
                        </div>

                        @if ($errors->any())
                            <div class="fe-form-error" role="alert">
                                <span class="material-symbols-outlined" aria-hidden="true">error</span>
                                <p>{{ $errors->first() }}</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ url('member/profile') }}" enctype="multipart/form-data" class="fe-profile-form">
                            @csrf

                            <div class="fe-form-field fe-form-field-full">
                                <label class="fe-form-label" for="email">電子信箱</label>
                                <input id="email" type="email" value="{{ $data['email'] ?? '' }}" readonly class="fe-input is-readonly">
                            </div>

                            <div class="fe-form-field">
                                <label class="fe-form-label" for="name">姓名</label>
                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    required
                                    aria-required="true"
                                    maxlength="100"
                                    value="{{ old('name', $postBasic['name'] ?? $data['name'] ?? '') }}"
                                    class="fe-input"
                                >
                            </div>

                            <div class="fe-form-field">
                                <label class="fe-form-label" for="phone">手機</label>
                                <input
                                    id="phone"
                                    type="text"
                                    name="phone"
                                    maxlength="30"
                                    value="{{ old('phone', $postBasic['phone'] ?? $data['phone'] ?? '') }}"
                                    placeholder="請輸入手機號碼"
                                    class="fe-input"
                                >
                            </div>

                            <div class="fe-form-field">
                                <label class="fe-form-label" for="birthday">生日</label>
                                <input
                                    id="birthday"
                                    type="date"
                                    name="birthday"
                                    value="{{ old('birthday', $postBasic['birthday'] ?? $data['birthday'] ?? '') }}"
                                    class="fe-input"
                                >
                            </div>

                            <div class="fe-form-field">
                                <label class="fe-form-label" for="gender_key">性別</label>
                                <select id="gender_key" name="gender_key" class="fe-input">
                                    @foreach ($genderOptions ?? [] as $key => $label)
                                        <option value="{{ $key }}" @selected((string) old('gender_key', $postBasic['gender_key'] ?? $data['gender_key'] ?? '') === (string) $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="fe-form-field fe-form-field-full">
                                <label class="fe-form-label" for="avatar">大頭照</label>
                                <input
                                    id="avatar"
                                    type="file"
                                    name="avatar"
                                    accept=".jpg,.jpeg,.png,.gif,.webp"
                                    class="fe-file-input"
                                >
                                <p class="fe-form-hint">建議尺寸 800 × 800，最大 5MB。</p>
                            </div>

                            <div class="fe-profile-form-actions">
                                <button type="submit" class="fe-btn fe-btn-dark">儲存基本資料</button>
                            </div>
                        </form>
                    </section>

                    <hr class="fe-profile-divider">

                    <section class="fe-profile-section">
                        <div class="fe-profile-section-head">
                            <h2 class="fe-h3">變更密碼</h2>
                            <p class="fe-meta">為了帳號安全，建議每 3 個月更新一次。</p>
                        </div>

                        @if ($errors->change_password->any())
                            <div class="fe-form-error" role="alert">
                                <span class="material-symbols-outlined" aria-hidden="true">error</span>
                                <p>{{ $errors->change_password->first() }}</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ url('member/profile/password') }}" class="fe-profile-form">
                            @csrf

                            <div class="fe-form-field fe-form-field-full">
                                <label class="fe-form-label" for="current_password">目前密碼</label>
                                <input id="current_password" type="password" name="current_password" required aria-required="true" class="fe-input">
                            </div>

                            <div class="fe-form-field">
                                <label class="fe-form-label" for="new_password">新密碼</label>
                                <input id="new_password" type="password" name="new_password" required aria-required="true" minlength="8" class="fe-input">
                            </div>

                            <div class="fe-form-field">
                                <label class="fe-form-label" for="new_password_confirmation">確認新密碼</label>
                                <input id="new_password_confirmation" type="password" name="new_password_confirmation" required aria-required="true" minlength="8" class="fe-input">
                            </div>

                            <p class="fe-form-hint fe-form-field-full">密碼欄位不會回填，儲存失敗需重新輸入。</p>

                            <div class="fe-profile-form-actions">
                                <button type="submit" class="fe-btn fe-btn-primary">更新密碼</button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </section>
@endsection
