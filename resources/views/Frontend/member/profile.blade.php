@extends('Frontend-share.layout')

@section('content')
    <section class="relative min-h-screen overflow-hidden px-6 pb-24 pt-36 md:px-12">
        <div class="pointer-events-none absolute inset-0 -z-10 opacity-30">
            <div class="absolute right-[-5%] top-[-10%] h-96 w-96 rounded-full bg-secondary-container mix-blend-multiply blur-3xl"></div>
            <div class="absolute bottom-[-10%] left-[-10%] h-[30rem] w-[30rem] rounded-full bg-tertiary-fixed mix-blend-multiply blur-[100px]"></div>
        </div>

        <div class="mx-auto w-full max-w-5xl space-y-8">
            <div class="space-y-3 text-center md:text-left">
                <h1 class="font-headline text-[2.3rem] font-light tracking-tight text-on-surface sm:text-[2.8rem]">會員個人資料</h1>
                <p class="font-label text-[0.75rem] uppercase tracking-[0.15em] text-on-surface-variant/80">Manage your Aura &amp; Heirloom account</p>
            </div>

            <div class="grid gap-8 lg:grid-cols-2">
                <div class="relative overflow-hidden rounded-xl border border-outline-variant/20 bg-surface-container-lowest p-8 shadow-[0_12px_40px_rgba(26,28,25,0.06)] md:p-10">
                    <div class="absolute left-0 top-0 h-1 w-full bg-linear-to-r from-primary to-primary-container"></div>

                    <h2 class="mb-6 text-xl font-semibold text-on-surface">基本資料</h2>

                    @if ($errors->any())
                        <div class="mb-6 rounded-lg border-l-2 border-error bg-error-container/30 p-4 text-sm leading-relaxed text-on-error-container">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ url('member/profile') }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="space-y-2">
                            <label class="block font-label text-[0.78rem] uppercase tracking-[0.14em] text-tertiary">最後登入時間</label>
                            @if (!empty($data['last_login_at']))
                                <p class="px-0 py-2 text-[1.02rem] font-light text-on-surface/65">
                                    {{ $data['last_login_at'] }}
                                </p>
                            @else
                                <p class="px-0 py-2 text-[1.02rem] font-light text-on-surface-variant/60">
                                    尚未有登入紀錄
                                </p>
                            @endif
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="block font-label text-[0.78rem] uppercase tracking-[0.14em] text-tertiary">電子信箱</label>
                            <input
                                id="email"
                                type="email"
                                value="{{ $data['email'] ?? '' }}"
                                readonly
                                class="w-full border-0 border-b-2 border-outline-variant/30 bg-transparent px-0 py-2 text-[1.02rem] font-light text-on-surface/65 focus:ring-0"
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="name" class="block font-label text-[0.78rem] uppercase tracking-[0.14em] text-tertiary">姓名</label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                required
                                maxlength="100"
                                value="{{ old('name', $postBasic['name'] ?? $data['name'] ?? '') }}"
                                class="w-full border-0 border-b-2 border-outline-variant/50 bg-transparent px-0 py-2 text-[1.02rem] font-light text-on-surface placeholder:font-light placeholder:text-outline/50 focus:border-primary focus:ring-0"
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="phone" class="block font-label text-[0.78rem] uppercase tracking-[0.14em] text-tertiary">手機</label>
                            <input
                                id="phone"
                                type="text"
                                name="phone"
                                maxlength="30"
                                value="{{ old('phone', $postBasic['phone'] ?? $data['phone'] ?? '') }}"
                                placeholder="請輸入手機號碼"
                                class="w-full border-0 border-b-2 border-outline-variant/50 bg-transparent px-0 py-2 text-[1.02rem] font-light text-on-surface placeholder:font-light placeholder:text-outline/50 focus:border-primary focus:ring-0"
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="birthday" class="block font-label text-[0.78rem] uppercase tracking-[0.14em] text-tertiary">生日</label>
                            <input
                                id="birthday"
                                type="date"
                                name="birthday"
                                value="{{ old('birthday', $postBasic['birthday'] ?? $data['birthday'] ?? '') }}"
                                class="w-full border-0 border-b-2 border-outline-variant/50 bg-transparent px-0 py-2 text-[1.02rem] font-light text-on-surface focus:border-primary focus:ring-0"
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="gender_key" class="block font-label text-[0.78rem] uppercase tracking-[0.14em] text-tertiary">性別</label>
                            <select
                                id="gender_key"
                                name="gender_key"
                                class="w-full border-0 border-b-2 border-outline-variant/50 bg-transparent px-0 py-2 text-[1.02rem] font-light text-on-surface focus:border-primary focus:ring-0"
                            >
                                @foreach ($genderOptions ?? [] as $key => $label)
                                    <option value="{{ $key }}" @selected((string) old('gender_key', $postBasic['gender_key'] ?? $data['gender_key'] ?? '') === (string) $key)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-3">
                            <label for="avatar" class="block font-label text-[0.78rem] uppercase tracking-[0.14em] text-tertiary">大頭照</label>
                            <input
                                id="avatar"
                                type="file"
                                name="avatar"
                                accept=".jpg,.jpeg,.png,.gif,.webp"
                                class="block w-full text-sm text-on-surface/85 file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-medium file:text-on-primary hover:file:bg-[#3148a8]"
                            >
                            <p class="text-xs text-outline">建議尺寸 800 x 800，最大 5MB。</p>
                            @if (!empty($data['avatar_url']))
                                <img src="{{ $data['avatar_url'] }}" alt="會員頭像" class="h-24 w-24 rounded-full border border-outline-variant/40 object-cover">
                            @endif
                        </div>

                        <button type="submit" class="w-full rounded-lg bg-primary py-3 text-base font-semibold text-on-primary shadow-[0_6px_14px_-10px_rgba(26,28,25,0.35)] transition-colors duration-200 hover:bg-[#3148a8] active:scale-[0.98]">
                            儲存基本資料
                        </button>
                    </form>
                </div>

                <div class="relative overflow-hidden rounded-xl border border-outline-variant/20 bg-surface-container-lowest p-8 shadow-[0_12px_40px_rgba(26,28,25,0.06)] md:p-10">
                    <div class="absolute left-0 top-0 h-1 w-full bg-linear-to-r from-secondary to-tertiary"></div>

                    <h2 class="mb-6 text-xl font-semibold text-on-surface">修改密碼</h2>

                    @if ($errors->change_password->any())
                        <div class="mb-6 rounded-lg border-l-2 border-error bg-error-container/30 p-4 text-sm leading-relaxed text-on-error-container">
                            {{ $errors->change_password->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ url('member/profile/password') }}" class="space-y-6">
                        @csrf

                        <div class="space-y-2">
                            <label for="current_password" class="block font-label text-[0.78rem] uppercase tracking-[0.14em] text-tertiary">目前密碼</label>
                            <input
                                id="current_password"
                                type="password"
                                name="current_password"
                                required
                                class="w-full border-0 border-b-2 border-outline-variant/50 bg-transparent px-0 py-2 text-[1.02rem] font-light text-on-surface focus:border-primary focus:ring-0"
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="new_password" class="block font-label text-[0.78rem] uppercase tracking-[0.14em] text-tertiary">新密碼</label>
                            <input
                                id="new_password"
                                type="password"
                                name="new_password"
                                required
                                minlength="8"
                                class="w-full border-0 border-b-2 border-outline-variant/50 bg-transparent px-0 py-2 text-[1.02rem] font-light text-on-surface focus:border-primary focus:ring-0"
                            >
                        </div>

                        <div class="space-y-2">
                            <label for="new_password_confirmation" class="block font-label text-[0.78rem] uppercase tracking-[0.14em] text-tertiary">確認新密碼</label>
                            <input
                                id="new_password_confirmation"
                                type="password"
                                name="new_password_confirmation"
                                required
                                minlength="8"
                                class="w-full border-0 border-b-2 border-outline-variant/50 bg-transparent px-0 py-2 text-[1.02rem] font-light text-on-surface focus:border-primary focus:ring-0"
                            >
                            <p class="text-xs text-outline">密碼欄位不會回填，儲存失敗需重新輸入。</p>
                        </div>

                        <button type="submit" class="w-full rounded-lg bg-secondary py-3 text-base font-semibold text-on-primary shadow-[0_6px_14px_-10px_rgba(26,28,25,0.35)] transition-colors duration-200 hover:bg-[#68428f] active:scale-[0.98]">
                            更新密碼
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
