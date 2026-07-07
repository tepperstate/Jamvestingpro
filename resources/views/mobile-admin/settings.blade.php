@extends('mobile-admin.layouts.app')

@section('header')
<div style="font-size: 18px; font-weight: 700;">Settings</div>
@endsection

@section('content')

<div class="ma-card" style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
    <div style="width: 64px; height: 64px; background: var(--accent-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700;">
        A
    </div>
    <div>
        <div style="font-weight: 600; font-size: 20px;">Admin User</div>
        <div class="text-sm">admin@jamvesting.com</div>
        <div style="margin-top: 4px;"><span style="background: var(--success-tint); color: var(--success-color); padding: 2px 8px; border-radius: 12px; font-size: 12px;">Super Admin</span></div>
    </div>
</div>

<x-mobile.card-detail title="Security">
    <div class="flex justify-between items-center" style="padding: 12px 0; border-bottom: 1px solid var(--card-border);">
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-secondary);"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <span>Change Password</span>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-secondary);"><path d="m9 18 6-6-6-6"/></svg>
    </div>
    <div class="flex justify-between items-center" style="padding: 12px 0;">
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-secondary);"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <span>Two-Factor Auth (2FA)</span>
        </div>
        <div style="background: var(--success-color); width: 40px; height: 24px; border-radius: 12px; position: relative;">
            <div style="background: white; width: 20px; height: 20px; border-radius: 50%; position: absolute; right: 2px; top: 2px;"></div>
        </div>
    </div>
</x-mobile.card-detail>

<x-mobile.card-detail title="Preferences">
    <div class="flex justify-between items-center" style="padding: 12px 0; border-bottom: 1px solid var(--card-border);">
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-secondary);"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
            <span>Dark Theme</span>
        </div>
        <div style="background: var(--success-color); width: 40px; height: 24px; border-radius: 12px; position: relative;">
            <div style="background: white; width: 20px; height: 20px; border-radius: 50%; position: absolute; right: 2px; top: 2px;"></div>
        </div>
    </div>
    <div class="flex justify-between items-center" style="padding: 12px 0;">
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-secondary);"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
            <span>Push Notifications</span>
        </div>
        <div style="background: var(--card-border); width: 40px; height: 24px; border-radius: 12px; position: relative;">
            <div style="background: var(--text-secondary); width: 20px; height: 20px; border-radius: 50%; position: absolute; left: 2px; top: 2px;"></div>
        </div>
    </div>
</x-mobile.card-detail>

<div style="margin-top: 32px;">
    <form method="POST" action="#">
        @csrf
        <button type="submit" class="btn" style="background: var(--danger-tint); color: var(--danger-color);">Sign Out</button>
    </form>
</div>
<div style="text-align: center; margin-top: 16px; color: var(--text-secondary); font-size: 12px;">
    JamVesting Pro Admin v2.0.1<br>
    Running on native mobile web wrapper
</div>

@endsection
