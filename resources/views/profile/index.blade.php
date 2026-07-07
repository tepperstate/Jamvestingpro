@extends('layouts.user.app')

@section('title', 'Account Settings')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">

<div class="container-fluid py-5">
    <div class="row g-5">
        <!-- Sidebar Navigation -->
        <div class="col-xl-3">
            <div class="glass-card p-5 h-100 shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(20px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.05);">
                <div class="text-center mb-5">
                    <div class="avatar-box mx-auto mb-4 position-relative d-inline-block">
                        <img src="{{ auth()->user()->image ? asset('storage/image/'.auth()->user()->image) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->first_name).'&background=020617&color=3b82f6&size=128' }}" class="rounded-circle border border-primary border-opacity-25 p-2 shadow-sm" style="width: 110px; height: 110px; background: rgba(59, 130, 246, 0.05); object-fit: cover;">
                        <span class="position-absolute bottom-0 end-0 bg-success border border-dark rounded-circle" style="width: 22px; height: 22px; border-width: 4px !important;"></span>
                        
                        <!-- Profile Photo Upload Form -->
                        <form action="{{ route('profile.edit') }}" method="POST" enctype="multipart/form-data" id="profile-photo-form" style="position: absolute; bottom: 0; left: 0;">
                            @csrf
                            <label for="profile_image" class="btn btn-sm btn-primary rounded-circle shadow p-1 d-flex align-items-center justify-content-center" style="cursor: pointer; width: 32px; height: 32px; border: 2px solid #000000; transform: translate(-20%, 20%);">
                                <i class="ri-camera-fill" style="font-size: 14px;"></i>
                            </label>
                            <input type="file" id="profile_image" name="image" class="d-none" accept="image/jpeg,image/png,image/gif" onchange="document.getElementById('profile-photo-form').submit();">
                        </form>
                    </div>
                    <h4 class="outfit font-weight-bold mb-1 text-white">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h4>
                    <p class="text-secondary small mb-0 opacity-75">{{ auth()->user()->email }}</p>
                </div>

                <div class="nav flex-column nav-pills nav-premium-vertical gap-2">
                    <button class="nav-link active py-3 px-4 mb-1" data-toggle="pill" data-target="#personal">
                        <i class="ri-user-settings-line me-3"></i> Intelligence Profile
                    </button>
                    <button class="nav-link py-3 px-4 mb-1" data-toggle="pill" data-target="#security">
                        <i class="ri-shield-keyhole-line me-3"></i> Security Protocol
                    </button>
                    <button class="nav-link py-3 px-4 mb-1" data-toggle="pill" data-target="#verification">
                        <i class="ri-id-card-line me-3"></i> Identity Verification
                    </button>
                    <button class="nav-link py-3 px-4 mb-1" data-toggle="pill" data-target="#settings">
                        <i class="ri-settings-3-line me-3"></i> Neural Settings
                    </button>
                    <button class="nav-link py-3 px-4 mb-1" data-toggle="pill" data-target="#payments">
                        <i class="ri-bank-card-line me-3"></i> Payment Relay
                    </button>
                </div>

                <div class="mt-5 pt-5 border-top border-dark text-center">
                    <div class="small text-secondary mb-2">Account Tier</div>
                    <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill outfit font-weight-bold mb-3">
                        {{ auth()->user()->package_plan ?? 'Standard Member' }}
                    </span>

                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-xl-9">
            <div class="tab-content h-100">
                <!-- Personal Info Tab -->
                <div class="tab-pane fade show active h-100" id="personal">
                    <div class="glass-card p-5 h-100 shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(20px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.05);">

                        @if(!\App\Models\UserOnboardingResponse::where('user_id', auth()->id())->exists())
                        <div class="p-4 bg-primary-soft rounded-4 border border-primary border-opacity-25 mb-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h5 class="text-primary font-weight-bold mb-1"><i class="ri-survey-line me-2"></i> Action Required: Investor Profile</h5>
                                <p class="small text-secondary mb-0">You haven't completed your investor questionnaire. Please do so to unlock personalized features.</p>
                            </div>
                            <button class="btn btn-premium px-4 py-2" onclick="$('#questionnairePromptModal').modal('show')">Complete Now</button>
                        </div>
                        @else
                        <div class="p-4 bg-success-soft rounded-4 border border-success border-opacity-25 mb-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <h5 class="text-success font-weight-bold mb-1"><i class="ri-checkbox-circle-fill me-2"></i> Investor Profile Completed</h5>
                                <p class="small text-secondary mb-0">Your neural intelligence profile is fully configured.</p>
                            </div>
                            <button class="btn btn-outline-success px-4 py-2" onclick="$('#questionnairePromptModal').modal('show')">Review / Retake</button>
                        </div>
                        @endif

                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="small text-secondary mb-2">First Name</label>
                                        <input type="text" name="first_name" class="form-control premium-input" value="{{ auth()->user()->first_name }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="small text-secondary mb-2">Last Name</label>
                                        <input type="text" name="last_name" class="form-control premium-input" value="{{ auth()->user()->last_name }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="small text-secondary mb-2">Email Address</label>
                                        <input type="email" class="form-control premium-input" value="{{ auth()->user()->email }}" disabled>
                                        <small class="text-secondary">Email cannot be changed manually.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="small text-secondary mb-2">Phone Number</label>
                                        <input type="text" name="phone" class="form-control premium-input" value="{{ auth()->user()->phone }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="small text-secondary mb-2">Country</label>
                                        <input type="text" name="country" class="form-control premium-input" value="{{ auth()->user()->country }}">
                                    </div>
                                </div>
                                <div class="col-12 mt-4 text-end">
                                    <button type="submit" class="btn btn-premium px-5 py-3">Update Profile</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Security Center Tab -->
                <div class="tab-pane fade h-100" id="security">
                    <div class="glass-card p-5 h-100 shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(20px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.05);">
                        <h3 class="outfit font-weight-bold mb-5 text-white">Security Command Protocols</h3>
                        
                        <div class="row g-5">
                            <div class="col-md-6">
                                <h6 class="outfit font-weight-bold mb-4 text-primary"><i class="ri-lock-password-line me-2"></i> Change Password</h6>
                                <form action="{{ route('profile.change-password') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <input type="password" name="old_password" class="form-control premium-input" placeholder="Current Password">
                                    </div>
                                    <div class="mb-3">
                                        <input type="password" name="new_password" class="form-control premium-input" placeholder="New Password">
                                    </div>
                                    <div class="mb-4">
                                        <input type="password" name="confirm_password" class="form-control premium-input" placeholder="Confirm New Password">
                                    </div>
                                    <button type="submit" class="btn btn-premium w-100 py-3 shadow-glow">Update Password</button>
                                </form>
                            </div>

                            <div class="col-md-6">
                                <h6 class="outfit font-weight-bold mb-4 text-success"><i class="ri-shield-keyhole-line me-2"></i> Two-Factor Auth</h6>
                                <div class="p-4 bg-black-soft rounded-4 border border-dark">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <div class="font-weight-bold">Google Authenticator</div>
                                            <div class="small text-secondary">Added security for transactions</div>
                                        </div>
                                        <div class="form-check form-switch cursor-pointer">
                                            <input class="form-check-input cursor-pointer" type="checkbox" id="2fa-toggle-trigger" {{ auth()->user()->is_2fa_enabled ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <hr class="border-secondary opacity-10">
                                    <div class="d-flex justify-content-between align-items-center mb-0 text-secondary small">
                                        <span>Status:</span>
                                        <span class="text-{{ auth()->user()->is_2fa_enabled ? 'success' : 'danger' }} fw-bold">{{ auth()->user()->is_2fa_enabled ? 'Enabled' : 'Disabled' }}</span>
                                    </div>
                                </div>
                                <div class="mt-4 p-3 glass-card bg-info-soft border-info-soft d-flex gap-3">
                                    <i class="ri-information-line text-info h4"></i>
                                    <p class="small text-secondary mb-0">Enabling 2FA is highly recommended for withdraw protection.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade h-100" id="verification">
                    <div class="glass-card p-5 h-100 text-center d-flex flex-column justify-content-center align-items-center shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(20px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.05);">
                        <div class="mb-4">
                            <img src="{{ site()->logo ? asset('assets/img/favicon.svg') : asset('assets/images/logo.svg') }}" alt="Logo" style="height: 40px; filter: drop-shadow(0 0 10px rgba(59, 130, 246, 0.5));">
                        </div>
                        <div class="badge-status mb-4 position-relative">
                            <i class="ri-verified-badge-line text-primary" style="font-size: 5rem;"></i>
                            @if(auth()->user()->kyc_status == 'approved')
                            <i class="ri-checkbox-circle-fill text-success position-absolute bottom-0 end-0" style="font-size: 2rem;"></i>
                            @endif
                        </div>
                        <h2 class="outfit font-weight-bold mb-2">Identity Verification</h2>
                        <p class="text-secondary mb-5" style="max-width: 450px;">Verify your identity to unlock higher limits and institutional-grade trading features.</p>

                        <div class="row g-4 w-100" style="max-width: 700px;">
                            <div class="col-md-4">
                                <div class="glass-card p-3 py-4 border-{{ auth()->user()->kyc_status == 'approved' ? 'success' : 'info' }}-soft">
                                    <small class="text-secondary text-uppercase d-block mb-1">Status</small>
                                    <div class="font-weight-bold text-{{ auth()->user()->kyc_status == 'approved' ? 'success' : (auth()->user()->kyc_status == 'pending' ? 'warning' : 'info') }}">
                                        {{ auth()->user()->kyc_status == 'pending' ? 'VERIFICATION IN PROCESS' : (auth()->user()->kyc_status == 'approved' ? 'VERIFIED' : 'NOT STARTED') }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="glass-card p-3 py-4">
                                    <small class="text-secondary text-uppercase d-block mb-1">Limit</small>
                                    <div class="font-weight-bold text-white">$10,000 / day</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="glass-card p-3 py-4">
                                    <small class="text-secondary text-uppercase d-block mb-1">Method</small>
                                    <div class="font-weight-bold text-white">Gov. ID</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            @if(auth()->user()->kyc_status == 'pending')
                            <div class="p-4 bg-warning-soft rounded-4 border border-warning border-opacity-25 mb-4">
                                <h5 class="text-warning font-weight-bold mb-2"><i class="ri-time-line me-2"></i> Document Review in Progress</h5>
                                <p class="small text-secondary mb-0">Our compliance team is currently validating your identity documents. This process typically takes 12-24 hours. You will be notified via email once approved.</p>
                            </div>
                            <button class="btn btn-secondary px-5 py-3 disabled opacity-50" style="cursor: not-allowed;"><i class="ri-lock-2-line me-2"></i> Verification Locked</button>
                            @elseif(auth()->user()->kyc_status != 'approved')
                            <button class="btn btn-premium px-5 py-3 shadow-glow" onclick="$('#id-modal').modal('show')">Verify Now</button>
                            @else
                            <button class="btn btn-outline-success px-5 py-3 disabled"><i class="ri-checkbox-circle-line me-2"></i> Account Fully Verified</button>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Global Settings Tab -->
                <div class="tab-pane fade h-100" id="settings">
                    <div class="glass-card p-5 h-100 shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(20px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.05);">
                        <h3 class="outfit font-weight-bold mb-5 text-white">System Configuration</h3>
                        <div class="row g-5">
                            <div class="col-md-6">
                                <h6 class="outfit font-weight-bold mb-4 text-primary"><i class="ri-money-dollar-circle-line me-2"></i> Display Currency</h6>
                                <div class="p-4 bg-black-soft rounded-4 border border-dark">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="font-weight-bold">Base Currency</div>
                                            <div class="small text-secondary">Select your preferred display currency</div>
                                        </div>
                                        <span class="badge bg-primary-soft text-primary px-3 py-2">{{ auth()->user()->currency ?? 'USD' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="outfit font-weight-bold mb-4 text-info"><i class="ri-notification-3-line me-2"></i> Notifications</h6>
                                <div class="p-4 bg-black-soft rounded-4 border border-dark">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <div class="font-weight-bold">Email Notifications</div>
                                            <div class="small text-secondary">Receive trade alerts via email</div>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" checked>
                                        </div>
                                    </div>
                                    <hr class="border-secondary opacity-10">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="font-weight-bold">Trade Confirmations</div>
                                            <div class="small text-secondary">Get notified on trade execution</div>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" checked>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Relay Tab -->
                <div class="tab-pane fade h-100" id="payments">
                    <div class="glass-card p-5 h-100 shadow-lg" style="background: rgba(16, 18, 27, 0.4); backdrop-filter: blur(20px); border-radius: 32px; border: 1px solid rgba(255,255,255,0.05); overflow-y: auto;">
                        <h3 class="outfit font-weight-bold mb-5 text-white">Payment Destinations</h3>
                        
                        <div class="row g-4">
                            <div class="col-lg-6">
                                <h6 class="outfit font-weight-bold mb-4 text-primary"><i class="ri-bit-coin-line me-2"></i> Crypto Assets</h6>
                                <form id="crypto-payment-form">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="small text-secondary mb-2">BTC Address</label>
                                        <input type="text" name="btc" class="form-control premium-input" value="{{ auth()->user()->btc }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="small text-secondary mb-2">ETH Address</label>
                                        <input type="text" name="eth" class="form-control premium-input" value="{{ auth()->user()->eth }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="small text-secondary mb-2">USDT (TRC20)</label>
                                        <input type="text" name="usdt" class="form-control premium-input" value="{{ auth()->user()->usdt }}">
                                    </div>
                                    <button type="submit" class="btn btn-premium w-100 py-3 shadow-glow">Update Security Wallets</button>
                                </form>
                            </div>

                            <div class="col-lg-6">
                                <h6 class="outfit font-weight-bold mb-4 text-success"><i class="ri-bank-line me-2"></i> Bank Relay</h6>
                                <form id="bank-payment-form">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="small text-secondary mb-2">Bank Name</label>
                                        <input type="text" name="bank" class="form-control premium-input" value="{{ auth()->user()->bank }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="small text-secondary mb-2">Account Number</label>
                                        <input type="text" name="account_number" class="form-control premium-input" value="{{ auth()->user()->account_number }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="small text-secondary mb-2">SWIFT Code</label>
                                        <input type="text" name="bank_swift_code" class="form-control premium-input" value="{{ auth()->user()->bank_swift_code }}">
                                    </div>
                                    <button type="submit" class="btn btn-premium w-100 py-3 shadow-glow">Update Bank Parameters</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    .nav-premium-vertical .nav-link { text-align: left; padding: 12px 20px; border-radius: 12px; color: var(--text-secondary); transition: 0.2s; border: 1px solid transparent; }
    .nav-premium-vertical .nav-link.active { background: transparent; color: var(--accent-primary); border-color: rgba(59, 130, 246, 0.2); font-weight: 600; }
    .nav-premium-vertical .nav-link:hover:not(.active) { background: rgba(255, 255, 255, 0.05); color: white; }
    
    .bg-black-soft { background: rgba(0,0,0,0.3); }
    .bg-primary-soft { background: rgba(59, 130, 246, 0.1); }
    .bg-info-soft { background: rgba(6, 182, 212, 0.05); }
    .border-info-soft { border: 1px solid rgba(6, 182, 212, 0.2); }
</style>


<!-- Identity Verification Modal -->
<div class="modal fade" id="id-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="outfit font-weight-bold mb-0">Upload Government ID</h4>
                <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('profile.update-id') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <div class="col-12">
                        <label class="small text-secondary mb-2">Front Side of ID</label>
                        <label class="upload-area p-4 border-dashed rounded-4 text-center d-block cursor-pointer" for="id-front" style="cursor: pointer;">
                            <i class="ri-image-add-line text-primary mb-2" style="font-size: 2rem;"></i>
                            <p class="small text-secondary mb-0" id="front-label">Click to upload ID front</p>
                            <input type="file" name="file" id="id-front" style="visibility: hidden; position: absolute; width:1px;" accept="image/*" required onchange="$('#front-label').text(this.files[0].name)">
                        </label>
                    </div>
                    <div class="col-12">
                        <label class="small text-secondary mb-2">Back Side of ID</label>
                        <label class="upload-area p-4 border-dashed rounded-4 text-center d-block cursor-pointer" for="id-back" style="cursor: pointer;">
                            <i class="ri-image-add-line text-primary mb-2" style="font-size: 2rem;"></i>
                            <p class="small text-secondary mb-0" id="back-label">Click to upload ID back</p>
                            <input type="file" name="file_back" id="id-back" style="visibility: hidden; position: absolute; width:1px;" accept="image/*" required onchange="$('#back-label').text(this.files[0].name)">
                        </label>
                    </div>
                    <div class="col-12 mt-4">
                        <div class="alert bg-primary-soft text-primary small border-0 mb-4">
                            <i class="ri-information-line me-1"></i> IDs are processed by our secure compliance system. Verification takes 12-24 hours.
                        </div>
                        <button type="submit" class="btn btn-premium w-100 py-3 font-weight-bold" id="submit-identity">
                            <span class="normal-text">Submit for Verification</span>
                            <span class="loading-text" style="display: none;"><i class="ri-loader-4-line ri-spin me-2"></i> Processing Upload...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .upload-area { cursor: pointer; transition: 0.3s; background: rgba(0,0,0,0.2); }
    .upload-area:hover { border-color: var(--accent-primary); background: rgba(59, 130, 246, 0.05); }
    .border-dashed { border: 2px dashed rgba(255,255,255,0.1); }
    .cursor-pointer { cursor: pointer !important; }
</style>

<!-- 2FA Verification Modal (Enable) -->
<div class="modal fade" id="2fa-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card p-4">
            <div class="text-center mb-4">
                <div class="icon-box bg-primary-soft mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 20px;">
                    <i class="ri-shield-keyhole-line text-primary h3 mb-0"></i>
                </div>
                <h4 class="outfit font-weight-bold mb-1">Verify Authenticator</h4>
                <p class="small text-secondary">Scan the QR code with your Google Authenticator app and enter the 6-digit code to enable 2FA.</p>
            </div>
            
            <div class="text-center mb-4 p-3 bg-white rounded-4 shadow-sm d-inline-block mx-auto">
                {!! (new \BaconQrCode\Writer(new \BaconQrCode\Renderer\ImageRenderer(new \BaconQrCode\Renderer\RendererStyle\RendererStyle(180), new \BaconQrCode\Renderer\Image\SvgImageBackEnd())))->writeString(app('pragmarx.google2fa')->getQRCodeUrl(config('app.name'), auth()->user()->email, $secret)) !!}
            </div>

            <div class="text-center mb-4">
                <div class="small text-secondary mb-2">Setup Key:</div>
                <code class="p-2 bg-black-soft rounded text-primary font-weight-bold" style="letter-spacing: 1px;">{{ $secret }}</code>
            </div>
            
            <form id="2fa-verify-form">
                @csrf
                <div class="form-group mb-4 text-center">
                    <label class="small text-secondary mb-2">Authenticator Code</label>
                    <input type="text" name="code" id="2fa-code-input" class="form-control premium-input text-center h2 font-weight-bold" placeholder="000 000" maxlength="6" autofocus>
                </div>
                <button type="submit" class="btn btn-premium w-100 py-3 font-weight-bold" id="verify-2fa-btn">
                    <span class="normal-text">Enable 2FA Protection</span>
                    <span class="loading-text" style="display: none;"><i class="ri-loader-4-line ri-spin me-2"></i> Verifying...</span>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- 2FA Disable Modal -->
<div class="modal fade" id="2fa-disable-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card p-4">
            <div class="text-center mb-4">
                <div class="icon-box bg-danger-soft mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 20px;">
                    <i class="ri-shield-flash-line text-danger h3 mb-0"></i>
                </div>
                <h4 class="outfit font-weight-bold mb-1">Disable 2FA Protection</h4>
                <p class="small text-secondary">To disable two-factor authentication, please enter the 6-digit code from your authenticator app.</p>
            </div>
            
            <form id="2fa-disable-form">
                @csrf
                <div class="form-group mb-4 text-center">
                    <label class="small text-secondary mb-2">Authenticator Code</label>
                    <input type="text" name="code" id="2fa-disable-code-input" class="form-control premium-input text-center h2 font-weight-bold" placeholder="000 000" maxlength="6" autofocus>
                </div>
                <button type="submit" class="btn btn-danger w-100 py-3 font-weight-bold" id="disable-2fa-btn">
                    <span class="normal-text">Disable 2FA Security</span>
                    <span class="loading-text" style="display: none;"><i class="ri-loader-4-line ri-spin me-2"></i> Verifying...</span>
                </button>
            </form>
        </div>
    </div>
</div>

</style>

<script>
    document.getElementById('id-modal').querySelector('form').onsubmit = function() {
        const btn = document.getElementById('submit-identity');
        btn.disabled = true;
        btn.querySelector('.normal-text').style.display = 'none';
        btn.querySelector('.loading-text').style.display = 'inline-block';
    };
</script>

@push('js')
<script>
    // Staggered Entrance Animation
    window.addEventListener('load', () => {
        if (typeof anime !== 'undefined') {
            anime({
                targets: '.col-xl-3 .glass-card',
                translateX: [-30, 0],
                opacity: [0, 1],
                duration: 1000,
                easing: 'easeOutQuint'
            });
            
            anime({
                targets: '.tab-pane.active .glass-card',
                translateX: [30, 0],
                opacity: [0, 1],
                delay: 200,
                duration: 1000,
                easing: 'easeOutQuint'
            });
            
            anime({
                targets: '.nav-premium-vertical .nav-link',
                translateX: [-20, 0],
                opacity: [0, 1],
                delay: anime.stagger(100, {start: 400}),
                easing: 'easeOutQuint',
                duration: 800
            });
        }
    });

    // Payment Form Handler
    function submitPayment(url, formData) {
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.status) {
                    toastr.success(response.status);
                }
            },
            error: function() {
                toastr.error('Failed to update payment settings');
            }
        });
    }

    $('#crypto-payment-form').on('submit', function(e) {
        e.preventDefault();
        submitPayment("{{ route('update_payment') }}", new FormData(this));
    });

    $('#bank-payment-form').on('submit', function(e) {
        e.preventDefault();
        submitPayment("{{ route('update_payment_bank') }}", new FormData(this));
    });

    // 2FA Toggle and Modal Logic
    $('#2fa-toggle-trigger').on('change', function() {
        const isChecked = $(this).is(':checked');
        const isEnabled = {{ auth()->user()->is_2fa_enabled ? 'true' : 'false' }};

        if (isChecked && !isEnabled) {
            $(this).prop('checked', false);
            $('#2fa-modal').modal('show');
        } else if (!isChecked && isEnabled) {
            $(this).prop('checked', true);
            $('#2fa-disable-modal').modal('show');
        }
    });

    $('#2fa-verify-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#verify-2fa-btn');
        const code = $('#2fa-code-input').val();
        
        if (code.length < 6) return toastr.error('Enter valid 6-digit code');

        btn.prop('disabled', true).find('.normal-text').hide();
        btn.find('.loading-text').show();

        $.ajax({
            url: "{{ route('verify2fa') }}",
            method: 'POST',
            data: {
                code: code,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.status) {
                    toastr.success(response.status);
                    $('#2fa-modal').modal('hide');
                    location.reload();
                } else {
                    toastr.error(response.error || 'Invalid code');
                }
            },
            error: function() {
                toastr.error('Connection timed out. Check your internet.');
            },
            complete: function() {
                btn.prop('disabled', false).find('.normal-text').show();
                btn.find('.loading-text').hide();
            }
        });
    });

    $('#2fa-disable-form').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#disable-2fa-btn');
        const code = $('#2fa-disable-code-input').val();
        
        if (code.length < 6) return toastr.error('Enter valid 6-digit code');

        btn.prop('disabled', true).find('.normal-text').hide();
        btn.find('.loading-text').show();

        $.ajax({
            url: "{{ route('disable2fa') }}",
            method: 'POST',
            data: {
                code: code,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.status) {
                    toastr.success(response.status);
                    $('#2fa-disable-modal').modal('hide');
                    location.reload();
                } else {
                    toastr.error(response.error || 'Invalid code');
                }
            },
            error: function() {
                toastr.error('Connection timed out. Check your internet.');
            },
            complete: function() {
                btn.prop('disabled', false).find('.normal-text').show();
                btn.find('.loading-text').hide();
            }
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
@endpush
@endsection

