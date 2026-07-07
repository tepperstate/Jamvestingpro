@extends('layouts.user.app')
@section("content")

<div class="container-full mt-4">
    <style>
        .outfit { font-family: 'Outfit', sans-serif !important; }
        .glass-ledger {
            background: rgba(10, 15, 30, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            overflow: hidden;
        }
        .table-premium {
            background: transparent !important;
            border-collapse: separate;
            border-spacing: 0 8px;
        }
        .table-premium thead th {
            background: rgba(255, 255, 255, 0.03);
            border: none !important;
            color: #94a3b8 !important;
            text-transform: uppercase;
            font-size: 0.65rem;
            letter-spacing: 1.5px;
            font-weight: 700;
            padding: 20px !important;
        }
        .table-premium tbody tr {
            background: rgba(255, 255, 255, 0.02);
            transition: all 0.3s ease;
        }
        .table-premium tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
            transform: scale(1.002);
        }
        .table-premium td {
            border: none !important;
            padding: 18px 20px !important;
            vertical-align: middle !important;
            color: #e2e8f0;
            font-size: 0.85rem;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-pending {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .status-completed {
            background: rgba(255, 51, 51, 0.1);
            color: #ff3333;
            border: 1px solid rgba(255, 51, 51, 0.2);
        }
        .hash-link {
            color: #0ea5e9;
            text-decoration: none;
            transition: all 0.2s;
            font-family: monospace;
            background: rgba(14, 165, 233, 0.05);
            padding: 4px 8px;
            border-radius: 6px;
        }
        .hash-link:hover {
            color: #38bdf8;
            background: rgba(14, 165, 233, 0.1);
        }
        /* DataTable Overrides */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            background: rgba(0, 0, 0, 0.3) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            border-radius: 8px !important;
            padding: 6px 12px !important;
        }
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            color: #94a3b8 !important;
            padding-top: 20px !important;
            font-size: 0.75rem;
        }
        .paginate_button {
            border-radius: 8px !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            background: rgba(255, 255, 255, 0.02) !important;
            color: white !important;
            margin: 0 4px !important;
        }
        .paginate_button.current {
            background: #0ea5e9 !important;
            border-color: #0ea5e9 !important;
            color: white !important;
        }
    </style>

    <div class="content-header p-0 mb-4">
        <h2 class="outfit font-weight-bold text-white mb-1">Financial Settlement History</h2>
        <p class="text-secondary small">Comprehensive ledger of all outbound capital dispatches.</p>
    </div>

    <section class="content p-0">
        <div class="glass-ledger p-4">
            <div class="table-responsive">
                <table id="withdrawalTable" class="table table-premium text-nowrap">
                    <thead>
                        <tr>
                            <th>Sequence</th>
                            <th>Asset Class</th>
                            <th>Timestamp</th>
                            <th>Volume (USD)</th>
                            <th>Registry Hash</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="outfit">
                        @forelse ($data as $key => $val)
                        <tr>
                            <td class="font-weight-bold opacity-50">#{{ str_pad(++$key, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="p-1 rounded bg-dark" style="border: 1px solid rgba(255,255,255,0.05);">
                                        <x-asset-logo :symbol="$val->type" size="20" />
                                    </div>
                                    <span class="font-weight-bold">{{ $val->type }}</span>
                                </div>
                            </td>
                            <td class="text-secondary small">{{ $val->created_at->format('M d, Y • H:i') }}</td>
                            <td class="font-weight-bold text-white">${{ number_format($val->amount, 2) }}</td>
                            <td>
                                @if($val->hash)
                                <a href="{{ str_contains($val->hash, 'http') ? $val->hash : '//' . $val->hash }}" target="_blank" class="hash-link">
                                    {{ substr($val->hash, 0, 10) }}...
                                </a>
                                @else
                                <span class="text-secondary opacity-50 italic small">Pending Registry</span>
                                @endif
                            </td>
                            <td>
                                <span class="status-badge {{ $val->status === 'pending' ? 'status-pending' : 'status-completed' }}">
                                    {{ $val->status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <!-- Empty State Handled by DataTables -->
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

@if(session('status'))
<script>
    toastr.success("{{session('status')}}", 'successful')
</script>
@endif

<script>
    $(document).ready(function() {
        $('#withdrawalTable').DataTable({
            "order": [[ 2, "desc" ]],
            "pageLength": 10,
            "language": {
                "search": "",
                "searchPlaceholder": "Search ledger...",
                "emptyTable": "<div class='py-5 text-center'><i class='ri-inbox-line display-4 d-block opacity-20 mb-3'></i><span class='text-secondary'>No settlement history identified in local registry.</span></div>"
            }
        });
    });
</script>
@endsection
