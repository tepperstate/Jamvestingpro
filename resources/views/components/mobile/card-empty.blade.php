@props(['title', 'message', 'actionText' => null, 'actionUrl' => '#', 'icon' => null])

<div class="ma-card" style="text-align: center; padding: 40px 20px;">
    @if($icon)
    <div style="margin-bottom: 20px; color: var(--text-secondary); display: flex; justify-content: center;">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--bg-color); display: flex; align-items: center; justify-content: center;">
            {!! $icon !!}
        </div>
    </div>
    @endif
    <div style="font-weight: 600; font-size: 18px; margin-bottom: 8px;">{{ $title }}</div>
    <div class="text-sm" style="margin-bottom: 24px;">{{ $message }}</div>
    
    @if($actionText)
    <a href="{{ $actionUrl }}" class="btn" style="display: inline-block; width: auto;">{{ $actionText }}</a>
    @endif
</div>
