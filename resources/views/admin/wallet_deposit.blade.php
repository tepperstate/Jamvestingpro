@extends('layouts.admin.app')
@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Deposit Infrastructure</h1>
            <p class="text-muted mb-0">Configure primary receiving endpoints for investment capital.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <button onclick="history.back()" class="btn btn-sm glass-panel border-0 text-white px-3 py-2 satin-border">
                <i data-lucide="arrow-left" class="mr-2" style="width: 14px; display: inline-block;"></i> Go Back
            </button>
        </div>
    </div>

    <!-- Bitcoin Infrastructure -->
    <div class="glass-card satin-border mb-4 overflow-hidden shadow-2xl">
        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
            <h3 class="h5 text-white mb-0 font-weight-bold d-flex align-items-center">
                <i data-lucide="bitcoin" class="mr-2 text-warning" style="width:20px"></i> Manual Bitcoin Deposit
            </h3>
        </div>
        <div class="card-body p-4">
            <form method='post' action="{{route('update_btc')}}" class='row align-items-end'>
                @csrf
                <div class='col-lg-6 mb-3'>
                    <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Uplink Address (BTC)</label>
                    <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='address' value="{{$btc_address}}" required style="border-radius:12px;">
                </div>
                <div class='col-lg-4 mb-3'>
                    <div class="glass-panel p-2 rounded text-center" style="border: 1px dashed var(--glass-border);">
                        <span class="text-white small x-small">{{ $btc }}</span>
                    </div>
                </div>
                <div class='col-lg-2 mb-3'>
                    <button class='btn btn-primary w-100 py-2 satin-border font-weight-bold' style="background: var(--accent-primary) !important; color: #ffffff !important; border-radius:12px; border:none;">
                        PERSIST
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ethereum Infrastructure -->
    <div class="glass-card satin-border mb-4 overflow-hidden shadow-2xl">
        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
            <h3 class="h5 text-white mb-0 font-weight-bold d-flex align-items-center">
                <i data-lucide="coins" class="mr-2 text-info" style="width:20px"></i> Manual Ethereum Deposit
            </h3>
        </div>
        <div class="card-body p-4">
            <form method='post' action="{{route('update_eth')}}" class='row align-items-end'>
                @csrf
                <div class='col-lg-6 mb-3'>
                    <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Uplink Address (ETH)</label>
                    <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='address' value="{{$eth_address}}" required style="border-radius:12px;">
                </div>
                <div class='col-lg-4 mb-3'>
                    <div class="glass-panel p-2 rounded text-center" style="border: 1px dashed var(--glass-border);">
                        <span class="text-white small x-small">{{ $eth }}</span>
                    </div>
                </div>
                <div class='col-lg-2 mb-3'>
                    <button class='btn btn-info w-100 py-2 satin-border font-weight-bold' style="color:white; border-radius:12px; border:none;">
                        PERSIST
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- USDT Infrastructure -->
    <div class="glass-card satin-border mb-4 overflow-hidden shadow-2xl">
        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
            <h3 class="h5 text-white mb-0 font-weight-bold d-flex align-items-center">
                <i data-lucide="shield-check" class="mr-2 text-success" style="width:20px"></i> Instant USDT Deposit
            </h3>
        </div>
        <div class="card-body p-4">
            <form method='post' action="{{route('update_usdt')}}" class='row align-items-end'>
                @csrf
                <div class='col-lg-6 mb-3'>
                    <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Uplink Address (USDT)</label>
                    <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='address' value="{{$usd_address}}" required style="border-radius:12px;">
                </div>
                <div class='col-lg-4 mb-3'>
                    <div class="glass-panel p-2 rounded text-center" style="border: 1px dashed var(--glass-border);">
                        <span class="text-white small x-small">{{ $usd }}</span>
                    </div>
                </div>
                <div class='col-lg-2 mb-3'>
                    <button class='btn btn-success w-100 py-2 satin-border font-weight-bold' style="color:white; border-radius:12px; border:none;">
                        PERSIST
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Solana Infrastructure -->
    <div class="glass-card satin-border mb-5 overflow-hidden shadow-2xl">
        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
            <h3 class="h5 text-white mb-0 font-weight-bold d-flex align-items-center">
                <i data-lucide="zap" class="mr-2 text-accent" style="width:20px; color: var(--accent-primary);"></i> Instant Solana Deposit
            </h3>
        </div>
        <div class="card-body p-4">
            <form method='post' action="{{route('update_solana')}}" class='row align-items-end'>
                @csrf
                <div class='col-lg-6 mb-3'>
                    <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Uplink Address (SOL)</label>
                    <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='address' value="{{$solana_address}}" required style="border-radius:12px;">
                </div>
                <div class='col-lg-4 mb-3'>
                    <div class="glass-panel p-2 rounded text-center" style="border: 1px dashed var(--glass-border);">
                        <span class="text-white small x-small">{{ $solana }}</span>
                    </div>
                </div>
                <div class='col-lg-2 mb-3'>
                    <button class='btn btn-primary w-100 py-2 satin-border font-weight-bold' style="background: var(--accent-primary) !important; color: #ffffff !important; border-radius:12px; border:none;">
                        PERSIST
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .x-small { font-size: 10px; }
    .tracking-wider { letter-spacing: 0.1em; }
    .gap-3 { gap: 1rem; }
    .form-control:focus { background: rgba(255,255,255,0.08) !important; box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2) !important; color: white !important; }
</style>

<script>
    $(document).ready(function() {
        lucide.createIcons();
    });
</script>

@if(session('status'))
    <script>toastr.success("{{session('status')}}","Sync Successful")</script>
@endif
@endsection

