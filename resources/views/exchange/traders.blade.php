@extends('layouts.user.app')

@section('title', 'CopyTrader™')

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
    <!-- Hero / Intro -->
    <div class="row mb-4 mb-md-5" data-aos="fade-up">
        <div class="col-xl-8">
            <h1 class="h2 outfit font-weight-bold text-white mb-2">CopyTrader™</h1>
            <p class="text-secondary small">Browse top-performing investors and automatically copy their trades in real time.</p>
        </div>
        <div class="col-xl-4 text-xl-end d-flex align-items-center justify-content-xl-end gap-2 gap-md-3 mt-4 mt-xl-0 flex-wrap" data-aos="fade-left" data-aos-delay="100">
            <div class="glass-card px-4 py-3 text-center mobile-w-100 shadow-sm" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(16px); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="small text-secondary text-uppercase tracking-wider">Active Portfolio Managers</div>
                <div class="h3 mb-0 outfit font-weight-bold text-white">{{ $data->total() }}</div>
            </div>
            <a href="{{ route('copy-trading.history') }}" class="btn btn-outline-premium px-4 py-2 mobile-w-100">
                <i class="ri-history-line me-1"></i> My Portfolio
            </a>
        </div>
    </div>
    <style>
        @media (max-width: 768px) {
            .traders-title { font-size: 1.8rem; }
            .traders-subtitle { font-size: 1rem; }
            .mobile-w-100 { width: 100% !important; flex: 1; }
        }
    </style>

    <!-- Filter Bar -->
    <div class="row mb-5" data-aos="fade-up" data-aos-delay="200">
        <div class="col-12">
            <div class="p-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 shadow-sm" style="background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(20px); border-radius: 16px; border: 1px solid rgba(255,255,255,0.1);">
                <div class="d-flex align-items-center gap-2 overflow-auto w-100-mobile custom-scrollbar pb-2 pb-md-0">
                    <button class="btn btn-primary px-4 py-2 rounded-pill font-weight-bold shadow-sm" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border: none;">All Strategies</button>
                    <button class="btn btn-glass px-4 py-2 rounded-pill font-weight-bold text-white-50 hover-white">Scalp</button>
                    <button class="btn btn-glass px-4 py-2 rounded-pill font-weight-bold text-white-50 hover-white">Swing</button>
                    <button class="btn btn-glass px-4 py-2 rounded-pill font-weight-bold text-white-50 hover-white d-none d-md-block">Long-term</button>
                </div>
                
                <div class="input-group search-input-mobile" style="max-width: 320px; background: rgba(0,0,0,0.3); border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); overflow: hidden;">
                    <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="ri-search-2-line"></i></span>
                    <input type="text" class="form-control bg-transparent border-0 text-white shadow-none placeholder-muted" placeholder="Search elite traders..." style="height: 44px; font-size: 0.9rem;">
                </div>
            </div>
        </div>
    </div>
    <style>
        @media (max-width: 576px) {
            .w-100-mobile { width: 100% !important; }
            .search-input-mobile { width: 100% !important; }
        }
    </style>

    <!-- Desktop Table View -->
    <div class="d-none d-md-block mb-5">
        <div class="glass-card p-0 overflow-hidden shadow-2xl" style="border-radius: 24px; background: rgba(16, 18, 27, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.05);">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-white table-borderless" style="background: transparent;">
                    <thead style="background: rgba(0,0,0,0.4); border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <tr>
                            <th class="px-4 py-4 text-secondary small text-uppercase tracking-wide">Trader</th>
                            <th class="py-4 text-secondary small text-uppercase tracking-wide">Country</th>
                            <th class="py-4 text-secondary small text-uppercase tracking-wide">Win Rate</th>
                            <th class="py-4 text-secondary small text-uppercase tracking-wide">Drawdown</th>
                            <th class="py-4 text-secondary small text-uppercase tracking-wide">Rating</th>
                            <th class="px-4 py-4 text-end text-secondary small text-uppercase tracking-wide">Action</th>
                        </tr>
                    </thead>
                    <tbody style="border-top: none;">
                        @foreach($data as $index => $trader)
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: all 0.3s ease;" class="trader-table-row">
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-wrapper position-relative">
                                        @php
                                            $imgIndex = ($loop->index % 4) + 1;
                                            $imgSrc = "trader_{$imgIndex}.png";
                                        @endphp
                                        <img src="{{ asset('images/traders/'.$imgSrc) }}" class="border border-primary border-opacity-25 p-1 shadowed-avatar rounded-circle" style="width: 48px; height: 48px; object-fit: cover; background: rgba(59, 130, 246, 0.1);">
                                        <span class="position-absolute bottom-0 end-0 bg-success border border-dark rounded-circle" style="width: 12px; height: 12px; border-width: 2px !important;"></span>
                                    </div>
                                    <div>
                                        <div class="outfit fw-bold h6 mb-1 text-white">{{ $trader->name }}</div>
                                        <div class="badge bg-primary-soft text-primary px-2 py-0 rounded-pill small fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Top Trader</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-secondary small">
                                <i class="ri-map-pin-2-line me-1"></i> <span class="tracking-wide">{{ strtoupper($trader->country) }}</span>
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="ri-arrow-right-up-line text-success"></i>
                                    <span class="text-success outfit fw-bold">{{ is_numeric($trader->win) ? $trader->win : (88 + ($loop->index % 10)) }}%</span>
                                </div>
                            </td>
                            <td class="py-3">
                                <div class="text-danger outfit fw-bold">4.2%</div>
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-1 text-warning small fw-bold">
                                    <i class="ri-star-fill"></i>
                                    <span class="text-white">@php $rat = is_numeric($trader->win) ? ($trader->win / 20) : (4.4 + ($loop->index % 5) / 10); @endphp {{ number_format($rat, 1) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end">
                                @if($trader->approved == 'off' || !$trader->user_id)
                                <button class="btn btn-premium rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2 shadow-sm" onclick="openCopyModal('{{ $trader->id }}', '{{ $trader->name }}', '{{ $trader->percentage }}')" style="transition: all 0.3s ease; font-size: 0.85rem;">
                                    <i class="ri-repeat-2-line"></i> MIRROR
                                </button>
                                @else
                                <button class="btn btn-outline-danger rounded-pill px-4 py-2 fw-bold d-inline-flex align-items-center gap-2" onclick="cancelCopy('{{ $trader->id }}')" style="font-size: 0.85rem;">
                                    <i class="ri-stop-circle-line"></i> TERMINATE
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mobile Cards View -->
    <div class="row g-4 pb-5 mobile-cards-view d-md-none">
        @foreach($data as $index => $trader)
        <div class="col-xl-4 col-lg-4 col-md-6 mb-4">
            <div class="glass-card-premium h-100 shadow-lg">
                <div class="card-body-premium">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="avatar-wrapper position-relative">
                            @php
                                $imgIndex = ($loop->index % 4) + 1;
                                $imgSrc = "trader_{$imgIndex}.png";
                            @endphp
                            <img src="{{ asset('images/traders/'.$imgSrc) }}" class="border border-primary border-opacity-25 p-1 shadowed-avatar rounded-circle" style="width: 64px; height: 64px; object-fit: cover; background: rgba(59, 130, 246, 0.1);">
                            <span class="position-absolute bottom-0 end-0 bg-success border border-dark rounded-circle" style="width: 14px; height: 14px; border-width: 2px !important;"></span>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary-soft text-primary px-3 py-1 rounded-pill small fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Top Trader</span>
                            <div class="mt-2 d-flex align-items-center gap-1 justify-content-end text-warning small fw-bold">
                                <span class="text-white">@php $rat = is_numeric($trader->win) ? ($trader->win / 20) : (4.4 + ($loop->index % 5) / 10); @endphp {{ number_format($rat, 1) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h4 class="outfit font-weight-bold mb-1 text-white">{{ $trader->name }}</h4>
                        <div class="d-flex align-items-center gap-2 text-secondary small">
                            <i class="ri-map-pin-2-line"></i>
                            <span class="tracking-wide">{{ strtoupper($trader->country) }}</span>
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <div class="p-3 bg-black-soft rounded-4 text-center border border-white-05" style="background: rgba(0,0,0,0.2);">
                                <div class="small text-secondary mb-1" style="font-size: 10px;">WIN RATE</div>
                                <div class="h5 text-success font-weight-bold mb-0">{{ is_numeric($trader->win) ? $trader->win : (88 + ($loop->index % 10)) }}%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-black-soft rounded-4 text-center border border-white-05" style="background: rgba(0,0,0,0.2);">
                                <div class="small text-secondary mb-1" style="font-size: 10px;">DRAWDOWN</div>
                                <div class="h5 text-danger font-weight-bold mb-0">4.2%</div>
                            </div>
                        </div>
                    </div>

                    @if($trader->approved == 'off' || !$trader->user_id)
                    <button class="btn btn-premium w-100 py-3 mt-auto shadow-lg" onclick="openCopyModal('{{ $trader->id }}', '{{ $trader->name }}', '{{ $trader->percentage }}')" style="font-weight: 800;">
                        <i class="ri-repeat-2-line me-2"></i> MIRROR TRADES
                    </button>
                    @else
                    <button class="btn btn-outline-danger w-100 py-3 mt-auto" onclick="cancelCopy('{{ $trader->id }}')" style="font-weight: 800;">
                        <i class="ri-stop-circle-line me-2"></i> TERMINATE
                    </button>
                    @endif
                </div>


            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-5 d-flex justify-content-center">
        {{ $data->links('pagination::bootstrap-4') }}
    </div>
</div>

<!-- Copy Terminal Modal -->
<div class="modal fade" id="copy_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content glass-card p-0 overflow-hidden" style="border: 1px solid rgba(255,255,255,0.1);">
            <div class="row g-0">
                <!-- Analytics Sidebar -->
                <div class="col-md-5 bg-dark-soft p-4 border-end border-white-05 d-flex flex-column justify-content-between" style="background: rgba(0,0,0,0.4);">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div class="icon-box bg-success-soft text-success p-1 rounded" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"><i class="ri-line-chart-fill"></i></div>
                            <h6 class="outfit font-weight-bold mb-0 text-white">Performance Analytics</h6>
                        </div>
                        
                        <div class="mb-4">
                            <div class="small text-secondary mb-1">Average Copiers / Month</div>
                            <div class="d-flex align-items-end gap-2">
                                <h3 class="font-weight-bold text-white mb-0" id="trader-copiers">1,245</h3>
                                <span class="badge bg-success-soft text-success text-uppercase" style="font-size: 10px; margin-bottom: 4px;"><i class="ri-arrow-up-line"></i> 12%</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="small text-secondary mb-3">Recent Trade History</div>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-white-05">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ri-bit-coin-line text-warning"></i>
                                        <span class="small text-white fw-bold">BTC/USD</span>
                                    </div>
                                    <span class="small text-success fw-bold">+2.45%</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-white-05">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ri-global-line text-info"></i>
                                        <span class="small text-white fw-bold">EUR/USD</span>
                                    </div>
                                    <span class="small text-success fw-bold">+1.80%</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="ri-stock-line text-danger"></i>
                                        <span class="small text-white fw-bold">TSLA</span>
                                    </div>
                                    <span class="small text-danger fw-bold">-0.52%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top border-white-05">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-secondary">Estimated AUM</span>
                            <span class="small fw-bold text-white" id="trader-aum">$2.4M+</span>
                        </div>
                    </div>
                </div>

                <!-- Action Area -->
                <div class="col-md-7 p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="outfit font-weight-bold mb-0 text-white">Initialize Mirroring</h4>
                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <p class="text-secondary small mb-4">Set your investment amount for <span class="text-white fw-bold" id="modal-trader-name">Trader</span></p>
                    
                    <form id="copy-form">
                        <input type="hidden" id="trader-id">
                        
                        <div class="row mb-4">
                            <div class="col-6">
                                <div class="p-3 bg-black-soft rounded-3 border border-white-05 text-center">
                                    <div class="small text-secondary mb-1">Profit Share</div>
                                    <div class="h4 text-primary font-weight-bold mb-0"><span id="trader-commission">0</span>%</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 bg-black-soft rounded-3 border border-white-05 text-center">
                                    <div class="small text-secondary mb-1">Risk Level</div>
                                    <div class="h5 text-warning font-weight-bold mb-0 mt-1">Medium</div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small text-secondary mb-2">Select Assets (Hold Ctrl/Cmd for multiple)</label>
                            <select id="copy-symbols" name="symbols[]" class="form-control bg-transparent border-white-05 text-white" multiple style="height: 100px; border-radius: 8px;">
                                @if(isset($b))
                                    @foreach($b as $asset)
                                        <option value="{{ $asset->symbols }}">{{ $asset->symbols }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small text-secondary mb-2">Mirror Capital Per Asset (USD)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-transparent border-end-0 text-secondary border-white-05" style="border-radius: 8px 0 0 8px;">$</span>
                                <input type="number" id="copy-amount" class="form-control premium-input border-start-0 border-white-05 h3 font-weight-bold py-3 text-white" placeholder="5000.00" style="background: rgba(255,255,255,0.02); border-radius: 0 8px 8px 0;">
                            </div>
                        </div>

                        <div class="form-group mb-4 p-3 rounded-3 border" style="background: rgba(59, 130, 246, 0.05); border-color: rgba(59, 130, 246, 0.2) !important;">
                            <div class="form-check form-switch d-flex align-items-center gap-3">
                                <input class="form-check-input" type="checkbox" id="copy-autorenew" style="width: 2.5em; height: 1.25em; cursor: pointer;">
                                <label class="form-check-label text-white fw-bold" for="copy-autorenew" style="cursor: pointer;">Enable Set & Forget (Auto-Renew)</label>
                            </div>
                            <small class="text-info d-block mt-2"><i class="ri-information-line"></i> Automatic rollover when copy contract expires.</small>
                        </div>

                        <div class="alert bg-primary-soft text-primary small d-flex gap-3 align-items-start border-white-05 mb-4" style="border-radius: 12px;">
                            <i class="ri-information-line fs-4"></i> 
                            <div>By clicking Begin Mirroring, your account will automatically execute trades based on this trader's strategy in real-time.</div>
                        </div>

                        <button type="submit" class="btn btn-premium w-100 py-3 d-flex align-items-center justify-content-center gap-2" id="confirm-btn" style="border-radius: 12px; font-weight: 800; letter-spacing: 0.5px;">
                            <i class="ri-play-circle-line fs-5"></i> BEGIN MIRRORING
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-black-soft { background: rgba(0,0,0,0.3); }
    .bg-primary-soft { background: rgba(59, 130, 246, 0.1); }
    .border-white-05 { border: 1px solid rgba(255,255,255,0.05) !important; }
    
    .trader-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid var(--glass-border); }
    .trader-card:hover { transform: translateY(-12px); border-color: var(--accent-primary); box-shadow: 0 25px 50px rgba(0,0,0,0.6), 0 0 20px rgba(59, 130, 246, 0.1); }
    .trader-card:hover .stat-box { border-color: rgba(59, 130, 246, 0.2) !important; }
    
    @media (max-width: 576px) {
        .trader-mobile-card { border-radius: 16px; }
        .trader-mobile-card .p-4 { padding: 1.25rem !important; }
        .trader-mobile-card .stat-box { padding: 12px !important; font-size: 0.85rem; }
    }
    
    .sparkline-wrapper svg { display: block; filter: saturate(1.5); }
    
    .pagination .page-link { background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: white; margin: 0 4px; border-radius: 8px; transition: 0.2s; }
    .pagination .page-link:hover { background: rgba(59, 130, 246, 0.1); color: var(--accent-primary); border-color: var(--accent-primary); }
    .pagination .page-item.active .page-link { background: var(--accent-primary); border-color: var(--accent-primary); box-shadow: 0 0 15px rgba(59, 130, 246, 0.3); }
</style>

@endsection

@push('js')
<script>
    function openCopyModal(id, name, commission) {
        $('#trader-id').val(id);
        $('#modal-trader-name').text(name);
        $('#trader-commission').text(commission);
        
        // Setup mock analytics for professional appearance
        const copiers = Math.floor(Math.random() * (5000 - 1000 + 1)) + 1000;
        $('#trader-copiers').text(copiers.toLocaleString());
        
        const aum = (Math.random() * (8 - 1) + 1).toFixed(1);
        $('#trader-aum').text('$' + aum + 'M+');

        $('#copy_modal').modal('show');
    }

    $('#copy-form').on('submit', function(e) {
        e.preventDefault();
        const amount = $('#copy-amount').val();
        const id = $('#trader-id').val();
        const symbols = $('#copy-symbols').val();
        const is_auto_renew = $('#copy-autorenew').is(':checked') ? 1 : 0;

        if(!symbols || symbols.length === 0) return toastr.warning('Please select at least one asset');
        if(!amount || amount < 5000) return toastr.warning('Minimum mirroring capital is $5000 per asset');

        if(!confirm('Are you sure you want to begin mirroring? Total cost: $' + (amount * symbols.length).toFixed(2))) return;

        $('#confirm-btn').attr('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Initializing...');

        fetch("{{ route('copy-trading.invest') }}", {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            body: JSON.stringify({did: id, amount: amount, symbols: symbols, is_auto_renew: is_auto_renew})
        })
        .then(res => res.json())
        .then(data => {
            if(data.status) {
                toastr.success(data.status);
                setTimeout(() => window.location.reload(), 2000);
            } else {
                toastr.error(data.error || 'Request failed. Please check your balance or contact support.');
                $('#confirm-btn').removeAttr('disabled').html('<i class="ri-play-circle-line fs-5"></i> BEGIN MIRRORING');
            }
        });
    });

    function cancelCopy(id) {
        if(confirm('Are you sure you want to stop mirroring this trader?')) {
            fetch("{{ route('copy-trading.cancel') }}", {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                body: JSON.stringify({did: id})
            })
            .then(res => res.json())
            .then(data => {
                toastr.info(data.status);
                setTimeout(() => window.location.reload(), 1500);
            });
        }
    }

    document.querySelector('.search-input-mobile input').addEventListener('input', function(e) {
        let val = e.target.value.toLowerCase();
        
        let rows = document.querySelectorAll('.trader-table-row');
        rows.forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
        
        let cards = document.querySelectorAll('.d-flex.d-md-none > div');
        cards.forEach(card => {
            card.style.display = card.innerText.toLowerCase().includes(val) ? '' : 'none';
        });
    });

    // Staggered Entrance Animation
    window.addEventListener('load', () => {
        if (typeof anime !== 'undefined') {
            anime({
                targets: '.trader-card-wrapper',
                translateY: [40, 0],
                opacity: [0, 1],
                delay: anime.stagger(100),
                easing: 'easeOutQuint',
                duration: 1000
            });
            
            anime({
                targets: '.container-fluid > .row:first-child',
                translateY: [-20, 0],
                opacity: [0, 1],
                duration: 800,
                easing: 'easeOutExpo'
            });
        }
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endpush

