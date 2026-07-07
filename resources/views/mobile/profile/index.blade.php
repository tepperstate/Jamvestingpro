@extends('layouts.user.app')
@section('title', 'Account Settings')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
<style>
    .mobile-glass-container { padding: 10px; padding-bottom: 80px; background: #0a0b0e; min-height: 100vh; font-family: 'Outfit', sans-serif; color: white; }
    .glass-card { background: rgba(255,255,255,0.02); backdrop-filter: blur(10px); border: 1px solid rgba(255,215,0,0.15); border-radius: 16px; padding: 15px; margin-bottom: 15px; }
    .gold-text { color: #FFD700; }
    .premium-input { background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 10px; padding: 10px; width: 100%; font-size: 14px; margin-bottom: 10px; }
    .premium-input:focus { border-color: #FFD700; outline: none; }
    .btn-gold { background: linear-gradient(135deg, #FFD700, #990000); color: #000; border: none; padding: 12px; border-radius: 10px; font-weight: 800; width: 100%; }
    
    .nav-pills.mobile-pills { display: flex; overflow-x: auto; flex-wrap: nowrap; gap: 10px; margin-bottom: 15px; padding-bottom: 5px; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .nav-pills.mobile-pills::-webkit-scrollbar { display: none; }
    .mobile-pills .nav-link { background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.5); border-radius: 8px; font-size: 12px; font-weight: 600; padding: 8px 12px; white-space: nowrap; border: 1px solid transparent; }
    .mobile-pills .nav-link.active { background: rgba(255,215,0,0.1); color: #FFD700; border-color: #FFD700; }
</style>

<div class="mobile-glass-container">
    <div class="text-center mb-4 mt-2">
        <div class="position-relative d-inline-block mb-2">
            <img src="{{ auth()->user()->image ? asset('storage/image/'.auth()->user()->image) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->first_name).'&background=020617&color=FFD700&size=128' }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #FFD700;">
            <form action="{{ route('profile.edit') }}" method="POST" enctype="multipart/form-data" id="profile-photo-form" style="position: absolute; bottom: 0; right: -5px;">
                @csrf
                <label for="profile_image" style="cursor: pointer; background: #FFD700; color: #000; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                    <i class="ri-camera-fill"></i>
                </label>
                <input type="file" id="profile_image" name="image" class="d-none" accept="image/jpeg,image/png,image/gif" onchange="document.getElementById('profile-photo-form').submit();">
            </form>
        </div>
        <h5 class="mb-0 font-weight-bold">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h5>
        <div style="font-size: 11px; color: rgba(255,255,255,0.5);">{{ auth()->user()->email }}</div>
        <div class="mt-2"><span class="badge" style="background: rgba(255,215,0,0.2); color: #FFD700; border: 1px solid rgba(255,215,0,0.4);">{{ auth()->user()->package_plan ?? 'Standard Member' }}</span></div>
    </div>

    <!-- Mobile Tabs -->
    <ul class="nav nav-pills mobile-pills" id="settings-tab">
        <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#personal"><i class="ri-user-line"></i> Profile</a></li>
        <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#security"><i class="ri-lock-line"></i> Security</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('verification') }}"><i class="ri-shield-check-line"></i> KYC</a></li>
        <li class="nav-item"><a class="nav-link text-danger" href="{{ route('logout') }}"><i class="ri-logout-box-r-line"></i> Logout</a></li>
    </ul>

    <div class="tab-content">
        <!-- Personal Info -->
        <div class="tab-pane fade show active" id="personal">
            <div class="glass-card">
                <h6 class="gold-text mb-3">Personal Information</h6>
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <label style="font-size: 10px; color: rgba(255,255,255,0.5);">First Name</label>
                    <input type="text" name="first_name" class="premium-input" value="{{ auth()->user()->first_name }}">
                    
                    <label style="font-size: 10px; color: rgba(255,255,255,0.5);">Last Name</label>
                    <input type="text" name="last_name" class="premium-input" value="{{ auth()->user()->last_name }}">
                    
                    <label style="font-size: 10px; color: rgba(255,255,255,0.5);">Email Address (Read-only)</label>
                    <input type="email" class="premium-input" value="{{ auth()->user()->email }}" disabled>
                    
                    <label style="font-size: 10px; color: rgba(255,255,255,0.5);">Phone Number</label>
                    <input type="text" name="phone" class="premium-input" value="{{ auth()->user()->phone }}">
                    
                    <label style="font-size: 10px; color: rgba(255,255,255,0.5);">Country</label>
                    <input type="text" name="country" class="premium-input" value="{{ auth()->user()->country }}">
                    
                    <button type="submit" class="btn-gold mt-2">Update Profile</button>
                </form>
            </div>
        </div>

        <!-- Security -->
        <div class="tab-pane fade" id="security">
            <div class="glass-card">
                <h6 class="gold-text mb-3">Change Password</h6>
                <form action="{{ route('profile.change-password') }}" method="POST">
                    @csrf
                    <input type="password" name="old_password" class="premium-input" placeholder="Current Password">
                    <input type="password" name="new_password" class="premium-input" placeholder="New Password">
                    <input type="password" name="confirm_password" class="premium-input" placeholder="Confirm New Password">
                    <button type="submit" class="btn-gold mt-2">Update Password</button>
                </form>
            </div>
            
            <div class="glass-card mt-3">
                <h6 class="gold-text mb-3">Two-Factor Authentication</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <div style="font-size: 12px;">Google Authenticator</div>
                    <div>
                        @if(auth()->user()->otp_enabled == '1')
                            <span class="badge badge-success">Enabled</span>
                        @else
                            <span class="badge badge-danger">Disabled</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('mobile.components.bottom-nav')

@endsection
