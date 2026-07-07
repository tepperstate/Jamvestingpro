@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">P2P Listings</h1>
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Manage Listings</h6>
            <div>
                <button class="btn btn-warning btn-sm mr-2" data-toggle="modal" data-target="#createTraderModal">Generate Fake Trader</button>
                <button class="btn btn-info btn-sm mr-2" id="autoPopulateBtn">Auto-Populate from Binance</button>
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createModal">Create Fake Listing</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>User/Admin</th>
                            <th>Type</th>
                            <th>Asset</th>
                            <th>Price</th>
                            <th>Amount</th>
                            <th>Completion Rate</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($listings as $item)
                        <tr>
                            <td>{{ $item->user->first_name ?? 'Admin' }}</td>
                            <td>{{ ucfirst($item->type) }}</td>
                            <td>{{ $item->asset }}</td>
                            <td>${{ $item->price }}</td>
                            <td>{{ $item->amount }}</td>
                            <td>
                                <form action="{{ route('admin.p2p.update_listing') }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <input type="text" name="completion_rate" value="{{ $item->completion_rate }}" class="form-control form-control-sm" style="width:70px; display:inline">
                                    <button class="btn btn-sm btn-success"><i class="fa fa-save"></i></button>
                                </form>
                            </td>
                            <td>{{ ucfirst($item->status) }}</td>
                            <td>
                                <a href="{{ route('admin.p2p.delete_listing', $item->id) }}" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Trader Modal -->
<div class="modal fade" id="createTraderModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('listings.generate_fake_trader') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Generate Fake Trader</h5>
                </div>
                <div class="modal-body">
                    <input type="text" name="first_name" class="form-control mb-2" placeholder="First Name" required>
                    <input type="text" name="last_name" class="form-control mb-2" placeholder="Last Name" required>
                    <input type="text" name="country" class="form-control mb-2" placeholder="Country (e.g., USA, UK)" value="USA">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.p2p.create_listing') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Create Admin Listing</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="is_admin_listing" value="1">
                    <select name="user_id" class="form-control mb-2" required>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->email }}</option>
                        @endforeach
                    </select>
                    <select name="type" class="form-control mb-2"><option value="buy">Buy</option><option value="sell">Sell</option></select>
                    <input type="text" name="asset" class="form-control mb-2" placeholder="Asset (BTC)" required>
                    <input type="text" name="currency" class="form-control mb-2" placeholder="Currency (USD)" required>
                    <input type="text" name="price" class="form-control mb-2" placeholder="Price" required>
                    <input type="text" name="amount" class="form-control mb-2" placeholder="Amount" required>
                    <input type="text" name="min_order" class="form-control mb-2" placeholder="Min Order" required>
                    <input type="text" name="max_order" class="form-control mb-2" placeholder="Max Order" required>
                    <input type="text" name="completion_rate" class="form-control mb-2" placeholder="Completion Rate %" value="98.5">
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('autoPopulateBtn').addEventListener('click', function() {
    let btn = this;
    btn.innerHTML = 'Populating...';
    btn.disabled = true;

    fetch('{{ route("admin.p2p.listings.sync_binance") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if(response.ok) {
            window.location.reload();
        } else {
            alert('Error populating data');
            btn.innerHTML = 'Auto-Populate from Binance';
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred.');
        btn.innerHTML = 'Auto-Populate from Binance';
        btn.disabled = false;
    });
});
</script>
@endsection

