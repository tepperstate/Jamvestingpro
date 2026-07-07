@extends('layouts.admin.app')
@section('title', 'Coupons & Rewards')
@section('content')
<div class="container-fluid">
    <a onclick="history.back()" href="javascript:void(0)">back</a>
    <div class="text-center mb-4">
        <h4 class="font-weight-bold m-0" style="color:var(--text-primary) !important">Coupons & Rewards</h4>
        <p class="text-muted mt-1">Manage promotional coupon codes and track redemptions</p>
    </div>

    <div class="row">
        <!-- Create Coupon -->
        <div class="col-lg-4">
            <div class="glass-card border-0 shadow p-4 mb-4">
                <h6 class="font-weight-bold mb-3" style="color:var(--accent-primary)">
                    <i data-lucide="plus-circle" style="width:16px;vertical-align:middle"></i> Create Coupon
                </h6>
                <form method="POST" action="{{route('admin.coupon.store')}}">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="font-text">Code (auto-generated if empty)</label>
                        <input type="text" class="form-control" name="code" placeholder="e.g. WELCOME50" style="text-transform:uppercase">
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-text">Bonus Amount ($)</label>
                        <input type="number" step="0.01" class="form-control" name="bonus_amount" placeholder="50.00" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-text">Max Uses</label>
                        <input type="number" class="form-control" name="max_uses" value="100" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-text">Target User (optional)</label>
                        <select class="form-control select2" name="user_id">
                            <option value="">-- ALL USERS --</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}} ({{$user->email}})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="font-text">Expires At (optional)</label>
                        <input type="datetime-local" class="form-control" name="expires_at">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i data-lucide="ticket" style="width:14px;vertical-align:middle"></i> Create Coupon
                    </button>
                </form>
            </div>
        </div>

        <!-- Coupon List -->
        <div class="col-lg-8">
            <div class="glass-card border-0 shadow-2xl p-0 mb-4 overflow-hidden">
                <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
                    <div>
                        <h6 class="font-weight-bold mb-1 text-white">Active Promo Codes</h6>
                        <p class="text-muted x-small mb-0">Operational coupon parameters and limit status.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table text-white mb-0" id="couponsTable">
                        <thead>
                            <tr class="text-muted small text-uppercase" style="letter-spacing: 1px;">
                                <th class="border-0 pl-4">Hologram Code</th>
                                <th class="border-0">Payload</th>
                                <th class="border-0">Saturation</th>
                                <th class="border-0">Target</th>
                                <th class="border-0">Expiration</th>
                                <th class="border-0 text-right pr-4">Controls</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($coupons as $coupon)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                <td class="align-middle pl-4">
                                    <code class="text-primary font-weight-bold" style="font-size:14px; letter-spacing:1px; background:rgba(59,130,246,0.1); padding:4px 8px; border-radius:6px;">{{$coupon->code}}</code>
                                </td>
                                <td class="align-middle">
                                    <div class="font-weight-bold text-white">${{number_format($coupon->bonus_amount, 2)}}</div>
                                    <div class="text-success x-small">TRADING BONUS</div>
                                </td>
                                <td class="align-middle">
                                    <div class="progress" style="height: 4px; width: 60px; background: rgba(255,255,255,0.05); border-radius:2px;">
                                        <div class="progress-bar bg-primary" style="width: {{ ($coupon->times_used / $coupon->max_uses) * 100 }}%"></div>
                                    </div>
                                    <div class="x-small text-muted mt-1">{{$coupon->times_used}} / {{$coupon->max_uses}} EXECUTED</div>
                                </td>
                                <td class="align-middle">
                                    <div class="x-small {{ $coupon->user_id ? 'text-info font-weight-bold' : 'text-muted' }}">
                                        {{ $coupon->user_id ? ($coupon->user->first_name . ' ' . $coupon->user->last_name) : 'PUBLIC' }}
                                    </div>
                                    @if($coupon->user_id)
                                        <div class="text-muted x-small opacity-50">{{$coupon->user->email}}</div>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="small {{ $coupon->expires_at && $coupon->expires_at->isPast() ? 'text-danger' : 'text-white' }}">
                                        {{$coupon->expires_at ? $coupon->expires_at->format('M d, Y') : 'INFINITY'}}
                                    </div>
                                </td>
                                <td class="align-middle text-right pr-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{route('admin.coupon.toggle', $coupon->id)}}" class="btn btn-xs glass-panel {{ $coupon->is_active ? 'text-warning' : 'text-success' }} border-0" title="Toggle State">
                                            <i data-lucide="{{$coupon->is_active ? 'pause' : 'play'}}" style="width:14px"></i>
                                        </a>
                                        <a href="{{route('admin.coupon.delete', $coupon->id)}}" class="btn btn-xs glass-panel text-danger border-0" onclick="return confirm('Purge this coupon code?')">
                                            <i data-lucide="trash-2" style="width:14px"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Redemption Log -->
            <div class="glass-card border-0 shadow-2xl p-0 mb-4 overflow-hidden">
                <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
                    <div>
                        <h6 class="font-weight-bold mb-1 text-white">Redemption Telemetry</h6>
                        <p class="text-muted x-small mb-0">Historical log of localized coupon injections.</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table text-white mb-0" id="redemptionsTable">
                        <thead>
                            <tr class="text-muted small text-uppercase" style="letter-spacing: 1px;">
                                <th class="border-0 pl-4">Operator</th>
                                <th class="border-0">Vector</th>
                                <th class="border-0">Magnitude</th>
                                <th class="border-0 text-right pr-4">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($redemptions as $r)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.03);">
                                <td class="align-middle pl-4">
                                    <div class="font-weight-bold text-white small">{{$r->user->first_name ?? 'N/A'}} {{$r->user->last_name ?? ''}}</div>
                                    <div class="text-muted x-small">{{$r->user->email ?? ''}}</div>
                                </td>
                                <td class="align-middle">
                                    <code class="text-info x-small">{{$r->coupon->code ?? 'PURGED'}}</code>
                                </td>
                                <td class="align-middle text-success font-weight-bold">
                                    +${{number_format($r->bonus_credited, 2)}}
                                </td>
                                <td class="align-middle text-right pr-4">
                                    <div class="small text-white opacity-75">{{$r->created_at->format('M d, Y')}}</div>
                                    <div class="text-muted x-small">{{$r->created_at->format('H:i')}} UTC</div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<script>toastr.success("{{session('success')}}", "Success")</script>
@endif
@if(session('error'))
<script>toastr.error("{{session('error')}}", "Error")</script>
@endif
<script>
$(document).ready(function(){ $('#couponsTable').DataTable(); $('#redemptionsTable').DataTable(); });
if(typeof lucide!=='undefined') lucide.createIcons();
</script>
@endsection
