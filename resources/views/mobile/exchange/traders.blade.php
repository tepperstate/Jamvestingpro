@extends('layouts.user.app')
@section('title', 'CopyTrader™')
@section('content')

<style>
.mobile-traders-container {
    padding: 15px;
    background: #0b0e14;
    min-height: 100vh;
    padding-bottom: 90px;
}
.trader-card-mobile {
    background: rgba(16, 18, 27, 0.7);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 20px;
    position: relative;
}
.trader-avatar-wrapper {
    width: 60px;
    height: 60px;
    position: relative;
    border-radius: 50%;
    padding: 2px;
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
}
.trader-avatar-wrapper img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 2px solid #0b0e14;
    object-fit: cover;
}
.verified-badge {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 16px;
    height: 16px;
    background: #ff3333;
    border: 2px solid #0b0e14;
    border-radius: 50%;
}
.stat-box-mobile {
    background: rgba(0,0,0,0.3);
    border-radius: 12px;
    padding: 10px;
    text-align: center;
    border: 1px solid rgba(255,255,255,0.05);
}
.btn-mirror-mobile {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff;
    font-weight: 800;
    border: none;
    border-radius: 12px;
    padding: 12px;
    width: 100%;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
}
.filter-scroll-mobile {
    display: flex;
    overflow-x: auto;
    gap: 10px;
    padding-bottom: 10px;
    margin-bottom: 15px;
}
.filter-scroll-mobile::-webkit-scrollbar { display: none; }
.filter-pill {
    background: rgba(255,255,255,0.05);
    color: #94a3b8;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    padding: 8px 16px;
    font-size: 0.8rem;
    font-weight: 600;
    white-space: nowrap;
}
.filter-pill.active {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff;
    border-color: transparent;
}
.search-bar-mobile {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    color: #fff;
    padding: 10px 15px 10px 40px;
    width: 100%;
}
.search-icon-mobile {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
}

/* Modal specific */
.glass-modal-mobile {
    background: rgba(16, 18, 27, 0.95) !important;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px 20px 0 0;
}
.form-control-glass {
    background: rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.1);
    color: #fff;
}
.form-control-glass:focus {
    background: rgba(0,0,0,0.5);
    color: #fff;
    border-color: #3b82f6;
    box-shadow: none;
}
</style>

<div class="mobile-traders-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-white font-weight-bold mb-0" style="font-family: 'Outfit', sans-serif;">CopyTrader™</h4>
            <div class="small text-secondary">Copy top performers</div>
        </div>
        <a href="{{ route('copy-trading.history') }}" class="btn btn-sm" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 8px;">
            <i class="ri-history-line"></i> Portfolio
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="position-relative mb-3">
        <i class="ri-search-2-line search-icon-mobile"></i>
        <input type="text" id="trader-search" class="search-bar-mobile" placeholder="Search elite traders...">
    </div>
    
    <div class="filter-scroll-mobile">
        <div class="filter-pill active">All Strategies</div>
        <div class="filter-pill">Scalp</div>
        <div class="filter-pill">Swing</div>
        <div class="filter-pill">Long-term</div>
    </div>

    <div id="traders-list">
        @foreach($data as $index => $trader)
        <div class="trader-card-mobile trader-item">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="d-flex gap-3 align-items-center">
                    <div class="trader-avatar-wrapper">
                        @php
                            $imgIndex = ($loop->index % 4) + 1;
                            $imgSrc = "trader_{$imgIndex}.png";
                        @endphp
                        <img src="{{ asset('images/traders/'.$imgSrc) }}">
                        <div class="verified-badge"></div>
                    </div>
                    <div>
                        <h5 class="text-white font-weight-bold mb-1 name-target">{{ $trader->name }}</h5>
                        <div class="d-flex align-items-center gap-1 text-secondary small" style="font-size: 0.7rem;">
                            <i class="ri-map-pin-2-line"></i> {{ strtoupper($trader->country) }}
                        </div>
                    </div>
                </div>
                <div class="text-end">
                    <div class="badge bg-primary-soft text-primary px-2 py-1 rounded" style="background: rgba(59,130,246,0.1); font-size: 0.65rem;">Top Trader</div>
                    <div class="d-flex align-items-center gap-1 justify-content-end text-warning mt-1" style="font-size: 0.75rem;">
                        <i class="ri-star-fill"></i>
                        <span class="text-white fw-bold">@php $rat = is_numeric($trader->win) ? ($trader->win / 20) : (4.4 + ($loop->index % 5) / 10); @endphp {{ number_format($rat, 1) }}</span>
                    </div>
                </div>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <div class="stat-box-mobile">
                        <div class="text-secondary mb-1" style="font-size: 0.65rem;">WIN RATE</div>
                        <div class="text-success fw-bold" style="font-size: 1.1rem;">
                            {{ is_numeric($trader->win) ? $trader->win : (88 + ($loop->index % 10)) }}%
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="stat-box-mobile">
                        <div class="text-secondary mb-1" style="font-size: 0.65rem;">DRAWDOWN</div>
                        <div class="text-danger fw-bold" style="font-size: 1.1rem;">4.2%</div>
                    </div>
                </div>
            </div>

            @if($trader->approved == 'off' || !$trader->user_id)
                <button class="btn btn-mirror-mobile" onclick="openCopyModal('{{ $trader->id }}', '{{ $trader->name }}', '{{ $trader->percentage }}')">
                    <i class="ri-repeat-2-line me-1"></i> MIRROR TRADES
                </button>
            @else
                <button class="btn w-100 py-3" onclick="cancelCopy('{{ $trader->id }}')" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; font-weight: bold;">
                    <i class="ri-stop-circle-line me-1"></i> TERMINATE
                </button>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $data->links('pagination::bootstrap-4') }}
    </div>
