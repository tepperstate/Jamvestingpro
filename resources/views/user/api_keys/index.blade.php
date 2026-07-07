@extends('layouts.user.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-primary mb-2">API Management</h2>
            <p class="text-muted">Generate and manage API keys to access platform features programmatically.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('new_api_secret'))
    <div class="alert alert-warning">
        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Important!</h5>
        <p>This is your API Secret Key. Please copy it and keep it secure. For security reasons, it will <strong>never be shown again</strong>.</p>
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="newSecretKey" value="{{ session('new_api_secret') }}" readonly>
            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('newSecretKey')">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Create API Key Form -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card glass-card">
                <div class="card-header border-bottom border-light">
                    <h5 class="mb-0">Create New API Key</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.api_keys.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Key Name / Label <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control bg-dark text-light border-secondary" placeholder="e.g. My Trading Bot" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Permissions</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="read" id="permRead" checked readonly>
                                <label class="form-check-label" for="permRead">Read Data (Default)</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="trade" id="permTrade">
                                <label class="form-check-label" for="permTrade">Enable Spot & Futures Trading</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="withdraw" id="permWithdraw">
                                <label class="form-check-label text-warning" for="permWithdraw">Enable Withdrawals</label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">IP Whitelist (Optional)</label>
                            <input type="text" name="ip_whitelist" class="form-control bg-dark text-light border-secondary" placeholder="e.g. 192.168.1.1, 10.0.0.5">
                            <small class="text-muted d-block mt-1">Separate multiple IPs with commas.</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Generate API Key</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- API Keys List -->
        <div class="col-xl-8 col-lg-7">
            <div class="card glass-card">
                <div class="card-header border-bottom border-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Your API Keys</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover text-light align-middle mb-0">
                            <thead class="text-muted border-bottom border-secondary">
                                <tr>
                                    <th class="ps-4">Label</th>
                                    <th>API Key</th>
                                    <th>Permissions</th>
                                    <th>Created</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($api_keys as $key)
                                <tr>
                                    <td class="ps-4"><strong>{{ $key->name }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="text-truncate" style="max-width: 150px;">{{ substr($key->api_key, 0, 8) }}...{{ substr($key->api_key, -8) }}</span>
                                            <button class="btn btn-sm btn-link text-muted ms-2 p-0" onclick="copyText('{{ $key->api_key }}')"><i class="fas fa-copy"></i></button>
                                        </div>
                                    </td>
                                    <td>
                                        @if(is_array($key->permissions))
                                            @foreach($key->permissions as $perm)
                                                <span class="badge bg-secondary me-1">{{ ucfirst($perm) }}</span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>{{ $key->created_at->format('M d, Y') }}</td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('user.api_keys.destroy', $key->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this API Key? This action cannot be undone and any connected bots will lose access immediately.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted mb-3"><i class="fas fa-key fa-3x"></i></div>
                                        <h6>No API Keys found</h6>
                                        <p class="text-muted mb-0">Create an API key using the form to get started.</p>
                                    </td>
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

@push('scripts')
<script>
    function copyToClipboard(elementId) {
        var copyText = document.getElementById(elementId);
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* For mobile devices */
        document.execCommand("copy");
        
        // Optional: show a small toast or change button text temporarily
        let btn = copyText.nextElementSibling;
        let originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.replace('btn-outline-secondary', 'btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.replace('btn-success', 'btn-outline-secondary');
        }, 2000);
    }
    
    function copyText(text) {
        var tempInput = document.createElement("input");
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        alert('API Key copied to clipboard!');
    }
</script>
@endpush
@endsection

