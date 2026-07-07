<div>
    <div class="min-vh-100 d-flex flex-column align-items-center justify-content-center" style="background: var(--bg-deep, #000000); position: relative; overflow: hidden;">
        
        <!-- Abstract Background -->
        <div class="position-absolute w-100 h-100" style="z-index: 0; top: 0; left: 0;">
            <div style="position: absolute; top: -10%; left: -10%; width: 50%; height: 50%; background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%); border-radius: 50%; filter: blur(60px);"></div>
            <div style="position: absolute; bottom: -10%; right: -10%; width: 50%; height: 50%; background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, transparent 70%); border-radius: 50%; filter: blur(60px);"></div>
        </div>

        <div class="container position-relative z-index-1" style="max-width: 800px; z-index: 1;">
            
            @if($currentQuestion)
            <div class="text-center mb-5" wire:key="header-{{ $currentQuestion->id }}">
                <h1 class="display-5 font-weight-bold text-white mb-3" style="font-family: 'Outfit', sans-serif;">{{ $currentQuestion->title }}</h1>
                @if($currentQuestion->subtitle)
                    <p class="lead text-secondary mx-auto" style="max-width: 600px;">{{ $currentQuestion->subtitle }}</p>
                @endif
            </div>

            <div class="row justify-content-center g-4" wire:key="options-{{ $currentQuestion->id }}">
                @foreach($currentQuestion->options as $option)
                    <div class="col-md-{{ count($currentQuestion->options) <= 2 ? '6' : '4' }} mb-4">
                        <div 
                            class="glass-card p-4 h-100 text-center cursor-pointer transition-all onboarding-card"
                            wire:click="selectOption({{ $currentQuestion->id }}, '{{ $option->value }}')"
                            style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 20px; transition: all 0.3s ease; cursor: pointer;"
                            onmouseover="this.style.background='rgba(59, 130, 246, 0.1)'; this.style.borderColor='rgba(59, 130, 246, 0.4)'; this.style.transform='translateY(-5px)';"
                            onmouseout="this.style.background='rgba(255, 255, 255, 0.03)'; this.style.borderColor='rgba(255, 255, 255, 0.08)'; this.style.transform='translateY(0)';"
                        >
                            @if($option->icon)
                                <div class="icon-wrapper mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; border-radius: 50%; background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                                    <i class="ri-{{ $option->icon }}-line fs-2"></i>
                                </div>
                            @endif
                            <h4 class="text-white font-weight-bold mb-0" style="font-size: 1.1rem;">{{ $option->label }}</h4>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-5">
                <a href="#" wire:click.prevent="skip" class="text-secondary text-decoration-none" style="font-size: 0.95rem; opacity: 0.7; transition: opacity 0.3s ease;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.7'">
                    Skip for now — I'll use a standard retail account.
                </a>
            </div>
            @else
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
