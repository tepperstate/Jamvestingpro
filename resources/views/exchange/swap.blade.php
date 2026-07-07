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

<div class="container py-5 min-vh-75 d-flex align-items-center">
    <div class="row justify-content-center w-100 align-items-center g-5">
        
        <!-- Left Side: Information / Branding -->
        <div class="col-xl-5 col-lg-6 d-none d-lg-block pe-lg-5">
            <div class="stagger-in">
                <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill mb-4 border border-primary-05" style="letter-spacing: 1px;">INSTANT SWAP</span>
                <h1 class="display-4 outfit fw-bold text-white mb-4" style="line-height: 1.1;">Swap <span class="text-primary">Assets</span></h1>
                <p class="text-secondary mb-5" style="line-height: 1.8; font-size: 1.15rem;">Convert between crypto and fiat instantly at market rates. Simple, fast, and with zero slippage.</p>
                
                <div class="d-flex align-items-center mb-5">
                    <div class="me-5">
                        <h3 class="text-white fw-bold mb-0">0.50%</h3>
                        <span class="small text-secondary text-uppercase tracking-wider fw-bold">Fixed Fee</span>
                    </div>
                    <div class="mx-4" style="height: 40px; width: 1px; background: rgba(255,255,255,0.1);"></div>
                    <div class="ms-4">
                        <h3 class="text-white fw-bold mb-0">Instant</h3>
                        <span class="small text-secondary text-uppercase tracking-wider fw-bold">Settlement</span>
                    </div>
                </div>

                <div class="glass-panel p-4" style="background: rgba(255,255,255,0.02);">
                    <div class="d-flex align-items-start gap-3">
                        <div class="p-3 rounded-circle bg-success-soft text-success">
                            <i class="ri-shield-check-line fs-3"></i>
                        </div>
                        <div>
                            <h5 class="text-white mb-1">Protected Execution</h5>
                            <p class="text-secondary small mb-0">Every swap is executed at the best available market rate with slippage protection.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Swap Interface -->
        <div class="col-xl-6 col-lg-6">
            <div class="spatial-depth">
                <div class="glass-panel p-4 p-md-5 position-relative stagger-in" style="border: 1px solid rgba(255,255,255,0.12);">
                    
                    <!-- Decorative Background Elements (Isolated clipping) -->
                    <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="pointer-events: none; border-radius: inherit; z-index: 0;">
                        <div class="position-absolute" style="top: -100px; right: -100px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(14, 165, 233, 0.12) 0%, transparent 70%);"></div>
                        <div class="position-absolute" style="bottom: -100px; left: -100px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, transparent 70%);"></div>
                    </div>

                    <div class="text-center mb-5 position-relative" style="z-index: 1;">
                        <h2 class="fw-bold outfit text-white mb-2" style="font-size: 2.2rem; letter-spacing: -1px;">Bridge Assets</h2>
                        <p class="text-secondary small text-uppercase tracking-widest" style="letter-spacing: 3px; font-weight: 700;">Instant Conversion</p>
                    </div>

                    <form action="{{ route('swap.coin') }}" method="POST" id="swap-form" class="position-relative" style="z-index: 1;">
                        @csrf
                        
                        <!-- From Section -->
                        <div class="swap-box glass-panel p-4 mb-3 hover-tilt" style="background: rgba(255,255,255,0.03); border-radius: 24px; border-color: rgba(255,255,255,0.08);">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-secondary small fw-bold tracking-wider" style="letter-spacing: 1px;">PAYING</span>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="text-secondary small">Available: <span class="text-white fw-bold" id="from-balance">{{ number_format($first_coin->balance, 6) }}</span></span>
                                    <button type="button" class="btn btn-primary-soft btn-sm px-2 py-0" onclick="setMax()" style="font-size: 0.7rem; height: 20px;">MAX</button>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <input type="number" name="amount" id="input-from" step="any" class="form-control border-0 bg-transparent p-0 outfit text-white flex-grow-1" placeholder="0.00" required style="box-shadow: none; font-size: 2.5rem; font-weight: 600; min-width: 0;">
                                <div class="select-wrapper" id="from-select-wrapper">
                                    <input type="hidden" name="from" id="select-from" value="{{ $first_coin->symbol }}">
                                    <div class="custom-select-trigger glass-panel px-3 py-2 d-flex align-items-center justify-content-between gap-3 cursor-pointer" style="border-radius: 18px; background: rgba(255,255,255,0.08); min-width: 130px; border: 1px solid rgba(255,255,255,0.1);">
                                        <div class="d-flex align-items-center gap-2" id="from-selected">
                                            <x-asset-logo :symbol="$first_coin->symbol" size="24" />
                                            <span class="fw-bold text-white fs-5">{{ $first_coin->symbol }}</span>
                                        </div>
                                        <i class="ri-arrow-down-s-line text-secondary fs-5"></i>
                                    </div>
                                    <div id="from-options" class="custom-options glass-panel position-absolute mt-2 p-2 shadow-lg" style="display: none; z-index: 1000; min-width: 180px; max-height: 300px; overflow-y: auto; right: 0; background: rgba(20, 24, 33, 0.98); border: 1px solid rgba(255,255,255,0.15);">
                                        @foreach($all_assets as $coin)
                                        <div class="custom-option p-2 d-flex align-items-center gap-3 rounded-3 cursor-pointer hover-bg-white-05 mb-1" data-value="{{ $coin->symbol }}" data-amount="{{ $coin->balance }}" data-icon="{{ strtolower($coin->symbol) }}">
                                            <x-asset-logo :symbol="$coin->symbol" size="28" />
                                            <div class="d-flex flex-column">
                                                <span class="text-white fw-bold lh-1">{{ $coin->symbol }}</span>
                                                <span class="text-secondary extra-small">{{ number_format($coin->balance, 4) }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reverse Button -->
                        <div class="text-center position-relative my-n4" style="z-index: 10;">
                            <button type="button" class="btn btn-primary rounded-circle p-0 shadow-lg floating-element" onclick="reverseSwap()" style="width: 52px; height: 52px; border: 4px solid #11141b; background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);">
                                <i class="ri-arrow-up-down-line fs-4"></i>
                            </button>
                        </div>

                        <!-- To Section -->
                        <div class="swap-box glass-panel p-4 mb-4 hover-tilt" style="background: rgba(0,0,0,0.25); border-radius: 24px; margin-top: 1.5rem; border-color: rgba(255,255,255,0.08); position: relative; z-index: 1;">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-secondary small fw-bold tracking-wider" style="letter-spacing: 1px;">RECEIVING (EST.)</span>
                                <div class="d-flex align-items-center gap-1">
                                    <div class="pulse-glow rounded-circle bg-success" style="width: 6px; height: 6px;"></div>
                                    <span class="text-success small fw-bold">Market Rate</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <input type="number" name="amount_to" id="input-to" step="any" class="form-control border-0 bg-transparent p-0 outfit text-white flex-grow-1" placeholder="0.00" readonly style="box-shadow: none; font-size: 2.5rem; font-weight: 600; min-width: 0;">
                                <div class="select-wrapper position-relative" id="to-select-wrapper">
                                    <input type="hidden" name="to" id="select-to" value="{{ $defaultTo->symbol }}">
                                    <div class="custom-select-trigger glass-panel px-3 py-2 d-flex align-items-center justify-content-between gap-3 cursor-pointer" style="border-radius: 18px; background: rgba(255,255,255,0.08); min-width: 130px; border: 1px solid rgba(255,255,255,0.1);">
                                        <div class="d-flex align-items-center gap-2" id="to-selected">
                                            <x-asset-logo :symbol="$defaultTo->symbol" size="24" />
                                            <span class="fw-bold text-white fs-5">{{ $defaultTo->symbol }}</span>
                                        </div>
                                        <i class="ri-arrow-down-s-line text-secondary fs-5"></i>
                                    </div>
                                    <div id="to-options" class="custom-options glass-panel position-absolute mt-2 p-2 shadow-lg" style="display: none; z-index: 10000; min-width: 180px; max-height: 300px; overflow-y: auto; right: 0; background: rgba(20, 24, 33, 0.98); border: 1px solid rgba(255,255,255,0.15); backdrop-filter: blur(25px);">
                                        @foreach($all_assets as $coin)
                                        <div class="custom-option p-2 d-flex align-items-center gap-3 rounded-3 cursor-pointer hover-bg-white-05 mb-1" data-value="{{ $coin->symbol }}" data-icon="{{ strtolower($coin->symbol) }}">
                                            <x-asset-logo :symbol="$coin->symbol" size="28" />
                                            <span class="text-white fw-bold">{{ $coin->symbol }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Details Section -->
                        <div class="glass-panel p-3 mb-4" style="background: rgba(0,0,0,0.1); border-radius: 16px; border: 1px solid rgba(255,255,255,0.03);">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary small">Exchange Rate</span>
                                <span class="text-white small fw-bold">1 <span class="rate-from-label">{{ $first_coin->symbol }}</span> = <span id="rate-display">...</span> <span class="rate-to-label">{{ $defaultTo->symbol }}</span></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-secondary small">Protocol Fee</span>
                                <span class="text-white small fw-bold">0.50%</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-lg" style="letter-spacing: 1px; background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%); border: none;">
                            CONFIRM SWAP
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .hover-bg-white-05:hover { background: rgba(255,255,255,0.05); }
    .extra-small { font-size: 0.7rem; }
    .bg-primary-soft { background: rgba(14, 165, 233, 0.1); }
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
    .border-primary-05 { border-color: rgba(14, 165, 233, 0.1) !important; }
    
    .custom-options { 
        z-index: 9999 !important; 
        box-shadow: 0 20px 50px rgba(0,0,0,0.5) !important;
    }
    .select-wrapper { position: relative; }
    
    /* Custom scrollbar for dropdown */
    .custom-options::-webkit-scrollbar { width: 4px; }
    .custom-options::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

    @media (max-width: 991px) {
        .container { padding-top: 2rem !important; }
        .spatial-depth { perspective: none; }
        .glass-panel { padding: 1.5rem !important; }
    }
</style>

<!-- Swap History Section -->
@if(isset($swap_history) && $swap_history->count() > 0)
<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-xl-11 col-lg-12">
            <div class="glass-panel p-4 p-md-5 stagger-in" style="border: 1px solid rgba(255,255,255,0.08);">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="outfit fw-bold text-white mb-1">Swap History</h4>
                        <p class="text-secondary small mb-0 text-uppercase tracking-wider" style="letter-spacing: 2px; font-weight: 700;">Recent conversions</p>
                    </div>
                    <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill border border-primary-05" style="font-size: 0.7rem;">{{ $swap_history->count() }} Records</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                                <th class="text-secondary small fw-bold text-uppercase tracking-wider py-3" style="font-size: 0.65rem; letter-spacing: 1px;">Date</th>
                                <th class="text-secondary small fw-bold text-uppercase tracking-wider py-3" style="font-size: 0.65rem; letter-spacing: 1px;">From</th>
                                <th class="text-secondary small fw-bold text-uppercase tracking-wider py-3 text-center" style="font-size: 0.65rem; letter-spacing: 1px;"></th>
                                <th class="text-secondary small fw-bold text-uppercase tracking-wider py-3" style="font-size: 0.65rem; letter-spacing: 1px;">To</th>
                                <th class="text-secondary small fw-bold text-uppercase tracking-wider py-3" style="font-size: 0.65rem; letter-spacing: 1px;">Rate</th>
                                <th class="text-secondary small fw-bold text-uppercase tracking-wider py-3 text-end" style="font-size: 0.65rem; letter-spacing: 1px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($swap_history as $swap)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                <td class="py-3">
                                    <div class="text-white small fw-medium">{{ \Carbon\Carbon::parse($swap->created_at)->format('M d, Y') }}</div>
                                    <div class="text-secondary" style="font-size: 0.65rem;">{{ \Carbon\Carbon::parse($swap->created_at)->format('h:i A') }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <x-asset-logo :symbol="$swap->from_symbol" size="24" />
                                        <div>
                                            <div class="text-white fw-bold small">{{ number_format($swap->from_amount, $swap->from_symbol === 'USD' ? 2 : 6) }}</div>
                                            <div class="text-secondary" style="font-size: 0.65rem;">{{ $swap->from_symbol }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center py-3">
                                    <i class="ri-arrow-right-line text-primary"></i>
                                </td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <x-asset-logo :symbol="$swap->to_symbol" size="24" />
                                        <div>
                                            <div class="text-white fw-bold small">{{ number_format($swap->to_amount, $swap->to_symbol === 'USD' ? 2 : 6) }}</div>
                                            <div class="text-secondary" style="font-size: 0.65rem;">{{ $swap->to_symbol }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="text-white small">1 {{ $swap->from_symbol }} = {{ number_format($swap->rate, 4) }} {{ $swap->to_symbol }}</span>
                                </td>
                                <td class="py-3 text-end">
                                    <span class="badge px-3 py-2 rounded-pill fw-bold" style="font-size: 0.6rem; letter-spacing: 0.5px;
                                        background: {{ $swap->status === 'completed' ? 'rgba(16,185,129,0.1)' : 'rgba(245,158,11,0.1)' }};
                                        color: {{ $swap->status === 'completed' ? '#ff3333' : '#f59e0b' }};">
                                        {{ strtoupper($swap->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Portal for escape stacking context clipping -->
<div id="dropdown-portal" style="position: absolute; top: 0; left: 0; width: 100%; pointer-events: none; z-index: 100000;"></div>
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
        let scrollY = window.scrollY || window.pageYOffset;
        let scrollX = window.scrollX || window.pageXOffset;
        
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
        let icon = el.data('icon');
        $('#select-from').val(val);
        $('#from-selected').html(`
            <img src="{{ url('/api/stock-logo') }}/${val}" width="24" height="24">
            <span class="fw-bold text-white fs-5">${val}</span>
        `);
        updateEstimation();
    }

    function handleToSelection(el) {
        let val = el.data('value');
        let icon = el.data('icon');
        $('#select-to').val(val);
        $('#to-selected').html(`
            <img src="{{ url('/api/stock-logo') }}/${val}" width="24" height="24">
            <span class="fw-bold text-white fs-5">${val}</span>
        `);
        updateEstimation();
    }

    // Note: Selection handlers and close logic are unified in the delegation section above.

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
        
        // Update UI
        let fromIcon = to.toLowerCase();
        let toIcon = from.toLowerCase();
        
        $('#from-selected').html(`
            <img src="{{ url('/api/stock-logo') }}/${to}" width="24" height="24">
            <span class="fw-bold text-white fs-5">${to}</span>
        `);
        
        $('#to-selected').html(`
            <img src="{{ url('/api/stock-logo') }}/${from}" width="24" height="24">
            <span class="fw-bold text-white fs-5">${from}</span>
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
