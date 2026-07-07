@extends('layouts.admin.app')
@section('title', 'System Configuration')

@section('content')

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-5">
        <div>
            <h1 class="h2 mb-1 text-white gradient-text" style="font-weight: 800;">Global System Configurations</h1>
            <p class="text-muted mb-0">Manage platform settings and operational parameters.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
             <div class="glass-panel px-4 py-2 satin-border rounded-pill">
                <span class="text-white small font-weight-bold"><i data-lucide="shield-check" class="mr-2" style="width:14px; display:inline-block; vertical-align:middle;"></i> SYSTEM OPERATIONAL</span>
            </div>
            <button onclick="window.location.reload()" class="btn btn-primary glass-panel border-0 px-4 py-2 satin-border" style="background: var(--accent-primary) !important; color: #ffffff !important; font-weight: 700;">
                <i data-lucide="refresh-cw" class="mr-2" style="width: 16px; display: inline-block; vertical-align: middle;"></i> Save Changes
            </button>
        </div>
    </div>

    @forelse ($data as $key => $value )
        <!-- Identity Section -->
        <div class="glass-card satin-border mb-5 overflow-hidden shadow-2xl">
            <div class="p-4 d-flex justify-content-between align-items-center" style="background: rgba(255,255,255,0.02); border-bottom: 1px solid var(--glass-border);">
                <h3 class="h5 text-white mb-0 font-weight-bold d-flex align-items-center">
                    <i data-lucide="layout" class="mr-2 text-primary" style="width:20px"></i> Platform Identity & Branding
                </h3>
                <span class="badge badge-primary-glass px-3 py-1">BUILD v4.2</span>
            </div>
            <div class="card-body p-4">
                <form method='post' action="{{route('site.post')}}" class='row g-4' enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name='id' value='{{$value->id}}'>
                    
                    <div class='col-lg-4 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Site Name</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='name' value="{{$value->name}}" required style="border-radius:12px;">
                    </div>

                    <div class='col-lg-3 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Site Logo</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="glass-panel p-2 rounded" style="background: rgba(0,0,0,0.2) !important; border: 1px solid var(--glass-border);">
                                <img src="{{asset('assets/img/favicon.svg')}}" style="height: 40px; width: 40px; object-fit: contain;">
                            </div>
                            <input type='file' class='form-control glass-panel border-0 text-white small x-small' name='logo'>
                        </div>
                    </div>

                    <div class='col-lg-3 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Platform Favicon</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="glass-panel p-2 rounded" style="background: rgba(0,0,0,0.2) !important; border: 1px solid var(--glass-border);">
                                <img src="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}" style="height: 40px; width: 40px; object-fit: contain;">
                            </div>
                            <input type='file' class='form-control glass-panel border-0 text-white small x-small' name='favicon'>
                        </div>
                    </div>

                    <div class='col-lg-6 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Auth Interface branding</label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="glass-panel p-2 rounded" style="background: rgba(0,0,0,0.2) !important; border: 1px solid var(--glass-border);">
                                <img src="{{asset('storage/image/'.site()->login)}}" style="height: 40px; object-fit: contain;">
                            </div>
                            <input type='file' class='form-control glass-panel border-0 text-white small x-small' name='login'>
                        </div>
                    </div>

                    <div class='col-lg-4 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Meta Signature (SEO)</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='meta' value="{{$value->meta}}" required style="border-radius:12px;">
                    </div>

                    <div class='col-lg-4 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Support Channel (Email)</label>
                        <input type='email' class='form-control glass-panel border-0 text-white px-3 py-2' name='email' value="{{$value->email}}" required style="border-radius:12px;">
                    </div>

                    <div class='col-lg-4 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Direct Hotline (Phone)</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='phone' value="{{$value->phone}}" required style="border-radius:12px;">
                    </div>

                    <div class='col-lg-12 mb-4'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Corporate Headquarters (Address)</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='address' value="{{$value->address}}" required style="border-radius:12px;">
                    </div>

                    <div class="col-12 text-right">
                         <button type="submit" class='btn btn-primary px-5 py-3 satin-border font-weight-bold' style="background: var(--accent-primary) !important; color: #ffffff !important; border:none; border-radius:16px; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);">
                            <i data-lucide="save" class="mr-2" style="width:18px; display:inline-block; vertical-align:middle;"></i> PERSIST CHANGES
                         </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Communication & Limits Section -->
        <div class="row g-4 mb-5">
            <!-- System Message -->
            <div class="col-lg-6">
                <!-- ... existing system message ... -->
            </div>
        </div>

        <!-- NEW: SMTP & Environment Configuration -->
        <div class="glass-card satin-border mb-5 p-4 shadow-2xl">
            <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                <i data-lucide="mail" class="mr-2 text-primary" style="width:22px"></i> SMTP & System Environment
            </h5>
            <form method='post' action="{{route('site.post')}}">
                @csrf
                <input type="hidden" name='id' value='{{$value->id}}'>
                <div class="row g-3">
                    <div class='col-lg-4 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">SMTP Host</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='smtp_host' value="{{$value->smtp_host}}" placeholder="smtp.mailtrap.io" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-2 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Port</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='smtp_port' value="{{$value->smtp_port}}" placeholder="587" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-3 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Encryption</label>
                        <select class='form-control glass-panel border-0 text-white px-3 py-2 h-auto' name='smtp_encryption' style="border-radius:12px;">
                            <option value="tls" {{$value->smtp_encryption == 'tls' ? 'selected' : ''}}>TLS</option>
                            <option value="ssl" {{$value->smtp_encryption == 'ssl' ? 'selected' : ''}}>SSL</option>
                            <option value="null" {{$value->smtp_encryption == 'null' ? 'selected' : ''}}>None</option>
                        </select>
                    </div>
                    <div class='col-lg-4 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">SMTP Username</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='smtp_user' value="{{$value->smtp_user}}" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-4 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">SMTP Password</label>
                        <input type='password' class='form-control glass-panel border-0 text-white px-3 py-2' name='smtp_pass' value="{{$value->smtp_pass}}" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-4 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Mail From Address</label>
                        <input type='email' class='form-control glass-panel border-0 text-white px-3 py-2' name='mail_from_address' value="{{$value->mail_from_address}}" placeholder="noreply@example.com" style="border-radius:12px;">
                    </div>
                    <hr class="border-glass-light my-4">
                    <div class='col-lg-6 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Application URL</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='app_url' value="{{$value->app_url}}" placeholder="https://..." style="border-radius:12px;">
                    </div>
                    <div class='col-lg-3 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Debug Mode</label>
                        <select class='form-control glass-panel border-0 text-white px-3 py-2 h-auto' name='app_debug' style="border-radius:12px;">
                            <option value="1" {{$value->app_debug == 1 ? 'selected' : ''}}>Enabled (Development)</option>
                            <option value="0" {{$value->app_debug == 0 ? 'selected' : ''}}>Disabled (Production)</option>
                        </select>
                    </div>
                </div>
                <div class="text-right mt-4">
                    <button type="submit" class='btn btn-primary px-5 py-3 satin-border font-weight-bold' style="border-radius:12px;">UPDATE SYSTEM ENV</button>
                </div>
            </form>
        </div>

        <!-- NEW: Pusher Realtime Configuration -->
        <div class="glass-card satin-border mb-5 p-4 shadow-2xl">
            <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                <i data-lucide="zap" class="mr-2 text-warning" style="width:22px"></i> Realtime & WebSocket (Pusher)
            </h5>
            <form method='post' action="{{route('site.post')}}">
                @csrf
                <input type="hidden" name='id' value='{{$value->id}}'>
                <div class="row g-3">
                    <div class='col-lg-3 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">App ID</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='pusher_app_id' value="{{$value->pusher_app_id}}" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-3 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">App Key</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='pusher_app_key' value="{{$value->pusher_app_key}}" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-3 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">App Secret</label>
                        <input type='password' class='form-control glass-panel border-0 text-white px-3 py-2' name='pusher_app_secret' value="{{$value->pusher_app_secret}}" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-3 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Cluster</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='pusher_app_cluster' value="{{$value->pusher_app_cluster}}" placeholder="mt1" style="border-radius:12px;">
                    </div>
                </div>
                <div class="text-right mt-4">
                    <button type="submit" class='btn btn-warning px-5 py-3 satin-border font-weight-bold' style="border-radius:12px; color: #000;">SAVE REALTIME CONFIG</button>
                </div>
            </form>
        </div>

        <!-- NEW: Global Market API Keys -->
        <div class="glass-card satin-border mb-5 p-4 shadow-2xl">
            <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                <i data-lucide="bar-chart-2" class="mr-2 text-info" style="width:22px"></i> Market Data API Master Keys
            </h5>
            <form method='post' action="{{route('site.post')}}">
                @csrf
                <input type="hidden" name='id' value='{{$value->id}}'>
                <div class="row g-3">
                    <div class='col-lg-6 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">AlphaVantage Key</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='alphavantage_api_key' value="{{$value->alphavantage_api_key}}" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-6 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Finnhub Key</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='finnhub_api_key' value="{{$value->finnhub_api_key}}" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-6 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">CoinGecko Key (Optional)</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='coingecko_api_key' value="{{$value->coingecko_api_key}}" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-3 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Binance Key</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='binance_api_key' value="{{$value->binance_api_key}}" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-3 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Binance Secret</label>
                        <input type='password' class='form-control glass-panel border-0 text-white px-3 py-2' name='binance_api_secret' value="{{$value->binance_api_secret}}" style="border-radius:12px;">
                    </div>
                </div>

                <!-- NEW: Round Robin Toggle -->
                <div class="glass-panel p-3 satin-border mt-3 d-flex align-items-center justify-content-between" style="border-radius:16px; background: rgba(59, 130, 246, 0.05);">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 p-2 bg-primary-soft rounded-lg">
                            <i data-lucide="refresh-cw" class="text-primary" style="width:20px"></i>
                        </div>
                        <div>
                            <p class="text-white font-weight-bold mb-0">Strict Round Robin Mode</p>
                            <p class="text-muted small mb-0">Force sequential rotation across all active providers to evenly distribute API load.</p>
                        </div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input custom-switch" type="checkbox" name="use_round_robin" id="useRoundRobin" {{ $value->use_round_robin ? 'checked' : '' }} style="transform: scale(1.5); cursor: pointer;">
                    </div>
                </div>

                <div class="text-right mt-4">
                    <button type="submit" class='btn btn-info px-5 py-3 satin-border font-weight-bold' style="border-radius:12px;">UPDATE MASTER KEYS</button>
                </div>
            </form>
        </div>

        <!-- NEW: Asset Logo & Identity Providers -->
        <div class="glass-card satin-border mb-5 p-4 shadow-2xl">
            <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                <i data-lucide="image" class="mr-2 text-success" style="width:22px"></i> Asset Identity & Logo Providers
            </h5>
            <form method='post' action="{{route('site.post')}}">
                @csrf
                <input type="hidden" name='id' value='{{$value->id}}'>
                <div class="row g-3">
                    <div class='col-lg-6 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Twelve Data API Key (Advanced Stocks/Crypto Logos)</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='twelve_data_api_key' value="{{$value->twelve_data_api_key}}" placeholder="Required for high-fidelity institutional logos" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-6 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Polygon.io API Key (Official Equities Data)</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='polygon_api_key' value="{{$value->polygon_api_key}}" placeholder="Secondary fallback for stock identity" style="border-radius:12px;">
                    </div>
                    
                    <div class="col-12">
                        <div class="glass-panel p-4 satin-border mt-2" style="border-radius:16px; background: rgba(255, 51, 51, 0.05);">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3 p-2 bg-success-soft rounded-lg">
                                        <i data-lucide="refresh-cw" class="text-success" style="width:20px"></i>
                                    </div>
                                    <div>
                                        <p class="text-white font-weight-bold mb-0">Autonomous Logo Synchronization</p>
                                        <p class="text-muted small mb-0">Automatically attempt to resolve and cache logos when new assets are discovered or imported.</p>
                                    </div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input custom-switch" type="checkbox" name="auto_sync_logos" id="autoSyncLogos" {{ $value->auto_sync_logos ? 'checked' : '' }} style="transform: scale(1.5); cursor: pointer;">
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center gap-4 mt-3 pt-3 border-top border-glass-light">
                                <span class="text-muted x-small text-uppercase font-weight-bold">Active Free Providers:</span>
                                <span class="badge badge-secondary-glass x-small">Seeking Alpha</span>
                                <span class="badge badge-secondary-glass x-small">TickerLogos</span>
                                <span class="badge badge-secondary-glass x-small">CoinCap</span>
                                <span class="badge badge-secondary-glass x-small">FlagCDN</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-right mt-4">
                    <button type="submit" class='btn btn-success px-5 py-3 satin-border font-weight-bold' style="border-radius:12px; border:none; background: var(--accent-primary) !important; color: #ffffff !important;">
                        <i data-lucide="save" class="mr-2" style="width:18px; display:inline-block; vertical-align:middle;"></i> SAVE LOGO CONFIG
                    </button>
                </div>
            </form>
        </div>

        <!-- NEW: Google OAuth / Gmail Sign-in Configuration -->
        <div class="glass-card satin-border mb-5 p-4 shadow-2xl">
            <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                <i data-lucide="log-in" class="mr-2 text-danger" style="width:22px"></i> Google OAuth & Gmail Authentication
            </h5>
            <form method='post' action="{{route('site.post')}}">
                @csrf
                <input type="hidden" name='id' value='{{$value->id}}'>
                <div class="row g-3">
                    <div class='col-lg-12 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Google Client ID</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='google_client_id' value="{{$value->google_client_id}}" placeholder="77**********-****************.apps.googleusercontent.com" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-6 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Google Client Secret</label>
                        <input type='password' class='form-control glass-panel border-0 text-white px-3 py-2' name='google_client_secret' value="{{$value->google_client_secret}}" placeholder="GOCSPX-**************************" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-6 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Google Redirect URL (Callback)</label>
                        <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='google_redirect_url' value="{{$value->google_redirect_url}}" placeholder="{{url('/auth/google/callback')}}" style="border-radius:12px;">
                    </div>
                </div>

                <div class="text-right mt-4">
                    <button type="submit" class='btn btn-danger px-5 py-3 satin-border font-weight-bold' style="border-radius:12px; background: #ea4335 !important; color: #fff !important; border:none;">
                        <i data-lucide="save" class="mr-2" style="width:18px; display:inline-block; vertical-align:middle;"></i> AUTHORIZE GOOGLE CONFIG
                    </button>
                </div>
            </form>
        </div>

        <!-- NEW: Screenshot Generation API Configuration -->
        <div class="glass-card satin-border mb-5 p-4 shadow-2xl">
            <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                <i data-lucide="camera" class="mr-2 text-primary" style="width:22px"></i> Institutional Screenshot Factory API
            </h5>
            <form method='post' action="{{route('site.post')}}">
                @csrf
                <input type="hidden" name='id' value='{{$value->id}}'>
                <div class="row g-3">
                    <div class='col-lg-12 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Private API Gateway Key (Secure Header)</label>
                        <div class="input-group">
                            <input type='text' class='form-control glass-panel border-0 text-white px-3 py-2' name='screenshot_api_key' id="screenshot_api_key" value="{{$value->screenshot_api_key}}" placeholder="Leave empty to disable authentication" style="border-radius:12px 0 0 12px;">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary satin-border" onclick="document.getElementById('screenshot_api_key').value = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15)" style="border-radius: 0 12px 12px 0; background: rgba(255,255,255,0.05);">
                                    <i data-lucide="refresh-cw" style="width:14px"></i>
                                </button>
                            </div>
                        </div>
                        <p class="x-small text-muted mt-2">Required for all requests to <code>/api/screenshot/generate</code> via <code>X-API-KEY</code> header.</p>
                    </div>
                </div>
                <div class="text-right mt-4">
                    <button type="submit" class='btn btn-primary px-5 py-3 satin-border font-weight-bold' style="border-radius:12px; background: var(--accent-primary) !important; color: #fff !important; border:none;">
                        <i data-lucide="save" class="mr-2" style="width:18px; display:inline-block; vertical-align:middle;"></i> AUTHORIZE API ACCESS
                    </button>
                </div>
            </form>
        </div>

        <!-- Financial & Content Section -->
        <div class="row g-4 mb-5">
            <!-- Bank Limit -->
            <div class="col-lg-4">
                <div class="glass-card satin-border p-4 h-100 shadow-lg">
                    <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                        <i data-lucide="landmark" class="mr-2 text-success" style="width:20px"></i> Extraction Floor
                    </h5>
                    <form method='post' action="{{route('bank_limit')}}">
                        @csrf
                        <div class='form-group mb-4'>
                            <label class="text-muted small text-uppercase font-weight-bold mb-2">Min Withdrawal Threshold</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text glass-panel border-0 text-muted" style="border-radius: 12px 0 0 12px;">$</span></div>
                                <input type='number' class='form-control glass-panel border-0 text-white px-3 py-2' name='bank_limit' value="{{$value->bank_limit}}" style="border-radius: 0 12px 12px 0;" required>
                            </div>
                        </div>
                        <button class='btn btn-success w-100 py-3 font-weight-bold' style="border-radius:12px;">SET SYSTEM LIMIT</button>
                    </form>
                </div>
            </div>

            <!-- Bank Wire Details (NEW) -->
            <div class="col-lg-8" id="bank-wire-config">
                <div class="glass-card satin-border p-4 h-100 shadow-lg">
                    <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                        <i data-lucide="building-2" class="mr-2 text-primary" style="width:20px"></i> Bank Wire System (IBAN/SWIFT)
                    </h5>
                    <form method='post' action="{{route('bank_content')}}">
                        @csrf
                        <div class='form-group mb-4'>
                            <label class="text-muted small text-uppercase font-weight-bold mb-2">Wire Transfer Instructions</label>
                            <textarea class='form-control glass-panel border-0 text-white p-4 summernote' name="summernote3" style="height:200px; border-radius:16px;">{{site()->bank}}</textarea>
                            <p class="x-small text-muted mt-2">Specify IBAN, SWIFT/BIC, Bank Name, and Routing for institutional deposits.</p>
                        </div>
                        <button class='btn btn-primary w-100 py-3 font-weight-bold' style="border-radius:12px;">PERSIST WIRE CONFIG</button>
                    </form>
                </div>
            </div>

            <!-- Bank Error Message (NEW) -->
            <div class="col-lg-4">
                <div class="glass-card satin-border p-4 h-100 shadow-lg">
                    <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                        <i data-lucide="alert-circle" class="mr-2 text-danger" style="width:20px"></i> Withdrawal Security Notifications
                    </h5>
                    <form method='post' action="{{route('bank_error_message')}}">
                        @csrf
                        <div class='form-group mb-4'>
                            <label class="text-muted small text-uppercase font-weight-bold mb-2">Custom Failure Message</label>
                            <textarea class='form-control glass-panel border-0 text-white px-3 py-3' name='withdrawal_message' style="height: 120px; resize: none; border-radius:12px;">{{site()->withdrawal_message}}</textarea>
                            <p class="x-small text-muted mt-2">Displayed when a bank withdrawal fails/blocks.</p>
                        </div>
                        <button class='btn btn-danger w-100 py-3 font-weight-bold' style="border-radius:12px;">UPDATE FAILURE LOGIC</button>
                    </form>
                </div>
            </div>

            <!-- Chat Widget -->
            <div class="col-lg-4">
                <div class="glass-card satin-border p-4 h-100 shadow-lg">
                    <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                        <i data-lucide="message-square" class="mr-2 text-primary" style="width:20px"></i> Live Chat
                    </h5>
                    <form method='post' action="{{route('chat')}}">
                        @csrf
                        <div class='form-group mb-4'>
                            <label class="text-muted small text-uppercase font-weight-bold mb-2">Integration Key (Script Code)</label>
                            <input class='form-control glass-panel border-0 text-white px-3 py-2' type="text" name="code" value="{{$code->code}}" style="border-radius:12px;">
                        </div>
                        <button class='btn btn-primary w-100 py-2 font-weight-bold' style="border-radius:12px;">START HOOK</button>
                    </form>
                </div>
            </div>

            <!-- Video Presentation -->
            <div class="col-lg-4">
                <div class="glass-card satin-border p-4 h-100 shadow-lg">
                    <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                        <i data-lucide="play-circle" class="mr-2 text-danger" style="width:20px"></i> Corporate Briefing
                    </h5>
                    <form method='post' action="{{route('store_video')}}" enctype="multipart/form-data">
                        @csrf
                        <div class='form-group mb-4'>
                            <label class="text-muted small text-uppercase font-weight-bold mb-2">Upload briefing (.mp4)</label>
                            <input class='form-control glass-panel border-0 text-white px-3' type="file" name="video" required style="border-radius:12px;">
                        </div>
                        <button class='btn btn-danger w-100 py-2 font-weight-bold' style="border-radius:12px;">SYNC CINEMATIC</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Trading System Config Section -->
        <div class="glass-card satin-border mb-5 p-4 shadow-2xl">
            <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                <i data-lucide="cpu" class="mr-2 text-accent" style="width:22px; color: var(--accent-primary);"></i> Institutional Trading Execution Engine
            </h5>
            <form method='post' action="{{route('admin.update_engine_settings')}}">
                @csrf
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">System Operational Mode</label>
                        <select class="form-control glass-panel border-0 text-white px-3 py-3" name="engine_mode" style="height: auto; border-radius:12px;">
                            <option value="0" {{$engine->engine_mode == 0 ? 'selected' : ''}}>LIVE (Real-time Market Sync & Signal Execution)</option>
                            <option value="1" {{$engine->engine_mode == 1 ? 'selected' : ''}}>ALGORITHMIC (Management & Precise Yield Control)</option>
                        </select>
                        <p class="x-small text-muted mt-2">Offline mode enforces the win-rate targets across all user accounts.</p>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <label class="text-muted small text-uppercase font-weight-bold mb-2 tracking-wider">Global Target Yield (Win Rate %)</label>
                        <div class="input-group">
                            <input type='number' class='form-control glass-panel border-0 text-white px-3 py-3' name='win_rate' value="{{$engine->win_rate}}" style="border-radius: 12px 0 0 12px;" required>
                            <div class="input-group-append"><span class="input-group-text glass-panel border-0 text-muted" style="border-radius: 0 12px 12px 0;">%</span></div>
                        </div>
                        <p class="x-small text-muted mt-2">The exact ratio of won trades for organic execution blocks.</p>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class='btn btn-primary px-5 py-3 satin-border font-weight-bold' style="background: var(--accent-primary) !important; color: #ffffff !important; border:none; border-radius:12px;">
                        <i data-lucide="zap" class="mr-2" style="width:18px; display:inline-block; vertical-align:middle;"></i> COMMENCE SYNC
                    </button>
                </div>
            </form>
        </div>
        
        <!-- NEW: Unified Fee Structure -->
        <div class="glass-card satin-border mb-5 p-4 shadow-2xl">
            <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                <i data-lucide="percent" class="mr-2 text-warning" style="width:22px"></i> Unified Fee Structure
            </h5>
            <form method='post' action="{{route('site.post')}}">
                @csrf
                <input type="hidden" name='id' value='{{$value->id}}'>
                <div class="row g-3">
                    <div class='col-lg-4 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Maker Fee (%)</label>
                        <input type='number' step='0.0001' class='form-control glass-panel border-0 text-white px-3 py-2' name='maker_fee' value="{{$value->maker_fee ?? 0.0010}}" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-4 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Taker Fee (%)</label>
                        <input type='number' step='0.0001' class='form-control glass-panel border-0 text-white px-3 py-2' name='taker_fee' value="{{$value->taker_fee ?? 0.0020}}" style="border-radius:12px;">
                    </div>
                    <div class='col-lg-4 mb-3'>
                        <label class="text-muted small text-uppercase font-weight-bold mb-2">Withdrawal Fee ($)</label>
                        <input type='number' step='0.0001' class='form-control glass-panel border-0 text-white px-3 py-2' name='withdrawal_fee' value="{{$value->withdrawal_fee ?? 5.0000}}" style="border-radius:12px;">
                    </div>
                </div>
                <div class="text-right mt-4">
                    <button type="submit" class='btn btn-warning px-5 py-3 satin-border font-weight-bold' style="border-radius:12px; color: #000;">UPDATE FEES</button>
                </div>
            </form>
        </div>

        <!-- Rich Content Editor Sections -->
        <div class="glass-card satin-border mb-5 p-4 shadow-2xl">
            <h5 class="text-white font-weight-bold mb-4 d-flex align-items-center">
                <i data-lucide="scroll" class="mr-2 text-info" style="width:22px"></i> Terms, Compliance & Service Agreements
            </h5>
             <form method='get' action="{{route('admin.user_lock')}}">
                @csrf
                <div class='form-group mb-3'>
                    <label class="text-muted small text-uppercase font-weight-bold mb-2">Directive Title</label>
                    <input class='form-control glass-panel border-0 text-white px-3 py-3' type="text" name="title" value="{{$lock->title}}" required style="border-radius:12px;">
                </div>
                <div class='form-group mb-4'>
                    <label class="text-muted small text-uppercase font-weight-bold mb-2">Standard Trading Agreement (Content)</label>
                    <textarea class='form-control glass-panel border-0 text-white p-4' name="message" style="height:350px; border-radius:16px;">{{$lock->message}}</textarea>
                </div>
                <div class="text-right">
                    <button type="submit" class='btn btn-info px-5 py-3 satin-border font-weight-bold' style="border-radius:12px;">
                        <i data-lucide="shield-check" class="mr-2" style="width:18px; display:inline-block; vertical-align:middle;"></i> PERSIST DIRECTIVE
                    </button>
                </div>
            </form>
        </div>

    @empty
        <div class="glass-panel p-5 text-center text-muted satin-border rounded-lg" style="background: rgba(255,100,100,0.05);">
            <i data-lucide="alert-triangle" class="mb-3 text-danger" style="width:48px; height:48px;"></i>
            <h3 class="text-white font-weight-bold">Infrastructure Null</h3>
            <p>No site settings found. Please configure your platform settings.</p>
        </div>
    @endforelse

</div>

<style>
    .x-small { font-size: 10px; }
    .tracking-wider { letter-spacing: 0.1em; }
    .gap-3 { gap: 1rem; }
    .gap-2 { gap: 0.5rem; }
    .form-control:focus { background: rgba(255,255,255,0.08) !important; box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2) !important; color: white !important; }
    .input-group-text { background: rgba(255,255,255,0.05) !important; border: 1px solid var(--glass-border) !important; color: var(--text-muted) !important; }
    textarea { font-family: 'JetBrains Mono', 'Fira Code', monospace; font-size: 0.9rem; line-height: 1.6; }
</style>

<script>
    $(document).ready(function() {
        lucide.createIcons();
        $('.summernote').summernote({
            height: 250,
            tabsize: 2
        });
    });
</script>
@endsection

