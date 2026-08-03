@props([
    /** @var array<int, array{label: string, value: int|null, display?: string, title?: string}> */
    'bars',
    'color' => 'var(--bs-primary)',
    'height' => '260px',
])

@php
    $max = collect($bars)->max('value') ?: 0;
@endphp

<div class="bar-chart" style="height: {{ $height }};">
    @foreach($bars as $bar)
        <div class="bar-chart-column">
            <div class="small fw-semibold">{{ $bar['display'] ?? $bar['value'] }}</div>

            <div class="bar-chart-bar @if(! $bar['value']) bar-chart-bar-empty @endif"
                 style="height: {{ $max > 0 ? round(($bar['value'] ?? 0) / $max * 100) : 0 }}%; @if($bar['value']) background-color: {{ $color }}; @endif"
                 title="{{ $bar['title'] ?? "{$bar['label']}: ".($bar['display'] ?? $bar['value'] ?? '-') }}"></div>
        </div>
    @endforeach
</div>

<div class="d-flex gap-2" style="overflow-x: auto;">
    @foreach($bars as $bar)
        <div class="text-center text-muted" style="flex: 1 0 48px; font-size: .7rem;">{{ $bar['label'] }}</div>
    @endforeach
</div>
