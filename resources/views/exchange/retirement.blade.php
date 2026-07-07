@extends('layouts.user.app')
@section('title', 'Retirement')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-5" data-aos="fade-up">
        <div class="col-xl-8">
            <h1 class="outfit font-weight-bold text-white mb-1" style="font-size: 2rem;">Tax-Advantaged Retirement (IRA)</h1>
            <p class="text-secondary mb-0">Maximize your long-term wealth with flexible tax-advantaged retirement accounts (Traditional & Roth IRAs).</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        @foreach($plans as $plan)
        @php $accent = [1=>'#3b82f6',2=>'#8b5cf6',3=>'#f59e0b',4=>'#ff3333'][$plan->tier] ?? '#3b82f6'; @endphp
        <div class="col-xl-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
            <div class="h-100" style="background: rgba(16,18,27,0.5); border-radius: 24px; border: 1px solid {{ $accent }}20; padding: 2rem;">
                <h4 class="font-weight-bold text-white mb-1">{{ $plan->name }}</h4>
                <div class="small mb-4" style="color:{{ $accent }};">Tier {{ $plan->tier }}</div>
                <div class="mb-4 p-3" style="background:rgba(0,0,0,0.2);border-radius:14px;">
                    <div class="d-flex justify-content-between mb-2"><span class="small text-secondary">Match</span><span class="font-weight-bold" style="color:{{ $accent }};">{{ $plan->employer_match_pct }}%</span></div>
                    <div class="d-flex justify-content-between mb-2"><span class="small text-secondary">Vesting</span><span class="small text-white">{{ ucfirst($plan->vesting_schedule) }}</span></div>
                    <div class="d-flex justify-content-between"><span class="small text-secondary">Max/Yr</span><span class="small text-white">${{ number_format($plan->max_contribution) }}</span></div>
                </div>
                <button class="btn w-100 py-3" onclick="contribute('{{ $plan->id }}','{{ $plan->name }}','{{ $plan->min_contribution }}','{{ $plan->max_contribution }}')" style="background:{{ $accent }};color:#fff;font-weight:800;border-radius:14px;border:none;">Contribute</button>
            </div>
        </div>
        @endforeach
    </div>

    @if($accounts->count() > 0)
    <div class="row" data-aos="fade-up"><div class="col-12">
        <h2 class="outfit font-weight-bold text-white mb-4">Active IRA Accounts</h2>
        <div class="glass-card satin-border overflow-hidden" style="border-radius:20px;"><div class="table-responsive">
            <table class="table text-white mb-0">
                <thead style="background:rgba(255,255,255,0.02);"><tr>
                    <th class="border-0 small text-secondary py-3">PLAN</th>
                    <th class="border-0 small text-secondary py-3">BALANCE</th>
                    <th class="border-0 small text-secondary py-3">YOUR $</th>
                    <th class="border-0 small text-secondary py-3">EMPLOYER $</th>
                    <th class="border-0 small text-secondary py-3">STATUS</th>
                </tr></thead>
                <tbody>
                    @foreach($accounts as $a)
                    <tr style="border-bottom:1px solid rgba(255,255,255,0.03);">
                        <td class="py-4 font-weight-bold">{{ $a->plan?->name ?? 'N/A' }}</td>
                        <td class="py-4 text-success font-weight-bold">${{ number_format($a->balance, 2) }}</td>
                        <td class="py-4">${{ number_format($a->employee_contributions, 2) }}</td>
                        <td class="py-4 text-info">${{ number_format($a->employer_contributions, 2) }}</td>
                        <td class="py-4"><span class="badge rounded-pill px-3 py-2" style="background:rgba(16,185,129,0.1);color:#ff3333;font-size:0.65rem;">{{ strtoupper($a->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div></div>
    </div></div>
    @endif
</div>
@endsection

@push('js')
<script>
function contribute(id, name, min, max) {
    let amount = prompt(`Contribute to ${name} (Min: $${min}, Max: $${max}):`, min);
    if (amount && parseFloat(amount) >= parseFloat(min)) {
        fetch("{{ route('user.retirement.contribute') }}", {
            method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':"{{ csrf_token() }}"},
            body: JSON.stringify({id, amount})
        }).then(r=>r.json().then(d=>({ok:r.ok,data:d}))).then(({ok,data})=>{
            if(ok&&data.status){toastr.success(data.status);setTimeout(()=>window.location.reload(),1500);}
            else{toastr.error(data.error||'Failed');}
        }).catch(()=>toastr.error('Network error'));
    }
}
</script>
@endpush
