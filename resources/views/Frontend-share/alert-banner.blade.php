@if (!empty($alertBanner['message']))
<aside
    id="frontend-alert-banner"
    class="fe-alert-banner"
    role="region"
    aria-label="系統公告"
>
    <div class="fe-container fe-alert-banner-inner">
        <span class="material-symbols-outlined fe-alert-banner-icon" aria-hidden="true">campaign</span>
        <div class="fe-alert-banner-body">
            <span class="fe-eyebrow">{{ $alertBanner['title'] ?? '系統公告' }}</span>
            <p class="fe-alert-banner-message">
                {{ $alertBanner['message'] ?? '' }}
                @if (!empty($alertBanner['link_label']))
                    <a href="{{ $alertBanner['link_url'] ?? '#' }}" class="fe-alert-banner-link" aria-label="{{ $alertBanner['link_label'] }}">
                        {{ $alertBanner['link_label'] }}
                    </a>
                @endif
            </p>
        </div>
        <button
            type="button"
            class="fe-icon-pill fe-alert-banner-close"
            aria-label="關閉系統公告"
            aria-expanded="true"
            data-alert-close
        >
            <span class="material-symbols-outlined" aria-hidden="true">close</span>
        </button>
    </div>
</aside>
@endif
