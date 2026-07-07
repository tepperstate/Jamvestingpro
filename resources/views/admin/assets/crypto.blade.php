@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Crypto Assets Generation</h1>
        <form action="{{ route('admin.assets_gen.crypto.sync') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-sync-alt fa-sm text-white-50"></i> Populate / Sync Crypto APIs</button>
        </form>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Crypto Assets Managed</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.assets_gen.mass_delete') }}" method="POST" id="massDeleteForm">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Icon</th>
                                <th>Name</th>
                                <th>Symbol</th>
                                <th>Buy Price</th>
                                <th>Sell Price</th>
                                <th>24h Change</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assets as $asset)
                            <tr>
                                <td><input type="checkbox" name="ids[]" value="{{ $asset->id }}" class="selectItem"></td>
                                <td>
                                    <img src="{{ asset('storage/image/' . $asset->image) }}" width="30" height="30" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ $asset->symbol }}&background=random&color=fff&size=30'">
                                </td>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->symbol }}</td>
                                <td>{{ $asset->buy }}</td>
                                <td>{{ $asset->sell }}</td>
                                <td class="{{ $asset->changes > 0 ? 'text-success' : 'text-danger' }}">{{ $asset->changes }}%</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $asset->id }}">Delete</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete selected assets?')">Delete Selected</button>
                    <button type="button" class="btn btn-info ml-2" data-toggle="modal" data-target="#massEditPLModal">Mass Edit P&L</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Mass Edit P&L Modal -->
<div class="modal fade" id="massEditPLModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title font-weight-bold">Mass Edit Profit & Loss</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body p-4">
        <div class="form-group mb-4">
            <label class="text-muted small text-uppercase font-weight-bold mb-2">New Profit Percentage (%)</label>
            <input type="number" step="0.01" id="mass_profit_percentage" class="form-control" placeholder="e.g. 10.5">
        </div>
        <div class="form-group mb-4">
            <label class="text-muted small text-uppercase font-weight-bold mb-2">New Loss Percentage (%)</label>
            <input type="number" step="0.01" id="mass_loss_percentage" class="form-control" placeholder="e.g. 5.0">
        </div>
        <button type="button" id="btn-confirm-mass-edit-pl" class="btn btn-info w-100 py-3 font-weight-bold">
            Apply to Selected
        </button>
      </div>
    </div>
  </div>
</div>

<script>
    document.getElementById('selectAll').addEventListener('click', function(e) {
        let checkboxes = document.querySelectorAll('.selectItem');
        checkboxes.forEach(checkbox => checkbox.checked = e.target.checked);
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if(confirm('Are you sure you want to delete this asset?')) {
                let id = this.getAttribute('data-id');
                let form = document.getElementById('massDeleteForm');
                document.querySelectorAll('.selectItem').forEach(c => c.checked = false);
                let checkbox = this.closest('tr').querySelector('.selectItem');
                checkbox.checked = true;
                form.submit();
            }
        });
    });

    document.getElementById('btn-confirm-mass-edit-pl').addEventListener('click', function() {
        let selectedIds = [];
        document.querySelectorAll('.selectItem:checked').forEach(c => {
            selectedIds.push(c.value);
        });

        let profitPercentage = document.getElementById('mass_profit_percentage').value;
        let lossPercentage = document.getElementById('mass_loss_percentage').value;

        if(selectedIds.length === 0) return alert('No assets selected.');
        if(!profitPercentage && !lossPercentage) {
            return alert("Please provide at least one percentage to update.");
        }

        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = 'Updating...';

        fetch("{{ route('admin.assets_gen.mass_edit_pl') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                ids: selectedIds,
                profit_percentage: profitPercentage,
                loss_percentage: lossPercentage
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            if(data.status) {
                alert(data.message || 'Assets updated successfully.');
                window.location.reload();
            } else {
                alert(data.message || 'Failed to update items.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert('Server communication failed.');
        });
    });
</script>
@endsection
