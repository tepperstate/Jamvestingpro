@props(['title' => null, 'icon' => null, 'subtitle' => null, 'status' => null, 'statusColor' => 'var(--text-secondary)', 'rightText' => null, 'swipeLeft' => null, 'swipeRight' => null])

<div class="ma-card swipeable-card" style="padding: 0;">
    <div class="swipe-actions left" style="position: absolute; left: 0; top: 0; bottom: 0; width: 80px; display: flex; align-items: center; justify-content: center; background: var(--success-tint); z-index: 1;">
        {!! $swipeLeft ?? 'Action' !!}
    </div>
    
    <div class="swipe-actions right" style="position: absolute; right: 0; top: 0; bottom: 0; width: 80px; display: flex; align-items: center; justify-content: center; background: var(--danger-tint); z-index: 1;">
        {!! $swipeRight ?? 'Delete' !!}
    </div>

    <div class="swipe-content flex items-center justify-between" style="position: relative; z-index: 2; background: var(--card-bg); padding: 20px; border-radius: inherit; height: 100%;">
        <div class="flex items-center gap-3">
            @if($icon)
            <div class="avatar-circle" style="width: 40px; height: 40px; border-radius: 50%; background: #2A2A35; display: flex; align-items: center; justify-content: center;">
                {!! $icon !!}
            </div>
            @endif
            <div>
                <div style="font-weight: 600; font-size: 15px;">{{ $title }}</div>
                @if($subtitle)
                <div class="text-sm" style="margin-top: 2px;">{{ $subtitle }}</div>
                @endif
            </div>
        </div>
        <div class="flex flex-col" style="align-items: flex-end;">
            @if($rightText)
            <div class="text-sm">{{ $rightText }}</div>
            @endif
            @if($status)
            <div style="font-size: 12px; margin-top: 4px; color: {{ $statusColor }};">{{ $status }}</div>
            @endif
        </div>
    </div>
</div>
