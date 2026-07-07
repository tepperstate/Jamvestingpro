@extends('layouts.user.app')

@section('title', 'Manage Wallets')

@section('content')
<style>
    .glass-card-premium {
        background: rgba(16, 18, 27, 0.4);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 16px;
    }
    
    .wallet-toggle {
        width: 48px;
        height: 24px;
        background: rgba(255,255,255,0.1);
        border-radius: 20px;
        position: relative;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .wallet-toggle.active {
        background: var(--accent-success);
    }
    
    .wallet-toggle .toggle-knob {
        width: 20px;
        height: 20px;
        background: white;
        border-radius: 50%;
        position: absolute;
        top: 2px;
        left: 2px;
        transition: transform 0.3s;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    .wallet-toggle.active .toggle-knob {
        transform: translateX(24px);
    }

    .wallet-item {
        transition: all 0.2s;
    }
    
    .wallet-item:hover {
        background: rgba(255,255,255,0.02) !important;
    }
</style>

<div class="container py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="font-weight-bold text-white mb-1"><i data-lucide="wallet" class="mr-2"></i> Manage Wallets</h4>
            <p class="text-secondary small mb-0">Enable or disable wallets for trading and deposits.</p>
        </div>
        <div class="col-md-6 text-md-right mt-3 mt-md-0">
            <a href="{{ route('dashboard.wallets') }}" class="btn btn-outline-secondary btn-sm"><i data-lucide="arrow-left" class="mr-1"></i> Back</a>
        </div>
    </div>

    <div class="glass-card-premium p-0">
        <div class="table-responsive">
            <table class="table table-borderless text-white mb-0">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <th class="py-3 px-4 text-secondary small font-weight-bold">ASSET</th>
                        <th class="py-3 px-4 text-secondary small font-weight-bold">BALANCE</th>
                        <th class="py-3 px-4 text-secondary small font-weight-bold text-right">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($systemCoins as $coin)
                    @php
                        $userWallet = $userWallets->get($coin->symbol);
                        $isEnabled = $userWallet ? $userWallet->is_enabled : false;
                        $balance = $userWallet ? $userWallet->balance : 0;
                    @endphp
                    <tr class="wallet-item border-bottom" style="border-color: rgba(255,255,255,0.05) !important;">
                        <td class="py-3 px-4">
                            <div class="d-flex align-items-center gap-3">
                                <x-asset-logo :symbol="$coin->symbol" size="md" />
                                <div>
                                    <div class="font-weight-bold text-white">{{ $coin->name }}</div>
                                    <div class="text-secondary" style="font-size: 0.75rem;">{{ $coin->symbol }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-4 align-middle">
                            <div class="font-weight-bold text-white">{{ number_format($balance, 6) }} {{ $coin->symbol }}</div>
                        </td>
                        <td class="py-3 px-4 align-middle text-right">
                            <div class="d-flex justify-content-end align-items-center">
                                <div class="wallet-toggle {{ $isEnabled ? 'active' : '' }}" onclick="toggleWallet('{{ $coin->symbol }}', this)">
                                    <div class="toggle-knob"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('js')
<script>
    lucide.createIcons();

    function toggleWallet(symbol, element) {
        const isActive = $(element).hasClass('active');
        const newState = !isActive;
        
        // Optimistic UI update
        $(element).toggleClass('active');
        
        fetch('{{ route('wallets.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                symbol: symbol,
                is_enabled: newState
            })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                toastr.success(data.message);
            } else {
                // Revert if failed
                $(element).toggleClass('active');
                toastr.error('Failed to update wallet status');
            }
        })
        .catch(err => {
            // Revert if error
            $(element).toggleClass('active');
            toastr.error('An error occurred');
        });
    }
</script>
@endpush
@endsection
