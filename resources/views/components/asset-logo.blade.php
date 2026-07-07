@props(['symbol', 'size' => 'md', 'class' => '', 'imgId' => null, 'image' => null, 'assetType' => null])

@php
    $dimensions = [
        'xs' => ['box' => 20, 'img' => 18],
        'sm' => ['box' => 38, 'img' => 28],
        'md' => ['box' => 40, 'img' => 32],
        'lg' => ['box' => 56, 'img' => 48],
        'xl' => ['box' => 72, 'img' => 64],
    ];

    $dim = $dimensions[$size] ?? $dimensions['md'];
    $finalImgId = $imgId ?? 'logo_' . \Illuminate\Support\Str::random(8);
    
    $resolvedType = $assetType;
    if (!$resolvedType && $symbol) {
        $cleanSymbol = strtoupper(trim(preg_replace('/[^a-zA-Z0-9.\/-]/', '', $symbol)));
        $assetTypesMap = cache()->remember('global_asset_types_map', 3600, function() {
            return \App\Models\Asset::pluck('type', 'symbols')->toArray();
        });
        $resolvedType = $assetTypesMap[$cleanSymbol] ?? 'unknown';
    }
    
    $initialSrc = $symbol ? \App\Services\AssetLogoService::getLogoUrl($symbol, $resolvedType ?? 'unknown', $image ?? '') : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1 1'%3E%3C/svg%3E";
@endphp

<div class="asset-logo-container {{ $class }}" 
     style="width: {{ $dim['box'] }}px; height: {{ $dim['box'] }}px; background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 50% !important; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px) saturate(180%); overflow: hidden; position: relative; box-shadow: inset 0 0 12px rgba(255,255,255,0.02);">
    
    <img id="{{ $finalImgId }}"
         src="{{ $initialSrc }}" 
         alt="{{ $symbol }}"
         loading="lazy"
         width="{{ $dim['img'] }}"
         height="{{ $dim['img'] }}"
         style="object-fit: contain !important; border-radius: 0 !important; transition: all 0.3s ease; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));"
         onerror="let fallback='{{ \App\Services\AssetLogoService::getFallbackUrl($symbol) }}'; if(this.src !== fallback) { this.src=fallback; } this.onerror=null;">

</div>

<style>
    .asset-logo-container:hover img {
        transform: scale(1.1);
    }
</style>
