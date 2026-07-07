@extends('layouts.admin.app')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="font-weight-bold text-white mb-1">Inflow Gateway Registry</h4>
            <p class="text-secondary mb-0">Manage cryptographic and fiat deposit endpoints for the liquidity network.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="badge bg-primary-soft text-primary px-4 py-2 rounded-pill border-glass">
                <span class="small font-weight-bold">GATEWAY STATUS: OPERATIONAL</span>
            </div>
            <button class="btn btn-primary btn-sm rounded-pill px-4 shadow-lg font-weight-bold" data-toggle="modal" data-target="#addWalletModal">
                <i class="ri-add-line me-1"></i> Initialize Gateway
            </button>
        </div>
    </div>

    <!-- Gateway Grid -->
    <div class="row">
        @foreach($wallets as $wallet)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="glass-card p-4 h-100 shadow-2xl border-glass">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box bg-primary-soft text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="{{ $wallet->icon_class ?? 'ri-coin-line' }} h3 mb-0"></i>
                        </div>
                        <div>
                            <h5 class="outfit font-weight-bold mb-0 text-white">{{ $wallet->name }}</h5>
                            <span class="badge {{ $wallet->is_active ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger' }} rounded-pill px-3 py-1 x-small text-uppercase mt-1">
                                {{ $wallet->is_active ? 'ENABLED' : 'DISABLED' }}
                            </span>
                        </div>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link text-secondary p-0 opacity-50 hover-opacity-100" data-toggle="dropdown">
                            <i class="ri-more-2-fill h4"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right glass-card border-glass shadow-xl py-2">
                            <a class="dropdown-item text-white edit-wallet-btn" href="javascript:void(0)" 
                               data-id="{{ $wallet->id }}" 
                               data-name="{{ $wallet->name }}"
                               data-symbol="{{ $wallet->symbol }}"
                               data-address="{{ $wallet->address }}"
                               data-network="{{ $wallet->network }}"
                               data-icon="{{ $wallet->icon_class }}"
                               data-active="{{ $wallet->is_active }}">
                               <i class="ri-edit-line me-2 text-info"></i> Reconfigure
                            </a>
                            <div class="dropdown-divider border-glass"></div>
                            <a class="dropdown-item text-danger" href="{{ route('admin.wallets.delete', $wallet->id) }}" onclick="return confirm('Security Check: Decommission this gateway permanently?')">
                               <i class="ri-delete-bin-line me-2"></i> Terminate
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="x-small text-secondary font-weight-bold text-uppercase tracking-wider mb-2 d-block">System Specifications</label>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-dark-soft text-white-50 border-glass px-3 py-2 rounded-3 small">
                            <i class="ri-rss-line me-1"></i> {{ $wallet->symbol }}
                        </span>
                        <span class="badge bg-black-soft text-primary border-glass px-3 py-2 rounded-3 small">
                            <i class="ri-links-line me-1"></i> {{ $wallet->network ?? 'GLOBAL' }}
                        </span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="x-small text-secondary font-weight-bold text-uppercase tracking-wider mb-2 d-block">Endpoint Address</label>
                    <div class="bg-black-soft p-3 rounded-4 border-glass d-flex align-items-center justify-content-between">
                        <code class="text-primary small text-break opacity-90">{{ $wallet->address }}</code>
                        <button class="btn btn-sm btn-link text-secondary p-0" onclick="navigator.clipboard.writeText('{{ $wallet->address }}')">
                            <i class="ri-file-copy-line"></i>
                        </button>
                    </div>
                </div>

                @if($wallet->image)
                <div class="text-center mt-3 pt-3 border-top border-glass">
                    <img src="{{ asset('storage/image/'.$wallet->image) }}" class="rounded-3 shadow-lg" style="height: 50px; width: auto; max-width: 100%; border: 1px solid rgba(255,255,255,0.05);">
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('modals')
<!-- Add Modal -->
<div class="modal fade" id="addWalletModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal border-glass shadow-2xl">
            <div class="p-4 border-bottom border-glass bg-black-soft d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="outfit font-weight-bold text-white mb-0">Gateway Initialization</h4>
                    <p class="text-secondary small mb-0">Register a new liquidity entry point.</p>
                </div>
                <button type="button" class="close text-white opacity-50 hover-opacity-100" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="p-4">
                <form action="{{ route('admin.wallets.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="x-small text-secondary font-weight-bold text-uppercase mb-2">System Name</label>
                            <input type="text" name="name" class="form-control bg-dark-soft border-glass text-white py-2 rounded-3" placeholder="e.g. Ethereum" required>
                        </div>
                        <div class="col-md-6">
                            <label class="x-small text-secondary font-weight-bold text-uppercase mb-2">Asset Symbol</label>
                            <input type="text" name="symbol" class="form-control bg-dark-soft border-glass text-white py-2 rounded-3" placeholder="e.g. ETH" required>
                        </div>
                        <div class="col-12">
                            <label class="x-small text-secondary font-weight-bold text-uppercase mb-2">Endpoint Address</label>
                            <input type="text" name="address" class="form-control bg-dark-soft border-glass text-white py-2 rounded-3" placeholder="0x..." required>
                        </div>
                        <div class="col-md-6">
                            <label class="x-small text-secondary font-weight-bold text-uppercase mb-2">Network ID</label>
                            <input type="text" name="network" class="form-control bg-dark-soft border-glass text-white py-2 rounded-3" placeholder="ERC-20">
                        </div>
                        <div class="col-md-6">
                            <label class="x-small text-secondary font-weight-bold text-uppercase mb-2">Icon Class</label>
                            <input type="text" name="icon_class" class="form-control bg-dark-soft border-glass text-white py-2 rounded-3" placeholder="ri-eth-line">
                        </div>
                        <div class="col-12">
                            <label class="x-small text-secondary font-weight-bold text-uppercase mb-2">Avatar Signature (PNG/SVG)</label>
                            <input type="file" name="image" class="form-control bg-dark-soft border-glass text-white py-1 rounded-3">
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-3 font-weight-bold rounded-3 shadow-lg">
                                <i class="ri-shield-check-line me-1"></i> AUTHORIZE GATEWAY
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editWalletModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal border-glass shadow-2xl">
            <div class="p-4 border-bottom border-glass bg-black-soft d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="outfit font-weight-bold text-white mb-0">System Reconfiguration</h4>
                    <p class="text-secondary small mb-0">Alter operational gateway parameters.</p>
                </div>
                <button type="button" class="close text-white opacity-50 hover-opacity-100" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="p-4">
                <form action="{{ route('admin.wallets.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="edit_id">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="x-small text-secondary font-weight-bold text-uppercase mb-2">System Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control bg-dark-soft border-glass text-white py-2 rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="x-small text-secondary font-weight-bold text-uppercase mb-2">Asset Symbol</label>
                            <input type="text" name="symbol" id="edit_symbol" class="form-control bg-dark-soft border-glass text-white py-2 rounded-3" required>
                        </div>
                        <div class="col-12">
                            <label class="x-small text-secondary font-weight-bold text-uppercase mb-2">Endpoint Address</label>
                            <input type="text" name="address" id="edit_address" class="form-control bg-dark-soft border-glass text-white py-2 rounded-3" required>
                        </div>
                        <div class="col-md-6">
                            <label class="x-small text-secondary font-weight-bold text-uppercase mb-2">Network ID</label>
                            <input type="text" name="network" id="edit_network" class="form-control bg-dark-soft border-glass text-white py-2 rounded-3">
                        </div>
                        <div class="col-md-6">
                            <label class="x-small text-secondary font-weight-bold text-uppercase mb-2">Icon Class</label>
                            <input type="text" name="icon_class" id="edit_icon" class="form-control bg-dark-soft border-glass text-white py-2 rounded-3">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch custom-switch p-0 mt-2 d-flex align-items-center justify-content-between bg-black-soft p-3 rounded-3 border-glass">
                                <label class="small text-white mb-0" for="edit_active">Live Transmission Status</label>
                                <input type="checkbox" name="is_active" class="form-check-input ms-0" id="edit_active" style="width: 40px; height: 20px;">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="x-small text-secondary font-weight-bold text-uppercase mb-2">Update Avatar Signature (Optional)</label>
                            <input type="file" name="image" class="form-control bg-dark-soft border-glass text-white py-1 rounded-3">
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary w-100 py-3 font-weight-bold rounded-3 shadow-lg">
                                <i class="ri-refresh-line me-1"></i> PUSH RECONFIGURATION
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        $('.edit-wallet-btn').on('click', function() {
            const data = $(this).data();
            $('#edit_id').val(data.id);
            $('#edit_name').val(data.name);
            $('#edit_symbol').val(data.symbol);
            $('#edit_address').val(data.address);
            $('#edit_network').val(data.network);
            $('#edit_icon').val(data.icon);
            $('#edit_active').prop('checked', data.active == 1);
            $('#editWalletModal').modal('show');
        });
    });
</script>
@endpush


<style>
    .bg-dark-soft { background: rgba(0,0,0,0.2) !important; }
    .bg-black-soft { background: rgba(0,0,0,0.3) !important; }
    .border-glass { border-color: rgba(255,255,255,0.05) !important; }
    .bg-primary-soft { background: rgba(59, 130, 246, 0.1); }
    .bg-success-soft { background: rgba(255, 51, 51, 0.1); }
    .bg-danger-soft { background: rgba(239, 68, 68, 0.1); }
    .x-small { font-size: 10px; }
    .hover-opacity-100:hover { opacity: 1 !important; }
    .dropdown-item { transition: all 0.2s; }
    .dropdown-item:hover { background: rgba(255,255,255,0.05); }
    .form-check-input:checked { background-color: #3b82f6; border-color: #3b82f6; }
</style>
@endsection

