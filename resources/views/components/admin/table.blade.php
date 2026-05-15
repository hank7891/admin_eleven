@props([
    'bulk' => false,
])

<div class="admin-card admin-card-flush" @if($bulk) data-bulk-table @endif>
    @if (isset($head))
        <div class="admin-section-head">{{ $head }}</div>
    @endif
    @if (isset($toolbar))
        <div class="admin-bulk-toolbar" data-bulk-toolbar>
            {{ $toolbar }}
        </div>
    @endif
    <div class="admin-table-wrap">
        <table {{ $attributes->merge(['class' => 'admin-table']) }}>
            <thead>{{ $thead }}</thead>
            <tbody>{{ $slot }}</tbody>
        </table>
    </div>
    @if (isset($foot))
        {{ $foot }}
    @endif
</div>
