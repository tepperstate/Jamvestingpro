@extends('layouts.admin.app')
@section('content')
<style>
    .gradient-text {
        background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .signal-badge {
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-win { background: rgba(255, 51, 51, 0.1); color: #ff3333; border: 1px solid rgba(255, 51, 51, 0.2); }
    .badge-loss { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
    
    .signals-text {
        font-family: 'JetBrains Mono', monospace;
        color: #94a3b8;
        font-size: 0.85rem;
    }
    .signals-text strong { color: #fff; }
    .tf-check {
        display: flex; align-items: center; gap: 6px;
        padding: 4px 8px; border-radius: 6px; cursor: pointer;
        font-size: 0.8rem; color: #94a3b8; transition: background 0.15s;
        margin: 0;
    }
    .tf-check:hover { background: rgba(255,255,255,0.04); }
    .tf-check input[type=checkbox] { accent-color: #3b82f6; cursor: pointer; }
    .tf-check input[type=checkbox]:checked + span { color: #fff; font-weight: 600; }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text font-weight-bold">Signal Intelligence</h1>
            <p class="text-muted mb-0">Architectural signal generation for institutional-grade market entry.</p>
        </div>
        <div class="d-flex gap-3">
            <button data-toggle="modal" data-target="#generateModal" class="btn btn-primary glass-panel border-0 px-4 py-2 satin-border" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700;">
                <i data-lucide="zap" class="mr-2" style="width: 16px; display: inline-block; vertical-align: middle;"></i> Generate Signals
            </button>
            <button id="copyAll" class="btn btn-outline-light glass-panel px-4 py-2 satin-border font-weight-bold">
                <i data-lucide="copy" class="mr-2" style="width: 16px; display: inline-block; vertical-align: middle;"></i> Copy Registry
            </button>
            <a href="{{route('deleteAllSignal')}}" class="btn btn-outline-danger glass-panel px-4 py-2 satin-border font-weight-bold">
                <i data-lucide="trash-2" class="mr-2" style="width: 16px; display: inline-block; vertical-align: middle;"></i> Clear Stack
            </a>
        </div>
    </div>

    <!-- Signals Grid -->
    <div class="row">
        @forelse($data as $d)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="glass-panel p-4 satin-border h-100 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-3">
                        @php $sym = explode('/', $d->symbols)[0]; @endphp
                        <div class="icon-box glass-panel p-1" style="background: rgba(255, 255, 255, 0.05) !important; border-radius: 10px;">
                            <img src="https://img.logokit.com/crypto/{{ $sym }}?token=pk_frb53d906f2b73e352f31e" 
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ $sym }}&background=000&color=fff';" 
                                 style="width: 32px; height: 32px; border-radius: 8px; object-fit: cover;">
                        </div>
                        <h4 class="h5 text-white mb-0 font-weight-bold">{{ $d->symbols }}</h4>
                    </div>
                    <span class="signal-badge {{ $d->profits == 'win' ? 'badge-win' : 'badge-loss' }}">{{ $d->profits }}</span>
                </div>

                <div class="signals-text mb-4 flex-grow-1 card-body signals">
                    <div class="mb-2"><strong>MARKET:</strong> {{ $d->symbols }}</div>
                    <div class="mb-2"><strong>DIRECTION:</strong> <span class="{{ $d->type == 'call' ? 'text-success' : 'text-danger' }} font-weight-bold">{{ strtoupper($d->type) }}</span></div>
                    <div class="mb-2"><strong>INVESTMENT:</strong> ${{ number_format($d->amount, 2) }}</div>
                    <div class="mb-2"><strong>TIMEFRAME:</strong> {{ $d->time }}</div>
                    <div class="mb-0"><strong>STRIKE RATE:</strong> <span class="text-primary">{{ $d->strike_rate }}</span></div>
                </div>

                <div class="d-flex gap-2">
                    <button class="btn btn-sm glass-panel text-white border-0 flex-grow-1 copy py-2">
                        <i data-lucide="copy" class="mr-1" style="width:14px; display: inline-block; vertical-align: middle;"></i> Copy
                    </button>
                    <a href="{{route('delete_g', $d->id)}}" class="btn btn-sm glass-panel text-danger border-0 px-3 py-2">
                        <i data-lucide="trash-2" style="width:14px"></i>
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 py-5 text-center">
            <div class="glass-panel p-5 satin-border d-inline-block">
                <i data-lucide="inbox" class="text-muted mb-3" style="width: 48px; height: 48px;"></i>
                <h3 class="h4 text-white font-weight-bold">Intelligence Feed Idle</h3>
                <p class="text-muted mb-0">Inject new signals into the registry to begin monitoring.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

@push('modals')
<!-- Generate Modal -->
<div class="modal fade" id="generateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document"> 
        <div class="modal-content satin-border shadow-2xl overflow-hidden border-0" style="background: var(--bg-main) !important; border-radius: var(--radius-2xl);">
            <div class="modal-header border-0 p-4" style="background: rgba(255,255,255,0.02);">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box glass-panel p-2" style="background: rgba(59, 130, 246, 0.1) !important; border-radius: 12px;">
                        <i data-lucide="zap" class="text-primary" style="width: 20px; height: 20px;"></i>
                    </div>
                    <h4 class="modal-title font-weight-bold text-white mb-0">Architectural Signal Execution</h4>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form action="{{route('getRandomAssets')}}" method="post" class="row">
                    @csrf
                    <div class="col-lg-12 form-group mb-4">
                        <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Target Market</label>
                        <select name="market" class="form-control glass-panel border-0 text-white px-3" style="height: 50px; border-radius:12px;" required>
                            @foreach($cat as $v)
                                <option value="{{$v->id}}">{{$v->name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- CALL Signals -->
                    <div class="col-12 mb-2">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge px-3 py-2 font-weight-bold" style="background: rgba(255, 51, 51, 0.15); color: #ff3333; border-radius: 8px; font-size: 0.7rem; letter-spacing: 0.5px;">CALL (UPSWING)</span>
                        </div>
                    </div>
                    <div class="col-lg-6 form-group mb-4">
                        <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Call Wins</label>
                        <input type="number" name="call_wins" class="form-control glass-panel border-0 text-white px-3" style="height: 50px; border-radius:12px;" placeholder="e.g. 4" value="0" min="0" required>
                    </div>
                    <div class="col-lg-6 form-group mb-4">
                        <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Call Losses</label>
                        <input type="number" name="call_losses" class="form-control glass-panel border-0 text-white px-3" style="height: 50px; border-radius:12px;" placeholder="e.g. 1" value="0" min="0" required>
                    </div>

                    <!-- PUT Signals -->
                    <div class="col-12 mb-2">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge px-3 py-2 font-weight-bold" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border-radius: 8px; font-size: 0.7rem; letter-spacing: 0.5px;">PUT (DECLINE)</span>
                        </div>
                    </div>
                    <div class="col-lg-6 form-group mb-4">
                        <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Put Wins</label>
                        <input type="number" name="put_wins" class="form-control glass-panel border-0 text-white px-3" style="height: 50px; border-radius:12px;" placeholder="e.g. 3" value="0" min="0" required>
                    </div>
                    <div class="col-lg-6 form-group mb-4">
                        <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Put Losses</label>
                        <input type="number" name="put_losses" class="form-control glass-panel border-0 text-white px-3" style="height: 50px; border-radius:12px;" placeholder="e.g. 1" value="0" min="0" required>
                    </div>

                    <div class="col-lg-4 form-group mb-4">
                        <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Execution Window(s) <span class="text-info" style="font-size:0.65rem;">(select multiple)</span></label>
                        <div class="glass-panel p-3" style="border-radius:12px; border: 1px solid rgba(255,255,255,0.06);">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Timeframes</small>
                                <a href="#" onclick="event.preventDefault(); toggleAllTimeframes()" class="small text-primary font-weight-bold" id="tf-toggle-link">Select All</a>
                            </div>
                            <div class="row g-1">
                                <div class="col-6"><label class="tf-check"><input type="checkbox" name="time[]" value="1min" checked> <span>1 min</span></label></div>
                                <div class="col-6"><label class="tf-check"><input type="checkbox" name="time[]" value="5mins" checked> <span>5 mins</span></label></div>
                                <div class="col-6"><label class="tf-check"><input type="checkbox" name="time[]" value="10mins"> <span>10 mins</span></label></div>
                                <div class="col-6"><label class="tf-check"><input type="checkbox" name="time[]" value="15mins" checked> <span>15 mins</span></label></div>
                                <div class="col-6"><label class="tf-check"><input type="checkbox" name="time[]" value="30mins"> <span>30 mins</span></label></div>
                                <div class="col-6"><label class="tf-check"><input type="checkbox" name="time[]" value="1 hour"> <span>1 hour</span></label></div>
                                <div class="col-6"><label class="tf-check"><input type="checkbox" name="time[]" value="2 hours"> <span>2 hours</span></label></div>
                                <div class="col-6"><label class="tf-check"><input type="checkbox" name="time[]" value="4 hours"> <span>4 hours</span></label></div>
                                <div class="col-6"><label class="tf-check"><input type="checkbox" name="time[]" value="1 day"> <span>1 day</span></label></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 form-group mb-4">
                        <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Min Amount</label>
                        <input type="number" name="min" class="form-control glass-panel border-0 text-white px-3" style="height: 50px; border-radius:12px;" value="100" required>
                    </div>

                    <div class="col-lg-4 form-group mb-4">
                        <label class="text-muted small text-uppercase font-weight-bold tracking-wider mb-2">Max Amount</label>
                        <input type="number" name="max" class="form-control glass-panel border-0 text-white px-3" style="height: 50px; border-radius:12px;" value="1000" required>
                    </div>

                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-premium w-100 py-3 font-weight-bold">
                            INJECT SIGNAL SYSTEM
                        </button>
                    </div>
                </form>
            </div>  
        </div>
    </div>
</div>
@endpush

<script>
    $(document).ready(function() {
        lucide.createIcons();

        // Toggle all timeframe checkboxes
        window.toggleAllTimeframes = function() {
            var boxes = document.querySelectorAll('input[name="time[]"]');
            var allChecked = Array.from(boxes).every(b => b.checked);
            boxes.forEach(b => b.checked = !allChecked);
            document.getElementById('tf-toggle-link').textContent = allChecked ? 'Select All' : 'Deselect All';
        };

        // Mass Copy All - copies all signal cards
        document.getElementById('copyAll').addEventListener('click', function() {
            let content = '📡 SIGNAL INTELLIGENCE REGISTRY — ' + new Date().toLocaleString() + '\n\n';
            document.querySelectorAll('.card-body.signals').forEach(function(element, idx) {
                content += '━━━ Signal #' + (idx + 1) + ' ━━━\n';
                content += element.innerText.trim() + '\n\n';
            });

            navigator.clipboard.writeText(content.trim()).then(function() {
                toastr.success('All ' + document.querySelectorAll('.card-body.signals').length + ' signals copied to clipboard!');
            }).catch(function() {
                // Fallback for older browsers
                const ta = document.createElement('textarea');
                ta.value = content.trim();
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                toastr.success('Registry copied to clipboard!');
            });
        });

        // Individual Copy - copies single signal card
        $(document).on("click", ".copy", function() {
            var signalsElement = $(this).closest(".h-100").find(".signals")[0];
            if (!signalsElement) return;

            var textToCopy = "📡 SIGNAL Intel:\n" + signalsElement.innerText.trim();
            var btn = $(this);

            navigator.clipboard.writeText(textToCopy).then(function() {
                btn.html('<i data-lucide="check" class="mr-1" style="width:14px; display: inline-block; vertical-align: middle;"></i> Copied!');
                btn.addClass('text-success');
                lucide.createIcons();
                toastr.success("Signal copied!");
                setTimeout(function() {
                    btn.html('<i data-lucide="copy" class="mr-1" style="width:14px; display: inline-block; vertical-align: middle;"></i> Copy');
                    btn.removeClass('text-success');
                    lucide.createIcons();
                }, 2000);
            }).catch(function() {
                // Fallback
                const ta = document.createElement('textarea');
                ta.value = textToCopy;
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                toastr.success("Signal copied!");
            });
        });
    });
</script>
@endsection


