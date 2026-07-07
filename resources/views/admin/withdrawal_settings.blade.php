@extends('layouts.admin.app')
@section('title', 'Withdrawal Institutional Configuration')

@section('content')

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Withdrawal Management Console</h1>
            <p class="text-muted mb-0">Configure multi-stage institutional verification flows and settlement parameters.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="glass-panel px-4 py-2 satin-border rounded-pill">
                <span class="text-white small font-weight-bold"><i data-lucide="shield-check" class="mr-2" style="width:14px; display:inline-block; vertical-align:middle;"></i> SETTLEMENT ENGINE ACTIVE</span>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-pills mb-4" id="withdrawalTabs" role="tablist" style="background: rgba(255,255,255,0.03); border-radius: 14px; padding: 6px; border: 1px solid var(--glass-border); display: inline-flex;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="config-tab" data-toggle="pill" data-target="#config-pane" type="button" role="tab" style="border-radius: 10px; padding: 10px 24px; font-weight: 600; font-size: 0.85rem;">
                <i data-lucide="settings" style="width:14px; display:inline-block; vertical-align:middle;" class="mr-1"></i> Configuration
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="codes-tab" data-toggle="pill" data-target="#codes-pane" type="button" role="tab" style="border-radius: 10px; padding: 10px 24px; font-weight: 600; font-size: 0.85rem;">
                <i data-lucide="key-round" style="width:14px; display:inline-block; vertical-align:middle;" class="mr-1"></i> Code Generation
            </button>
        </li>
    </ul>

    <div class="tab-content" id="withdrawalTabContent">
        <!-- Configuration Tab -->
        <div class="tab-pane fade show active" id="config-pane" role="tabpanel">
            <form method='post' action="{{route('admin.withdrawal_settings.update')}}" class='row g-4'>
                @csrf
                
                <!-- Global Workflow Control -->
                <div class="col-12 mb-5">
                    <div class="glass-card satin-border overflow-hidden shadow-2xl">
                        <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
                            <h3 class="h5 text-white mb-0 font-weight-bold d-flex align-items-center">
                                <i data-lucide="activity" class="mr-2 text-primary" style="width:20px"></i> Global Workflow Clearance Flow
                            </h3>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="withdrawal_flow_enabled" name="withdrawal_flow_enabled" {{ site()->withdrawal_flow_enabled ? 'checked' : '' }}>
                                <label class="custom-control-label text-white small" for="withdrawal_flow_enabled">SYSTEM-WIDE CLEARANCE FLOW</label>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Default Security for New Users</label>
                                    <select name="default_withdrawal_security" class="form-control glass-panel border-0 text-white px-3 py-2" style="border-radius:12px;">
                                        <option value="0" {{ !site()->default_withdrawal_security ? 'selected' : '' }}>Standard (No Extra Clearance Required)</option>
                                        <option value="1" {{ site()->default_withdrawal_security ? 'selected' : '' }}>Institutional (Full MFIS Clearance Required)</option>
                                    </select>
                                    <p class="x-small text-muted mt-2">Determines if new registrants have the multi-stage verification enabled by default.</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Compliance Portal Mode</label>
                                    <select name="tax_mode" class="form-control glass-panel border-0 text-white px-3 py-2" style="border-radius:12px;">
                                        <option value="optimized" selected>Optimized (Modern Financial Portal)</option>
                                        <option value="legacy">Legacy (Regulatory/IRS themed)</option>
                                    </select>
                                    <p class="x-small text-muted mt-2">Controls the visual theme of the Compliance/Tax verification pages.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Institutional Terminology & PINs -->
                <div class="col-lg-8">
                    <div class="glass-card satin-border h-100 shadow-xl overflow-hidden">
                        <div class="p-4" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
                            <h5 class="text-white font-weight-bold mb-0 d-flex align-items-center">
                                <i data-lucide="edit-3" class="mr-2 text-info" style="width:20px"></i> Settlement Stage Configuration
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-4 p-3 glass-panel rounded-lg satin-border" style="background: rgba(6, 182, 212, 0.05) !important;">
                                <label class="text-info small text-uppercase font-weight-bold mb-2 d-block">Stage 1: Account Standing Verification</label>
                                <input type="text" name="clearance_pin_name" class="form-control bg-transparent border-0 text-white font-weight-bold h4 mb-0" value="{{ site()->clearance_pin_name }}" placeholder="e.g. Institutional Clearance PIN">
                            </div>

                            <div class="mb-4 p-3 glass-panel rounded-lg satin-border" style="background: rgba(139, 92, 246, 0.05) !important;">
                                <label class="text-primary small text-uppercase font-weight-bold mb-2 d-block">Stage 2: Regulatory Compliance Gateway</label>
                                <input type="text" name="tax_pin_name" class="form-control bg-transparent border-0 text-white font-weight-bold h4 mb-0" value="{{ site()->tax_pin_name }}" placeholder="e.g. Regulatory Tax Authorization">
                            </div>

                            <div class="mb-4 p-3 glass-panel rounded-lg satin-border" style="background: rgba(245, 158, 11, 0.05) !important;">
                                <label class="text-warning small text-uppercase font-weight-bold mb-2 d-block">Stage 3: Final Asset Liquidation</label>
                                <input type="text" name="liquidation_pin_name" class="form-control bg-transparent border-0 text-white font-weight-bold h4 mb-0" value="{{ site()->liquidation_pin_name }}" placeholder="e.g. Asset Liquidation Processing PIN">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tax & Fee Parameters -->
                <div class="col-lg-4">
                    <div class="glass-card satin-border h-100 p-4 shadow-xl">
                        <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                            <i data-lucide="percent" class="mr-2 text-success" style="width:20px"></i> Financial Parameters
                        </h5>
                        
                        <div class='form-group mb-4'>
                            <label class="text-muted small text-uppercase font-weight-bold mb-2">Standard Tax Liability (%)</label>
                            <div class="input-group">
                                <input type='number' step="0.01" class='form-control glass-panel border-0 text-white px-3 py-3' name='tax_percentage' value="{{ $tax->percentage }}" style="border-radius: 12px 0 0 12px;">
                                <div class="input-group-append"><span class="input-group-text glass-panel border-0 text-muted" style="border-radius: 0 12px 12px 0;">%</span></div>
                            </div>
                        </div>

                        <div class='form-group mb-4'>
                            <label class="text-muted small text-uppercase font-weight-bold mb-2">Minimum Settlement Fee ($)</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text glass-panel border-0 text-muted" style="border-radius: 12px 0 0 12px;">$</span></div>
                                <input type='number' class='form-control glass-panel border-0 text-white px-3 py-3' name='tax_amount' value="{{ $tax->amount }}" style="border-radius: 0 12px 12px 0;">
                            </div>
                        </div>

                        <div class="mt-auto pt-4">
                            <button type="submit" class="btn btn-primary w-100 py-3 satin-border font-weight-bold" style="background: var(--accent-primary) !important; color: #ffffff !important; border-radius:16px;">
                                <i data-lucide="save" class="mr-2" style="width:18px; display:inline-block; vertical-align:middle;"></i> PERSIST CONFIGURATION
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Code Generation Tab -->
        <div class="tab-pane fade" id="codes-pane" role="tabpanel">
            <div class="glass-card satin-border overflow-hidden shadow-2xl">
                <div class="p-4 d-flex flex-wrap justify-content-between align-items-center gap-3" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
                    <h5 class="text-white font-weight-bold mb-0 d-flex align-items-center">
                        <i data-lucide="key-round" class="mr-2 text-warning" style="width:20px"></i> Institutional PIN Code Manager
                    </h5>
                    <div class="d-flex align-items-center gap-3">
                        <div class="input-group" style="max-width: 320px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text glass-panel border-0 text-muted" style="border-radius: 12px 0 0 12px;"><i data-lucide="search" style="width:14px"></i></span>
                            </div>
                            <input type="text" id="userSearchInput" class="form-control glass-panel border-0 text-white px-3 py-2" placeholder="Search by name or email..." style="border-radius: 0 12px 12px 0;" onkeyup="filterUsers()">
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Optimized Institutional Pipeline Legend -->
                    <div class="px-4 py-4" style="background: rgba(255,255,255,0.01); border-bottom: 1px solid var(--glass-border);">
                        <div class="row g-3">
                            <!-- Stage 1 -->
                            <div class="col-md-4">
                                <div class="glass-panel p-3 h-100 d-flex align-items-center gap-3" style="border-radius: 16px; transition: 0.3s; background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.15);">
                                    <div class="d-flex align-items-center justify-content-center" style="width:42px; height:42px; border-radius:12px; background: rgba(6, 182, 212, 0.1); color: #06b6d4;">
                                        <i data-lucide="shield-check" style="width:20px;"></i>
                                    </div>
                                    <div>
                                        <div class="text-white font-weight-bold" style="font-size: 0.85rem; letter-spacing: 0.5px;">Verification Stage 1</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ site()->clearance_pin_name ?? 'Institutional Clearance PIN' }}</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Stage 2 -->
                            <div class="col-md-4">
                                <div class="glass-panel p-3 h-100 d-flex align-items-center gap-3" style="border-radius: 16px; transition: 0.3s; background: rgba(139, 92, 246, 0.05); border: 1px solid rgba(139, 92, 246, 0.15);">
                                    <div class="d-flex align-items-center justify-content-center" style="width:42px; height:42px; border-radius:12px; background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                                        <i data-lucide="file-text" style="width:20px;"></i>
                                    </div>
                                    <div>
                                        <div class="text-white font-weight-bold" style="font-size: 0.85rem; letter-spacing: 0.5px;">Verification Stage 2</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ site()->tax_pin_name ?? 'Regulatory Tax Authorization' }}</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Stage 3 -->
                            <div class="col-md-4">
                                <div class="glass-panel p-3 h-100 d-flex align-items-center gap-3" style="border-radius: 16px; transition: 0.3s; background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.15);">
                                    <div class="d-flex align-items-center justify-content-center" style="width:42px; height:42px; border-radius:12px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                        <i data-lucide="check-circle" style="width:20px;"></i>
                                    </div>
                                    <div>
                                        <div class="text-white font-weight-bold" style="font-size: 0.85rem; letter-spacing: 0.5px;">Final Stage 3</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ site()->liquidation_pin_name ?? 'Asset Liquidation Processing PIN' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0" id="codesTable" style="background: transparent;">
                            <thead>
                                <tr style="border-bottom: 2px solid var(--glass-border);">
                                    <th class="text-muted small text-uppercase px-4 py-3" style="font-weight: 700; letter-spacing: 0.05em;">User Account</th>
                                    <th class="text-center px-3 py-3" style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.1em; color: #06b6d4 !important; text-transform:uppercase; background: rgba(6, 182, 212, 0.02) !important;">
                                        <i data-lucide="lock" style="width:12px; vertical-align: middle; margin-top:-2px;"></i> STAGE 1 PIN
                                    </th>
                                    <th class="text-center px-3 py-3" style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.1em; color: #8b5cf6 !important; text-transform:uppercase; background: rgba(139, 92, 246, 0.02) !important;">
                                        <i data-lucide="lock" style="width:12px; vertical-align: middle; margin-top:-2px;"></i> STAGE 2 PIN
                                    </th>
                                    <th class="text-center px-3 py-3" style="font-size: 0.7rem; font-weight: 800; letter-spacing: 0.1em; color: #f59e0b !important; text-transform:uppercase; background: rgba(245, 158, 11, 0.02) !important;">
                                        <i data-lucide="lock" style="width:12px; vertical-align: middle; margin-top:-2px;"></i> STAGE 3 PIN
                                    </th>
                                    <th class="text-muted small text-uppercase px-3 py-3 text-center" style="font-weight: 700;">TOGGLES</th>
                                    <th class="text-muted small text-uppercase px-3 py-3 text-center" style="font-weight: 700;">ACTIONS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr class="user-row" data-name="{{ strtolower($user->first_name . ' ' . $user->last_name) }}" data-email="{{ strtolower($user->email) }}" style="border-bottom: 1px solid var(--glass-border);">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center" style="width:36px; height:36px; border-radius:10px; background: rgba(59,130,246,0.1); color: var(--accent-primary); font-weight:700; font-size:0.75rem;">
                                                {{ strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="text-white font-weight-bold small">{{ $user->first_name }} {{ $user->last_name }}</div>
                                                <div class="text-muted" style="font-size: 0.7rem;">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-center" id="code1-{{ $user->id }}" style="background: rgba(6, 182, 212, 0.01);">
                                        <code class="px-2 py-1" style="background: rgba(6,182,212,0.08); color: #06b6d4; border: 1px solid rgba(6,182,212,0.1); border-radius: 6px; font-size: 0.8rem; letter-spacing: 1px; font-family: 'Courier New', monospace; font-weight: 600;">{{ $user->upgrade_code ?? '------' }}</code>
                                    </td>
                                    <td class="px-3 py-3 text-center" id="code2-{{ $user->id }}" style="background: rgba(139, 92, 246, 0.01);">
                                        <code class="px-2 py-1" style="background: rgba(139,92,246,0.08); color: #8b5cf6; border: 1px solid rgba(139,92,246,0.1); border-radius: 6px; font-size: 0.8rem; letter-spacing: 1px; font-family: 'Courier New', monospace; font-weight: 600;">{{ $user->tax_code ?? '------' }}</code>
                                    </td>
                                    <td class="px-3 py-3 text-center" id="code3-{{ $user->id }}" style="background: rgba(245, 158, 11, 0.01);">
                                        <code class="px-2 py-1" style="background: rgba(245,158,11,0.08); color: #f59e0b; border: 1px solid rgba(245,158,11,0.1); border-radius: 6px; font-size: 0.8rem; letter-spacing: 1px; font-family: 'Courier New', monospace; font-weight: 600;">{{ $user->demorage ?? '------' }}</code>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <button type="button" class="btn btn-sm toggle-btn" id="toggle1-{{ $user->id }}" onclick="toggleCode(this, {{ $user->id }}, 'upgrade_code_check')" style="width:30px; height:30px; padding:0; border-radius:8px; border: 1px solid {{ $user->upgrade_code_check == 'on' ? '#06b6d4' : 'var(--glass-border)' }}; background: {{ $user->upgrade_code_check == 'on' ? 'rgba(6,182,212,0.15)' : 'transparent' }}; color: {{ $user->upgrade_code_check == 'on' ? '#06b6d4' : 'var(--text-muted)' }}; font-size:0.7rem; font-weight:800; transition: 0.2s;" title="Stage 1">
                                                S1
                                            </button>
                                            <button type="button" class="btn btn-sm toggle-btn" id="toggle2-{{ $user->id }}" onclick="toggleCode(this, {{ $user->id }}, 'tax_code_check')" style="width:30px; height:30px; padding:0; border-radius:8px; border: 1px solid {{ $user->tax_code_check == 'on' ? '#8b5cf6' : 'var(--glass-border)' }}; background: {{ $user->tax_code_check == 'on' ? 'rgba(139,92,246,0.15)' : 'transparent' }}; color: {{ $user->tax_code_check == 'on' ? '#8b5cf6' : 'var(--text-muted)' }}; font-size:0.7rem; font-weight:800; transition: 0.2s;" title="Stage 2">
                                                S2
                                            </button>
                                            <button type="button" class="btn btn-sm toggle-btn" id="toggle3-{{ $user->id }}" onclick="toggleCode(this, {{ $user->id }}, 'demorage_check')" style="width:30px; height:30px; padding:0; border-radius:8px; border: 1px solid {{ $user->demorage_check == 'on' ? '#f59e0b' : 'var(--glass-border)' }}; background: {{ $user->demorage_check == 'on' ? 'rgba(245,158,11,0.15)' : 'transparent' }}; color: {{ $user->demorage_check == 'on' ? '#f59e0b' : 'var(--text-muted)' }}; font-size:0.7rem; font-weight:800; transition: 0.2s;" title="Stage 3">
                                                S3
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-1">
                                            <button type="button" class="btn btn-sm" onclick="generateCodes(this, {{ $user->id }}, 'generate_all')" style="background: rgba(16,185,129,0.1); color: #ff3333; border: 1px solid rgba(16,185,129,0.2); border-radius: 8px; padding: 4px 10px; font-size: 0.7rem; font-weight: 700;" title="Generate All Codes">
                                                <i data-lucide="refresh-cw" style="width:12px; display:inline-block; vertical-align:middle;"></i> ALL
                                            </button>
                                            <div class="dropdown">
                                                <button class="btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown" style="background: rgba(255,255,255,0.04); color: var(--text-muted); border: 1px solid var(--glass-border); border-radius: 8px; padding: 4px 8px; font-size: 0.7rem;">
                                                    <i data-lucide="more-vertical" style="width:12px; display:inline-block; vertical-align:middle;"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right shadow-lg" style="background: #1e293b; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 8px 0; z-index: 1050; min-width: 200px; box-shadow: 0 10px 25px rgba(0,0,0,0.5) !important;">
                                                    <a class="dropdown-item text-white small py-2" href="#" onclick="generateCodes(this, {{ $user->id }}, 'generate_clearance'); return false;">
                                                        <span style="color:#06b6d4; margin-right:6px; font-size:10px;">●</span> Generate Stage 1 Only
                                                    </a>
                                                    <a class="dropdown-item text-white small py-2" href="#" onclick="generateCodes(this, {{ $user->id }}, 'generate_tax'); return false;">
                                                        <span style="color:#8b5cf6; margin-right:6px; font-size:10px;">●</span> Generate Stage 2 Only
                                                    </a>
                                                    <a class="dropdown-item text-white small py-2" href="#" onclick="generateCodes(this, {{ $user->id }}, 'generate_liquidation'); return false;">
                                                        <span style="color:#f59e0b; margin-right:6px; font-size:10px;">●</span> Generate Stage 3 Only
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($users->isEmpty())
                    <div class="text-center py-5">
                        <i data-lucide="users" class="text-muted mb-3" style="width:48px; height:48px;"></i>
                        <p class="text-muted">No users found in the system.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #withdrawalTabs .nav-link { color: var(--text-secondary); border: none; transition: 0.2s; }
    #withdrawalTabs .nav-link.active { background: var(--accent-primary); color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2); }
    #codesTable th, #codesTable td { vertical-align: middle; background: transparent !important; }
    #codesTable tr:hover { background: rgba(255,255,255,0.02) !important; }
    .toggle-btn { transition: all 0.2s ease; }
    .toggle-btn:hover { transform: scale(1.1); }
    .dropdown-item:hover { background: rgba(255,255,255,0.05) !important; }
</style>

@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Re-init icons when switching tabs
    $('button[data-toggle="pill"]').on('shown.bs.tab', function () {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });

    // Set up CSRF for all jQuery AJAX
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    });

    function filterUsers() {
        var query = $('#userSearchInput').val().toLowerCase();
        $('.user-row').each(function() {
            var name = $(this).data('name') || '';
            var email = $(this).data('email') || '';
            $(this).toggle(name.indexOf(query) > -1 || email.indexOf(query) > -1);
        });
    }

    function generateCodes(element, userId, type) {
        var payload = { user_id: userId };
        payload[type] = 1;

        // Show loading state
        var btn = element.closest('button') || element;
        var origText = btn.innerHTML;
        btn.innerHTML = '<i class="ri-loader-4-line ri-spin" style="font-size:12px"></i>';
        btn.disabled = true;

        $.ajax({
            url: "{{ route('admin.generate_user_codes') }}",
            method: 'POST',
            data: payload,
            success: function(data) {
                if(data.status) {
                    var u = data.user;
                    if(u.upgrade_code) {
                        $('#code1-' + userId).html('<code class="px-2 py-1" style="background: rgba(6,182,212,0.1); color: #06b6d4; border-radius: 6px; font-size: 0.8rem; letter-spacing: 1px;">' + u.upgrade_code + '</code>');
                    }
                    if(u.tax_code) {
                        $('#code2-' + userId).html('<code class="px-2 py-1" style="background: rgba(139,92,246,0.1); color: #8b5cf6; border-radius: 6px; font-size: 0.8rem; letter-spacing: 1px;">' + u.tax_code + '</code>');
                    }
                    if(u.demorage) {
                        $('#code3-' + userId).html('<code class="px-2 py-1" style="background: rgba(245,158,11,0.1); color: #f59e0b; border-radius: 6px; font-size: 0.8rem; letter-spacing: 1px;">' + u.demorage + '</code>');
                    }
                    toastr.success(data.message || 'Codes generated successfully');
                } else {
                    toastr.error(data.message || 'Failed to generate codes');
                }
            },
            error: function(xhr) {
                console.error('Generate codes error:', xhr.responseText);
                toastr.error('Request failed: ' + (xhr.statusText || 'Server error'));
            },
            complete: function() {
                btn.innerHTML = origText;
                btn.disabled = false;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        });
    }

    function toggleCode(element, userId, field) {
        var btn = element.closest('button') || element;
        var origBorder = btn.style.border;
        btn.style.opacity = '0.5';

        $.ajax({
            url: "{{ route('admin.toggle_user_code_check') }}",
            method: 'POST',
            data: { user_id: userId, field: field },
            success: function(data) {
                if(data.status) {
                    var isOn = data.value === 'on';
                    var colors = {
                        'upgrade_code_check': '#06b6d4',
                        'tax_code_check': '#8b5cf6',
                        'demorage_check': '#f59e0b'
                    };
                    var color = colors[field];
                    $(btn).css({
                        'border': '1px solid ' + (isOn ? color : 'rgba(255,255,255,0.08)'),
                        'background': isOn ? color + '26' : 'transparent',
                        'color': isOn ? color : '#9ca3af'
                    });
                    toastr.success('Stage toggled ' + data.value.toUpperCase());
                } else {
                    toastr.error(data.message || 'Toggle failed');
                }
            },
            error: function(xhr) {
                console.error('Toggle error:', xhr.responseText);
                toastr.error('Request failed: ' + (xhr.statusText || 'Server error'));
            },
            complete: function() {
                btn.style.opacity = '1';
            }
        });
    }
</script>
@endpush



