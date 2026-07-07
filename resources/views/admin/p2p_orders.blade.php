@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">P2P Orders & Disputes</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Asset</th>
                            <th>Buyer</th>
                            <th>Seller</th>
                            <th>Amount</th>
                            <th>Escrow Status</th>
                            <th>Status</th>
                            <th>Dispute / Admin Res</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $item)
                        <tr>
                            <td>{{ $item->order_id }}</td>
                            <td>{{ $item->listing->asset ?? 'N/A' }}</td>
                            <td>{{ $item->buyer->first_name ?? 'N/A' }}</td>
                            <td>{{ $item->seller->first_name ?? 'N/A' }}</td>
                            <td>{{ $item->amount }}</td>
                            <td>{{ ucfirst($item->escrow_status) }}</td>
                            <td>{{ ucfirst($item->status) }}</td>
                            <td>{{ $item->admin_resolution ?? 'None' }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm mb-1" data-toggle="modal" data-target="#resolveModal{{ $item->id }}">Resolve</button>
                                <a href="{{ route('admin.p2p.orders.chat', $item->id) }}" class="btn btn-info btn-sm mb-1">Intercept Chat</a>
                                <a href="{{ route('admin.p2p.orders.delete', $item->id) }}" class="btn btn-danger btn-sm mb-1">Delete</a>
                            </td>
                        </tr>

                        <!-- Resolve Modal -->
                        <div class="modal fade" id="resolveModal{{ $item->id }}">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.p2p.resolve_order') }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Resolve P2P Dispute / Order</h5>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="{{ $item->id }}">
                                            <label>Resolution</label>
                                            <select name="admin_resolution" class="form-control mb-2">
                                                <option value="release_to_buyer">Release Funds to Buyer</option>
                                                <option value="release_to_seller">Return/Release to Seller</option>
                                                <option value="cancelled">Cancel Order</option>
                                            </select>
                                            <textarea name="admin_notes" class="form-control" placeholder="Admin Notes">{{ $item->admin_notes }}</textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-primary">Apply Resolution</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

