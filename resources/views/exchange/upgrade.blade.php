@extends('layouts.user.app')

@section('title', 'Account Upgrade')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="outfit font-weight-bold">Account Upgrade</h2>
                <p class="text-secondary">Get more out of your account with higher limits, premium features, and priority support.</p>
            </div>

            <div class="card glass-card border-0 shadow-lg" data-aos="fade-up" data-aos-delay="100">
                <div class="card-body p-4 p-md-5">
                    @if(session('error'))
                        <div class="alert bg-danger-soft text-danger border-0 mb-4">
                            <i class="ri-error-warning-line me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="alert bg-success-soft text-success border-0 mb-4">
                            <i class="ri-check-line me-2"></i> {{ session('status') }}
                        </div>
                    @endif

                    <form method="post" action="{{ route('upgrade.post') }}">
                        @csrf
                        <div class="form-group mb-4">
                            <label class="small text-secondary fw-bold mb-2">Select Target Tier</label>
                            <select name="name" class="form-control premium-input text-white py-3" style="background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); border-radius: 12px; appearance: none;" required>
                                <option value="" disabled selected>-- Choose Premium Tier --</option>
                                @php
                                    $planOrder = ['Standard' => 1, 'Silver' => 2, 'Gold' => 3, 'Platinum' => 4, 'Platinum Plus' => 5, 'Diamond' => 6];
                                    $currentOrder = $planOrder[auth()->user()->package_plan] ?? 0;
                                @endphp
                                @foreach ($data as $d)
                                    @php $thisOrder = $planOrder[$d->name] ?? 0; @endphp
                                    @if($thisOrder > $currentOrder)
                                        <option value="{{ $d->name }}">{{ $d->name }}</option>
                                    @elseif($thisOrder == $currentOrder)
                                        <option value="{{ $d->name }}" disabled>{{ $d->name }} (Current Active Tier)</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-5">
                            <label class="small text-secondary fw-bold mb-2">Upgrade Justification / Comment</label>
                            <textarea name="message" class="form-control premium-input text-white" rows="3" style="background: rgba(0,0,0,0.2); border: 1px solid var(--glass-border); border-radius: 12px;" placeholder="Briefly describe why you are requesting this upgrade..." required></textarea>
                        </div>

                        <div class="text-center">
                            @if(auth()->user()->is_demo == 1)
                                <button type="submit" disabled class="btn btn-secondary btn-lg w-100 py-3 rounded-pill fw-bold" style="opacity: 0.6;">
                                    <i class="ri-lock-line me-2"></i> Switch to Live Account Required
                                </button>
                            @else
                                <button type="submit" class="btn btn-premium btn-lg w-100 py-3 rounded-pill fw-bold bg-primary text-white border-0 shadow" style="background: linear-gradient(135deg, #ef4444, #dc2626) !important; transition: all 0.3s;">
                                    <i class="ri-rocket-line me-2"></i> Submit Upgrade Request
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-5 text-secondary small" data-aos="fade-up" data-aos-delay="200">
                <i class="ri-shield-check-line me-1"></i> All upgrade requests are securely processed and subject to account review.
            </div>
        </div>
    </div>
</div>

<style>
    .premium-input:focus {
        background: rgba(0,0,0,0.3) !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
    }
    select.premium-input option {
        background: #1e1e1e;
        color: white;
    }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.1); }
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
</style>
@endsection
