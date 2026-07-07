<?php

use App\Http\Controllers\Admin\IndexController as admin;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PriceSettingsController;
use App\Http\Controllers\Auth\IndexController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CronJobController;
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Http\Controllers\User\ApiKeysController;
use App\Http\Controllers\User\IndexController as user_index;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::middleware(['guest:web'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    })->name('home');

    // Insights routes moved

    Route::get('/login', function () {
        $lock = null;
        if (Schema::hasTable('lock_message')) {
            $lock = DB::table('lock_message')->first();
        }

        return view('login', [
            'lock' => $lock,
        ]);
    })->name('login');

    Route::get('/register/{referral?}/{id?}', function ($referral = null, $id = null) {
        return view('signup', [
            'id' => $id,
            'referral' => null,
        ]);
    })->name('register');

    Route::get('/starter', function () {
        return view('signup', ['id' => null]);
    })->name('register-starter');

    Route::get('/reset', function () {
        return view('auth.passwords.email');
    })->name('reset');

    // Route::get('/register/{data}',[RegisterController::class,'index']);

    // Route::get('/register/{data}',[RegisterController::class,'index']);
    Route::post('/register/post', [IndexController::class, 'signup'])->name('s.post')->middleware('throttle:6,1');
    Route::post('/login/post', [LoginController::class, 'store'])->name('login.post')->middleware('throttle:6,1');
    // Route::get('/verify/{id}',[LoginController::class,'verify']);

    Route::post('/start/post', [IndexController::class, 'start_page'])->name('start_page');

    // forgot pass
    Route::post('/reset/post', [LoginController::class, 'passwordReset'])->name('reset.post')->middleware('throttle:6,1');
    Route::get('/reset/{token}', [LoginController::class, 'showPasswordResetForm'])->name('reset.token');
    Route::post('/reset/post/post', [LoginController::class, 'resetPassword'])->name('reset.post.post')->middleware('throttle:6,1');
});

Route::get('/insights', [BlogController::class, 'index'])->name('public.blog.index');
Route::get('/sitemap.xml', [BlogController::class, 'sitemap'])->name('public.sitemap');

Route::get('/google/2fa', [LoginController::class, 'google'])->name('google');
Route::post('/google/2fa/post', [LoginController::class, 'google2fa'])->name('google2fa');

Route::get('/verify-account', [IndexController::class, 'start'])->name('start');
Route::get('/start', function () {
    return redirect()->route('start');
});

// otp
Route::get('/otp', [LoginController::class, 'otp'])->name('otp');
// resend otp
Route::post('/resend/otp', [LoginController::class, 'resend_otp'])->name('resend_otp');

Route::post('/login/otp', [LoginController::class, 'validateOtp'])->name('login.otp');

// Cron jobs must be publicly accessible for external services like cron-job.org to ping them
Route::middleware(['web'])->group(function () {
    Route::get('/cron/is-forex', [CronJobController::class, 'is_forex'])->name('cron.is-forex');

    Route::get('/cron/is-crypto', [CronJobController::class, 'is_crypto'])->name('cron.is-crypto');

    Route::get('/cron/is-stock', [CronJobController::class, 'is_stock'])->name('cron.is-stock');

    Route::get('/cron/is-stocks', [CronJobController::class, 'is_stocks'])->name('cron.is-stocks');

    Route::get('/cron/is-trade', [CronJobController::class, 'is_trade'])->name('cron.is-trade');

    Route::get('/cron/is-bot', [CronJobController::class, 'is_bot'])->name('cron.is-bot');

    Route::get('/cron/is-deposit', [CronJobController::class, 'is_deposit'])->name('cron.is-deposit');

    Route::get('/cron/is-trade_copy', [CronJobController::class, 'is_trade_copy'])->name('cron.is-trade-copy');

    Route::get('/is_delete', [CronJobController::class, 'is_removed']);

    Route::get('/cron/is-logo-sync', [CronJobController::class, 'is_logo_sync'])->name('cron.is-logo-sync');

    Route::get('/cron/is-scan-blockchain', [CronJobController::class, 'is_scan_blockchain'])->name('cron.is-scan-blockchain');

    // Master System Cron (Executes all scheduled Kernel tasks)
    Route::get('/system/cron/run', [CronJobController::class, 'runMasterCron'])->name('system.cron.run');
});

Route::get('/activate', [PaymentController::class, 'post_config'])->name('post_config');

Route::get('auth/google', [TwoFactorAuthController::class, 'redirectToGoogle'])->name('login.google');
Route::get('auth/google/callback', [TwoFactorAuthController::class, 'handleGoogleCallback'])->name('login.google.callback');

Route::get('back/api/call', [user_index::class, 'execute_result_after_time'])->name('execute_result_after_time');
Route::get('back/api/call-copy', [user_index::class, 'execute_result_after_time_for_copy_trade'])->name('execute_result_after_time_for_copy_trade');

Route::middleware(['guest:admin'])->group(function () {
    Route::get('/admin', [admin::class, 'index'])->name('admin');
    Route::post('admin/post', [admin::class, 'adminLogin'])->name('admin.login');
});

Route::get('/mobile-preview', function () {
    $assets = DB::table('assets')->limit(50)->get();
    $usd = 0;
    $trade = [];

    return view('mobile.pages.dashboard', [
        'assets' => $assets,
        'usd' => $usd,
        'trade' => $trade,
    ]);
})->name('mobile.premium.preview');

Route::get('/admin/2fa', [admin::class, 'google_login'])->name('google_login_admin');
Route::post('admin/sfa/google', [admin::class, 'verify2faAdmin'])->name('google_loginslogin');

