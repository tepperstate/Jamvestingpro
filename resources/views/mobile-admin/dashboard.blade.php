@extends('mobile-admin.layouts.app')

@section('header')
<div style="font-size: 18px; font-weight: 700;">JamVesting Admin</div>
<div style="position: relative;">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
    <div style="position: absolute; top: 0; right: 2px; width: 8px; height: 8px; background: var(--danger-color); border-radius: 50%;"></div>
</div>
@endsection

@section('content')
<div class="ma-card" style="display: flex; align-items: center; gap: 16px;">
    <div style="width: 48px; height: 48px; background: var(--accent-color); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700;">
        A
    </div>
    <div>
        <div style="font-weight: 600; font-size: 18px;">Good morning, Admin</div>
        <div class="text-sm">Super Admin • Online</div>
    </div>
</div>

<div class="stat-grid">
    <x-mobile.card-stat title="Total Users" value="{{ number_format($total_users) }}" trend="Platform users" :trendUp="true" icon="<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2'/><circle cx='9' cy='7' r='4'/><path d='M22 21v-2a4 4 0 0 0-3-3.87'/><path d='M16 3.13a4 4 0 0 1 0 7.75'/></svg>" />
    <x-mobile.card-stat title="Total Capital" value="${{ number_format($total_capital, 2) }}" trend="Platform liquidity" :trendUp="true" icon="<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><line x1='12' y1='1' x2='12' y2='23'/><path d='M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'/></svg>" />
    <x-mobile.card-stat title="Platform Admins" value="{{ $admin_count }}" trend="Admin Privity" :trendUp="true" tint="success" icon="<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='11' width='18' height='11' rx='2' ry='2'/><path d='M7 11V7a5 5 0 0 1 10 0v4'/></svg>" />
    <x-mobile.card-stat title="System Status" value="Online" trend="All systems go" :trendUp="true" tint="success" icon="<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M22 11.08V12a10 10 0 1 1-5.93-9.14'/><polyline points='22 4 12 14.01 9 11.01'/></svg>" />
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px; margin-bottom: 8px;">
    <h2 style="font-size: 16px;">Recent User Registrations</h2>
    <a href="{{ route('admin.mobile.users') }}" style="color: var(--accent-color); font-size: 14px; text-decoration: none;">View All</a>
</div>

@forelse($recent_users as $user)
    <x-mobile.card-list 
        title="{{ $user->first_name }} {{ $user->last_name }}" 
        subtitle="{{ $user->email }}" 
        status="{{ $user->created_at ? $user->created_at->diffForHumans() : 'Recent' }}" 
        icon="<svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2'/><circle cx='12' cy='7' r='4'/></svg>" 
    />
@empty
    <div style="text-align: center; color: var(--text-secondary); padding: 20px;">No recent users</div>
@endforelse

@endsection
