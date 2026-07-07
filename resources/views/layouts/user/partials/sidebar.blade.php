<div id="sidebar-overlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header d-flex align-items-center justify-content-between px-4 border-bottom mb-2" style="border-color: rgba(255,255,255,0.05) !important; height: var(--header-height); min-height: var(--header-height);">
        <a href="{{ route('dashboard.index') }}" class="logo-bg-premium sidebar-logo-container" style="display: flex; width: 100%; max-width: 100%; height: 100%; min-height: 50px; overflow: hidden; align-items: center; justify-content: center; background: transparent; border-radius: 10px; padding: 0; transition: 0.3s; position: relative;">
            <x-ui.logo variant="light" size="lg" />
        </a>
        <div class="d-flex align-items-center gap-2">
            <button class="sidebar-mini-toggle d-none d-lg-flex" onclick="toggleSidebarMini()" style="background:transparent; border:none; color:var(--text-secondary); font-size: 1.25rem; padding: 4px; transition: 0.2s;">
                <i class="ri-side-bar-fill toggle-icon-full"></i>
                <i class="ri-menu-unfold-line toggle-icon-mini"></i>
            </button>
            <button class="btn btn-sm btn-outline-light border-0 d-lg-none" onclick="toggleSidebar()" style="padding: 4px; color: var(--text-secondary);">
                <i class="ri-close-line h4 mb-0"></i>
            </button>
        </div>
    </div>

    <div class="sidebar-scroller">
        <div class="nav-section" data-target="menu-main">
            <span><i class="ri-home-5-fill section-icon"></i>Home</span>
            <i class="ri-arrow-down-s-line chevron"></i>
        </div>
        <div class="nav-group show" id="menu-main">
            <a href="{{ route('dashboard.index') }}" class="nav-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <i class="ri-home-5-line"></i> <span class="nav-text">Home</span>
            </a>
            <a href="{{ route('dashboard.portfolio') }}" class="nav-link {{ request()->routeIs('dashboard.portfolio') ? 'active' : '' }}">
                <i class="ri-pie-chart-2-line"></i> <span class="nav-text">Portfolio</span>
            </a>
            <a href="{{ route('trades.history') }}" class="nav-link {{ request()->routeIs('trades.history') ? 'active' : '' }}">
                <i class="ri-history-line"></i> <span class="nav-text">Investing History</span>
            </a>
            <a href="{{ auth()->user()->hasFeature('bot_trading') ? route('bots.history') : route('user.upgrade') }}" class="nav-link {{ request()->routeIs('bots.history') ? 'active' : '' }}">
                <i class="ri-robot-2-line"></i> <span class="nav-text">Bot Trading History</span>
                @unless(auth()->user()->hasFeature('bot_trading'))<span class="nav-lock-badge"><i class="ri-lock-line"></i></span>@endunless
            </a>
            <a href="{{ route('copy-trading.history') }}" class="nav-link {{ request()->routeIs('copy-trading.history') ? 'active' : '' }}" style="white-space: nowrap;">
                <i class="ri-user-received-2-line"></i> <span class="nav-text">Copy Trading</span>
            </a>
            <a href="{{ route('history') }}" class="nav-link {{ request()->routeIs('history') ? 'active' : '' }}">
                <i class="ri-file-list-3-line"></i> <span class="nav-text">Transfer History</span>
            </a>
        </div>
        
        <div class="nav-section" data-target="menu-marketplace">
            <span><i class="ri-line-chart-fill section-icon"></i>Invest</span>
            <i class="ri-arrow-down-s-line chevron"></i>
        </div>
        <div class="nav-group" id="menu-marketplace">
            <a href="{{ route('dashboard.trade-home') }}" class="nav-link {{ request()->routeIs('dashboard.trade-home') || request()->routeIs('dashboard.trade') ? 'active' : '' }}">
                <i class="ri-line-chart-line"></i> <span class="nav-text">Trade Room</span>
            </a>
            <a href="{{ route('stocks.trade') }}" class="nav-link {{ request()->routeIs('stocks.trade') ? 'active' : '' }}">
                <i class="ri-building-line"></i> <span class="nav-text">Stocks & ETFs</span>
            </a>
            <a href="{{ auth()->user()->hasFeature('mutual_funds') ? route('user.mutual_funds') : route('user.upgrade') }}" class="nav-link {{ request()->routeIs('user.mutual_funds') ? 'active' : '' }}">
                <i class="ri-pie-chart-line"></i> <span class="nav-text">Mutual Funds</span>
                @unless(auth()->user()->hasFeature('mutual_funds'))<span class="nav-lock-badge"><i class="ri-lock-line"></i></span>@endunless
            </a>
            <a href="{{ auth()->user()->hasFeature('trading_signals') ? route('signal') : route('user.upgrade') }}" class="nav-link {{ request()->routeIs('signal') ? 'active' : '' }}">
                <i class="ri-signal-tower-line"></i> <span class="nav-text">Purchase Signals</span>
                @unless(auth()->user()->hasFeature('trading_signals'))<span class="nav-lock-badge"><i class="ri-lock-line"></i></span>@endunless
            </a>
            <a href="{{ auth()->user()->hasFeature('trading_signals') ? route('signals.user-history') : route('user.upgrade') }}" class="nav-link {{ request()->routeIs('signals.user-history') ? 'active' : '' }}">
                <i class="ri-history-line"></i> <span class="nav-text">Signal History</span>
                @unless(auth()->user()->hasFeature('trading_signals'))<span class="nav-lock-badge"><i class="ri-lock-line"></i></span>@endunless
            </a>
            <a href="{{ auth()->user()->hasFeature('vip_stocks') ? route('user.vip_stocks') : route('user.upgrade') }}" class="nav-link {{ request()->routeIs('user.vip_stocks') ? 'active' : '' }}">
                <i class="ri-vip-crown-line"></i> <span class="nav-text">Premium Stocks</span>
                @unless(auth()->user()->hasFeature('vip_stocks'))<span class="nav-lock-badge"><i class="ri-lock-line"></i></span>@endunless
            </a>
            <a href="{{ auth()->user()->hasFeature('bot_trading') ? route('bot') : route('user.upgrade') }}" class="nav-link {{ request()->routeIs('bot') ? 'active' : '' }}">
                <i class="ri-robot-2-line"></i> <span class="nav-text">Bot Trading</span>
                @unless(auth()->user()->hasFeature('bot_trading'))<span class="nav-lock-badge"><i class="ri-lock-line"></i></span>@endunless
            </a>
            <a href="{{ auth()->user()->hasFeature('copy_trading') ? route('copy-trading.index') : route('user.upgrade') }}" class="nav-link {{ request()->routeIs('copy-trading.index') ? 'active' : '' }}">
                <i class="ri-user-received-2-line"></i> <span class="nav-text">CopyTrader™</span>
                @unless(auth()->user()->hasFeature('copy_trading'))<span class="nav-lock-badge"><i class="ri-lock-line"></i></span>@endunless
            </a>
            <a href="{{ route('futures.trade') }}" class="nav-link {{ request()->routeIs('futures.trade') ? 'active' : '' }}">
                <i class="ri-stock-line"></i> <span class="nav-text">Futures Trading</span>
            </a>
            <a href="{{ route('margin.trade') }}" class="nav-link {{ request()->routeIs('margin.trade') ? 'active' : '' }}">
                <i class="ri-exchange-funds-fill"></i> <span class="nav-text">Margin Trading</span>
            </a>
            <a href="{{ route('user.p2p') }}" class="nav-link {{ request()->routeIs('web.user.p2p') ? 'active' : '' }}">
                <i class="ri-group-line"></i> <span class="nav-text">P2P Market</span>
            </a>
            <a href="{{ route('user.launchpad') }}" class="nav-link {{ request()->routeIs('web.user.launchpad') ? 'active' : '' }}">
                <i class="ri-rocket-2-line"></i> <span class="nav-text">Launchpad (IEO)</span>
            </a>
        </div>

        <div class="nav-section" data-target="menu-finance">
            <span><i class="ri-wallet-fill section-icon"></i>Spending & Saving</span>
            <i class="ri-arrow-down-s-line chevron"></i>
        </div>
        <div class="nav-group" id="menu-finance">
            <a href="{{ route('dashboard.wallets') }}" class="nav-link {{ request()->routeIs('dashboard.wallets') ? 'active' : '' }}">
                <i class="ri-wallet-3-line"></i> <span class="nav-text">Wallet & Transfers</span>
            </a>
            <a href="{{ route('user.swap') }}" class="nav-link {{ request()->routeIs('user.swap') || request()->routeIs('user.swap.coin') ? 'active' : '' }}">
                <i class="ri-swap-line"></i> <span class="nav-text">Swap / Convert</span>
            </a>
            <a href="{{ route('user.credit_card') }}" class="nav-link {{ request()->routeIs('user.credit_card') ? 'active' : '' }}">
                <i class="ri-bank-card-line"></i> <span class="nav-text">Credit Card</span>
            </a>
            <a href="{{ route('connect') }}" class="nav-link {{ request()->routeIs('connect') ? 'active' : '' }}">
                <i class="ri-link-unlink"></i> <span class="nav-text">Connect Wallet</span>
            </a>
            <a href="{{ route('user.upgrade') }}" class="nav-link {{ request()->routeIs('user.upgrade') ? 'active' : '' }}">
                <i class="ri-medal-line"></i> <span class="nav-text">Account Upgrade</span>
            </a>
            <a href="{{ route('user.spot_trading') }}" class="nav-link {{ request()->routeIs('user.spot_trading') ? 'active' : '' }}">
                <i class="ri-exchange-dollar-line"></i> <span class="nav-text">Spot Trading</span>
            </a>
        </div>

        <div class="nav-section" data-target="menu-wealth">
            <span><i class="ri-safe-2-fill section-icon"></i>Wealth Management</span>
            <i class="ri-arrow-down-s-line chevron"></i>
        </div>
        <div class="nav-group" id="menu-wealth">
            <a href="{{ auth()->user()->hasFeature('high_yield') ? route('investment') : route('user.upgrade') }}" class="nav-link {{ request()->routeIs('investment') ? 'active' : '' }}">
                <i class="ri-funds-box-line"></i> <span class="nav-text">Crypto ETFs</span>
                @unless(auth()->user()->hasFeature('high_yield'))<span class="nav-lock-badge"><i class="ri-lock-line"></i></span>@endunless
            </a>
            <a href="{{ auth()->user()->hasFeature('high_yield') ? route('user.stock_etfs') : route('user.upgrade') }}" class="nav-link {{ request()->routeIs('user.stock_etfs') ? 'active' : '' }}">
                <i class="ri-vip-crown-line"></i> <span class="nav-text">Stock ETFs</span>
                @unless(auth()->user()->hasFeature('high_yield'))<span class="nav-lock-badge"><i class="ri-lock-line"></i></span>@endunless
            </a>
            <a href="{{ route('user.staking') }}" class="nav-link {{ request()->routeIs('user.staking') ? 'active' : '' }}">
                <i class="ri-coin-line"></i> <span class="nav-text">Crypto Staking</span>
            </a>
            <a href="{{ route('user.student_savings') }}" class="nav-link {{ request()->routeIs('user.student_savings') ? 'active' : '' }}">
                <i class="ri-graduation-cap-line"></i> <span class="nav-text">Family & Kids Accounts</span>
            </a>
            <a href="{{ route('user.retirement') }}" class="nav-link {{ request()->routeIs('user.retirement') ? 'active' : '' }}">
                <i class="ri-shield-star-line"></i> <span class="nav-text">Retirement</span>
            </a>
            <a href="{{ route('user.liquidity') }}" class="nav-link {{ request()->routeIs('web.user.liquidity') ? 'active' : '' }}">
                <i class="ri-water-flash-line"></i> <span class="nav-text">Liquidity Farming</span>
            </a>
            <a href="{{ route('user.launchpad') }}" class="nav-link {{ request()->routeIs('web.user.launchpad') ? 'active' : '' }}">
                <i class="ri-rocket-line"></i> <span class="nav-text">Launchpad</span>
            </a>
            <a href="{{ route('user.loans') }}" class="nav-link {{ request()->routeIs('web.user.loans') ? 'active' : '' }}">
                <i class="ri-hand-coin-line"></i> <span class="nav-text">Crypto Loans</span>
            </a>
            <a href="{{ route('user.dual_investment') }}" class="nav-link {{ request()->routeIs('web.user.dual_investment') ? 'active' : '' }}">
                <i class="ri-scales-3-line"></i> <span class="nav-text">Dual Investment</span>
            </a>
            <a href="{{ route('user.dca') }}" class="nav-link {{ request()->routeIs('web.user.dca') ? 'active' : '' }}">
                <i class="ri-calendar-event-line"></i> <span class="nav-text">Auto-Invest (DCA)</span>
            </a>
        </div>

        <div class="nav-section" data-target="menu-account">
            <span><i class="ri-compass-3-fill section-icon"></i>Community</span>
            <i class="ri-arrow-down-s-line chevron"></i>
        </div>
        <div class="nav-group show" id="menu-account">
            <a href="{{ route('user.rewards') }}" class="nav-link {{ request()->routeIs('user.rewards') ? 'active' : '' }}">
                <i class="ri-coupon-3-line"></i> <span class="nav-text">Rewards</span>
            </a>
            <a href="{{ route('user.support_tickets') }}" class="nav-link {{ request()->routeIs('user.support_tickets') ? 'active' : '' }}">
                <i class="ri-customer-service-2-line"></i> <span class="nav-text">Help & Support</span>
            </a>
        </div>
        
        @if(auth()->user() && auth()->user()->hasHipProAccess())
        <div class="nav-section" data-target="menu-institutional">
            <span><i class="ri-bank-fill section-icon" style="color: gold;"></i>Institutional</span>
            <i class="ri-arrow-down-s-line chevron"></i>
        </div>
        <div class="nav-group show" id="menu-institutional">
            <a href="{{ route('hip.pro.dashboard') }}" class="nav-link nav-link-hip-pro mt-2 flex items-center px-4 py-2 text-sm font-medium rounded-md shadow-lg transition {{ request()->routeIs('hip.pro.dashboard') ? 'active' : '' }}">
                <i class="ri-vip-diamond-line mr-3" style="color: white; margin-right: 10px;"></i>
                <span class="nav-text" style="color: white;">HIP Pro Access</span>
            </a>
        </div>
        @endif

        <div class="nav-section" data-target="menu-system">
            <span><i class="ri-settings-4-fill section-icon"></i>Settings</span>
            <i class="ri-arrow-down-s-line chevron"></i>
        </div>
        <div class="nav-group show" id="menu-system">
            <a href="{{ route('profile') }}" class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}">
                <i class="ri-user-settings-line"></i> <span class="nav-text">Account Settings</span>
            </a>
            <a href="{{ route('verification') }}" class="nav-link {{ request()->routeIs('verification') ? 'active' : '' }}">
                <i class="ri-shield-check-line"></i> <span class="nav-text">Account Verification (KYC)</span>
            </a>
            <a href="{{ route('logout') }}" class="nav-link text-danger mt-2">
                <i class="ri-logout-box-r-line"></i> <span class="nav-text">Sign Out</span>
            </a>
        </div>


        <!-- Sidebar Promo Card -->
        <div class="sidebar-promo-card mx-3 my-4">
            <div class="sidebar-promo-tag">GOLD</div>
            <img src="{{ asset('asset/login_v2.png') }}" class="sidebar-promo-image" alt="Gold Member">
            <div class="sidebar-promo-title">Unlock Gold</div>
            <div class="sidebar-promo-desc">Get higher yields, bigger instant deposits, and premium research.</div>
            <a href="{{ route('user.upgrade') }}" class="btn btn-sm btn-primary w-100 mt-3 py-2" style="border-radius: 12px; font-weight: 700;">Try Gold Free</a>
        </div>
    </div>

    <div class="mt-auto px-4 py-3 border-top" style="border-color: rgba(255,255,255,0.03) !important;">
        <div class="d-flex align-items-center opacity-50">
            <div class="status-dot pulse-green me-2"></div>
            <span class="small font-weight-bold letter-spacing-1">SECURE CONNECTED</span>
        </div>
    </div>
</aside>