</div>

<!-- Mobile Copy Modal -->
<div class="modal fade" id="copy_modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable m-0" style="align-items: flex-end; min-height: 100%;">
        <div class="modal-content glass-modal-mobile text-white w-100" style="min-height: 80vh;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold">Mirror Strategy</h5>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-3" style="background: rgba(255,255,255,0.05);">
                    <div class="trader-avatar-wrapper" style="width: 40px; height: 40px;">
                        <img src="{{ asset('images/traders/trader_1.png') }}" id="modal-img">
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold" id="modal-trader-name">Trader</h6>
                        <div class="small text-success">Active Strategy</div>
                    </div>
                </div>

                <form id="copy-form">
                    <input type="hidden" id="trader-id">
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="stat-box-mobile">
                                <div class="small text-secondary mb-1">Profit Share</div>
                                <div class="text-primary fw-bold fs-5"><span id="trader-commission">0</span>%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-box-mobile">
                                <div class="small text-secondary mb-1">Risk Level</div>
                                <div class="text-warning fw-bold fs-5">Med</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small text-secondary mb-1">Select Assets (Multiple)</label>
                        <select id="copy-symbols" name="symbols[]" class="form-control form-control-glass" multiple style="height: 90px;" required>
                            @if(isset($b))
                                @foreach($b as $asset)
                                    <option value="{{ $asset->symbols }}">{{ $asset->symbols }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="small text-secondary mb-1">Capital Per Asset (USD)</label>
                        <div class="input-group">
                            <span class="input-group-text border-0" style="background: rgba(0,0,0,0.3); color: #fff;">$</span>
                            <input type="number" id="copy-amount" class="form-control form-control-glass border-start-0 py-2 fs-5 font-weight-bold" placeholder="5000" required>
                        </div>
                    </div>

                    <div class="mb-4 p-2 rounded border" style="background: rgba(59, 130, 246, 0.05); border-color: rgba(59, 130, 246, 0.2) !important;">
                        <div class="form-check form-switch d-flex align-items-center">
                            <input class="form-check-input me-2" type="checkbox" id="copy-autorenew">
                            <label class="form-check-label text-white small" for="copy-autorenew">Auto-Renew Contract</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-mirror-mobile py-3" id="confirm-btn">
                        <i class="ri-play-circle-line me-1"></i> BEGIN MIRRORING
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    document.getElementById('trader-search').addEventListener('keyup', function() {
        let val = this.value.toLowerCase();
        let items = document.querySelectorAll('.trader-item');
        items.forEach(item => {
            let text = item.querySelector('.name-target').innerText.toLowerCase();
            item.style.display = text.includes(val) ? '' : 'none';
        });
    });

    function openCopyModal(id, name, commission) {
        $('#trader-id').val(id);
        $('#modal-trader-name').text(name);
        $('#trader-commission').text(commission);
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

        if(!confirm('Begin mirroring? Total cost: $' + (amount * symbols.length).toFixed(2))) return;

        $('#confirm-btn').attr('disabled', true).html('<i class="ri-loader-4-line fa-spin"></i> Initializing...');

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
                toastr.error(data.error || 'Request failed.');
                $('#confirm-btn').removeAttr('disabled').html('<i class="ri-play-circle-line me-1"></i> BEGIN MIRRORING');
            }
        });
    });

    function cancelCopy(id) {
        if(confirm('Stop mirroring this trader?')) {
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
</script>
@endsection
