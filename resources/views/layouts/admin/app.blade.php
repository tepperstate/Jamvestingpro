<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="icon" href="{{ site()->favicon ? asset('storage/image/'.site()->favicon) : asset('assets/img/favicon.svg') }}">
  <title>@yield('title', 'Admin Management Panel')</title>
  
  <script>
      // Auto-redirect mobile devices to the native-feeling Mobile Admin UI
      if (window.innerWidth <= 768 && !window.location.pathname.startsWith('/admin/mobile')) {
          window.location.replace("{{ route('admin.mobile.dashboard') }}");
      }
  </script>
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/motion@11.11.13/dist/motion.js"></script>
  @notifyCss
  {!! ToastMagic::styles() !!}
  {!! laravelGeoGenius()->initIntlPhoneInput() !!}
  
  <style>
      @font-face {
          font-family: 'Circular Pro';
          src: url('{{ asset("fonts/gilroy-medium.woff2") }}') format('woff2');
          font-weight: 400;
          font-style: normal;
          font-display: swap;
      }
      @font-face {
          font-family: 'Circular Pro';
          src: url('{{ asset("fonts/gilroy-bold.woff2") }}') format('woff2');
          font-weight: 700;
          font-style: normal;
          font-display: swap;
      }
  </style>

  <!-- Core Icons & Fonts -->
  <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
  <link href="{{asset('vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
  <link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet">
  <link href="{{asset('css/design-tokens.css')}}" rel="stylesheet">
  <link href="{{asset('css/mycss.css')}}" rel="stylesheet">
  <link href="{{asset('css/notification-system.css')}}?v=5{{ time() }}" rel="stylesheet">
  <link href="{{asset('css/notification-system-modals.css')}}?v=1{{ time() }}" rel="stylesheet">
  <link href="{{asset('css/admin-theme.css')}}" rel="stylesheet">

  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- AOS Scroll Animations -->
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">

  <!-- Core Libraries -->
  <script src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
  <script src="{{asset('vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
  
  <!-- UI Enhancement Libraries -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="{{ asset('js/notification-system.js') }}?v=4{{ time() }}"></script>
  
  <!-- DataTables -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.js"></script>

  <!-- Summernote (Backup) -->
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

  <!-- AOS JS -->
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

  @stack('head')
</head>

