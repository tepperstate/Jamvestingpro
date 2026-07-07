@props(['title', 'actionText' => 'Submit', 'cancelText' => 'Cancel', 'actionMethod' => 'post', 'actionUrl' => '#'])

<div class="ma-card">
    @if($title)
    <div style="font-weight: 600; margin-bottom: 16px; font-size: 18px; border-bottom: 1px solid var(--card-border); padding-bottom: 12px;">
        {{ $title }}
    </div>
    @endif
    <form action="{{ $actionUrl }}" method="{{ $actionMethod }}">
        {{ $slot }}
        <div class="flex gap-3" style="margin-top: 24px;">
            <button type="button" class="btn btn-secondary" onclick="window.closeSheet()">{{ $cancelText }}</button>
            <button type="submit" class="btn">{{ $actionText }}</button>
        </div>
    </form>
</div>
