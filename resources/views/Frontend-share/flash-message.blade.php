@php
    $frontendMessages = session(MEMBER_MESSAGE_SESSION, []);
    session()->forget(MEMBER_MESSAGE_SESSION);
@endphp
@if (is_array($frontendMessages) && !empty($frontendMessages))
    <div class="fe-flash-area" role="status" aria-live="polite" data-frontend-flash>
        @foreach ($frontendMessages as $message)
            @php
                $type = $message['type'] ?? 'info';
                $toneClass = match ($type) {
                    'success' => 'fe-flash-success',
                    'danger' => 'fe-flash-danger',
                    'warning' => 'fe-flash-warning',
                    default => 'fe-flash-info',
                };
            @endphp
            <div
                class="fe-flash-item {{ $toneClass }}"
                role="button"
                tabindex="0"
                aria-label="點擊關閉訊息"
                data-frontend-flash-item
            >
                <span class="fe-flash-text">{{ $message['message'] ?? '' }}</span>
                <span class="material-symbols-outlined fe-flash-close-icon" aria-hidden="true">close</span>
            </div>
        @endforeach
    </div>
@endif
