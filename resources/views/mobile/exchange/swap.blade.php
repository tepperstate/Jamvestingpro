@extends('layouts.user.app')

@section('title', 'Swap')

@section('content')
@php
    // Normalize user balances and admin supported wallets into a single cohesive collection
    $user_assets = collect($data)->map(function($item) {
        $item->balance = (float)($item->amount ?? 0);
        return $item;
    });

    $all_assets = collect();
    
    // 1. Inject supported assets and link user holdings
    foreach($admin_wallets as $wallet) {
        $user_bal = $user_assets->where('symbol', $wallet->symbol)->first();
        $wallet->balance = $user_bal ? $user_bal->balance : 0;
        $all_assets->push($wallet);
    }
    
    // 2. Inject existing user balances not covered by defaults (e.g., USD, custom tokens)
    foreach($user_assets as $asset) {
        if(!$all_assets->contains('symbol', $asset->symbol)) {
            $all_assets->push($asset);
        }
    }

    // 3. Initialize state with primary trading pair
    $first_coin = $all_assets->first() ?? (object)['symbol' => 'USD', 'balance' => 0, 'name' => 'US Dollar'];
    $defaultTo = $all_assets->where('symbol', '!=', $first_coin->symbol)->first() ?? $first_coin;
@endphp

<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<div class="mobile-swap-container pt-3 pb-5 px-3 position-relative overflow-hidden">
    <!-- Decorative Gold Glows -->
    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="pointer-events: none; z-index: 0;">
        <div class="position-absolute" style="top: -50px; right: -50px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(153, 0, 0, 0.15) 0%, transparent 70%);"></div>
        <div class="position-absolute" style="bottom: 100px; left: -50px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(153, 0, 0, 0.1) 0%, transparent 70%);"></div>
    </div>

    <div class="stagger-in position-relative" style="z-index: 1;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="outfit fw-bold text-white mb-0" style="font-size: 1.8rem;">Swap</h2>
                <p class="small mb-0 text-gold opacity-75">Instant Conversion</p>
            </div>
            <div class="p-2 rounded-circle glass-panel d-flex align-items-center justify-content-center text-gold" style="width: 40px; height: 40px;">
                <i class="ri-exchange-funds-line fs-5"></i>
            </div>
        </div>

        <form action="{{ route('swap.coin') }}" method="POST" id="swap-form" class="position-relative">
            @csrf
            
            <!-- From Section -->
            <div class="swap-box glass-panel p-3 mb-2 rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-white-50 small fw-medium text-uppercase tracking-wider" style="font-size: 0.7rem;">You Pay</span>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-white-50 small" style="font-size: 0.75rem;">Bal: <span class="text-white fw-bold" id="from-balance">{{ number_format($first_coin->balance, 6) }}</span></span>
                        <button type="button" class="btn btn-gold-soft btn-sm px-2 py-0 fw-bold rounded" onclick="setMax()" style="font-size: 0.65rem; height: 20px;">MAX</button>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="number" name="amount" id="input-from" step="any" class="form-control border-0 bg-transparent p-0 outfit text-white flex-grow-1 fs-1 fw-bold" placeholder="0.00" required style="box-shadow: none; min-width: 0;">
                    
                    <div class="select-wrapper" id="from-select-wrapper">
                        <input type="hidden" name="from" id="select-from" value="{{ $first_coin->symbol }}">
                        <div class="custom-select-trigger glass-panel-gold px-2 py-2 d-flex align-items-center justify-content-between gap-2 cursor-pointer rounded-pill">
                            <div class="d-flex align-items-center gap-2" id="from-selected">
                                <x-asset-logo :symbol="$first_coin->symbol" size="24" />
                                <span class="fw-bold text-white">{{ $first_coin->symbol }}</span>
                            </div>
                            <i class="ri-arrow-down-s-line text-white-50"></i>
                        </div>
                        
                        <div id="from-options" class="custom-options glass-panel position-absolute mt-2 p-2 rounded-4 shadow-lg" style="display: none; z-index: 1000; min-width: 180px; max-height: 250px; overflow-y: auto; right: 0;">
                            @foreach($all_assets as $coin)
                            <div class="custom-option p-2 d-flex align-items-center gap-3 rounded-3 cursor-pointer mb-1" data-value="{{ $coin->symbol }}" data-amount="{{ $coin->balance }}" data-icon="{{ strtolower($coin->symbol) }}">
                                <x-asset-logo :symbol="$coin->symbol" size="28" />
                                <div class="d-flex flex-column">
                                    <span class="text-white fw-bold lh-1">{{ $coin->symbol }}</span>
                                    <span class="text-white-50" style="font-size: 0.7rem;">{{ number_format($coin->balance, 4) }}</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reverse Button -->
            <div class="text-center position-relative my-n3" style="z-index: 10;">
                <button type="button" class="btn rounded-circle p-0 shadow-lg floating-element gold-gradient-bg border-dark-thick" onclick="reverseSwap()" style="width: 44px; height: 44px; border: 4px solid #0f131a;">
                    <i class="ri-arrow-up-down-line fs-5 text-dark fw-bold"></i>
                </button>
            </div>

            <!-- To Section -->
            <div class="swap-box glass-panel p-3 mt-2 mb-4 rounded-4" style="background: rgba(0,0,0,0.2);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-white-50 small fw-medium text-uppercase tracking-wider" style="font-size: 0.7rem;">You Receive</span>
                    <div class="d-flex align-items-center gap-1">
                        <div class="pulse-glow rounded-circle bg-gold" style="width: 5px; height: 5px;"></div>
                        <span class="text-gold small fw-bold" style="font-size: 0.7rem;">Market Rate</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="number" name="amount_to" id="input-to" step="any" class="form-control border-0 bg-transparent p-0 outfit text-white flex-grow-1 fs-1 fw-bold" placeholder="0.00" readonly style="box-shadow: none; min-width: 0;">
                    
                    <div class="select-wrapper position-relative" id="to-select-wrapper">
                        <input type="hidden" name="to" id="select-to" value="{{ $defaultTo->symbol }}">
                        <div class="custom-select-trigger glass-panel-gold px-2 py-2 d-flex align-items-center justify-content-between gap-2 cursor-pointer rounded-pill">
                            <div class="d-flex align-items-center gap-2" id="to-selected">
                                <x-asset-logo :symbol="$defaultTo->symbol" size="24" />
                                <span class="fw-bold text-white">{{ $defaultTo->symbol }}</span>
                            </div>
                            <i class="ri-arrow-down-s-line text-white-50"></i>
                        </div>
                        
                        <div id="to-options" class="custom-options glass-panel position-absolute mt-2 p-2 rounded-4 shadow-lg" style="display: none; z-index: 10000; min-width: 180px; max-height: 250px; overflow-y: auto; right: 0;">
                            @foreach($all_assets as $coin)
                            <div class="custom-option p-2 d-flex align-items-center gap-3 rounded-3 cursor-pointer mb-1" data-value="{{ $coin->symbol }}" data-icon="{{ strtolower($coin->symbol) }}">
                                <x-asset-logo :symbol="$coin->symbol" size="28" />
                                <span class="text-white fw-bold">{{ $coin->symbol }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Section -->
            <div class="glass-panel p-3 mb-4 rounded-4" style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(153,0,0,0.2);">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white-50 small" style="font-size: 0.8rem;">Exchange Rate</span>
                    <span class="text-white small fw-bold" style="font-size: 0.8rem;">1 <span class="rate-from-label">{{ $first_coin->symbol }}</span> = <span id="rate-display">...</span> <span class="rate-to-label">{{ $defaultTo->symbol }}</span></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white-50 small" style="font-size: 0.8rem;">Protocol Fee</span>
                    <span class="text-white small fw-bold" style="font-size: 0.8rem;">0.50%</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-white-50 small" style="font-size: 0.8rem;">Slippage Tolerance</span>
                    <span class="text-gold small fw-bold" style="font-size: 0.8rem;">Auto</span>
                </div>
            </div>

            <button type="submit" class="btn w-100 py-3 rounded-pill fw-bold text-dark shadow-lg fs-5 outfit gold-gradient-bg border-0">
                Confirm Swap
            </button>
        </form>
    </div>
</div>

<!-- Swap History Section -->
@if(isset($swap_history) && $swap_history->count() > 0)
<div class="mobile-history-container px-3 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-3 stagger-in">
        <h5 class="outfit fw-bold text-white mb-0">Recent Swaps</h5>
        <span class="text-white-50 small">{{ $swap_history->count() }} Records</span>
    </div>
    
    <div class="d-flex flex-column gap-2">
        @foreach($swap_history as $swap)
        <div class="glass-panel p-3 rounded-4 stagger-in position-relative overflow-hidden" style="border-left: 3px solid {{ $swap->status === 'completed' ? '#990000' : '#f59e0b' }};">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="text-white-50" style="font-size: 0.7rem;">
                    {{ \Carbon\Carbon::parse($swap->created_at)->format('M d, Y • h:i A') }}
                </div>
                <span class="badge rounded-pill fw-bold" style="font-size: 0.6rem; letter-spacing: 0.5px;
                    background: {{ $swap->status === 'completed' ? 'rgba(153,0,0,0.1)' : 'rgba(245,158,11,0.1)' }};
                    color: {{ $swap->status === 'completed' ? '#990000' : '#f59e0b' }};">
                    {{ strtoupper($swap->status) }}
                </span>
            </div>
            
            <div class="d-flex align-items-center justify-content-between mt-2">
                <div class="d-flex align-items-center gap-2">
                    <x-asset-logo :symbol="$swap->from_symbol" size="28" />
                    <div>
                        <div class="text-white fw-bold lh-1" style="font-size: 0.9rem;">{{ number_format($swap->from_amount, $swap->from_symbol === 'USD' ? 2 : 6) }}</div>
                        <div class="text-white-50 fw-medium" style="font-size: 0.7rem;">{{ $swap->from_symbol }}</div>
                    </div>
                </div>
                
                <div class="text-gold opacity-50 px-2">
                    <i class="ri-arrow-right-line"></i>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    <div class="text-end">
                        <div class="text-white fw-bold lh-1" style="font-size: 0.9rem;">{{ number_format($swap->to_amount, $swap->to_symbol === 'USD' ? 2 : 6) }}</div>
                        <div class="text-white-50 fw-medium" style="font-size: 0.7rem;">{{ $swap->to_symbol }}</div>
                    </div>
                    <x-asset-logo :symbol="$swap->to_symbol" size="28" />
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Portal for escape stacking context clipping -->
<div id="dropdown-portal" style="position: absolute; top: 0; left: 0; width: 100%; pointer-events: none; z-index: 100000;"></div>

<style>
    /* Mobile-First Premium Dark/Gold Styles */
    body { background-color: #0b0e14; }
    .outfit { font-family: 'Outfit', sans-serif; }
    .text-gold { color: #990000 !important; }
    .bg-gold { background-color: #990000 !important; }
    
    .gold-gradient-bg {
        background: linear-gradient(135deg, #990000 0%, #aa8c2c 100%);
    }
    .gold-gradient-bg:active {
        background: linear-gradient(135deg, #aa8c2c 0%, #856d22 100%);
        transform: scale(0.98);
    }
    
    .glass-panel {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .glass-panel-gold {
        background: rgba(153, 0, 0, 0.05);
        border: 1px solid rgba(153, 0, 0, 0.2);
    }
    
    .btn-gold-soft {
        background: rgba(153, 0, 0, 0.1);
        color: #990000;
        border: 1px solid rgba(153, 0, 0, 0.2);
    }
    
    .border-dark-thick {
        border: 4px solid #0b0e14 !important;
    }

    .cursor-pointer { cursor: pointer; }
    
    .custom-option:hover, .custom-option:active { 
        background: rgba(153, 0, 0, 0.1); 
    }
    
    .tracking-wider { letter-spacing: 1px; }
    
    .custom-options { 
        z-index: 9999 !important; 
        box-shadow: 0 20px 50px rgba(0,0,0,0.8) !important;
        background: rgba(15, 19, 28, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(153, 0, 0, 0.15);
    }
    
    /* Custom scrollbar for dropdown */
    .custom-options::-webkit-scrollbar { width: 4px; }
    .custom-options::-webkit-scrollbar-thumb { background: rgba(153, 0, 0, 0.3); border-radius: 10px; }

    /* Remove number input arrows */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
    
    .pulse-glow {
        animation: pulseGold 2s infinite;
    }
    
    @keyframes pulseGold {
        0% { box-shadow: 0 0 0 0 rgba(153, 0, 0, 0.4); }
        70% { box-shadow: 0 0 0 6px rgba(153, 0, 0, 0); }
        100% { box-shadow: 0 0 0 0 rgba(153, 0, 0, 0); }
    }
</style>
@endsection

@push('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>
    // Custom Select Logic with Portal Support
    $('.custom-select-trigger').on('click', function(e) {
        e.stopPropagation();
        let wrapper = $(this).closest('.select-wrapper');
        let options = wrapper.find('.custom-options');
        let portal = $('#dropdown-portal');
        
        // If already open, close it (this handles toggle)
        if (options.parent().is('#dropdown-portal') && options.is(':visible')) {
            closeAllDropdowns();
            return;
        }
        
        closeAllDropdowns();
        
        // Measure trigger position
        let rect = this.getBoundingClientRect();
        
        // Move to portal
        options.appendTo(portal);
        
        // Position it
        options.css({
            position: 'fixed',
            top: (rect.bottom + 8) + 'px',
            left: rect.left + 'px',
            width: rect.width + 'px',
            display: 'block',
            pointerEvents: 'auto',
            zIndex: 100001
        });
        
        // Record which wrapper this belongs to for return trip
        options.data('original-wrapper', wrapper.attr('id'));
        
        // Lift parent box slightly for visual focus
        $(this).closest('.swap-box').css('z-index', '10001');
    });

    function closeAllDropdowns() {
        $('.custom-options').each(function() {
            let options = $(this);
            let wrapperId = options.data('original-wrapper');
            if (wrapperId) {
                // Return to original parent
                options.appendTo('#' + wrapperId);
                options.css({
                    position: 'absolute',
                    top: '',
                    left: '',
                    width: '',
                    display: 'none',
                    zIndex: ''
                });
            } else {
                options.hide();
            }
        });
        $('.select-wrapper').css('z-index', '');
        $('.swap-box').css('z-index', '');
    }

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.custom-options').length) {
            closeAllDropdowns();
        }
    });

    // Handle clicks inside the teleported options
    $(document).on('click', '.custom-option', function(e) {
        let options = $(this).closest('.custom-options');
        let wrapperId = options.data('original-wrapper');
        
        if (wrapperId === 'from-select-wrapper') {
            handleFromSelection($(this));
        } else if (wrapperId === 'to-select-wrapper') {
            handleToSelection($(this));
        }
        
        closeAllDropdowns();
    });

    function handleFromSelection(el) {
        let val = el.data('value');
        $('#select-from').val(val);
        $('#from-selected').html(`
            <img src="{{ url('/api/stock-logo') }}/${val}" width="24" height="24">
            <span class="fw-bold text-white">${val}</span>
        `);
        updateEstimation();
    }

    function handleToSelection(el) {
        let val = el.data('value');
        $('#select-to').val(val);
        $('#to-selected').html(`
            <img src="{{ url('/api/stock-logo') }}/${val}" width="24" height="24">
            <span class="fw-bold text-white">${val}</span>
        `);
        updateEstimation();
    }

    function setMax() {
        const balance = $('#from-options .custom-option[data-value="'+$('#select-from').val()+'"]').data('amount');
        if (balance !== undefined) {
            $('#input-from').val(balance);
            updateEstimation();
        }
    }

    async function updateEstimation() {
        const from = $('#select-from').val();
        const to = $('#select-to').val();
        const amount = parseFloat($('#input-from').val()) || 0;
        
        $('.rate-from-label').text(from);
        $('.rate-to-label').text(to);

        if (from === to) {
            $('#input-to').val(amount.toFixed(2));
            $('#rate-display').text('1.0000');
            return;
        }

        try {
            // Market Rate Fetching
            const response = await fetch(`https://min-api.cryptocompare.com/data/price?fsym=${from}&tsyms=${to}`);
            const data = await response.json();
            
            if (data[to]) {
                const rate = data[to];
                const feePercentage = 0.005; // 0.5% protocol fee
                const total = amount * rate * (1 - feePercentage);
                
                $('#input-to').val(total.toFixed(to === 'USD' || to === 'USDT' ? 2 : 8));
                $('#rate-display').text(rate.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 4 }));
                
                // Update balance display
                const balance = $('#from-options .custom-option[data-value="'+$('#select-from').val()+'"]').data('amount');
                if (balance !== undefined) {
                   $('#from-balance').text(parseFloat(balance).toLocaleString(undefined, {
                       minimumFractionDigits: from === 'USD' ? 2 : 6,
                       maximumFractionDigits: from === 'USD' ? 2 : 8
                   }));
                }
            }
        } catch (error) {
            console.error("Market rates currently unavailable", error);
        }
    }

    $('#input-from').on('change input', updateEstimation);

    function reverseSwap() {
        const from = $('#select-from').val();
        const to = $('#select-to').val();
        $('#select-from').val(to);
        $('#select-to').val(from);
        
        $('#from-selected').html(`
            <img src="{{ url('/api/stock-logo') }}/${to}" width="24" height="24">
            <span class="fw-bold text-white">${to}</span>
        `);
        
        $('#to-selected').html(`
            <img src="{{ url('/api/stock-logo') }}/${from}" width="24" height="24">
            <span class="fw-bold text-white">${from}</span>
        `);
        
        // Animate the swap box containers
        if (typeof anime !== 'undefined') {
            anime({
                targets: '.swap-box',
                translateY: [10, 0],
                opacity: [0.8, 1],
                duration: 400,
                easing: 'easeOutQuint'
            });
            
            anime({
                targets: '.floating-element',
                rotate: '+=180',
                duration: 400,
                easing: 'easeOutQuint'
            });
        }
        
        updateEstimation();
    }

    $(document).ready(() => {
        updateEstimation();
        setInterval(updateEstimation, 15000); // 15s live refresh
    });

    // Staggered Entrance Animation
    window.addEventListener('load', () => {
        if (typeof anime !== 'undefined') {
            anime({
                targets: '.stagger-in',
                translateY: [20, 0],
                opacity: [0, 1],
                delay: anime.stagger(150),
                duration: 1000,
                easing: 'easeOutQuint'
            });
        }
    });
</script>
@endpush
