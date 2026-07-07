@extends('layouts.user.app')
@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <h4 class="card-title">P2P Market (Peer-to-Peer Trading)</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Trade directly with other users via escrow. Create your own listings or fill existing ones.</p>
                        
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Advertiser</th>
                                        <th>Asset</th>
                                        <th>Price</th>
                                        <th>Limits</th>
                                        <th>Payment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($listings as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->user->first_name ?? 'Trader' }}</strong><br>
                                            <small class="text-success">{{ $item->total_trades }} orders | {{ $item->completion_rate }}% completion</small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ \App\Services\AssetLogoService::getLogoUrl($item->asset, $item->asset_type ?? 'crypto') }}" alt="{{ $item->asset }}" style="width: 24px; height: 24px; border-radius: 50%; margin-right: 8px;">
                                                <span>{{ $item->asset }}</span>
                                            </div>
                                        </td>
                                        <td class="text-primary font-weight-bold">{{ $item->price }} {{ $item->currency }}</td>
                                        <td>{{ $item->min_order }} - {{ $item->max_order }} {{ $item->currency }}</td>
                                        <td>
                                            @foreach(json_decode($item->payment_methods) as $pm)
                                                <span class="badge badge-outline-primary mb-1">{{ $pm }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <button class="btn btn-{{ $item->type == 'sell' ? 'success' : 'danger' }} btn-sm">
                                                {{ $item->type == 'sell' ? 'Buy' : 'Sell' }} {{ $item->asset }}
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No listings available.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