<body id="page-top">
  @include('marketing.partials.ambient')
  <div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}" style="height: auto; padding: 0;">
         <div class="logo-bg-premium" style="display: flex; width: 100%; justify-content: center; align-items: center; margin-bottom: 20px; background: transparent;">
            <x-ui.logo variant="light" size="lg" />
         </div>
      </a>

      <!-- Group 1: Dashboard -->
      <li class="nav-item {{ Route::is('dashboard') ? 'active' : '' }} mt-4">
        <a class="nav-link" href="{{route('dashboard')}}">
          <i class="ri-dashboard-line"></i>
          <span>Dashboard</span>
        </a>
      </li>

      <!-- Group 2: User Management -->
      <div class="sidebar-heading">User Management</div>
      <li class="nav-item">
        <a class="nav-link {{ Route::is(['admin.user', 'admin.support_tickets', 'admin.onboarding.*', 'admin.kyc']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseUsers">
          <i class="ri-team-line"></i>
          <span>Users & Support</span>
        </a>
        <div id="collapseUsers" class="collapse {{ Route::is(['admin.user', 'admin.support_tickets', 'admin.onboarding.*', 'admin.kyc']) ? 'show' : '' }}" data-parent="#accordionSidebar">
          <div class="collapse-inner">
            <a class="collapse-item {{ Route::is('admin.user') ? 'active' : '' }}" href="{{route('admin.user')}}">Users</a>
            <a class="collapse-item {{ Route::is('admin.support_tickets') ? 'active' : '' }}" href="{{route('admin.support_tickets')}}">Support Tickets</a>
            <a class="collapse-item {{ Route::is('admin.onboarding.*') ? 'active' : '' }}" href="{{route('admin.onboarding.index')}}">Onboarding Forms</a>
            <a class="collapse-item {{ Route::is('admin.kyc') ? 'active' : '' }}" href="{{route('admin.kyc')}}">Verification (KYC)</a>
          </div>
        </div>
      </li>

      <!-- Group 3: Financial Operations -->
      <div class="sidebar-heading">Financial Operations</div>
      <li class="nav-item">
        <a class="nav-link {{ Route::is(['admin.deposit', 'withdrawal.admin', 'admin.proof', 'card', 'admin.wallets.index', 'withdrawal_transfer', 'withdrawal.levels', 'admin.wire_request.index']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseFinance">
          <i class="ri-wallet-3-line"></i>
          <span>Payments & Funding</span>
        </a>
        <div id="collapseFinance" class="collapse {{ Route::is(['admin.deposit', 'withdrawal.admin', 'admin.proof', 'card', 'admin.wallets.index', 'withdrawal_transfer', 'withdrawal.levels', 'admin.wire_request.index']) ? 'show' : '' }}" data-parent="#accordionSidebar">
          <div class="collapse-inner">
            <a class="collapse-item {{ Route::is('admin.deposit') ? 'active' : '' }}" href="{{route('admin.deposit')}}">Deposits</a>
            <a class="collapse-item {{ Route::is('withdrawal.admin') ? 'active' : '' }}" href="{{route('withdrawal.admin')}}">Withdrawals</a>
            <a class="collapse-item {{ Route::is('withdrawal_transfer') ? 'active' : '' }}" href="{{route('withdrawal_transfer')}}">Transfers</a>
            <a class="collapse-item {{ Route::is('admin.wire_request.index') ? 'active' : '' }}" href="{{route('admin.wire_request.index')}}">Bank Wires</a>
            <a class="collapse-item {{ Route::is('admin.proof') ? 'active' : '' }}" href="{{route('admin.proof')}}">Payment Proofs</a>
            <a class="collapse-item {{ Route::is('card') ? 'active' : '' }}" href="{{route('card')}}">Card Payments</a>
            <a class="collapse-item {{ Route::is('admin.wallets.index') ? 'active' : '' }}" href="{{route('admin.wallets.index')}}">Deposit Addresses</a>
            <a class="collapse-item {{ Route::is('withdrawal.levels') ? 'active' : '' }}" href="{{route('withdrawal.levels')}}">Withdrawal Limits</a>
          </div>
        </div>
      </li>

      <!-- Group 4: Trading & Markets -->
      <div class="sidebar-heading">Trading & Markets</div>
      <li class="nav-item">
        <a class="nav-link {{ Route::is(['admin.assets_gen.crypto', 'admin.assets_gen.stocks', 'admin.assets_gen.forex', 'add', 'all_trade', 'bot_trades_index', 'admin.spot_orders', 'stock', 'bots', 'signals', 'generate_signal', 'add_copy', 'trader_request', 'add.copy_details', 'copy_trades_index', 'admin.screenshot', 'admin.all_bot_trades', 'admin.all_copy_trades', 'private_keys']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseTrading">
          <i class="ri-exchange-funds-line"></i>
          <span>Trading Floor</span>
        </a>
        <div id="collapseTrading" class="collapse {{ Route::is(['admin.assets_gen.crypto', 'admin.assets_gen.stocks', 'admin.assets_gen.forex', 'add', 'all_trade', 'bot_trades_index', 'admin.spot_orders', 'stock', 'bots', 'signals', 'generate_signal', 'add_copy', 'trader_request', 'add.copy_details', 'copy_trades_index', 'admin.screenshot', 'admin.all_bot_trades', 'admin.all_copy_trades', 'private_keys']) ? 'show' : '' }}" data-parent="#accordionSidebar">
          <div class="collapse-inner">
            <h6 class="collapse-header text-muted x-small px-3 mb-1">Market Operations</h6>
            <a class="collapse-item {{ Route::is(['all_trade', 'copy_trades_index', 'bot_trades_index']) ? 'active' : '' }}" href="{{route('all_trade')}}">All Spot Trades</a>
            <a class="collapse-item {{ Route::is('admin.all_bot_trades') ? 'active' : '' }}" href="{{route('admin.all_bot_trades')}}">All Bot Trades</a>
            <a class="collapse-item {{ Route::is('admin.all_copy_trades') ? 'active' : '' }}" href="{{route('admin.all_copy_trades')}}">All Copy Trades</a>
            <a class="collapse-item {{ Route::is('admin.spot_orders') ? 'active' : '' }}" href="{{route('admin.spot_orders')}}">Pending Orders</a>
            <div class="dropdown-divider mx-3 border-secondary opacity-25"></div>
            <h6 class="collapse-header text-muted x-small px-3 mb-1">Asset Generation (APIs)</h6>
            <a class="collapse-item {{ Route::is('admin.assets_gen.crypto') ? 'active' : '' }}" href="{{route('admin.assets_gen.crypto')}}">Crypto Assets</a>
            <a class="collapse-item {{ Route::is('admin.assets_gen.stocks') ? 'active' : '' }}" href="{{route('admin.assets_gen.stocks')}}">Stock Assets</a>
            <a class="collapse-item {{ Route::is('admin.assets_gen.forex') ? 'active' : '' }}" href="{{route('admin.assets_gen.forex')}}">Forex Assets</a>
            
            <div class="dropdown-divider mx-3 border-secondary opacity-25"></div>
            <h6 class="collapse-header text-muted x-small px-3 mb-1">Bot & Algo Trading</h6>
            <a class="collapse-item {{ Route::is('bots') ? 'active' : '' }}" href="{{route('bots')}}">Manage Bots</a>
            <a class="collapse-item {{ Route::is('signals') ? 'active' : '' }}" href="{{route('signals')}}">Manage Signals</a>
            <a class="collapse-item {{ Route::is('generate_signal') ? 'active' : '' }}" href="#">Create Signal</a>

            <div class="dropdown-divider mx-3 border-secondary opacity-25"></div>
            <h6 class="collapse-header text-muted x-small px-3 mb-1">CopyTrading Network</h6>
            <a class="collapse-item {{ Route::is('add_copy') ? 'active' : '' }}" href="{{route('add_copy')}}">Provider Profiles</a>
            <a class="collapse-item {{ Route::is('trader_request') ? 'active' : '' }}" href="{{route('trader_request')}}">Application Requests</a>
            <a class="collapse-item {{ Route::is('add.copy_details') ? 'active' : '' }}" href="{{route('add.copy_details')}}">System Settings</a>

            <div class="dropdown-divider mx-3 border-secondary opacity-25"></div>
            <h6 class="collapse-header text-muted x-small px-3 mb-1">API & Tools</h6>
            <a class="collapse-item {{ Route::is('private_keys') ? 'active' : '' }}" href="{{route('private_keys')}}">Private Keys</a>
            <a class="collapse-item {{ Route::is('admin.screenshot') ? 'active' : '' }}" href="{{route('admin.screenshot')}}">Screenshot Tool</a>
          </div>
        </div>
      </li>

      <!-- Group 4.5: Derivatives & DeFi -->
      <div class="sidebar-heading">Derivatives & DeFi</div>
      <li class="nav-item">
        <a class="nav-link {{ Route::is(['admin.futures.*', 'admin.margin.*', 'admin.p2p.*', 'admin.liquidity.*', 'admin.launchpad.*', 'admin.loans.*', 'admin.dual.*', 'admin.dca.*']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseDerivatives">
          <i class="ri-swap-box-line"></i>
          <span>Derivatives & DeFi</span>
        </a>
        <div id="collapseDerivatives" class="collapse {{ Route::is(['admin.futures.*', 'admin.margin.*', 'admin.p2p.*', 'admin.liquidity.*', 'admin.launchpad.*', 'admin.loans.*', 'admin.dual.*', 'admin.dca.*']) ? 'show' : '' }}" data-parent="#accordionSidebar">
          <div class="collapse-inner">
            <h6 class="collapse-header text-muted x-small px-3 mb-1">Futures & Margin</h6>
            <a class="collapse-item {{ Route::is('admin.futures.positions.index') ? 'active' : '' }}" href="{{route('admin.futures.positions.index')}}">Futures Positions</a>
            <a class="collapse-item {{ Route::is('admin.futures.pairs.index') ? 'active' : '' }}" href="{{route('admin.futures.pairs.index')}}">Futures Pairs</a>
            <a class="collapse-item {{ Route::is('admin.margin.positions.index') ? 'active' : '' }}" href="{{route('admin.margin.positions.index')}}">Margin Positions</a>
            <a class="collapse-item {{ Route::is('admin.margin.pairs.index') ? 'active' : '' }}" href="{{route('admin.margin.pairs.index')}}">Margin Pairs</a>
            
            <div class="dropdown-divider mx-3 border-secondary opacity-25"></div>
            <h6 class="collapse-header text-muted x-small px-3 mb-1">DeFi & Web3</h6>
            <a class="collapse-item {{ Route::is('admin.p2p.listings.index') ? 'active' : '' }}" href="{{route('admin.p2p.listings.index')}}">P2P Listings</a>
            <a class="collapse-item {{ Route::is('admin.p2p.orders.index') ? 'active' : '' }}" href="{{route('admin.p2p.orders.index')}}">P2P Orders</a>
            <a class="collapse-item {{ Route::is('admin.liquidity.pools.index') ? 'active' : '' }}" href="{{route('admin.liquidity.pools.index')}}">Liquidity Pools</a>
            <a class="collapse-item {{ Route::is('admin.liquidity.positions.index') ? 'active' : '' }}" href="{{route('admin.liquidity.positions.index')}}">Liquidity Positions</a>
            
            <div class="dropdown-divider mx-3 border-secondary opacity-25"></div>
            <h6 class="collapse-header text-muted x-small px-3 mb-1">Launchpad & Loans</h6>
            <a class="collapse-item {{ Route::is('admin.launchpad.projects.index') ? 'active' : '' }}" href="{{route('admin.launchpad.projects.index')}}">Launchpad Projects</a>
            <a class="collapse-item {{ Route::is('admin.launchpad.participations.index') ? 'active' : '' }}" href="{{route('admin.launchpad.participations.index')}}">Participations</a>
            <a class="collapse-item {{ Route::is('admin.loans.plans.index') ? 'active' : '' }}" href="{{route('admin.loans.plans.index')}}">Loan Plans</a>
            <a class="collapse-item {{ Route::is('admin.loans.positions.index') ? 'active' : '' }}" href="{{route('admin.loans.positions.index')}}">Active Loans</a>
            
            <div class="dropdown-divider mx-3 border-secondary opacity-25"></div>
            <h6 class="collapse-header text-muted x-small px-3 mb-1">Advanced Products</h6>
            <a class="collapse-item {{ Route::is('admin.dual.products.index') ? 'active' : '' }}" href="{{route('admin.dual.products.index')}}">Dual Inv. Products</a>
            <a class="collapse-item {{ Route::is('admin.dual.subscriptions.index') ? 'active' : '' }}" href="{{route('admin.dual.subscriptions.index')}}">Dual Subscriptions</a>
            <a class="collapse-item {{ Route::is('admin.dca.plans.index') ? 'active' : '' }}" href="{{route('admin.dca.plans.index')}}">DCA Plans</a>
            <a class="collapse-item {{ Route::is('admin.dca.subscriptions.index') ? 'active' : '' }}" href="{{route('admin.dca.subscriptions.index')}}">DCA Subscriptions</a>
          </div>
        </div>
      </li>

      <!-- Group 5: Smart Portfolios & Earn -->
      <div class="sidebar-heading">Wealth & Portfolios</div>
      <li class="nav-item">
        <a class="nav-link {{ Route::is(['admin.mutual_funds', 'admin.vip_stocks', 'packages', 'admin.investments', 'admin.crypto_etfs', 'admin.stock_etfs', 'admin.staking.index', 'admin.trust.index', 'admin.ira.index', 'admin.staking_plans.*', 'admin.retirement_plans.*', 'admin.student_plans.*']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseWealth">
          <i class="ri-bank-line"></i>
          <span>Smart Portfolios</span>
        </a>
        <div id="collapseWealth" class="collapse {{ Route::is(['admin.mutual_funds', 'admin.vip_stocks', 'packages', 'admin.investments', 'admin.crypto_etfs', 'admin.stock_etfs', 'admin.staking.index', 'admin.trust.index', 'admin.ira.index', 'admin.staking_plans.*', 'admin.retirement_plans.*', 'admin.student_plans.*']) ? 'show' : '' }}" data-parent="#accordionSidebar">
          <div class="collapse-inner">
            <h6 class="collapse-header text-muted x-small px-3 mb-1">Earn Products</h6>
            <a class="collapse-item {{ Route::is('admin.investments') ? 'active' : '' }}" href="{{route('admin.investments')}}">Active Earn Orders</a>
            <a class="collapse-item {{ Route::is('admin.mutual_funds') ? 'active' : '' }}" href="{{route('admin.mutual_funds')}}">Strategies (Mutual Funds)</a>
            <a class="collapse-item {{ Route::is('admin.crypto_etfs') ? 'active' : '' }}" href="{{route('admin.crypto_etfs')}}">Crypto ETFs</a>
            <a class="collapse-item {{ Route::is('admin.stock_etfs') ? 'active' : '' }}" href="#">Stock ETFs</a>
            <a class="collapse-item {{ Route::is('admin.vip_stocks') ? 'active' : '' }}" href="{{route('admin.vip_stocks')}}">Premium Stocks</a>
            <a class="collapse-item {{ Route::is('packages') ? 'active' : '' }}" href="{{route('packages')}}">Earn Packages</a>
            <a class="collapse-item {{ Route::is('admin.hip_pro.index') ? 'active' : '' }}" href="#">HIP Pro Plans</a>
            
            <div class="dropdown-divider mx-3 border-secondary opacity-25"></div>
            <h6 class="collapse-header text-muted x-small px-3 mb-1">Wealth Management Plans</h6>
            <a class="collapse-item {{ Route::is('admin.staking_plans.index') ? 'active' : '' }}" href="{{route('admin.staking_plans.index')}}">Staking Plans</a>
            <a class="collapse-item {{ Route::is('admin.retirement_plans.index') ? 'active' : '' }}" href="{{route('admin.retirement_plans.index')}}">Retirement Plans</a>
            <a class="collapse-item {{ Route::is('admin.student_plans.index') ? 'active' : '' }}" href="{{route('admin.student_plans.index')}}">Student Plans</a>

            <div class="dropdown-divider mx-3 border-secondary opacity-25"></div>
            <h6 class="collapse-header text-muted x-small px-3 mb-1">Wealth Management Positions</h6>
            <a class="collapse-item {{ Route::is('admin.staking.index') ? 'active' : '' }}" href="{{route('admin.staking.index')}}">Crypto Staking</a>
            <a class="collapse-item {{ Route::is('admin.trust.index') ? 'active' : '' }}" href="{{route('admin.trust.index')}}">Family & Kids</a>
            <a class="collapse-item {{ Route::is('admin.ira.index') ? 'active' : '' }}" href="{{route('admin.ira.index')}}">Retirement (IRA)</a>
          </div>
        </div>
      </li>

      <!-- Group 6: Marketing & Content -->
      <div class="sidebar-heading">Marketing & Content</div>
      <li class="nav-item">
        <a class="nav-link {{ Route::is(['admin.blogs', 'admin.blog_manager.*', 'admin.coupons', 'vidoes']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseMarketing">
          <i class="ri-megaphone-line"></i>
          <span>Growth Tools</span>
        </a>
        <div id="collapseMarketing" class="collapse {{ Route::is(['admin.blogs', 'admin.blog_manager.*', 'admin.coupons', 'vidoes']) ? 'show' : '' }}" data-parent="#accordionSidebar">
          <div class="collapse-inner">
            <a class="collapse-item {{ Route::is('admin.coupons') ? 'active' : '' }}" href="{{route('admin.coupons')}}">Coupons</a>
            <a class="collapse-item {{ Route::is('admin.blogs') ? 'active' : '' }}" href="{{route('admin.blogs')}}">Blog & News</a>
            <a class="collapse-item {{ Route::is('vidoes') ? 'active' : '' }}" href="{{route('vidoes')}}">Educational Videos</a>
            <a class="collapse-item {{ Route::is('admin.blog_manager.*') ? 'active' : '' }}" href="{{route('admin.blog_manager.index')}}">AI Blog Manager</a>
          </div>
        </div>
      </li>

      <!-- Group 7: Security & Compliance -->
      <div class="sidebar-heading">Security & Audits</div>
      <li class="nav-item">
        <a class="nav-link {{ Route::is(['emergency', 'tax_proof']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSecurity">
          <i class="ri-shield-keyhole-line"></i>
          <span>Risk Management</span>
        </a>
        <div id="collapseSecurity" class="collapse {{ Route::is(['emergency', 'tax_proof']) ? 'show' : '' }}" data-parent="#accordionSidebar">
          <div class="collapse-inner">
            <a class="collapse-item text-danger {{ Route::is('emergency') ? 'active' : '' }}" href="{{route('emergency')}}">Emergency System</a>
            <a class="collapse-item {{ Route::is('tax_proof') ? 'active' : '' }}" href="{{route('tax_proof')}}">Tax Verification</a>
          </div>
        </div>
      </li>

      <!-- Group 8: Platform Configuration -->
      <div class="sidebar-heading">Platform System</div>
      <li class="nav-item">
        <a class="nav-link {{ Route::is(['site.index', 'admin.prices.index', 'admin.telegram', 'admin.emails.index', 'admin.cronjobs', 'tax_settings', 'admin.admins.index']) ? '' : 'collapsed' }}" href="#" data-toggle="collapse" data-target="#collapseSystem">
          <i class="ri-settings-4-line"></i>
          <span>Configuration</span>
        </a>
        <div id="collapseSystem" class="collapse {{ Route::is(['site.index', 'admin.prices.index', 'admin.telegram', 'admin.emails.index', 'admin.cronjobs', 'tax_settings', 'admin.admins.index']) ? 'show' : '' }}" data-parent="#accordionSidebar">
          <div class="collapse-inner">
            <a class="collapse-item {{ Route::is('site.index') ? 'active' : '' }}" href="{{route('site.index')}}">System Settings</a>
            @if(auth('admin')->user()->is_super_admin)
            <a class="collapse-item {{ Route::is('admin.admins.index') ? 'active' : '' }}" href="{{route('admin.admins.index')}}">Admin Management</a>
            @endif
            <a class="collapse-item {{ Route::is('admin.prices.index') ? 'active' : '' }}" href="{{route('admin.prices.index')}}">API Prices Config</a>
            <a class="collapse-item {{ Route::is('tax_settings') ? 'active' : '' }}" href="{{route('tax_settings')}}">Tax Configurations</a>
            <a class="collapse-item {{ Route::is('admin.telegram') ? 'active' : '' }}" href="{{route('admin.telegram')}}">Telegram Alerts</a>
            <a class="collapse-item {{ Route::is('admin.emails.index') ? 'active' : '' }}" href="{{route('admin.emails.index')}}">Email Templates</a>
            <a class="collapse-item {{ Route::is('admin.cronjobs') ? 'active' : '' }}" href="{{route('admin.cronjobs')}}">Cronjobs Tracker</a>
          </div>
        </div>
      </li>

      <li class="nav-item mt-auto mb-4">
        <a class="nav-link text-danger" href="{{route('admin.logout')}}">
          <i class="ri-logout-box-r-line"></i>
          <span>Logout</span>
        </a>
      </li>
    </ul>

    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-dark topbar static-top shadow">
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="ri-menu-line text-white"></i>
          </button>
          <div class="d-flex align-items-center">
            <span class="badge badge-primary mr-2" style="font-size: 0.65rem; background: var(--accent-primary); border: none;">PRO</span>
            <h6 class="m-0 font-weight-bold text-white outfit" style="font-size: 0.95rem; letter-spacing: 0.5px;">Admin Dashboard</h6>
          </div>
          <ul class="navbar-nav ml-auto">
            <li class="nav-item mr-3">
              <a class="nav-link btn btn-sm btn-outline-warning mt-3 mb-3 d-flex align-items-center justify-content-center" href="{{route('admin.clear_cache')}}" style="border-radius: 6px; padding: 0.3rem 0.8rem; font-size: 0.75rem; font-weight: 600; color: var(--gold); border-color: rgba(153, 0, 0, 0.3);">
                <i class="ri-refresh-line mr-1"></i> Clear Cache
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="{{route('admin.logout')}}">
                <i class="ri-shut-down-line text-danger"></i>
              </a>
            </li>
          </ul>
        </nav>

        <div class="container-fluid pt-4 animate-content">
          @yield('content')
        </div>
      </div>
    </div>
  </div>

  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  @stack('modals')

  <script>
    const { animate, stagger, inView } = Motion;

    $(document).ready(function() {
        // Sidebar toggle
        $('#sidebarToggle, #sidebarToggleTop').on('click', function() {
            $("body").toggleClass("sidebar-toggled");
            $(".sidebar").toggleClass("toggled");
        });
        
        if (typeof lucide !== 'undefined') lucide.createIcons();

        // AOS Scroll Animations
        if (typeof AOS !== 'undefined') {
            AOS.init({ duration: 500, easing: 'ease-out-cubic', once: true, offset: 30 });
        }
        
        // Motion One — Page Load Animation
        animate(
            ".animate-content", 
            { opacity: [0, 1], y: [15, 0] }, 
            { duration: 0.5, easing: "ease-out" }
        );

        // Card stagger animation
        const cards = document.querySelectorAll('.animate-content .glass-card, .animate-content .card');
        if (cards.length > 0) {
            animate(
                cards,
                { opacity: [0, 1], y: [20, 0] },
                { delay: stagger(0.08, { start: 0.1 }), duration: 0.4, easing: "ease-out" }
            );
        }

        // Table row in-view animation
        inView(".table tbody tr", (info) => {
            animate(info.target, { opacity: [0, 1], x: [-8, 0] }, { duration: 0.25, easing: "ease-out" });
        });

        // ═══════════════════════════════════════════════
        // GLOBAL TABLE ACTION BUTTON MODERNIZATION
        // Scans ALL tables on ALL 102 pages automatically
        // ═══════════════════════════════════════════════
        $('.table tbody td').each(function() {
            var $td = $(this);
            var html = $td.html();
            if (!html) return;
            var lower = html.toLowerCase();
            
            // Only transform cells that look like action columns
            if (lower.includes('edit') || lower.includes('delete') || lower.includes('approve') || lower.includes('reject')) {
                $td.find('a').each(function() {
                    var $a = $(this);
                    var text = $a.text().toLowerCase().trim();
                    
                    if (text === 'edit') {
                        $a.addClass('btn btn-sm btn-info text-white me-1 mb-1').removeClass('button-btn background');
                        $a.attr('title', 'Edit this record').attr('data-toggle', 'tooltip');
                        $a.html('<i class="ri-pencil-line"></i> Edit');
                    } else if (text === 'delete') {
                        $a.addClass('btn btn-sm btn-danger text-white me-1 mb-1 admin-delete-btn').removeClass('button-btn background');
                        $a.attr('title', 'Delete this record').attr('data-toggle', 'tooltip');
                        $a.html('<i class="ri-delete-bin-line"></i> Delete');
                    } else if (text === 'approve') {
                        $a.addClass('btn btn-sm btn-success text-white me-1 mb-1').removeClass('button-btn background');
                        $a.attr('title', 'Approve this request').attr('data-toggle', 'tooltip');
                        $a.html('<i class="ri-check-line"></i> Approve');
                    } else if (text === 'reject' || text === 'decline') {
                        $a.addClass('btn btn-sm btn-warning text-dark me-1 mb-1').removeClass('button-btn background');
                        $a.attr('title', 'Reject this request').attr('data-toggle', 'tooltip');
                        $a.html('<i class="ri-close-line"></i> Reject');
                    } else if (text === 'view' || text === 'details') {
                        $a.addClass('btn btn-sm btn-dark me-1 mb-1').removeClass('button-btn background');
                        $a.attr('title', 'View details').attr('data-toggle', 'tooltip');
                        $a.html('<i class="ri-eye-line"></i> View');
                    }
                });
                // Remove pipe separators
                $td.contents().filter(function() {
                    return this.nodeType === 3 && this.nodeValue.trim() === '|';
                }).remove();
            }
        });
        
        // ═══════════════════════════════════════════════
        // SWEETALERT2 DELETE CONFIRMATION
        // Intercepts ALL delete links globally
        // ═══════════════════════════════════════════════
        $(document).on('click', '.admin-delete-btn, a[href*="delete"]', function(e) {
            var $link = $(this);
            // Skip if already confirmed
            if ($link.data('confirmed')) return true;
            
            e.preventDefault();
            var href = $link.attr('href');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#475569',
                confirmButtonText: '<i class="ri-delete-bin-line"></i> Yes, delete it',
                cancelButtonText: 'Cancel',
                background: 'rgba(15, 15, 20, 0.95)',
                color: '#f1f5f9',
                backdrop: 'rgba(0,0,0,0.7)',
                customClass: {
                    popup: 'border border-secondary rounded-3',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $link.data('confirmed', true);
                    window.location.href = href;
                }
            });
        });
        
        // ═══════════════════════════════════════════════
        // BOOTSTRAP TOOLTIPS INITIALIZATION
        // ═══════════════════════════════════════════════
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"], [title]'));
        tooltipTriggerList.forEach(function(el) {
            if (!el.getAttribute('data-toggle')) {
                el.setAttribute('data-toggle', 'tooltip');
            }
            new bootstrap.Tooltip(el);
        });

        // ═══════════════════════════════════════════════
        // TOASTR DARK THEME CONFIGURATION (Legacy fallback)
        // ═══════════════════════════════════════════════
        if (typeof toastr !== 'undefined' && !window.JVNotify) {
            toastr.options = {
                "closeButton": true,
                "positionClass": "toast-top-right",
                "progressBar": true,
                "timeOut": "3500",
                "showEasing": "swing",
                "hideEasing": "linear"
            };
        }
    });
  </script>
  @stack('scripts')
  @include('notify::components.notify')
  @notifyJs
  {!! ToastMagic::scripts() !!}
  
  <!-- Global Image Error Handler for Broken Asset Logos -->
  <script>
  document.addEventListener('error', function(e) {
      if (e.target.tagName && e.target.tagName.toLowerCase() === 'img') {
          if (!e.target.dataset.fallbackApplied) {
              e.target.dataset.fallbackApplied = "true";
              
              // Try to extract name from alt text
              let name = e.target.alt ? e.target.alt.replace(/[^a-zA-Z0-9]/g, '').substring(0, 2) : 'XX';
              if (name.trim() === '') name = 'XX';
              
              // Use a generic placeholder or UI Avatars
              e.target.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name) + '&background=2d3748&color=fff&rounded=true&bold=true&font-size=0.4';
          }
      }
  }, true);
  </script>
  <!-- SweetAlert2 & Async Modals System -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
  <script src="{{ asset('js/notification-system-modals.js') }}?v=1{{ time() }}"></script>
  <script src="{{ asset('js/drawer-sheet.js') }}?v=1{{ time() }}"></script>
  <script src="{{ asset('js/async-modals.js') }}?v={{ time() }}"></script>
</body>
</html>


