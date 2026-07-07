@props(['title', 'subtitle' => '', 'status' => '', 'statusColor' => 'primary', 'actions' => ''])

<div class="mobile-bezel-outer">
    <div class="mobile-bezel-inner flex justify-between items-center">
        <div class="flex-col">
            <h6 class="mb-1 text-white font-weight-bold" style="font-size: 16px;">{{ $title }}</h6>
            @if($subtitle)
                <div class="text-sm">{{ $subtitle }}</div>
            @endif
        </div>
        
        <div class="flex items-center gap-3">
            @if($status)
                <span class="eyebrow-tag eyebrow-{{ $statusColor }}">{{ $status }}</span>
            @endif
            
            @if($actions)
                <div class="actions">
                    {{ $actions }}
                </div>
            @endif
        </div>
    </div>
    
    @if(isset($details))
        <div class="mobile-bezel-inner mt-2" style="background: rgba(0,0,0,0.4); padding: 12px 16px; border-radius: calc(2rem - 0.7rem);">
            {{ $details }}
        </div>
    @endif
</div>
