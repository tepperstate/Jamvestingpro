@extends('layouts.user.app')

@section('title', 'Signal History')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="h2 outfit font-weight-bold text-white mb-2">Signal History</h1>
            <p class="text-secondary small mb-0">Track your signal performance, trades executed, and overall profit/loss.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('signals.user') }}" class="btn btn-outline-premium px-4 py-2">
                <i class="ri-radar-line me-2"></i> My Subscriptions
            </a>
            <a href="{{ route('signal') }}" class="btn btn-outline-premium px-4 py-2">
                <i class="ri-add-line me-2"></i> Browse Signals
            </a>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-5">
        <div class="col-xl-4 col-md-6">
            <div class="sh-stat-card">
                <div class="sh-stat-icon bg-blue-glow">
                    <i class="ri-bar-chart-grouped-line"></i>
                </div>
                <div class="sh-stat-info">
                    <div class="sh-stat-label">Total Trades</div>
                    <div class="sh-stat-value">${{ number_format($trade) }}</div>
                </div>
                <div class="sh-stat-trend text-info">
                    <i class="ri-arrow-right-up-line"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="sh-stat-card">
                <div class="sh-stat-icon bg-purple-glow">
                    <i class="ri-calendar-check-line"></i>
                </div>
                <div class="sh-stat-info">
                    <div class="sh-stat-label">Today's Trades</div>
                    <div class="sh-stat-value">${{ number_format($today) }}</div>
                </div>
                <div class="sh-stat-trend text-purple">
                    <i class="ri-calendar-line"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12">
            <div class="sh-stat-card">
                <div class="sh-stat-icon {{ $today_profit >= 0 ? 'bg-success-glow' : 'bg-danger-glow' }}">
                    <i class="ri-funds-line"></i>
                </div>
                <div class="sh-stat-info">
                    <div class="sh-stat-label">Today's P&L</div>
                    <div class="sh-stat-value {{ $today_profit >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $today_profit >= 0 ? '+' : '-' }}${{ number_format(abs($today_profit)) }}
                    </div>
                </div>
                <div class="sh-stat-trend {{ $today_profit >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="ri-arrow-{{ $today_profit >= 0 ? 'right-up' : 'right-down' }}-line"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Signal History Table -->
    <div class="sh-table-card">
        <div class="sh-table-header">
            <div class="d-flex align-items-center gap-3">
                <div class="sh-table-icon"><i class="ri-list-ordered-2"></i></div>
                <div>
                    <h5 class="outfit font-weight-bold text-white mb-0">Live Signal History</h5>
                    <p class="text-secondary small mb-0">All executed signals and their outcomes</p>
                </div>
            </div>
        </div>

        <div class="sh-table-body">
            @if(count($data) > 0)
            <div class="table-responsive">
                <table id="example" class="table sh-table mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Signal</th>
                            <th>Symbol</th>
                            <th>Volume</th>
                            <th>Type</th>
                            <th>Profit / Loss</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $d)
                        <tr>
                            <td>
                                <span class="row-num">{{ $loop->iteration }}</span>
                            </td>
                            <td>
                                <div class="signal-name-cell">
                                    <div class="signal-name-dot"></div>
                                    <span class="font-weight-bold">{{ $d->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="symbol-badge">{{ $d->symbol }}</span>
                            </td>
                            <td class="font-weight-bold">${{ number_format($d->amount) }}</td>
                            <td>
                                <span class="type-pill {{ $d->type == 'Buy' ? 'type-buy' : 'type-sell' }}">
                                    <i class="ri-arrow-{{ $d->type == 'Buy' ? 'right-up' : 'right-down' }}-line me-1"></i>
                                    {{ $d->type }}
                                </span>
                            </td>
                            <td>
                                @if($d->status == 'win')
                                    <span class="text-success font-weight-bold">+${{ number_format($d->profit - $d->amount) }}</span>
                                @else
                                    <span class="text-danger font-weight-bold">-${{ number_format($d->amount) }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge {{ $d->status == 'win' ? 'status-win' : 'status-loss' }}">
                                    <span class="status-dot-tiny {{ $d->status == 'win' ? 'bg-success' : 'bg-danger' }}"></span>
                                    {{ ucfirst($d->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-secondary small">{{ \Carbon\Carbon::parse($d->created_at)->format('M d, Y') }}</span>
                                <br><span class="text-secondary" style="font-size:0.7rem;">{{ \Carbon\Carbon::parse($d->created_at)->format('h:i A') }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="sh-empty-state">
                <div class="sh-empty-icon"><i class="ri-file-list-3-line"></i></div>
                <h5 class="outfit font-weight-bold text-white mb-2">No Signal Activity</h5>
                <p class="text-secondary small mb-0">Your signal trade history will appear here once trades are executed.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Stat Cards */
    .sh-stat-card {
        background: rgba(16, 18, 27, 0.6);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 20px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.3s ease;
    }
    .sh-stat-card:hover {
        border-color: rgba(255,255,255,0.1);
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.25);
    }
    .sh-stat-icon {
        width: 52px; height: 52px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
    }
    .bg-blue-glow { background: rgba(59, 130, 246, 0.12); color: #60a5fa; }
    .bg-purple-glow { background: rgba(168, 85, 247, 0.12); color: #a78bfa; }
    .bg-success-glow { background: rgba(255, 51, 51, 0.12); color: #34d399; }
    .bg-danger-glow { background: rgba(239, 68, 68, 0.12); color: #f87171; }
    .sh-stat-info { flex-grow: 1; }
    .sh-stat-label { font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .sh-stat-value { font-size: 1.5rem; font-weight: 800; color: #fff; font-family: 'Outfit', sans-serif; }
    .sh-stat-trend { font-size: 1.4rem; opacity: 0.6; }
    .text-purple { color: #a78bfa; }

    /* Table Card */
    .sh-table-card {
        background: rgba(16, 18, 27, 0.6);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 24px;
        overflow: hidden;
    }
    .sh-table-header {
        padding: 24px 28px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .sh-table-icon {
        width: 42px; height: 42px;
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.15);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        color: #60a5fa; font-size: 1.1rem;
    }
    .sh-table-body { padding: 0; }

    /* Table Styles */
    .sh-table {
        color: #e2e8f0;
    }
    .sh-table thead th {
        background: rgba(0,0,0,0.2);
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 14px 20px;
        border: none;
        white-space: nowrap;
    }
    .sh-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.03);
        vertical-align: middle;
        font-size: 0.88rem;
    }
    .sh-table tbody tr {
        transition: background 0.2s;
    }
    .sh-table tbody tr:hover {
        background: rgba(59, 130, 246, 0.04);
    }
    .sh-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Row Number */
    .row-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px; height: 28px;
        background: rgba(255,255,255,0.04);
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
    }

    /* Signal Name Cell */
    .signal-name-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .signal-name-dot {
        width: 8px; height: 8px;
        background: #3b82f6;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* Symbol Badge */
    .symbol-badge {
        background: rgba(168, 85, 247, 0.1);
        border: 1px solid rgba(168, 85, 247, 0.15);
        color: #a78bfa;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    /* Type Pill */
    .type-pill {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 5px 14px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        letter-spacing: 0.3px;
    }
    .type-buy { background: rgba(255, 51, 51, 0.1); color: #34d399; border: 1px solid rgba(255, 51, 51, 0.15); }
    .type-sell { background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.15); }

    /* Status Badge */
    .status-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 5px 14px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .status-win { background: rgba(255, 51, 51, 0.1); color: #34d399; border: 1px solid rgba(255, 51, 51, 0.15); }
    .status-loss { background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.15); }
    .status-dot-tiny { width: 6px; height: 6px; border-radius: 50%; }

    /* Empty State */
    .sh-empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .sh-empty-icon {
        width: 72px; height: 72px;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 16px;
        font-size: 2rem; color: #475569;
    }

    /* Header Buttons */
    .btn-outline-premium {
        border: 1px solid rgba(59, 130, 246, 0.4);
        color: #60a5fa;
        border-radius: 14px;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.3s;
    }
    .btn-outline-premium:hover {
        background: rgba(59, 130, 246, 0.1);
        color: white;
        border-color: #3b82f6;
    }

    /* DataTables overrides */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        color: #94a3b8 !important;
        padding: 16px 20px;
    }
    .dataTables_wrapper .dataTables_filter input {
        background: rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 10px;
        color: #fff;
        padding: 6px 14px;
    }
    .dataTables_wrapper .dataTables_length select {
        background: rgba(0,0,0,0.3);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 8px;
        color: #fff;
        padding: 4px 8px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        color: #94a3b8 !important;
        border: 1px solid rgba(255,255,255,0.06) !important;
        border-radius: 8px !important;
        background: rgba(0,0,0,0.2) !important;
        margin: 0 2px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: rgba(59, 130, 246, 0.15) !important;
        border-color: rgba(59, 130, 246, 0.3) !important;
        color: #60a5fa !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(59, 130, 246, 0.1) !important;
        border-color: rgba(59, 130, 246, 0.2) !important;
        color: #fff !important;
    }

    @media (max-width: 768px) {
        .sh-table thead th,
        .sh-table tbody td {
            padding: 12px 14px;
        }
    }
</style>

@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            order: [[0, 'desc']],
            language: {
                search: '<i class="ri-search-line me-2"></i>',
                searchPlaceholder: 'Search signals...',
                lengthMenu: 'Show _MENU_ entries',
                emptyTable: 'No signal history found',
                zeroRecords: 'No matching signals found'
            },
            dom: '<"d-flex flex-wrap justify-content-between align-items-center"lf>rt<"d-flex flex-wrap justify-content-between align-items-center"ip>'
        });
    });
</script>

@if(session('error'))
<script>toastr.error("{{ session('error') }}", 'Error');</script>
@endif
@if(session('status'))
<script>toastr.success("{{ session('status') }}", 'Success');</script>
@endif
@endpush
