@if (!empty($alertBanner['message']))
<aside
    id="frontend-alert-banner"
    class="frontend-alert sticky top-0 z-70 w-full border-b border-tertiary/10 bg-tertiary-fixed text-on-tertiary-fixed"
    role="region"
    aria-label="系統公告"
>
    <div class="mx-auto flex max-w-7xl items-start gap-3 px-4 py-3 sm:px-6 lg:px-8">
        <span class="material-symbols-outlined mt-0.5 text-[1.125rem]" aria-hidden="true">campaign</span>
        <div class="flex-1 text-sm leading-6 sm:text-[0.9375rem]">
            <span class="font-label font-semibold tracking-[0.12em] text-on-tertiary-fixed/70 uppercase">{{ $alertBanner['title'] ?? '系統公告' }}</span>
            <p class="mt-1 font-headline text-[0.95rem] sm:text-[1rem]">
                {{ $alertBanner['message'] ?? '' }}
                @if (!empty($alertBanner['link_label']))
                    <a href="{{ $alertBanner['link_url'] ?? '#' }}" class="ml-2 underline underline-offset-4 transition-colors hover:text-tertiary" aria-label="{{ $alertBanner['link_label'] }}">
                        {{ $alertBanner['link_label'] }}
                    </a>
                @endif
            </p>
        </div>
        <button
            type="button"
            class="frontend-alert-close inline-flex h-10 w-10 items-center justify-center rounded-full text-on-tertiary-fixed/70 transition-colors hover:bg-on-tertiary-fixed/8 hover:text-on-tertiary-fixed focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-on-tertiary-fixed"
            aria-label="關閉系統公告"
            data-alert-close
        >
            <span class="material-symbols-outlined text-[1.25rem]" aria-hidden="true">close</span>
        </button>
    </div>
</aside>
@endif


