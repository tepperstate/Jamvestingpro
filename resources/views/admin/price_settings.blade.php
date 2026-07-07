@extends('layouts.admin.app')
@section('title', 'API Price Settings')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-white font-weight-bold">API Price Settings</h1>
        <button class="btn btn-primary btn-sm shadow-sm" data-toggle="modal" data-target="#addProviderModal">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add Provider
        </button>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="glass-card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Active Providers</div>
                            <div class="h5 mb-0 font-weight-bold text-white">{{ $premium->where('is_active', true)->count() + $public->where('is_active', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-plug fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs border-0 mb-4" id="priceTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link font-weight-bold text-muted border-0 bg-transparent" id="premium-tab" data-toggle="tab" href="#premium" role="tab">
                Premium Providers (Keys)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold text-white active border-0 bg-transparent" id="public-tab" data-toggle="tab" href="#public" role="tab" style="border-bottom: 2px solid var(--accent-primary) !important;">
                Public APIs (Primary)
            </a>
        </li>
    </ul>

    <div class="tab-content" id="priceTabsContent">
        <!-- Premium Providers -->
        <div class="tab-pane fade" id="premium" role="tabpanel">
            <div class="glass-card shadow mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="color: var(--text-main)">
                            <thead class="bg-gray-900 border-0">
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Asset</th>
                                    <th>API Key</th>
                                    <th>Status</th>
                                    <th>Spread %</th>
                                    <th>Last Used</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($premium as $p)
                                <tr data-id="{{ $p->id }}">
                                    <td class="font-weight-bold">{{ $p->name }}</td>
                                    <td><span class="badge badge-info">{{ $p->provider_type }}</span></td>
                                    <td><span class="badge badge-secondary">{{ $p->asset_type }}</span></td>
                                    <td>
                                        <div class="input-group input-group-sm" style="width: 150px;">
                                            <input type="password" class="form-control bg-dark text-white border-0 provider-key" value="{{ $p->api_key }}" readonly>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary toggle-visibility" type="button"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($p->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                        <small class="d-block text-muted">{{ $p->last_status }}</small>
                                    </td>
                                    <td><span class="badge badge-warning">{{ $p->spread_percentage }}%</span></td>
                                    <td>{{ $p->last_used_at ? $p->last_used_at->diffForHumans() : 'Never' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <form action="{{ route('admin.prices.toggle', $p->id) }}" method="POST" style="display:inline">
                                                @csrf
                                                <button class="btn btn-sm {{ $p->is_active ? 'btn-warning' : 'btn-success' }}" title="Toggle Status">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>
                                            <button class="btn btn-sm btn-info edit-provider" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="{{ route('admin.prices.delete', $p->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Delete this provider?')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center py-4">No premium providers configured.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Public Providers -->
        <div class="tab-pane fade show active" id="public" role="tabpanel">
            <div class="glass-card shadow mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="color: var(--text-main)">
                            <thead class="bg-gray-900 border-0">
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Spread %</th>
                                    <th>Last Used</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($public as $p)
                                <tr>
                                    <td class="font-weight-bold">{{ $p->name }}</td>
                                    <td><span class="badge badge-info">{{ $p->provider_type }}</span></td>
                                    <td>{{ $p->priority }}</td>
                                    <td>
                                        @if($p->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-warning">{{ $p->spread_percentage }}%</span></td>
                                    <td>{{ $p->last_used_at ? $p->last_used_at->diffForHumans() : 'Never' }}</td>
                                    <td>
                                        <form action="{{ route('admin.prices.toggle', $p->id) }}" method="POST" style="display:inline">
                                            @csrf
                                            <button class="btn btn-sm {{ $p->is_active ? 'btn-warning' : 'btn-success' }}">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4">No public providers configured.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
<!-- Add/Edit Modal -->
<div class="modal fade" id="addProviderModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content glass-card border-0">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title text-white">Add Price Provider</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.prices.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id" id="modal-provider-id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="text-muted">Display Name</label>
                        <input type="text" name="name" class="form-control bg-dark text-white border-0" required placeholder="e.g. AlphaVantage Primary">
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="text-muted">Provider Type</label>
                            <select name="provider_type" class="form-control bg-dark text-white border-0">
                                <option value="binance">Binance</option>
                                <option value="polygon">Polygon</option>
                                <option value="alphavantage">AlphaVantage</option>
                                <option value="coincap">CoinCap</option>
                                <option value="coingecko">CoinGecko</option>
                                <option value="mexc">MEXC</option>
                                <option value="kucoin">KuCoin</option>
                                <option value="other">Other/Custom</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="text-muted">Asset Class</label>
                            <select name="asset_type" class="form-control bg-dark text-white border-0">
                                <option value="crypto">Crypto</option>
                                <option value="forex">Forex</option>
                                <option value="stock">Stock</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label class="text-muted">Tier/Category</label>
                            <select name="category" class="form-control bg-dark text-white border-0">
                                <option value="public">Public (Fallback)</option>
                                <option value="premium">Premium (API Key)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="text-muted">API Key (Optional for public)</label>
                        <input type="text" name="api_key" class="form-control bg-dark text-white border-0" placeholder="Paste your API key here">
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="text-muted">Priority (1-1000)</label>
                            <input type="number" name="priority" class="form-control bg-dark text-white border-0" value="10">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="text-muted">Spread Offset (%)</label>
                            <input type="number" step="0.001" name="spread_percentage" class="form-control bg-dark text-white border-0" value="2.0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Provider</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('.toggle-visibility').click(function() {
        const input = $(this).closest('.input-group').find('input');
        const icon = $(this).find('i');
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('.edit-provider').click(function() {
        // Implementation for quick edit can be added here or just link to a separate page
        // For now, it just shows the intent. Integration with the Modal can be done.
        toastr.info("Edit mode: Use the 'Add Provider' modal for new configuration.");
    });
});
</script>
@endpush

<style>
    .glass-card {
        background: rgba(0, 0, 0, 0.6) !important;
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.05) !important;
        border-radius: 12px;
    }
    .table-hover tbody tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }
    .nav-tabs .nav-link:hover {
        color: white !important;
    }
    .nav-tabs .nav-link.active {
        background: transparent !important;
        border-bottom: 2px solid var(--accent-primary) !important;
    }
</style>
@endsection

