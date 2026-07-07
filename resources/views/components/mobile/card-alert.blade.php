@props(['title', 'message', 'type' => 'warning', 'dismissible' => true])

@php
    $borderColor = match($type) {
        'danger' => 'var(--danger-color)',
        'success' => 'var(--success-color)',
        'warning' => 'var(--warning-color)',
        default => 'var(--accent-color)'
    };
@endphp

<div class="ma-card" style="border-left: 4px solid {{ $borderColor }}; display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <div style="font-weight: 600; margin-bottom: 4px;">{{ $title }}</div>
        <div class="text-sm" style="line-height: 1.4;">{{ $message }}</div>
        @if(isset($actions))
        <div class="flex gap-2" style="margin-top: 12px;">
            {{ $actions }}
        </div>
        @endif
    </div>
    @if($dismissible)
    <button style="background: none; border: none; color: var(--text-secondary); padding: 4px;" onclick="this.closest('.ma-card').remove()">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
    </button>
    @endif
</div>
