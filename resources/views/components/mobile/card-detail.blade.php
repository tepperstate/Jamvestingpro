@props(['title'])

<div class="ma-card" style="margin-bottom: 8px;">
    @if($title)
    <div class="text-sm" style="margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">{{ $title }}</div>
    @endif
    <div class="flex flex-col gap-3">
        {{ $slot }}
    </div>
</div>
