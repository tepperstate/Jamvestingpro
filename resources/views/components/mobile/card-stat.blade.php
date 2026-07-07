@props(['title', 'value', 'trend' => null, 'trendUp' => true, 'tint' => null, 'icon' => null])

<div class="ma-card {{ $tint ? 'tint-'.$tint : '' }}">
    @if($icon)
    <div class="ma-card-icon" style="margin-bottom: 8px; color: var(--text-secondary);">
        {!! $icon !!}
    </div>
    @endif
    <div class="text-sm">{{ $title }}</div>
    <div class="text-lg" style="margin-top: 4px;">{{ $value }}</div>
    @if($trend)
    <div style="font-size: 11px; margin-top: 8px; color: {{ $trendUp ? 'var(--success-color)' : 'var(--danger-color)' }};">
        {{ $trendUp ? '↑' : '↓' }} {{ $trend }}
    </div>
    @endif
</div>