// Price Provider Management
Route::middleware(['auth:admin'])->name('admin.prices.')->prefix('admin/prices')->group(function () {
    Route::get('/', [PriceSettingsController::class, 'index'])->name('index');
    Route::post('/store', [PriceSettingsController::class, 'store'])->name('store');
    Route::post('/{id}/update', [PriceSettingsController::class, 'update'])->name('update');
    Route::post('/{id}/toggle', [PriceSettingsController::class, 'toggle'])->name('toggle');
    Route::get('/{id}/delete', [PriceSettingsController::class, 'delete'])->name('delete');
});

Route::get('/verify/{email}', [user_index::class, 'verify'])->name('verify');

// ===== LOCALE SWITCHER =====
Route::get('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'es', 'fr', 'zh', 'ar'])) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('locale.switch');

// ===== ASSET PROXY =====
Route::get('/api/stock-logo/{symbol}', [ProxyController::class, 'resolve'])->name('stock.logo')->where('symbol', '.*');

// ===== ANALYTICS & LOGGING =====

// Plans routes removed

// Derivatives & DeFi User Routes (Handled in user.php to avoid conflicts)
Route::middleware(['auth:web'])->group(function () {

    Route::get('/insights/{slug}', [BlogController::class, 'show'])->name('public.blog.show');

    Route::get('/p2p', [user_index::class, 'p2pMarket'])->name('user.p2p');
    Route::post('/p2p/create', [user_index::class, 'p2pCreateListing'])->name('user.p2p.create');
    Route::post('/p2p/order', [user_index::class, 'p2pPlaceOrder'])->name('user.p2p.order');
    Route::post('/p2p/confirm', [user_index::class, 'p2pConfirmPayment'])->name('user.p2p.confirm');
    Route::post('/p2p/dispute', [user_index::class, 'p2pDispute'])->name('user.p2p.dispute');
    Route::get('/p2p/chat/{order_id}', [user_index::class, 'p2pChat'])->name('user.p2p.chat');
    Route::post('/p2p/chat/send', [user_index::class, 'p2pChatSend'])->name('user.p2p.chat.send');

    Route::get('/liquidity', [user_index::class, 'liquidityIndex'])->name('user.liquidity');
    Route::post('/liquidity/deposit', [user_index::class, 'liquidityDeposit'])->name('user.liquidity.deposit');
    Route::post('/liquidity/withdraw', [user_index::class, 'liquidityWithdraw'])->name('user.liquidity.withdraw');

    Route::get('/dual-investment', [user_index::class, 'dualInvestment'])->name('user.dual_investment');
    Route::post('/dual-investment/buy', [user_index::class, 'buyDualInvestment'])->name('user.dual.buy');

    Route::get('/dca', [user_index::class, 'dca'])->name('user.dca');
    Route::post('/dca/subscribe', [user_index::class, 'dcaSubscribe'])->name('user.dca.subscribe');

    // User API Keys
    Route::get('/api-keys', [ApiKeysController::class, 'index'])->name('user.api_keys');
    Route::post('/api-keys', [ApiKeysController::class, 'store'])->name('user.api_keys.store');
    Route::delete('/api-keys/{id}', [ApiKeysController::class, 'destroy'])->name('user.api_keys.destroy');
});

// Mobile Admin Routes
Route::middleware(['auth:admin'])->prefix('admin/mobile')->name('admin.mobile.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\MobileAdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [\App\Http\Controllers\Admin\MobileAdminController::class, 'users'])->name('users');
    Route::get('/trades', [\App\Http\Controllers\Admin\MobileAdminController::class, 'trades'])->name('trades');
    Route::get('/signals', [\App\Http\Controllers\Admin\MobileAdminController::class, 'signals'])->name('signals');
    Route::get('/menu', [\App\Http\Controllers\Admin\MobileAdminController::class, 'menu'])->name('menu');
    Route::get('/kyc', [\App\Http\Controllers\Admin\MobileAdminController::class, 'kyc'])->name('kyc');
    Route::get('/deposits', [\App\Http\Controllers\Admin\MobileAdminController::class, 'deposits'])->name('deposits');
    Route::get('/withdrawals', [\App\Http\Controllers\Admin\MobileAdminController::class, 'withdrawals'])->name('withdrawals');
    Route::get('/support', [\App\Http\Controllers\Admin\MobileAdminController::class, 'support'])->name('support');
    
    // Phase 4: Advanced Modules
    Route::get('/copy-trading', [\App\Http\Controllers\Admin\MobileAdminController::class, 'copy_trading'])->name('copy_trading');
    Route::get('/futures', [\App\Http\Controllers\Admin\MobileAdminController::class, 'futures'])->name('futures');
    Route::get('/bots', [\App\Http\Controllers\Admin\MobileAdminController::class, 'bots'])->name('bots');
    Route::get('/wallets', [\App\Http\Controllers\Admin\MobileAdminController::class, 'wallets'])->name('wallets');
});

Route::post('/webhook/oxapay', [App\Http\Controllers\PaymentWebhookController::class, 'oxapay'])->name('webhook.oxapay');
Route::post('/webhook/nowpayments', [App\Http\Controllers\PaymentWebhookController::class, 'nowpayments'])->name('webhook.nowpayments');

Route::middleware(['auth:web'])->group(function () {
    Route::post('/dashboard/deposit/initiate-crypto', [App\Http\Controllers\Exchange\CryptoDepositController::class, 'initiate'])->name('deposit.initiate-crypto');
});
