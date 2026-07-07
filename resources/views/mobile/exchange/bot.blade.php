@extends('layouts.user.app')
@section('title', 'Smart Portfolios')
@section('content')

<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<style>
.mobile-bots-container {
    padding: 15px;
    background: #0b0e14;
    min-height: 100vh;
    padding-bottom: 90px;
}
.page-header-m {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.page-title-m {
    font-size: 1.5rem;
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    color: #fff;
    margin-bottom: 0;
}
.page-desc-m {
    font-size: 0.8rem;
    color: #94a3b8;
    line-height: 1.4;
    margin-bottom: 20px;
}

.bot-card-m {
    background: rgba(16, 18, 27, 0.7);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.06);
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
/* Glowing orb */
.bot-card-m::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
    border-radius: 50%;
    z-index: 0;
}

.bot-card-m-content {
    position: relative;
    z-index: 1;
}

.bot-header-m {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}
.bot-icon-m {
    width: 48px; height: 48px;
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
}
.bot-icon-m img {
    width: 100%; height: 100%; object-fit: cover;
}
.tier-badge-m {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: #94a3b8;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 4px 10px;
    border-radius: 8px;
    letter-spacing: 1px;
}

.bot-stats-row {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}
.bot-stat-box {
    flex: 1;
    background: rgba(0,0,0,0.3);
    border-radius: 12px;
    padding: 10px;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.03);
}
.bot-stat-lbl { font-size: 0.6rem; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 3px; }
.bot-stat-val { font-size: 1rem; font-weight: 800; color: #fff; font-family: 'Outfit', sans-serif; display: flex; align-items: center; justify-content: center; gap: 4px; }

.bot-footer-m {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 15px;
    border-top: 1px solid rgba(255,255,255,0.05);
}
.cost-lbl { font-size: 0.65rem; color: #64748b; }
.cost-val { font-size: 1.4rem; font-weight: 800; color: #fff; font-family: 'Outfit', sans-serif; }

.btn-activate-m {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 10px 15px;
    font-weight: 700;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}
.btn-activate-m:active { transform: scale(0.95); }
</style>

<div class="mobile-bots-container">
    <div class="page-header-m">
        <h1 class="page-title-m">Smart Portfolios <span class="text-primary">AI</span></h1>
        <a href="{{ route('bots.user') }}" class="btn btn-sm" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px;">
            <i class="ri-robot-line"></i> My Bots
        </a>
    </div>
    <p class="page-desc-m">Automated neural strategies for optimal execution.</p>

    <div class="d-flex mb-4 gap-2">
        <a href="{{ route('bots.history') }}" class="btn w-100" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2); color: #60a5fa; border-radius: 10px; font-weight: bold; font-size: 0.85rem;">
            <i class="ri-history-line me-1"></i> Bot History
        </a>
    </div>

    @foreach($bots as $index => $bot)
    <div class="bot-card-m">
        <div class="bot-card-m-content">
            <div class="bot-header-m">
                <div class="bot-icon-m">
                    <img src="{{ asset('storage/image/' . $bot->image) }}" alt="{{ $bot->name }}" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($bot->name) }}&background=random&color=fff&size=48&rounded=true'">
                </div>
                <div class="tier-badge-m">
                    @if($bot->max >= 5000) PRO @elseif($bot->max >= 1000) PREMIUM @else STARTER @endif
                </div>
            </div>

            <h4 class="outfit font-weight-bold text-white mb-2">{{ $bot->name }}</h4>
            <p class="text-secondary small mb-3">AI-optimized for <span class="text-white">{{ $bot->type ?? 'Market' }}</span> trends using high-frequency {{ $bot->day ?? '5' }}-minute cycles.</p>

            <div class="bot-stats-row">
                <div class="bot-stat-box">
                    <div class="bot-stat-lbl">Win Rate</div>
                    <div class="bot-stat-val text-success">
                        <i class="ri-arrow-right-up-line"></i> {{ $bot->win ?? '92' }}%
                    </div>
                </div>
                <div class="bot-stat-box">
                    <div class="bot-stat-lbl">Max Cap</div>
                    <div class="bot-stat-val text-primary">
                        <i class="ri-safe-line"></i> ${{ number_format($bot->max) }}
                    </div>
                </div>
            </div>

            <div class="bot-footer-m">
                <div>
                    <div class="cost-lbl">Setup Cost</div>
                    <div class="cost-val">${{ number_format($bot->amount) }}</div>
                </div>
                <button type="button" class="btn-activate-m activate-btn shadow-sm" data-id="{{ $bot->id }}" data-name="{{ $bot->name }}" data-amount="{{ $bot->amount }}">
                    Activate <i class="ri-flashlight-fill"></i>
                </button>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $bots->links('pagination::bootstrap-4') }}
    </div>
</div>

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
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#ef4444',
            backdrop: 'rgba(0,0,0,0.8)',
            confirmButtonText: 'Activate Now'
        }).then((result) => {
            if(result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Deploying...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                    background: '#10121b',
                    color: '#fff'
                });

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
                            background: '#10121b',
                            color: '#fff',
                            confirmButtonColor: '#3b82f6',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        setTimeout(() => window.location.href = "{{ route('bots.user') }}", 1500);
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.error,
                            icon: 'error',
                            background: '#10121b',
                            color: '#fff',
                            confirmButtonColor: '#3b82f6'
                        });
                    }
                })
                .catch(err => {
                    console.error("Bot activation error:", err);
                    Swal.fire({
                        title: 'Error',
                        text: 'Network error. Please try again.',
                        icon: 'error',
                        background: '#10121b',
                        color: '#fff',
                        confirmButtonColor: '#3b82f6'
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
</script>
@endpush
