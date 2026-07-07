@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">User Asset Account Registry</h4>
            <p class="text-secondary mb-0">Overview of distributed balances across the network.</p>
        </div>
        <a onclick="history.back()" href="javascript:void(0)" class="btn btn-outline-light btn-sm rounded-pill px-3 glass-btn">
            <i class="ri-arrow-left-line me-1"></i> Back
        </a>
    </div>

    <div class="row">
        @forelse($data as $index => $c)
            @php
                // Generate varied subtle accents for differentiation
                $accents = ['primary', 'success', 'warning', 'info', 'purple'];
                $accentClass = $accents[$index % count($accents)];
                $bgColors = [
                    'primary' => 'rgba(59, 130, 246, 0.05)',
                    'success' => 'rgba(16, 185, 129, 0.05)',
                    'warning' => 'rgba(245, 158, 11, 0.05)',
                    'info'    => 'rgba(6, 182, 212, 0.05)',
                    'purple'  => 'rgba(139, 92, 246, 0.05)',
                ];
                $bgGlow = $bgColors[$accentClass];
            @endphp
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="glass-card shadow-sm p-4 h-100 position-relative premium-card-hover d-flex flex-column" style="background: rgba(30, 32, 38, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 12px;">
                    
                    <!-- Card Header -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; background: {{ $bgGlow }}; border: 1px solid rgba(255, 255, 255, 0.1);">
                                <i class="ri-wallet-3-line" style="color: var(--{{ $accentClass }});"></i>
                            </div>
                            <span class="font-weight-bold text-white tracking-wider" style="font-size: 0.9rem; letter-spacing: 0.5px;">
                                {{ $c->name }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Balance -->
                    <div class="mb-4 flex-grow-1">
                        <div class="text-secondary small text-uppercase font-weight-bold mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">Balance</div>
                        <h3 class="text-white outfit mb-0 font-weight-bold d-flex align-items-baseline gap-1">
                            {{ number_format($c->amount, 5) }} 
                            <span class="small text-secondary" style="font-size: 0.9rem; font-weight: 500;">{{ $c->symbol }}</span>
                        </h3>
                    </div>

                    <!-- Actions -->
                    <div class="mt-auto pt-3 border-top d-flex gap-2" style="border-color: rgba(255,255,255,0.05) !important;">
                        <a href="{{ route('fund', ['user' => $c->user_id, 'symbol' => $c->symbol]) }}" class="btn btn-success-soft text-success btn-sm flex-grow-1 font-weight-bold rounded glass-btn">
                            <i class="ri-add-line me-1"></i> Credit
                        </a>
                        <a href="{{ route('debit', ['user' => $c->user_id, 'symbol' => $c->symbol]) }}" class="btn btn-danger-soft text-danger btn-sm flex-grow-1 font-weight-bold rounded glass-btn">
                            <i class="ri-subtract-line me-1"></i> Debit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="glass-card p-5 text-center text-secondary" style="background: rgba(20, 21, 26, 0.7); border: 1px dashed rgba(255, 255, 255, 0.1); border-radius: 16px;">
                    <i class="ri-inbox-line h1 d-block mb-3 opacity-20"></i>
                    <p class="mb-0">No active account balances found for this user context.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .glass-btn { transition: all 0.3s ease; }
    .glass-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
    .premium-card-hover { transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease; }
    .premium-card-hover:hover { transform: translateY(-5px); box-shadow: 0 15px 35px 0 rgba(0, 0, 0, 0.4) !important; }
    .z-index-1 { z-index: 1; }
    
    /* Corrected Soft Buttons for Glassmorphism */
    .btn-success-soft { background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399 !important; }
    .btn-success-soft:hover { background: rgba(16, 185, 129, 0.25); color: #10b981 !important; border-color: rgba(16, 185, 129, 0.5); }
    
    .btn-danger-soft { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171 !important; }
    .btn-danger-soft:hover { background: rgba(239, 68, 68, 0.25); color: #ef4444 !important; border-color: rgba(239, 68, 68, 0.5); }
    
    .tracking-wider { letter-spacing: 0.05em; }
</style>
@endsection
