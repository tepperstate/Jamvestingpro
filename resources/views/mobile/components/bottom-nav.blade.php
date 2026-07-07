<div class="mobile-footer">
    <a href="{{ route('dashboard.index') }}" class="nav-item {{ Request::routeIs('dashboard.index') ? 'active' : '' }}">
        <i class="ri-home-5-{{ Request::routeIs('dashboard.index') ? 'fill' : 'line' }}"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('stocks.trade') }}" class="nav-item {{ Request::routeIs('stocks.trade') || Request::routeIs('stocks.trade-single') ? 'active' : '' }}">
        <i class="ri-bar-chart-box-{{ Request::routeIs('stocks.trade') || Request::routeIs('stocks.trade-single') ? 'fill' : 'line' }}"></i>
        <span>Market</span>
    </a>
    <a href="{{ route('dashboard.trade-home') }}" class="nav-item {{ Request::routeIs('dashboard.trade-home') ? 'active' : '' }}">
        <div class="trade-button">
            <i class="ri-exchange-funds-line"></i>
        </div>
        <span>Trade</span>
    </a>
    <a href="{{ route('dashboard.wallets') }}" class="nav-item {{ Request::routeIs('dashboard.wallets') ? 'active' : '' }}">
        <i class="ri-wallet-3-{{ Request::routeIs('dashboard.wallets') ? 'fill' : 'line' }}"></i>
        <span>Wallet</span>
    </a>
    <a href="{{ route('profile') }}" class="nav-item {{ Request::routeIs('profile') ? 'active' : '' }}">
        <i class="ri-user-settings-{{ Request::routeIs('profile') ? 'fill' : 'line' }}"></i>
        <span>Settings</span>
    </a>
</div>

<style>
/* Mobile Footer Styles */
.mobile-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 75px;
    background: rgba(10, 11, 14, 0.85);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 0 10px;
    z-index: 9999;
    padding-bottom: env(safe-area-inset-bottom);
    box-shadow: 0 -10px 30px rgba(0,0,0,0.5);
    font-family: 'Outfit', sans-serif;
}

.mobile-footer .nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.4);
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    width: 20%;
    gap: 4px;
}

.mobile-footer .nav-item.active {
    color: #FFD700;
    transform: translateY(-2px);
}

.mobile-footer .nav-item i {
    font-size: 22px;
    transition: 0.3s;
}

.mobile-footer .nav-item.active i {
    font-size: 24px;
    filter: drop-shadow(0 2px 8px rgba(255, 215, 0, 0.4));
}

.mobile-footer .nav-item span {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.mobile-footer .trade-button {
    background: linear-gradient(135deg, #FFD700 0%, #990000 100%);
    width: 54px;
    height: 54px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(255, 215, 0, 0.3);
    margin-top: -24px;
    border: 5px solid #0a0b0e;
    color: #000;
    transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.mobile-footer .nav-item:active .trade-button {
    transform: scale(0.9);
    box-shadow: 0 4px 10px rgba(255, 215, 0, 0.2);
}

.mobile-footer .trade-button i {
    font-size: 26px;
    margin: 0;
}

body {
    padding-bottom: 90px; /* Space for the footer */
}

@media (min-width: 992px) {
    .mobile-footer { display: none !important; }
    body { padding-bottom: 0 !important; }
}
</style>
