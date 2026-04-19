@extends('Frontend-share.layout')

@section('content')
    <section class="relative flex min-h-screen items-center justify-center overflow-hidden px-6 pb-24 pt-36 md:px-12">
        <div class="pointer-events-none absolute inset-0 -z-10 opacity-30">
            <div class="absolute right-[-5%] top-[-10%] h-96 w-96 rounded-full bg-secondary-container mix-blend-multiply blur-3xl"></div>
            <div class="absolute bottom-[-10%] left-[-10%] h-[30rem] w-[30rem] rounded-full bg-tertiary-fixed mix-blend-multiply blur-[100px]"></div>
        </div>

        <div class="w-full max-w-md">
            <div class="mb-12 space-y-3 text-center">
                <h1 class="font-headline text-[2.5rem] font-light tracking-tight text-on-surface sm:text-[3.1rem]">加入會員</h1>
                <p class="font-label text-[0.75rem] uppercase tracking-[0.15em] text-on-surface-variant/80">Create your Aura &amp; Heirloom account</p>
            </div>

            <div class="relative overflow-hidden rounded-xl border border-outline-variant/20 bg-surface-container-lowest p-8 shadow-[0_12px_40px_rgba(26,28,25,0.06)] md:p-10">
                <div class="absolute left-0 top-0 h-1 w-full bg-linear-to-r from-primary to-primary-container"></div>

                @if ($errors->any())
                    <div class="frontend-register-alert mb-8 flex items-start gap-3 rounded-lg border-l-2 border-error bg-error-container/30 p-4 opacity-0 transition-opacity duration-200" data-register-server-alert>
                        <span class="material-symbols-outlined mt-0.5 text-sm text-error" style="font-variation-settings: 'FILL' 1;">error</span>
                        <p class="text-sm leading-relaxed text-on-error-container">{{ $errors->first() }}</p>
                    </div>
                @endif

                <div class="mb-8 hidden items-start gap-3 rounded-lg border-l-2 border-error bg-error-container/30 p-4 opacity-0 transition-opacity duration-200" data-register-client-alert>
                    <span class="material-symbols-outlined mt-0.5 text-sm text-error" style="font-variation-settings: 'FILL' 1;">error</span>
                    <p class="text-sm leading-relaxed text-on-error-container" data-register-client-alert-text></p>
                </div>

                <form method="POST" action="{{ url('member/register') }}" class="space-y-8" novalidate data-register-form>
                    @csrf

                    <div class="space-y-2">
                        <label for="email" class="block font-label text-[0.78rem] uppercase tracking-[0.14em] text-tertiary">電子信箱</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            required
                            maxlength="255"
                            value="{{ old('email', $formData['email'] ?? '') }}"
                            placeholder="請輸入您的 Email"
                            class="w-full border-0 border-b-2 border-outline-variant/50 bg-transparent px-0 py-2 text-[1.02rem] font-light text-on-surface placeholder:font-light placeholder:text-outline/50 focus:border-primary focus:ring-0"
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
                            value="{{ old('name', $formData['name'] ?? '') }}"
                            placeholder="請輸入您的姓名"
                            class="w-full border-0 border-b-2 border-outline-variant/50 bg-transparent px-0 py-2 text-[1.02rem] font-light text-on-surface placeholder:font-light placeholder:text-outline/50 focus:border-primary focus:ring-0"
                        >
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="block font-label text-[0.78rem] uppercase tracking-[0.14em] text-tertiary">密碼</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            minlength="8"
                            placeholder="請輸入密碼（至少 8 碼）"
                            class="w-full border-0 border-b-2 border-outline-variant/50 bg-transparent px-0 py-2 text-[1.02rem] font-light text-on-surface placeholder:font-light placeholder:text-outline/50 focus:border-primary focus:ring-0"
                        >
                        <p class="pt-1 text-xs text-outline">密碼至少需要 8 個字元，儲存失敗時需重新輸入密碼</p>
                    </div>

                    <div class="space-y-2">
                        <label for="password_confirmation" class="block font-label text-[0.78rem] uppercase tracking-[0.14em] text-tertiary">確認密碼</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            minlength="8"
                            placeholder="請再次輸入密碼"
                            class="w-full border-0 border-b-2 border-outline-variant/50 bg-transparent px-0 py-2 text-[1.02rem] font-light text-on-surface placeholder:font-light placeholder:text-outline/50 focus:border-primary focus:ring-0"
                        >
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full rounded-lg bg-primary py-4 text-lg font-semibold text-on-primary shadow-[0_6px_14px_-10px_rgba(26,28,25,0.35)] transition-colors duration-200 hover:bg-[#3148a8] active:scale-[0.98]">
                            建立帳號
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ url('member/login') }}" class="border-b border-transparent pb-0.5 text-sm text-tertiary transition-colors hover:border-primary hover:text-primary no-underline">已經有帳號？立即登入</a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            const form = document.querySelector('[data-register-form]');
            if (!form) {
                return;
            }

            const clientAlert = document.querySelector('[data-register-client-alert]');
            const clientAlertText = document.querySelector('[data-register-client-alert-text]');
            const serverAlert = document.querySelector('[data-register-server-alert]');

            if (serverAlert) {
                requestAnimationFrame(function () {
                    serverAlert.classList.add('opacity-100');
                });
            }

            function showClientAlert(message) {
                if (!clientAlert || !clientAlertText) {
                    return;
                }

                clientAlertText.textContent = message;
                clientAlert.classList.remove('hidden');
                clientAlert.classList.add('flex');
                requestAnimationFrame(function () {
                    clientAlert.classList.add('opacity-100');
                });
            }

            function hideClientAlert() {
                if (!clientAlert) {
                    return;
                }

                clientAlert.classList.remove('opacity-100');
                clientAlert.classList.add('hidden');
                clientAlert.classList.remove('flex');
            }

            form.addEventListener('submit', function (event) {
                hideClientAlert();

                const email = (form.email?.value || '').trim();
                const name = (form.name?.value || '').trim();
                const password = form.password?.value || '';
                const passwordConfirmation = form.password_confirmation?.value || '';

                if (email === '') {
                    event.preventDefault();
                    showClientAlert('請輸入電子信箱。');
                    form.email?.focus();
                    return;
                }

                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email)) {
                    event.preventDefault();
                    showClientAlert('電子信箱格式錯誤，請重新輸入。');
                    form.email?.focus();
                    return;
                }

                if (name === '') {
                    event.preventDefault();
                    showClientAlert('請輸入姓名。');
                    form.name?.focus();
                    return;
                }

                if (password.length < 8) {
                    event.preventDefault();
                    showClientAlert('密碼至少需要 8 個字元。');
                    form.password?.focus();
                    return;
                }

                if (password !== passwordConfirmation) {
                    event.preventDefault();
                    showClientAlert('兩次密碼輸入不一致。');
                    form.password_confirmation?.focus();
                }
            });
        })();
    </script>
@endpush

