@props([
    'variant' => 'light',
    'size' => 'md',
    'class' => ''
])

@php
    $height = match($size) {
        'xs' => '24px',
        'sm' => '32px',
        'md' => '40px',
        'lg' => '56px',
        'xl' => '80px',
        default => '40px'
    };
    
    // Logo resolution
    $mainLogo = site()->logo;
    
    if ($variant === 'dark') {
        if ($mainLogo === 'logo.svg') {
            $logoPath = asset('storage/image/logo_dark.svg');
        } else {
            $logoPath = $mainLogo ? asset('storage/image/' . $mainLogo) : asset('assets/images/logo_dark.svg');
        }
    } else {
        $logoPath = $mainLogo ? asset('storage/image/' . $mainLogo) : asset('assets/images/logo.svg');
    }
@endphp

<img src="{{ $logoPath }}" alt="{{ site()->name ?? 'Platform' }} Logo" style="height: {{ $height }}; width: auto; object-fit: contain;" class="jv-logo {{ $class }}">
