@props([
    'type' => 'info',
    'message',
    'ctaText' => null,
    'ctaUrl' => null,
    'dismissible' => true,
    'id' => null
])

@php
    $bannerId = $id ?? 'banner-' . Str::random(8);
@endphp

<div class="jv-banner jv-banner-{{ $type }}" id="jv-banner-{{ $bannerId }}" role="alert" style="display: none;">
    <div class="jv-banner-icon">
        @if($type === 'success')
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        @elseif($type === 'warning')
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" x2="12" y1="9" y2="13"></line><line x1="12" x2="12.01" y1="17" y2="17"></line></svg>
        @elseif($type === 'error')
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" x2="9" y1="9" y2="15"></line><line x1="9" x2="15" y1="9" y2="15"></line></svg>
        @else
            <!-- info -->
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" x2="12" y1="16" y2="12"></line><line x1="12" x2="12.01" y1="8" y2="8"></line></svg>
        @endif
    </div>
    <div class="jv-banner-content">
        {{ $message }}
    </div>
    @if($ctaText && $ctaUrl)
        <a href="{{ $ctaUrl }}" class="jv-banner-cta">{{ $ctaText }}</a>
    @endif
    @if($dismissible)
        <button type="button" class="jv-banner-close" onclick="dismissJvBanner('{{ $bannerId }}')" aria-label="Dismiss banner">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" x2="6" y1="6" y2="18"></line><line x1="6" x2="18" y1="6" y2="18"></line></svg>
        </button>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bannerId = '{{ $bannerId }}';
        if (!sessionStorage.getItem('jv_banner_dismissed_' + bannerId)) {
            document.getElementById('jv-banner-' + bannerId).style.display = 'flex';
        }
    });

    function dismissJvBanner(id) {
        const el = document.getElementById('jv-banner-' + id);
        if (el) {
            el.style.display = 'none';
            sessionStorage.setItem('jv_banner_dismissed_' + id, '1');
        }
    }
</script>
