@extends('layouts.user.app')

@section('title', 'Manage Wallets')

@section('content')
<style>
    /* Premium Mobile CSS */
    .mobile-wallet-wrapper {
        min-height: 100vh;
        padding: 1.5rem 1rem 5rem 1rem;
        background: #090B10;
        background-image: 
            radial-gradient(circle at 15% 50%, rgba(153, 0, 0, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 85% 30%, rgba(153, 0, 0, 0.03) 0%, transparent 50%);
        color: #fff;
        font-family: 'Inter', -apple-system, sans-serif;
    }

    .glass-header {
        background: rgba(18, 22, 33, 0.6);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(153, 0, 0, 0.15);
        border-radius: 20px;
        padding: 1.25rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    }

    .glass-header h4 {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 800;
        color: #fff;
        display: flex;
        align-items: center;
        letter-spacing: -0.5px;
    }

    .glass-header h4 i {
        color: #990000;
        margin-right: 0.6rem;
    }

    .glass-header p {
        font-size: 0.8rem;
        color: rgba(255,255,255,0.5);
        margin: 0.3rem 0 0 0;
        line-height: 1.4;
    }

    .back-btn-gold {
        background: linear-gradient(135deg, rgba(153, 0, 0, 0.15), rgba(153, 0, 0, 0.05));
        border: 1px solid rgba(153, 0, 0, 0.4);
        color: #E5C158;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(153, 0, 0, 0.1);
        white-space: nowrap;
    }

    .back-btn-gold:hover, .back-btn-gold:active {
        background: linear-gradient(135deg, rgba(153, 0, 0, 0.25), rgba(153, 0, 0, 0.1));
        color: #FFF;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(153, 0, 0, 0.2);
    }

    .wallet-list {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .wallet-card {
        background: rgba(22, 27, 38, 0.5);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 24px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .wallet-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, #990000, #F3E5AB);
        opacity: 0.6;
        border-top-left-radius: 24px;
        border-bottom-left-radius: 24px;
    }

    .wallet-card:active {
        transform: scale(0.98);
    }

    .wallet-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-left: 0.5rem;
    }

    .wallet-info-main {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .asset-logo-container {
        background: rgba(255, 255, 255, 0.05);
        padding: 0.5rem;
        border-radius: 16px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .wallet-name {
        font-weight: 700;
        font-size: 1.15rem;
        color: #FFFFFF;
        margin-bottom: 0.15rem;
        letter-spacing: 0.2px;
    }

    .wallet-symbol {
        font-size: 0.85rem;
        color: #A0AEC0;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    .wallet-balance-box {
        background: rgba(10, 13, 20, 0.5);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid rgba(255, 255, 255, 0.03);
    }

    .balance-label {
        font-size: 0.8rem;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .balance-amount {
        font-weight: 800;
        font-size: 1.1rem;
        color: #F6E05E;
        font-family: 'SF Pro Display', -apple-system, sans-serif;
        text-shadow: 0 2px 10px rgba(153, 0, 0, 0.2);
    }

    .gold-toggle {
        width: 56px;
        height: 30px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 30px;
        position: relative;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0.0, 0.2, 1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
    }

    .gold-toggle.active {
        background: linear-gradient(135deg, #990000, #B7950B);
        border-color: transparent;
        box-shadow: 0 4px 12px rgba(153, 0, 0, 0.3), inset 0 2px 4px rgba(255,255,255,0.2);
    }

    .gold-toggle .toggle-knob {
        width: 24px;
        height: 24px;
        background: #FFFFFF;
        border-radius: 50%;
        position: absolute;
        top: 2px;
        left: 2px;
        transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }

    .gold-toggle.active .toggle-knob {
        transform: translateX(26px);
    }
</style>

<div class="mobile-wallet-wrapper">
    <div class="glass-header">
        <div>
            <h4><i data-lucide="wallet"></i> Wallets</h4>
            <p>Enable or disable wallets for trading and deposits.</p>
        </div>
        <a href="{{ route('dashboard.wallets') }}" class="back-btn-gold">
            <i data-lucide="arrow-left" style="width: 14px; height: 14px; margin-right: 4px;"></i> Back
        </a>
    </div>

    <div class="wallet-list">
        @foreach($systemCoins as $coin)
        @php
            $userWallet = $userWallets->get($coin->symbol);
            $isEnabled = $userWallet ? $userWallet->is_enabled : false;
            $balance = $userWallet ? $userWallet->balance : 0;
        @endphp
        <div class="wallet-card">
            <div class="wallet-top">
                <div class="wallet-info-main">
                    <div class="asset-logo-container">
                        <x-asset-logo :symbol="$coin->symbol" size="md" />
                    </div>
                    <div>
                        <div class="wallet-name">{{ $coin->name }}</div>
                        <div class="wallet-symbol">{{ $coin->symbol }}</div>
                    </div>
                </div>
                <div class="wallet-toggle-wrap">
                    <div class="gold-toggle {{ $isEnabled ? 'active' : '' }}" onclick="toggleWallet('{{ $coin->symbol }}', this)">
                        <div class="toggle-knob"></div>
                    </div>
                </div>
            </div>
            
            <div class="wallet-balance-box">
                <span class="balance-label">Balance</span>
                <span class="balance-amount">{{ number_format($balance, 6) }} {{ $coin->symbol }}</span>
            </div>
        </div>
        @endforeach
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
