@extends('layouts.user.app')
@section('title', 'Family & Kids Accounts')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-5" data-aos="fade-up">
        <div class="col-xl-8">
            <h1 class="outfit font-weight-bold text-white mb-1" style="font-size: 2rem;">Family & Kids Accounts</h1>
            <p class="text-secondary mb-0">Invest for your kids' future. Set up savings accounts that grow over time with the power of compound returns.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        @foreach($plans as $plan)
        @php
            $tierColors = [1 => '#3b82f6', 2 => '#8b5cf6', 3 => '#f59e0b', 4 => '#ff3333'];
            $tierIcons = [1 => 'ri-book-2-line', 2 => 'ri-award-line', 3 => 'ri-trophy-line', 4 => 'ri-vip-crown-line'];
            $color = $tierColors[$plan->tier] ?? '#3b82f6';
            $icon = $tierIcons[$plan->tier] ?? 'ri-book-2-line';
        @endphp
        <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
            <div class="h-100" style="background: rgba(16, 18, 27, 0.5); backdrop-filter: blur(16px); border-radius: 24px; border: 1px solid rgba(255,255,255,0.06); padding: 2rem; transition: all 0.4s; position: relative; overflow: hidden;">
                <div style="position: absolute; top: 0; right: 0; width: 120px; height: 120px; background: radial-gradient(circle at top right, {{ $color }}15, transparent); border-radius: 0 24px 0 0;"></div>
                
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: {{ $color }}15; border: 1px solid {{ $color }}30;">
                        <i class="{{ $icon }}" style="color: {{ $color }}; font-size: 1.5rem;"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold text-white">{{ $plan->name }}</div>
                        <div class="small" style="color: {{ $color }};">Tier {{ $plan->tier }}</div>
                    </div>
                </div>

                <div class="text-center py-3 mb-4" style="background: {{ $color }}08; border-radius: 14px; border: 1px solid {{ $color }}15;">
                    <div class="h2 mb-0 font-weight-bold" style="color: {{ $color }};">{{ $plan->interest_rate }}%</div>
                    <div class="small text-secondary">APY · {{ $plan->duration_months }} months</div>
                </div>

                <ul class="list-unstyled mb-4 small text-secondary">
                    <li class="d-flex align-items-center gap-2 mb-2"><i class="ri-checkbox-circle-fill text-success"></i> Min: ${{ number_format($plan->min_amount) }}</li>
                    <li class="d-flex align-items-center gap-2 mb-2"><i class="ri-checkbox-circle-fill text-success"></i> Max: ${{ number_format($plan->max_amount) }}</li>
                    <li class="d-flex align-items-center gap-2 mb-2"><i class="ri-checkbox-circle-fill text-success"></i> {{ $plan->duration_months }}-month maturity</li>
                </ul>

                <button class="btn w-100 py-3" onclick="openSavings('{{ $plan->id }}', '{{ $plan->name }}', '{{ $plan->min_amount }}', '{{ $plan->max_amount }}')" style="background: {{ $color }}; color: #fff; font-weight: 800; border-radius: 14px; border: none;">
                    Open Account
                </button>
            </div>
        </div>
        @endforeach
    </div>

    @if($savings->count() > 0)
    <div class="row" data-aos="fade-up">
        <div class="col-12">
            <h2 class="outfit font-weight-bold text-white mb-4">Active Trust Accounts</h2>
            <div class="glass-card satin-border overflow-hidden" style="border-radius: 20px;">
                <div class="table-responsive">
                    <table class="table text-white mb-0">
                        <thead style="background: rgba(255,255,255,0.02);">
                            <tr>
                                <th class="border-0 small text-secondary py-3">PLAN</th>
                                <th class="border-0 small text-secondary py-3">DEPOSITED</th>
                                <th class="border-0 small text-secondary py-3">EARNED</th>
                                <th class="border-0 small text-secondary py-3">MATURITY</th>
                                <th class="border-0 small text-secondary py-3">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($savings as $s)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                <td class="py-4 font-weight-bold">{{ $s->plan?->name ?? 'N/A' }}</td>
                                <td class="py-4">${{ number_format($s->amount, 2) }}</td>
                                <td class="py-4 text-success">+${{ number_format($s->earned, 2) }}</td>
                                <td class="py-4 small text-secondary">{{ \Illuminate\Support\Carbon::parse($s->maturity_date)->format('M d, Y') }}</td>
                                <td class="py-4">
                                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(255, 51, 51, 0.1); color: #ff3333; font-size: 0.65rem;">{{ strtoupper($s->status) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('js')
<script>
    function openSavings(id, name, min, max) {
        let amount = prompt(`Enter deposit for ${name} (Min: $${min}, Max: $${max}):`, min);
        if (amount && parseFloat(amount) >= parseFloat(min)) {
            fetch("{{ route('user.student_savings.store') }}", {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                body: JSON.stringify({id, amount})
            })
            .then(res => res.json().then(data => ({ok: res.ok, data})))
            .then(({ok, data}) => {
                if (ok && data.status) { toastr.success(data.status); setTimeout(() => window.location.reload(), 1500); }
                else { toastr.error(data.error || 'Failed'); }
            })
            .catch(() => toastr.error('Network error'));
        }
    }
</script>
@endpush
