<header class="frontend-header fixed inset-x-0 z-60 border-b border-white/30 bg-background/72">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8 lg:py-5">
        <div class="flex items-center gap-3 lg:gap-10">
            <button
                type="button"
                class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-outline-variant/60 bg-surface-container-lowest/75 text-on-surface transition-colors hover:border-primary hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary lg:hidden"
                aria-label="開啟導覽選單"
                aria-expanded="false"
                aria-controls="frontend-mobile-menu"
                data-mobile-menu-toggle
            >
                <span class="material-symbols-outlined text-[1.3rem]" aria-hidden="true">menu</span>
            </button>

            <a href="/#top" class="font-headline text-[1.4rem] font-semibold tracking-tight text-on-surface no-underline sm:text-[1.75rem]">
                Aura &amp; Heirloom
            </a>
        </div>

        <nav class="hidden items-center gap-8 lg:flex" aria-label="前台主導覽">
            @foreach ($navItems ?? [] as $item)
                <a
                    href="{{ $item['url'] }}"
                    class="text-[0.95rem] font-medium tracking-[0.01em] text-on-surface/70 transition-colors hover:text-primary no-underline first:border-b first:border-primary first:pb-1 first:text-primary"
                >
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
            <a href="/#journal" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-outline-variant/60 bg-surface-container-lowest/75 text-on-surface transition-colors hover:border-primary hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary no-underline" aria-label="搜尋靈感內容">
                <span class="material-symbols-outlined text-[1.2rem]" aria-hidden="true">search</span>
            </a>
            <a href="/#member" class="inline-flex items-center gap-2 rounded-full border border-outline-variant/60 bg-surface-container-lowest/75 px-4 py-2.5 text-[0.875rem] font-medium text-on-surface transition-colors hover:border-primary hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary no-underline">
                <span class="material-symbols-outlined text-[1.1rem]" aria-hidden="true">person</span>
                <span class="hidden sm:inline">會員入口</span>
            </a>
        </div>
    </div>

    <div id="frontend-mobile-menu" class="frontend-mobile-menu hidden border-t border-outline-variant/30 bg-background/96 px-4 py-4 shadow-[0_24px_40px_-24px_rgba(26,28,25,0.28)] backdrop-blur lg:hidden" data-mobile-menu>
        <nav class="flex flex-col gap-1" aria-label="前台手機導覽">
            @foreach ($navItems ?? [] as $item)
                <a href="{{ $item['url'] }}" class="rounded-xl px-4 py-3 text-[0.95rem] font-medium text-on-surface/80 transition-colors hover:bg-surface-container-low hover:text-primary no-underline">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>
    </div>
</header>


