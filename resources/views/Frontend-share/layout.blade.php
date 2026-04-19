<!DOCTYPE html>
<html lang="zh-Hant" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle ?? 'Aura & Heirloom' }}</title>
    <meta name="description" content="@yield('meta_description', 'Aura & Heirloom — 為日常留一個慢下來的位置')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Noto+Serif+TC:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1" rel="stylesheet">

    @vite(['resources/css/frontend.css', 'resources/js/frontend.js'])
    @stack('styles')
</head>
<body id="top" class="frontend-body bg-background text-on-background font-body antialiased selection:bg-primary-fixed selection:text-on-primary-fixed">
    @include('Frontend-share.alert-banner')
    @include('Frontend-share.header')

    @php
        $frontendMessages = session(MEMBER_MESSAGE_SESSION, []);
        session()->forget(MEMBER_MESSAGE_SESSION);
    @endphp
    @if (is_array($frontendMessages) && !empty($frontendMessages))
        <div class="fixed left-1/2 top-24 z-[80] w-full max-w-md -translate-x-1/2 px-4" role="status" aria-live="polite" data-frontend-flash>
            @foreach ($frontendMessages as $message)
                @php
                    $type = $message['type'] ?? 'info';
                    $toneClass = match ($type) {
                        'success' => 'border-primary/30 bg-primary-container text-on-primary-container',
                        'danger' => 'border-error/40 bg-error-container text-on-error-container',
                        'warning' => 'border-tertiary/40 bg-tertiary-container text-on-tertiary-container',
                        default => 'border-outline-variant/40 bg-surface-container-lowest text-on-surface',
                    };
                @endphp
                <div
                    class="frontend-flash-item mb-2 flex cursor-pointer items-start gap-3 rounded-xl border {{ $toneClass }} px-4 py-3 text-sm shadow-[0_16px_36px_-24px_rgba(26,28,25,0.32)]"
                    role="button"
                    tabindex="0"
                    aria-label="點擊關閉訊息"
                    data-frontend-flash-item
                >
                    <span class="flex-1 leading-relaxed">{{ $message['message'] ?? '' }}</span>
                    <span class="material-symbols-outlined text-[1.05rem] opacity-60" aria-hidden="true">close</span>
                </div>
            @endforeach
        </div>
        <script>
            (function () {
                const items = document.querySelectorAll('[data-frontend-flash-item]');
                if (!items.length) {
                    return;
                }

                const AUTO_DISMISS_MS = 6000;
                const FADE_MS = 260;

                function dismiss(item) {
                    if (!item || item.dataset.dismissed === '1') {
                        return;
                    }
                    item.dataset.dismissed = '1';
                    item.classList.add('is-leaving');
                    window.setTimeout(function () {
                        if (item.parentNode) {
                            item.parentNode.removeChild(item);
                        }
                    }, FADE_MS);
                }

                items.forEach(function (item) {
                    item.addEventListener('click', function () { dismiss(item); });
                    item.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            dismiss(item);
                        }
                    });
                    window.setTimeout(function () { dismiss(item); }, AUTO_DISMISS_MS);
                });
            })();
        </script>
    @endif

    <main class="frontend-main">
        @yield('content')
    </main>

    @include('Frontend-share.footer')
    @stack('scripts')
</body>
</html>

