@extends('layouts.user.app')

@section('title', 'Smart Portfolios')

@section('content')
<style>
@media (max-width: 767.98px) {
    .mobile-cards-view {
        display: flex !important;
    }
}
</style>
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-down">
        <div>
            <h1 class="outfit display-5 mb-1 text-white fw-bold" style="letter-spacing: -1px;">Smart Portfolios <span class="text-primary">AI</span></h1>
            <p class="text-secondary mb-0" style="font-size: 1.1rem;">Automated neural strategies for optimal execution</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('bots.user') }}" class="btn btn-outline-primary rounded-pill px-4">My Bots</a>
            <a href="{{ route('bots.history') }}" class="btn btn-outline-secondary rounded-pill px-4">History</a>
            <div class="glass-badge px-4 py-2 rounded-pill d-flex align-items-center gap-2 d-none d-md-flex" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
                <div class="pulse-dot bg-primary rounded-circle" style="width: 8px; height: 8px; box-shadow: 0 0 10px #3b82f6;"></div>
                <span class="text-primary fw-bold small tracking-wide">ALGORITHMS ONLINE</span>
            </div>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="d-none d-md-block mb-5">
        <div class="glass-card p-0 overflow-hidden shadow-2xl" style="border-radius: 24px; background: rgba(16, 18, 27, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.05);">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-white table-borderless" style="background: transparent;">
                    <thead style="background: rgba(0,0,0,0.4); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <tr>
                            <th class="px-4 py-4 text-secondary small text-uppercase tracking-wide">Algorithm</th>
                            <th class="py-4 text-secondary small text-uppercase tracking-wide">Performance (W/L)</th>
                            <th class="py-4 text-secondary small text-uppercase tracking-wide">Execution Cycle</th>
                            <th class="py-4 text-secondary small text-uppercase tracking-wide">Max Cap</th>
                            <th class="py-4 text-secondary small text-uppercase tracking-wide">Setup Cost</th>
                            <th class="px-4 py-4 text-end text-secondary small text-uppercase tracking-wide">Action</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: none;">
                        @foreach($bots as $bot)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.3s ease;" class="bot-table-row">
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-glow-box d-flex align-items-center justify-content-center rounded-circle overflow-hidden" style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
                                        <img src="{{ asset('storage/image/' . $bot->image) }}" alt="{{ $bot->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($bot->name) }}&background=random&color=fff&size=48&rounded=true'">
                                    </div>
                                    <div>
                                        <div class="outfit fw-bold h6 mb-1">{{ $bot->name }}</div>
                                        <div class="badge bg-dark text-secondary border border-secondary border-opacity-25 rounded-pill px-2 py-0 fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">
                                            @if($bot->max >= 5000) PRO TIER @elseif($bot->max >= 1000) PREMIUM @else STARTER @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-arrow-right-up-line text-success"></i>
                                    <span class="text-white outfit fw-bold">{{ $bot->win ?? '92' }}%</span>
                                </div>
                            </td>
                            <td class="py-3 text-secondary small">
                                {{ $bot->day ?? '5' }} mins
                            </td>
                            <td class="py-3">
                                <div class="text-white outfit fw-bold">${{ number_format($bot->max) }}</div>
                            </td>
                            <td class="py-3">
                                <div class="text-white outfit fw-bold">${{ number_format($bot->amount) }}</div>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 activate-btn shadow-sm" data-id="{{ $bot->id }}" data-name="{{ $bot->name }}" data-amount="{{ $bot->amount }}" style="transition: all 0.3s ease; font-size: 0.85rem;">
                                    Activate <i class="ri-flashlight-fill"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mobile Cards View -->
    <div class="row g-4 mb-5 mobile-cards-view d-md-none">
        @foreach($bots as $index => $bot)
        <div class="col-12 mb-4">
            <div class="bot-card h-100 position-relative overflow-hidden" style="border-radius: 24px; background: rgba(16, 18, 27, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.05); transition: all 0.4s ease;">
                <!-- Glowing Orb Background -->
                <div class="position-absolute top-0 end-0 rounded-circle" style="width: 150px; height: 150px; background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%); transform: translate(30%, -30%);"></div>
                
                <div class="p-4 d-flex flex-column h-100 position-relative z-1">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-glow-box d-flex align-items-center justify-content-center rounded-circle overflow-hidden" style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
                                <img src="{{ asset('storage/image/' . $bot->image) }}" alt="{{ $bot->name }}" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($bot->name) }}&background=random&color=fff&size=48&rounded=true'">
                            </div>
                            <div class="badge bg-dark text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem; letter-spacing: 1px;">
                                @if($bot->max >= 5000) PRO TIER @elseif($bot->max >= 1000) PREMIUM @else STARTER @endif
                            </div>
                        </div>
                    </div>

                    <h3 class="outfit fw-bold text-white mb-2">{{ $bot->name }}</h3>
                    <p class="text-secondary small mb-4 line-clamp-2" style="line-height: 1.6;">AI-optimized for <span class="text-white">{{ $bot->type ?? 'Market' }}</span> trends using high-frequency {{ $bot->day ?? '5' }}-minute execution cycles.</p>

                    <div class="row g-3 mb-4 mt-auto">
                        <div class="col-6">
                            <div class="stat-box p-3 rounded-4" style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.03);">
                                <div class="text-secondary text-uppercase mb-1 fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Win Rate</div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-arrow-right-up-line text-success"></i>
                                    <span class="h5 mb-0 text-white outfit fw-bold">{{ $bot->win ?? '92' }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box p-3 rounded-4" style="background: rgba(0,0,0,0.4); border: 1px solid rgba(255,255,255,0.03);">
                                <div class="text-secondary text-uppercase mb-1 fw-bold" style="font-size: 0.65rem; letter-spacing: 1px;">Max Cap</div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-safe-line text-primary"></i>
                                    <span class="h5 mb-0 text-white outfit fw-bold">${{ number_format($bot->max) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top border-secondary border-opacity-10 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-secondary small" style="font-size: 0.75rem;">Setup Cost</div>
                            <div class="h3 outfit fw-bold text-white mb-0">${{ number_format($bot->amount) }}</div>
                        </div>
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold d-flex align-items-center gap-2 activate-btn shadow-sm" data-id="{{ $bot->id }}" data-name="{{ $bot->name }}" data-amount="{{ $bot->amount }}" style="transition: all 0.3s ease;">
                            Activate <i class="ri-flashlight-fill"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $bots->links() }}
    </div>
</div>

<style>
    .bot-card:hover {
        transform: translateY(-8px);
        border-color: rgba(59, 130, 246, 0.4) !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 20px rgba(59, 130, 246, 0.15) !important;
    }
    
    .bot-table-row:hover {
        background: rgba(59, 130, 246, 0.05);
    }
    
    .activate-btn:hover {
        background: #2563eb !important;
        transform: scale(1.05);
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.5) !important;
    }

    .pulse-dot {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(59, 130, 246, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }
    
    .tracking-wide { letter-spacing: 0.05em; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>

@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function purchaseBot(id, name, amount) {
        Swal.fire({
            title: 'Deploy Algorithm?',
            text: `Would you like to activate the ${name} algorithm for $${amount}?`,
            icon: 'question',
            showCancelButton: true,
            background: 'var(--glass-bg, #10121b)',
            color: '#fff',
            confirmButtonColor: 'var(--accent-primary, #3b82f6)',
            cancelButtonColor: '#ef4444',
            backdrop: 'rgba(0,0,0,0.8)',
            confirmButtonText: 'Activate Now'
        }).then((result) => {
            if(result.isConfirmed) {
                fetch("{{ route('bot.post') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({id, name, amount})
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.status,
                            icon: 'success',
                            background: 'var(--glass-bg, #10121b)',
                            color: '#fff',
                            confirmButtonColor: 'var(--accent-primary, #3b82f6)',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => window.location.href = "{{ route('bots.user') }}", 1500);
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.error,
                            icon: 'error',
                            background: 'var(--glass-bg, #10121b)',
                            color: '#fff',
                            confirmButtonColor: 'var(--accent-primary, #3b82f6)'
                        });
                    }
                })
                .catch(err => {
                    console.error("Bot activation error:", err);
                    Swal.fire({
                        title: 'Error',
                        text: 'A network error occurred. Please try again.',
                        icon: 'error',
                        background: 'var(--glass-bg, #10121b)',
                        color: '#fff',
                        confirmButtonColor: 'var(--accent-primary, #3b82f6)'
                    });
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('click', function(e) {
            let btn = e.target.closest('.activate-btn');
            if (btn) {
                e.preventDefault();
                let id = btn.getAttribute('data-id');
                let name = btn.getAttribute('data-name');
                let amount = btn.getAttribute('data-amount');
                purchaseBot(id, name, amount);
            }
        });
    });

    window.addEventListener('load', () => {
        if (typeof anime !== 'undefined') {
            anime({
                targets: ['.bot-card', '.bot-table-row'],
                translateY: [30, 0],
                opacity: [0, 1],
                delay: anime.stagger(100),
                easing: 'easeOutExpo',
                duration: 800
            });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endpush

