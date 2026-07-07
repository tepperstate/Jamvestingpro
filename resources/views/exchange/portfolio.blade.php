@extends('layouts.user.app')

@section('title', 'Portfolio')

@section('content')


<div class="container-fluid py-4 position-relative" style="z-index: 10;">
    <!-- Header Summary Section -->
    <div class="d-sm-flex align-items-center justify-content-between mb-5" data-aos="fade-down">
        <div class="mb-4 mb-sm-0">
            <h1 class="h2 mb-1 text-white outfit font-weight-bold tracking-tight">Consolidated Portfolio</h1>
            <p class="text-secondary mb-0">Aggregate view across all trading and investment vertical assets.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="glass-card-premium px-4 py-3 d-flex flex-column align-items-end satin-border" style="min-width: 220px;">
                <span class="micro-label mb-1">UNIFIED EQUITY</span>
                <span class="h3 mb-0 text-white outfit font-weight-bold" style="font-size: clamp(1.5rem, 3vw, 2.2rem);">${{ number_format($totalEquity, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="row g-4 mb-5">
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="glass-card-premium h-100 satin-border p-4" style="min-height: 180px;">
                <div class="widget-header mb-3">
                    <div class="widget-title"><i data-lucide="zap" class="text-warning"></i> Binary Capital</div>
                </div>
                <div class="h3 text-white outfit mb-2">${{ number_format($binaryEquity, 2) }}</div>
                <div class="text-secondary small mt-auto opacity-75">Locked in active trade orders</div>
            </div>
        </div>
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="glass-card-premium h-100 satin-border p-4" style="min-height: 180px;">
                <div class="widget-header mb-3">
                    <div class="widget-title"><i data-lucide="bar-chart-2" class="text-info"></i> Equities Value</div>
                </div>
                <div class="h3 text-white outfit mb-2">${{ number_format($stocksEquity, 2) }}</div>
                <div class="text-secondary small mt-auto opacity-75">Real-time valuation of stock holdings</div>
            </div>
        </div>
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="glass-card-premium h-100 satin-border p-4" style="min-height: 180px;">
                <div class="widget-header mb-3">
                    <div class="widget-title"><i data-lucide="safe" class="text-success"></i> Mutual Assets</div>
                </div>
                <div class="h3 text-white outfit mb-2">${{ number_format($fundsEquity, 2) }}</div>
                <div class="text-secondary small mt-auto opacity-75">Managed fund positions and yield</div>
            </div>
        </div>
    </div>

    <!-- Multi-Asset Tabs -->
    <div class="glass-card-premium overflow-hidden mb-5 satin-border" style="padding: 0;" data-aos="fade-up" data-aos-delay="400">
        <div class="p-4 border-bottom border-glass-light d-flex justify-content-between align-items-center bg-glass-dark">
            <h5 class="mb-0 text-white font-weight-bold outfit">Active Positions</h5>
            <div class="nav nav-pills glass-pill p-1" role="tablist" aria-label="Asset Portfolios">
                <button class="nav-link active rounded-pill text-white px-4 small font-weight-bold" data-toggle="pill" data-target="#tab-stocks" type="button" role="tab" aria-selected="true" aria-controls="tab-stocks">STOCKS</button>
                <button class="nav-link rounded-pill text-white px-4 small font-weight-bold" data-toggle="pill" data-target="#tab-funds" type="button" role="tab" aria-selected="false" aria-controls="tab-funds">FUNDS</button>
                <button class="nav-link rounded-pill text-white px-4 small font-weight-bold" data-toggle="pill" data-target="#tab-binary" type="button" role="tab" aria-selected="false" aria-controls="tab-binary">BINARY</button>
            </div>
        </div>

        <div class="tab-content">
            <!-- Stocks Tab -->
            <div class="tab-pane fade show active" id="tab-stocks" role="tabpanel">
                <div class="table-responsive">
                    <table class="table mb-0 text-white">
                        <thead>
                            <tr class="bg-glass-dark">
                                <th class="micro-label px-4 py-3">ASSET</th>
                                <th class="micro-label px-4 py-3 text-end">UNITS</th>
                                <th class="micro-label px-4 py-3 text-end">AVG PRICE</th>
                                <th class="micro-label px-4 py-3 text-end">COST</th>
                                <th class="micro-label px-4 py-3 text-end">VALUATION</th>
                                <th class="micro-label px-4 py-3 text-end">P/L</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stocks as $s)
                            <tr class="portfolio-row">
                                <td class="px-4 py-3 border-glass-light align-middle">
                                    <div class="d-flex align-items-center gap-3">
                                        <x-asset-logo :symbol="$s->symbol" size="sm" />
                                        <div>
                                            <div class="font-weight-bold text-white mb-0">{{ $s->symbol }}</div>
                                            <div class="text-secondary x-small">{{ $s->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 border-glass-light text-end align-middle">{{ number_format($s->units, 4) }}</td>
                                <td class="px-4 py-3 border-glass-light text-end align-middle">${{ number_format($s->avg_price, 2) }}</td>
                                <td class="px-4 py-3 border-glass-light text-end align-middle">${{ number_format($s->total_cost, 2) }}</td>
                                <td class="px-4 py-3 border-glass-light text-end align-middle font-weight-bold">
                                    ${{ number_format($s->units * ($s->buy ?: $s->avg_price), 2) }}
                                </td>
                                <td class="px-4 py-3 border-glass-light text-end align-middle">
                                    @php
                                        $pl = ($s->units * ($s->buy ?: $s->avg_price)) - $s->total_cost;
                                        $plPerc = ($s->total_cost > 0) ? ($pl / $s->total_cost) * 100 : 0;
                                    @endphp
                                    <span class="{{ $pl >= 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                                        {{ $pl >= 0 ? '+' : '' }}{{ number_format($plPerc, 2) }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-secondary">No stock positions found in your portfolio.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Funds Tab -->
            <div class="tab-pane fade" id="tab-funds" role="tabpanel">
                <div class="table-responsive">
                    <table class="table mb-0 text-white">
                        <thead>
                            <tr class="bg-glass-dark">
                                <th class="micro-label px-4 py-3">MANAGED FUND</th>
                                <th class="micro-label px-4 py-3 text-end">UNITS</th>
                                <th class="micro-label px-4 py-3 text-end">NAV AT PURCHASE</th>
                                <th class="micro-label px-4 py-3 text-end">INVESTED</th>
                                <th class="micro-label px-4 py-3 text-end">CURRENT VALUE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($funds as $f)
                            <tr class="portfolio-row">
                                <td class="px-4 py-3 border-glass-light align-middle">
                                    <div class="d-flex align-items-center gap-3">
                                        <x-asset-logo :symbol="optional($f->fund)->symbol ?? 'FUND'" size="sm" />
                                        <div>
                                            <div class="font-weight-bold text-white mb-0">{{ optional($f->fund)->name ?? 'Unknown Fund' }}</div>
                                            <div class="text-secondary x-small">{{ optional($f->fund)->category ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 border-glass-light text-end align-middle">{{ number_format($f->units, 4) }}</td>
                                <td class="px-4 py-3 border-glass-light text-end align-middle">${{ number_format($f->nav_at_purchase, 4) }}</td>
                                <td class="px-4 py-3 border-glass-light text-end align-middle">${{ number_format($f->amount, 2) }}</td>
                                <td class="px-4 py-3 border-glass-light text-end align-middle font-weight-bold">
                                    ${{ number_format($f->units * (optional($f->fund)->nav_price ?? 0), 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-secondary">No mutual fund investments active.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Binary Tab -->
            <div class="tab-pane fade" id="tab-binary" role="tabpanel">
                <div class="table-responsive">
                    <table class="table mb-0 text-white">
                        <thead>
                            <tr class="bg-glass-dark">
                                <th class="micro-label px-4 py-3">TRADE EXECUTION</th>
                                <th class="micro-label px-4 py-3 text-end">INITIAL PRINCIPAL</th>
                                <th class="micro-label px-4 py-3 text-end">DIRECTION</th>
                                <th class="micro-label px-4 py-3 text-end">STRIKE PRICE</th>
                                <th class="micro-label px-4 py-3 text-end">STATE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($binaryOrders->take(15) as $bo)
                            <tr class="portfolio-row">
                                <td class="px-4 py-3 border-glass-light align-middle">
                                    <div class="d-flex align-items-center gap-3">
                                        <x-asset-logo :symbol="$bo->symbol" size="sm" />
                                        <div class="font-weight-bold text-white">{{ $bo->symbol }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 border-glass-light text-end align-middle">${{ number_format($bo->amount, 2) }}</td>
                                <td class="px-4 py-3 border-glass-light text-end align-middle">
                                    <span class="{{ $bo->type == 'call' ? 'text-success' : 'text-danger' }} font-weight-bold">{{ strtoupper($bo->type) }}</span>
                                </td>
                                <td class="px-4 py-3 border-glass-light text-end align-middle">${{ number_format($bo->rate, 4) }}</td>
                                <td class="px-4 py-3 border-glass-light text-end align-middle">
                                    <span class="badge {{ $bo->status == 'pending' ? 'bg-warning text-dark' : ($bo->status == 'win' ? 'bg-success' : 'bg-danger') }} px-3 py-1" style="font-size: 0.65rem; font-weight: 800; border-radius: 6px;">
                                        {{ strtoupper($bo->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-secondary">No binary trade history found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Atmospheric Elements */


    .micro-label { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: rgba(255,255,255,0.3); border: none !important; }
    .glass-pill { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 50px; }
    .nav-pills .nav-link { border: none !important; transition: all 0.3s ease; }
    .nav-pills .nav-link.active { background: var(--accent-primary) !important; color: #fff !important; box-shadow: 0 4px 15px rgba(220, 38, 38, 0.4); }
    .portfolio-row { transition: background 0.2s ease; cursor: pointer; }
    .portfolio-row:hover { background: rgba(255,255,255,0.03) !important; }
    .bg-glass-dark { background: rgba(0,0,0,0.2) !important; }
    .border-glass-light { border-color: rgba(255,255,255,0.05) !important; }
    .x-small { font-size: 11px; }

    .satin-border {
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        background-clip: padding-box !important;
    }
    .satin-border::after {
        content: '';
        position: absolute;
        inset: -1px;
        border-radius: inherit;
        padding: 1px;
        background: linear-gradient(135deg, rgba(255,255,255,0.15), rgba(255,255,255,0.02) 40%, rgba(255,255,255,0.02) 60%, rgba(255,255,255,0.12));
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask-composite: exclude;
        -webkit-mask-composite: destination-out;
        pointer-events: none;
    }
</style>

<script>
    $(document).ready(function() {
        if(typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endsection

