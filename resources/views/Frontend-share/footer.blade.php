<footer id="footer" class="border-t border-outline-variant/30 bg-surface-container-low text-on-surface">
    <div class="mx-auto grid max-w-7xl gap-12 px-4 py-16 sm:px-6 lg:grid-cols-[1.2fr_repeat(3,1fr)] lg:px-8 lg:py-20">
        <div class="space-y-6">
            <h2 class="font-headline text-[1.5rem] font-semibold tracking-tight">Aura &amp; Heirloom</h2>
            <p class="max-w-sm text-[0.95rem] leading-7 text-on-surface/72">
                以選物、工藝與編輯視角，為當代生活保留一種更緩慢、更耐看的質地。
            </p>
            <div class="flex items-center gap-3">
                <a href="#" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-outline-variant/70 bg-surface-container-lowest text-on-surface/70 transition-colors hover:border-primary hover:text-primary no-underline" aria-label="Language">
                    <span class="material-symbols-outlined text-[1.1rem]" aria-hidden="true">language</span>
                </a>
                <a href="#" class="inline-flex h-11 items-center justify-center rounded-full border border-outline-variant/70 bg-surface-container-lowest px-4 text-[0.75rem] font-semibold tracking-[0.16em] text-on-surface/70 uppercase transition-colors hover:border-primary hover:text-primary no-underline" aria-label="Instagram">Instagram</a>
                <a href="#" class="inline-flex h-11 items-center justify-center rounded-full border border-outline-variant/70 bg-surface-container-lowest px-4 text-[0.75rem] font-semibold tracking-[0.16em] text-on-surface/70 uppercase transition-colors hover:border-primary hover:text-primary no-underline" aria-label="Pinterest">Pinterest</a>
            </div>
        </div>

        @foreach ($footerColumns ?? [] as $column)
            <div>
                <h3 class="font-label text-[0.7rem] font-semibold tracking-[0.22em] text-on-surface/48 uppercase">{{ $column['title'] }}</h3>
                <ul class="mt-6 space-y-4">
                    @foreach ($column['links'] as $link)
                        <li>
                            <a href="#" class="text-[0.9rem] text-on-surface/72 transition-colors hover:text-primary no-underline">{{ $link }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    <div class="border-t border-outline-variant/25">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 text-[0.75rem] tracking-[0.12em] text-on-surface/42 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <p>© {{ date('Y') }} Aura &amp; Heirloom. All rights reserved.</p>
            <div class="flex flex-wrap items-center gap-5">
                <a href="#" class="transition-colors hover:text-on-surface no-underline">Privacy Policy</a>
                <a href="#" class="transition-colors hover:text-on-surface no-underline">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

