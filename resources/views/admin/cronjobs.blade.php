@extends('layouts.admin.app')
@section('title', 'Cronjob Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Automation & Schedules</h1>
            <p class="text-muted mb-0">Configure system heartbeat and automated market updates.</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Status Card -->
        <div class="col-lg-12 mb-4">
            <div class="glass-card satin-border p-4 shadow-2xl">
                <div class="d-flex align-items-center mb-4">
                    <div class="glass-panel p-3 rounded-circle mr-3" style="background: rgba(255, 51, 51, 0.1) !important; color: #ff3333;">
                        <i data-lucide="check-circle" style="width:24px; height:24px;"></i>
                    </div>
                    <div>
                        <h5 class="text-white mb-0 font-weight-bold">System Heartbeat Status</h5>
                        <p class="text-muted x-small mb-0">Last check: {{ now()->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>
                <div class="alert alert-info glass-panel border-0 text-white satin-border" style="background: rgba(59, 130, 246, 0.05) !important;">
                    <i data-lucide="info" class="mr-2" style="width:18px; display:inline-block; vertical-align:middle;"></i>
                    <strong>Important:</strong> Cronjobs are essential for live prices, trade settlements, and withdrawal processing.
                </div>
            </div>
        </div>

        <!-- Cronjob List -->
        <div class="col-lg-12">
            <div class="glass-card satin-border p-0 overflow-hidden shadow-2xl">
                <div class="p-4 bg-glass-dark border-bottom border-glass">
                    <h5 class="text-white mb-0 font-weight-bold">Required Cronjob Configurations</h5>
                    <p class="text-muted small mb-0">Add these to your cPanel or Server Crontab (Recommended: Every 5-10 minutes)</p>
                </div>
                    <div class="p-4">
                        <div class="alert glass-panel mb-4 text-white" style="background: rgba(255, 51, 51, 0.1); border: 1px solid rgba(255, 51, 51, 0.2);">
                            <div class="d-flex">
                                <div class="mr-3">
                                    <i data-lucide="zap" style="color: #ff3333; width: 24px; height: 24px;"></i>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold mb-1">Master System Cronjob</h6>
                                    <p class="small text-muted mb-0">This single endpoint powers all background operations including market prices, automated trading, bot execution, and deposit synchronization.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="font-weight-bold text-white mb-2">Option 1: cPanel / Web Cron (Recommended for Shared Hosting)</h6>
                            <div class="input-group">
                                <input type="text" class="form-control glass-panel border-0 text-muted small py-3" readonly value="wget -q -O /dev/null {{ route('system.cron.run') }}" id="cron_web">
                                <div class="input-group-append">
                                    <button class="btn btn-primary px-4" onclick="copyToClipboard('cron_web')">
                                        <i data-lucide="copy" class="mr-2" style="width:16px;"></i> Copy
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted d-block mt-2">Set frequency to: <span class="badge badge-primary-glass">*/1 * * * *</span> (Every Minute)</small>
                        </div>

                        <div class="mb-4">
                            <h6 class="font-weight-bold text-white mb-2">Option 2: Native CLI (Recommended for VPS / Dedicated)</h6>
                            <div class="input-group">
                                <input type="text" class="form-control glass-panel border-0 text-muted small py-3" readonly value="cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1" id="cron_cli">
                                <div class="input-group-append">
                                    <button class="btn btn-primary px-4" onclick="copyToClipboard('cron_cli')">
                                        <i data-lucide="copy" class="mr-2" style="width:16px;"></i> Copy
                                    </button>
                                </div>
                            </div>
                            <p class="text-muted mt-2 mb-0" style="font-size: 0.8rem;"><i data-lucide="info" class="mr-1" style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;"></i> If your server uses a specific PHP version (like Virtualmin or cPanel), you may need to replace <code>php</code> with your specific PHP path (e.g. <code>/usr/bin/php8.1</code>).</p>
                            <small class="text-muted d-block mt-2">Set frequency to: <span class="badge badge-primary-glass">* * * * *</span> (Every Minute)</small>
                        </div>

                        <div class="text-right mt-4 pt-3 border-top border-glass-light">
                            <button class="btn btn-success px-4 py-2 rounded-pill font-weight-bold" onclick="runCron('{{ route('system.cron.run') }}', this)">
                                <i data-lucide="play" class="mr-2" style="width:16px; display:inline-block;"></i> TEST MASTER CRON NOW
                            </button>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<script>
    function runCron(url, btn) {
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line ri-spin mr-1"></i> Running...';
        
        fetch(url)
            .then(response => {
                if(response.ok) {
                    toastr.success("Task executed successfully");
                } else {
                    toastr.error("Task failed to execute");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error("Connection error");
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                lucide.createIcons();
            });
    }

    function copyToClipboard(elementId) {
        var copyText = document.getElementById(elementId);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        
        Toastify({
            text: "Command copied to clipboard",
            duration: 3000,
            gravity: "top",
            position: "right",
            style: {
                background: "linear-gradient(to right, #8b5cf6, #3b82f6)",
            }
        }).showToast();
    }
</script>

<style>
    .hover-glass:hover { background: rgba(255,255,255,0.02); }
    .bg-glass-dark { background: rgba(0,0,0,0.2); }
    .bg-glass-light { background: rgba(255,255,255,0.05); }
    .border-glass { border-color: rgba(255,255,255,0.1) !important; }
    .border-glass-light { border-color: rgba(255,255,255,0.05) !important; }
</style>
@endsection
