<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AssetManagementController;
use App\Http\Controllers\Admin\AssetsController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\BlogManagerController;
use App\Http\Controllers\Admin\BotController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CryptoController;
use App\Http\Controllers\Admin\CryptoETFController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DcaController;
use App\Http\Controllers\Admin\DualInvestmentController;
use App\Http\Controllers\Admin\EmailTemplateController;
use App\Http\Controllers\Admin\FuturesController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\Admin\LaunchpadController;
use App\Http\Controllers\Admin\LiquidityController;
use App\Http\Controllers\Admin\LoanController;
use App\Http\Controllers\Admin\MarginController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\MutualFundController;
use App\Http\Controllers\Admin\P2pController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\RetirementPlanController;
use App\Http\Controllers\Admin\ScreenshotController;
use App\Http\Controllers\Admin\SignalController;
use App\Http\Controllers\Admin\SpotOrderController;
use App\Http\Controllers\Admin\StakingPlanController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Admin\StudentPlanController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\TelegramConfigController;
use App\Http\Controllers\Admin\TradeController;
use App\Http\Controllers\Admin\TraderController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\UserCodeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VipStockController;
use App\Http\Controllers\Admin\WealthOversightController;
use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:admin', 'block', 'mimi'])->group(function () {

    Route::get('/signals/delete-all', [SignalController::class, 'deleteAllSignal'])->name('deleteAllSignal');
    Route::get('/private_keys', [AdminController::class, 'private_keys'])->name('private_keys');

    // Finance Bank Wire Review Routes
    Route::prefix('finance/bank-wire')->name('admin.finance.bank-wire.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\FinanceReviewController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\FinanceReviewController::class, 'show'])->name('show');
        Route::put('/{id}', [\App\Http\Controllers\Admin\FinanceReviewController::class, 'update'])->name('update');
    });

    // Wire Requests
    Route::get('/wire-requests', [AdminController::class, 'wire_index'])->name('admin.wire_request.index');
    Route::get('/wire-requests/delete/{id}', [AdminController::class, 'wire_delete'])->name('admin.wire_request.delete');

    // upgrade_code_check
    Route::post('/update-withdrawals', [TransactionController::class, 'updatewithdrawals'])->name('updatewithdrawals');

    Route::post('/updatetransfer', [TransactionController::class, 'updateTransfer'])->name('updateTransfer');
    Route::post('/inter-transfer', [AdminController::class, 'transfer'])->name('transfer');

    Route::post('/user/general', [IndexController::class, 'general'])->name('general');

    // generate signal
    // Route::get('/trade/signal',[SignalController::class,'generate_signal'])->name('generate_signal');
    // Route::post('/trade/signal/get',[SignalController::class,'getRandomAssets'])->name('getRandomAssets');
    // Route::get('/trade/signal/{id}',[SignalController::class,'delete_g'])->name('delete_g');

    Route::post('/user/otp', [IndexController::class, 'onOffOtp'])->name('admin_onOffOtp');
    Route::post('/user/otp/google', [IndexController::class, 'onOffGoogle'])->name('admin_onOffGoogle');
    Route::post('/user/otp/email', [IndexController::class, 'onOffEmail'])->name('admin_onOffEmail');

    Route::get('/dashboard', [IndexController::class, 'dashboard'])->name('dashboard');
    Route::get('/cronjobs', [IndexController::class, 'cronjobs'])->name('admin.cronjobs');
    Route::get('/login-user/{id}', [IndexController::class, 'loginUsernow'])->name('loginUsernow');
    //  admin.trade

    // user Lock
    Route::post('/user-lock', [UserController::class, 'userLock'])->name('userLock');
    // Tax
    Route::get('/tax-proof', [TaxController::class, 'index'])->name('tax_proof');
    Route::post('/user/tax', [TaxController::class, 'onOfftax'])->name('onOfftax');

    // delete
    Route::get('/tax-proof/delete/{id}', [TaxController::class, 'delete'])->name('delete.proof');
    Route::get('/tax-proof/approve_one/{id}', [TaxController::class, 'approve_one'])->name('approve_one');
    Route::get('/tax-proof/approve_two/{id}', [TaxController::class, 'approve_two'])->name('approve_two');

    // default plan
    Route::post('/default', [AdminController::class, 'default_plan'])->name('default_plan');

    // add assets
    Route::get('/add', [AssetsController::class, 'index'])->name('add');

    Route::get('/delete_asset/{id}', [AssetsController::class, 'delete_cat'])->name('delete_cat');

    Route::post('/cat', [AssetsController::class, 'addCat'])->name('cat');
    Route::post('/add/mass', [AssetsController::class, 'mass_import'])->name('add.mass');
    Route::post('/add/discovery', [AssetsController::class, 'autonomous_discovery'])->name('admin.asset.discovery');
    Route::post('/loans/sync_binance', [LoanController::class, 'syncFromBinance'])->name('admin.loan.sync_binance');
    Route::post('/add/sync_binance', [AssetsController::class, 'syncFromBinance'])->name('admin.asset.sync_binance');
    Route::post('/add/post', [AssetsController::class, 'addAssets'])->name('add.post');
    Route::get('/add/get', [AssetsController::class, 'assetDetails'])->name('add.get');
    // update assets status
    // Route::post('/add/update',[AssetsController::class,'updateStatus'])->name('add.update');
    // single assets
    Route::get('/add/single/{id}/{eid}', [AssetsController::class, 'viewSingleAsset'])->name('single');
    // single cat
    Route::get('/add/cat/{id}', [AssetsController::class, 'viewSingleCat'])->name('single_cat');
    Route::post('/add/cat/edit', [AssetsController::class, 'editCat'])->name('editCat');
    // edit assets
    Route::post('/add/edit', [AssetsController::class, 'editAssets'])->name('add.edit');

    Route::get('/delete/{id}/{eid}', [AssetsController::class, 'deleteAssets']);

    Route::post('/add/sync_logos', [AssetsController::class, 'sync_logos'])->name('admin.asset.sync_logos');
    Route::post('/add/mass_delete', [AssetsController::class, 'massDelete'])->name('admin.asset.mass_delete');
    Route::post('/add/mass_edit_pl', [AssetsController::class, 'massEditProfitLoss'])->name('admin.asset.mass_edit_pl');
    Route::post('/add/post', [AssetsController::class, 'addAssets'])->name('add.post');

    Route::get('/add/get', [AssetsController::class, 'assetDetails'])->name('add.get');

    // instant btc and usdt
    // instant btc and usdt
    Route::middleware(['super_admin'])->group(function () {
        Route::get('/wallet-deposit', [AdminController::class, 'wallet_deposit'])->name('wallet_deposit');
        Route::post('/dash/app', [CryptoController::class, 'update_dash'])->name('update_dash');
        Route::post('/ltc/app', [CryptoController::class, 'update_ltc'])->name('update_ltc');

        Route::post('/bch/app', [CryptoController::class, 'update_bch'])->name('update_bch');
        Route::post('/doge/app', [CryptoController::class, 'update_doge'])->name('update_doge');
        Route::post('/btc/app', [CryptoController::class, 'update_btc'])->name('update_btc');
        Route::post('usdt/app', [CryptoController::class, 'update_usdt'])->name('update_usdt');
        Route::post('solana/app', [CryptoController::class, 'update_solana'])->name('update_solana');
        Route::post('eth/app', [CryptoController::class, 'update_eth'])->name('update_eth');
    });

    // update assets status
    // Route::post('/add/update',[AssetsController::class,'updateStatus'])->name('add.update');
    // single assets

    Route::get('/add/single/{id}/{eid}', [AssetsController::class, 'viewSingleAsset'])->name('single');
    // edit assets
    Route::post('/add/edit', [AssetsController::class, 'editAssets'])->name('add.edit');
    Route::get('/add/delete/{id}/{eid}', [AssetsController::class, 'deleteAssets']);
    // copy trade
    Route::get('/copy_trade', [TraderController::class, 'copy_trades'])->name('add_copy');
    Route::get('/copy_trade/details', [TraderController::class, 'copy_details'])->name('add.copy_details');
    Route::post('/copy/post', [TraderController::class, 'store_trader'])->name('add.store_trader');
    Route::get('/copy_trade/{trader}', [TraderController::class, 'copy_show'])->name('add.copy_show');

    Route::post('/copy_trades', [TraderController::class, 'delete_trader'])->name('add.copy_delete');

    Route::get('/trader-request', [TraderController::class, 'trader_request'])->name('trader_request');
    Route::get('/trader-request/{id}', [TraderController::class, 'approved_ctrader'])->name('approved_ctrader');
    Route::get('/trader-cancel/{id}', [TraderController::class, 'cancel_ctrader'])->name('cancel_ctrader');

    // Route::post('/tradess',[TradeController::class, 'trade'])->name('admin.trade');
    Route::get('/copy-tradess', [TraderController::class, 'all_copy_trades'])->name('admin.all_copy_trades');
    Route::get('/copy-trade-all', [TraderController::class, 'copy_trades_index'])->name('copy_trades_index');
    Route::get('/bot-trade-all', [AdminController::class, 'bot_trades_index'])->name('bot_trades_index');
    Route::get('/bot-tradess', [AdminController::class, 'all_bot_trades'])->name('admin.all_bot_trades');

    Route::get('/asset-list/{id}', [AdminController::class, 'getAssetByIdAdmin'])->name('getAssetByIdAdmin');

    Route::post('/withdrawal/on/off', [TransactionController::class, 'onOffWithdrawal'])->name('withdrawal.onOffWithdrawal');
    Route::post('/exit_trade', [AdminController::class, 'exit_trade'])->name('exit_trade');

    Route::post('/level/on/off', [AdminController::class, 'onOfflevel'])->name('onOfflevel');

    // stock
    Route::get('/stock', [AdminController::class, 'stock'])->name('stock');
    Route::post('/stock/add', [AdminController::class, 'store_stock'])->name('admin.stocks.store');
    Route::get('/stock/delete/{id}', [AdminController::class, 'delete_stock'])->name('delete_stock');
    Route::post('/stock/discovery', [AdminController::class, 'autonomous_discovery_stocks'])->name('admin.stock.discovery');
    Route::post('/stock/edit', [AdminController::class, 'edit_stock'])->name('edit_stock');
    Route::post('/stock/user/update', [AdminController::class, 'updateUserStock'])->name('admin.user_stock.update');

    // instant btc and usdt
    Route::middleware(['super_admin'])->group(function () {
        Route::post('/btc/app', [CryptoController::class, 'update_btc'])->name('update_btc');
        Route::post('usdt/app', [CryptoController::class, 'update_usdt'])->name('update_usdt');
        Route::post('eth/app', [CryptoController::class, 'update_eth'])->name('update_eth');
    });

    // proof
    Route::get('proof', [AdminController::class, 'proof'])->name('admin.proof');
    // cash app
    Route::middleware(['super_admin'])->group(function () {
        Route::post('/cash/app', [AdminController::class, 'update_cash_app'])->name('cash.app');
        Route::post('/bank/app', [AdminController::class, 'update_bank'])->name('bank.app');
    });

    // credit card'
    Route::get('/card-payment', [PaymentController::class, 'card'])->name('card');
    Route::get('/card-payment/{id}', [PaymentController::class, 'rel'])->name('rel');

    // all user
    Route::get('/user', [DashboardController::class, 'index'])->name('admin.user');
    Route::post('/user/update', [UserController::class, 'updateUser'])->name('admin.user.update');
    Route::post('/user/toggle-demo', [AdminController::class, 'admin_toggle_demo'])->name('admin.user.toggle_demo');
    Route::get('/user/delete/{id}', [AdminController::class, 'delete_user'])->name('admin.user.delete');
    Route::get('/user/delete_message/{id}', [MessageController::class, 'delete_message'])->name('delete_message');

    Route::get('/user/{user}', [UserController::class, 'getSingleUser'])->name('admin.user.single');
    Route::post('/user/demo-balance/update', [AdminController::class, 'update_demo_balance'])->name('admin.update_demo_balance');
    Route::post('/user/generate', [AdminController::class, 'generate_trade'])->name('admin.user.generate_trade');
    Route::post('/user/generate/deposit', [AdminController::class, 'generate_depopsit'])->name('admin.user.generate_depopsit');
    Route::post('/user/generate/withdrawal', [AdminController::class, 'generate_with'])->name('admin.user.generate_with');
    Route::post('/user/generate/bot', [AdminController::class, 'generate_bot'])->name('admin.generate.bot');
    Route::post('/user/generate/copy', [AdminController::class, 'generate_copy'])->name('admin.generate.generate_copy');
    Route::post('/user/wallet/add', [AdminController::class, 'add_wallet'])->name('add_wallets');
    Route::get('/user/wallet/delete_wallet/{id}', [AdminController::class, 'delete_wallet'])->name('delete_wallet');
    Route::get('/user/withtdrwal/{id}', [AdminController::class, 'deleteWithdrawaltMethod'])->name('deleteithdes');

    Route::get('/user/deposit/{id}', [AdminController::class, 'deleteDepositMethod'])->name('deletedeposi');

    // trades
    Route::get('/trades/admin', [IndexController::class, 'all_trades'])->name('all_trades');
    Route::get('/trades/all', [IndexController::class, 'all_trade'])->name('all_trade');
    Route::post('/trades/post', [IndexController::class, 'update_trades'])->name('update_trades');
    Route::post('/trades/{id}/override', [IndexController::class, 'override_trade'])->name('override_trade');
    Route::get('/trades-delete/{id}', [IndexController::class, 'delete_trade'])->name('delete_trade');

    // code for user
    Route::post('/user/code_name', [UserCodeController::class, 'update_code_name'])->name('update_code_name');
    Route::post('/user/code_generate', [UserCodeController::class, 'update_code_generate'])->name('update_code_generate');

    Route::post('/user/upgrade_code_check', [UserCodeController::class, 'upgrade_code_check'])->name('upgrade_code_check');
    Route::post('/user/tax_code_check', [UserCodeController::class, 'tax_code_check'])->name('tax_code_check');
    Route::post('/user/demorage_check', [UserCodeController::class, 'demorage_check'])->name('demorage_check');

    // custom message
    Route::post('/user/custom_message', [MessageController::class, 'custom_message'])->name('custom_message');
    Route::post('/user/custom', [AdminController::class, 'custom'])->name('custom');

    // update user level
    Route::post('/user/level', [UserController::class, 'update_user_level'])->name('admin.update_user_level');
    Route::post('/user/bonuns', [AdminController::class, 'bonus'])->name('admin.bonus');

    Route::post('/user/toggle-basic-plan', [AdminController::class, 'toggle_basic_plan_access'])->name('admin.user.toggle_basic_plan');
    Route::post('/user/toggle-hip-pro', [AdminController::class, 'toggle_hip_pro_access'])->name('admin.user.toggle_hip_pro');
    // Route::get('/user/bonuns/approve/{id}',[IndexController::class,'approved_bonus'])->name('admin.approved_bonus');

    // user wallet
    Route::get('/user-wallet/{id}', [AdminController::class, 'wallet_index'])->name('wallet_index');
    Route::get('/user/delete/{id}/{user_id}', [AdminController::class, 'delete_user_trade'])->name('delete_user_trade');
    // daily trade
    Route::post('/user/daily_trade', [AdminController::class, 'no_of_trades'])->name('no_of_trades');

    // update package
    Route::post('/user/pacakge', [AdminController::class, 'package_plan'])->name('package_plan');
    // kyc
    Route::get('/kyc', [UserController::class, 'kyc'])->name('admin.kyc');
    Route::post('/kyc/approved', [UserController::class, 'approved_Kyc'])->name('admin.kyc.approved');
    Route::post('/kyc/delete', [UserController::class, 'delete_kyc'])->name('admin.kyc.delete');

    // referral count
    Route::post('/referral-count', [AdminController::class, 'editReferral'])->name('editReferral');

    // admin
    Route::get('/gets', [AdminController::class, 'adminIndex'])->name('admin.get');
    Route::get('/admin/delete/{id}', [AdminController::class, 'delete_amdin'])->name('delete_amdin');
    Route::post('/admin/add', [AdminController::class, 'store'])->name('admin.add');

    Route::post('/admin/update', [AdminController::class, 'updateAdmin'])->name('admin.admin.update');

    Route::get('/fund/{user}/{symbol}', [AdminController::class, 'getModel'])->name('fund');
    Route::get('/debit/{user}/{symbol}', [AdminController::class, 'getModels'])->name('debit');

    Route::post('/admin/model/post', [AdminController::class, 'debit'])->name('ad');
    // fund demo
    Route::get('/demo/{user}', [AdminController::class, 'get_fund_demo'])->name('get_fund_demo');
    Route::post('/admin/demo', [AdminController::class, 'fund_demo'])->name('fund_demo');
    // debit demo
    Route::get('/demo/debit/{user}', [AdminController::class, 'get_debit_demo'])->name('get_debit_demo');

    // referral
    Route::get('/referral/{user}', [AdminController::class, 'get_fund_referral'])->name('get_fund_referral');

    Route::get('/referral/debit/{user}', [AdminController::class, 'get_debit_referral'])->name('get_debit_referral');
    Route::post('/admin/referral', [AdminController::class, 'fund_referral'])->name('fund_referral');

    // withdraw
    Route::get('/withdrawal', [IndexController::class, 'withdrawal'])->name('withdrawal.admin');
    Route::get('/withdrawal/transfers', [IndexController::class, 'withdrawal_transfer'])->name('withdrawal_transfer');
    // Route::post('/withdrawal/post',[IndexController::class,'updatewithdrawal'])->name('withdrawal.updated');
    Route::get('/withdrawal/post/bank/{id}', [IndexController::class, 'updatewithdrawal_bank'])->name('updatewithdrawal_bank.updated');
    Route::get('/withdrawal-levels', [IndexController::class, 'levels'])->name('withdrawal.levels');
    Route::post('/withdrawal-levels/post', [IndexController::class, 'edit_level'])->name('edit_level.levels');

    Route::get('/delete_withdrawal_history/{id}', [TransactionController::class, 'delete_withdrawal'])->name('delete_withdrawal');
    Route::get('/delete_deposit_history/{id}', [TransactionController::class, 'delete_deposit'])->name('delete_deposit');

    // Admin History Edit (with Auto-Recalculation)
    Route::get('/history/trade/{id}/edit', [IndexController::class, 'edit_trade'])->name('admin.history.trade.edit');
    Route::post('/history/trade/{id}/update', [IndexController::class, 'update_trade'])->name('admin.history.trade.update');

    Route::get('/history/deposit/{id}/edit', [TransactionController::class, 'edit_deposit'])->name('admin.history.deposit.edit');
    Route::post('/history/deposit/{id}/update', [TransactionController::class, 'update_deposit'])->name('admin.history.deposit.update');

    Route::get('/history/withdrawal/{id}/edit', [TransactionController::class, 'edit_withdrawal'])->name('admin.history.withdrawal.edit');
    Route::post('/history/withdrawal/{id}/update', [TransactionController::class, 'update_withdrawal'])->name('admin.history.withdrawal.update');

    // deposit
    Route::get('/deposit', [IndexController::class, 'deposit'])->name('admin.deposit');
    Route::post('/deposit/update', [IndexController::class, 'updatedeposit'])->name('updatedeposit');
    // deposit
    Route::get('/lock-message', [MessageController::class, 'user_lock'])->name('admin.user_lock');

    // chinese deposit
    Route::get('/chinese-deposits', [TransactionController::class, 'chineses_deposit'])->name('chineses_deposit');
    Route::post('/chineses_deposit/edit', [TransactionController::class, 'update_chiness_deposit'])->name('update_chiness_deposit');
    Route::get('/site_setting', [IndexController::class, 'siteindex'])->name('site.index');
    Route::post('/site_setting/tax', [TransactionController::class, 'tax_settings'])->name('tax_settings');
    Route::get('/withdrawal/settings', [IndexController::class, 'withdrawal_settings_index'])->name('admin.withdrawal_settings');
    Route::post('/withdrawal/settings/update', [IndexController::class, 'withdrawal_settings_update'])->name('admin.withdrawal_settings.update');
    Route::post('/withdrawal/generate-codes', [UserCodeController::class, 'generate_user_codes'])->name('admin.generate_user_codes');
    Route::post('/withdrawal/toggle-code-check', [UserCodeController::class, 'toggle_user_code_check'])->name('admin.toggle_user_code_check');
    Route::post('/site_setting/post', [IndexController::class, 'store_site'])->name('site.post');
    Route::post('/engine_settings/update', [IndexController::class, 'update_engine_settings'])->name('admin.update_engine_settings');
    Route::post('/settings/trading/auto-approve', [IndexController::class, 'updateTradingAutoApprove'])->name('admin.settings.trading.auto-approve');
    // Route::post('/site_setting/referral',[IndexController::class,'referral_percentage'])->name('site.post.referral');

    Route::post('/site_setting/bank', [TransactionController::class, 'bank_content'])->name('bank_content');
    Route::post('/site_setting/bank_limit', [TransactionController::class, 'bank_limit'])->name('bank_limit');
    Route::post('/site_setting/bank_error_message', [MessageController::class, 'bank_error_message'])->name('bank_error_message');

    Route::post('/site_setting/email', [IndexController::class, 'admin_email'])->name('admin_email');
    Route::post('/site_setting/system_message', [MessageController::class, 'system_message'])->name('system_message');
    Route::get('/payment', [PaymentController::class, 'index'])->name('payment');
    Route::post('/payment/updated', [PaymentController::class, 'updatePayment'])->name('payment.updated');

    // site template
    Route::post('/site_setting/template', [IndexController::class, 'site_template'])->name('site.template');
    Route::post('/site_setting/vidoe', [IndexController::class, 'store_video'])->name('store_video');

    // bots
    Route::get('/bots', [BotController::class, 'bots'])->name('bots');
    Route::post('/bots/add', [BotController::class, 'addBot'])->name('addBot');
    Route::get('/bots/{bot}', [BotController::class, 'single_bots'])->name('single_bot');
    Route::post('/bots/edit', [BotController::class, 'edit_bot'])->name('edit_bot');
    Route::get('/bots/{id}', [BotController::class, 'userBots'])->name('me_bot');

    // signals
    Route::get('/signals', [SignalController::class, 'signal'])->name('signals');
    Route::post('/signals/add', [SignalController::class, 'addSignal'])->name('addSignal');
    Route::get('/signals/{signal}', [SignalController::class, 'single_signal'])->name('single_signal');
    Route::post('/signals/edit', [SignalController::class, 'edit_signal'])->name('edit_signal');
    Route::post('/signals/user/update', [SignalController::class, 'updateUserSignal'])->name('admin.user_signal.update');
    Route::post('/signals/user', [SignalController::class, 'generateSignal'])->name('generateSignal');
    Route::get('/signals/single/{id}', [SignalController::class, 'userSignals'])->name('me');

    // welcome message
    Route::post('/welcome', [MessageController::class, 'update_wecome_message'])->name('welcome');
    // deposit message

    Route::post('/depo_message', [MessageController::class, 'deposit_message'])->name('depo_message');
    Route::get('/delete/depo_message/{id}', [MessageController::class, 'delete_deposit_message'])->name('delete_deposit_message');
    Route::get('/delete/with_message/{id}', [MessageController::class, 'delete_withdrawal_message'])->name('delete_withdrawal_message');

    // withdrawal message
    Route::post('/with_message', [MessageController::class, 'withdrawal_message'])->name('with_message');

    // message
    Route::post('/message', [MessageController::class, 'send_message'])->name('send_message');
    Route::get('/message/{id}/{user_id?}', [MessageController::class, 'single_message'])->name('single_message');

    // notification
    Route::post('/noti', [MessageController::class, 'send_notification'])->name('send_notification');

    // crypto area
    Route::get('/buy-crypto', [CryptoController::class, 'add_buy'])->name('add_buys');
    Route::get('/buy-crypto/{id}', [CryptoController::class, 'show_crypto'])->name('show_crypto');
    Route::post('/buy-crypto/store', [CryptoController::class, 'store_crypto'])->name('store_crypto');
    Route::post('/buy-crypto/edit', [CryptoController::class, 'edit_crypto'])->name('edit_crypto');
    Route::get('/buy-crypto/delete/{id}', [CryptoController::class, 'delete_crypto'])->name('delete_crypto');

    // packages area
    Route::get('/packages', [AdminController::class, 'packages'])->name('packages');
    Route::post('/packages/store', [AdminController::class, 'store_package'])->name('store_package');
    Route::get('/packages/delete/{id}', [AdminController::class, 'delete_package'])->name('delete_package');
    Route::get('/packages/show/{package}', [AdminController::class, 'show_package'])->name('show_package');
    Route::post('/packages/edit', [AdminController::class, 'edit_package'])->name('edit_package');

    // packages list
    Route::get('/packages/list/{package}', [AdminController::class, 'show_package_list'])->name('show_package_list');
    Route::post('/packages/list/add', [AdminController::class, 'store_package_list'])->name('store_package_list');
    Route::get('/packages/list/delete/{id}', [AdminController::class, 'delete_package_list'])->name('delete_package_list');

    // chat widget
    Route::post('/chat', [IndexController::class, 'chat_code'])->name('chat');
    Route::get('/videos', [AssetsController::class, 'vidoes'])->name('vidoes');

    Route::post('/videos/post', [AssetsController::class, 'upload_videos'])->name('upload_videos');

    Route::get('/videos/delete/{id}', [AssetsController::class, 'delete_vidoes'])->name('delete_vidoes');

    Route::get('/videos/edit/{id}', [AssetsController::class, 'edit_vidoes'])->name('edit_vidoes');

    // emergerncy
    Route::get('/emergency', [AdminController::class, 'offline'])->name('emergency');
    // walet
    Route::get('/admin/wallet', [IndexController::class, 'wallet'])->name('wallet');

    Route::post('/admin/wallet/post', [IndexController::class, 'wallet_post'])->name('wallet_post');

    Route::get('/admin/wallet/post/{id}', [IndexController::class, 'edit_wallet'])->name('edit_wallet');

    Route::post('/admin/wallet/post/post', [IndexController::class, 'edit_wallet_post'])->name('edit_wallet_post');

    // Dynamic Admin Wallets (Deposit Methods)
    Route::middleware(['super_admin'])->group(function () {
        Route::get('/admin-wallets', [AdminController::class, 'admin_wallets_index'])->name('admin.wallets.index');
        Route::post('/admin-wallets/store', [AdminController::class, 'admin_wallets_store'])->name('admin.wallets.store');
        Route::post('/admin-wallets/update', [AdminController::class, 'admin_wallets_update'])->name('admin.wallets.update');
        Route::get('/admin-wallets/delete/{id}', [AdminController::class, 'admin_wallets_delete'])->name('admin.wallets.delete');
        
        // Admin Management System
        Route::get('/admins', [\App\Http\Controllers\Admin\AdminManagementController::class, 'index'])->name('admin.admins.index');
        Route::post('/admins/store', [\App\Http\Controllers\Admin\AdminManagementController::class, 'store'])->name('admin.admins.store');
        Route::post('/admins/update', [\App\Http\Controllers\Admin\AdminManagementController::class, 'update'])->name('admin.admins.update');
        Route::get('/admins/delete/{id}', [\App\Http\Controllers\Admin\AdminManagementController::class, 'destroy'])->name('admin.admins.delete');
    });

    Route::get('/blog/{id?}', [BlogController::class, 'edit_blog'])->name('edit_blog');

    Route::post('/blog/post', [BlogController::class, 'store'])->name('blog.store');
    Route::post('/blogs/upload', [BlogController::class, 'upload'])->name('upload');
    Route::post('/blogs/edit', [BlogController::class, 'edit_store'])->name('edit_store');

    Route::get('/blogs', [BlogController::class, 'index'])->name('admin.blogs');

    Route::get('/blogs/{id}', [BlogController::class, 'delete_blog'])->name('blog.delete');

    Route::post('/import', [PdfController::class, 'importData'])->name('import.data');

    // ===== AI BLOG MANAGER =====
    Route::get('/blog-manager', [BlogManagerController::class, 'index'])->name('admin.blog_manager.index');
    Route::post('/blog-manager/store', [BlogManagerController::class, 'store'])->name('admin.blog_manager.store');
    Route::post('/blog-manager/update', [BlogManagerController::class, 'update'])->name('admin.blog_manager.update');
    Route::get('/blog-manager/delete/{id}', [BlogManagerController::class, 'destroy'])->name('admin.blog_manager.delete');
    Route::get('/blog-manager/sync/{id}', [BlogManagerController::class, 'forceSync'])->name('admin.blog_manager.sync');

    // ===== MUTUAL FUNDS =====
    Route::get('/mutual-funds', [MutualFundController::class, 'index'])->name('admin.mutual_funds');
    Route::post('/mutual-funds/store', [MutualFundController::class, 'store'])->name('admin.mutual_fund.store');
    Route::post('/mutual-funds/update', [MutualFundController::class, 'update'])->name('admin.mutual_fund.update');
    Route::get('/mutual-funds/delete/{id}', [MutualFundController::class, 'destroy'])->name('admin.mutual_fund.delete');
    Route::post('/mutual-funds/simulate', [MutualFundController::class, 'simulate'])->name('admin.mutual_fund.simulate');
    Route::get('/mutual-funds/investors/{id}', [MutualFundController::class, 'investors'])->name('admin.mutual_fund.investors');
    Route::post('/mutual-funds/investment/update', [MutualFundController::class, 'updateInvestment'])->name('admin.mutual_fund.investment.update');

    // ===== VIP STOCKS =====
    Route::get('/vip-stocks', [VipStockController::class, 'index'])->name('admin.vip_stocks');
    Route::post('/vip-stocks/toggle', [VipStockController::class, 'toggleVip'])->name('admin.vip_stock.toggle');
    Route::post('/vip-stocks/price', [VipStockController::class, 'updatePrice'])->name('admin.vip_stock.price');

    // ===== SUPPORT TICKETS =====
    Route::get('/support-tickets', [SupportTicketController::class, 'index'])->name('admin.support_tickets');
    Route::post('/support-tickets/reply', [SupportTicketController::class, 'reply'])->name('admin.support_ticket.reply');
    Route::post('/support-tickets/status', [SupportTicketController::class, 'updateStatus'])->name('admin.support_ticket.status');
    Route::get('/support-tickets/delete/{id}', [SupportTicketController::class, 'destroy'])->name('admin.support_ticket.delete');

    // ===== TELEGRAM NOTIFICATIONS =====
    Route::get('/telegram', [TelegramConfigController::class, 'index'])->name('admin.telegram');
    Route::post('/telegram/store', [TelegramConfigController::class, 'store'])->name('admin.telegram.store');
    Route::post('/telegram/update', [TelegramConfigController::class, 'update'])->name('admin.telegram.update');
    Route::get('/telegram/delete/{id}', [TelegramConfigController::class, 'destroy'])->name('admin.telegram.delete');
    Route::get('/telegram/test/{id}', [TelegramConfigController::class, 'test'])->name('admin.telegram.test');

    // ===== COUPONS & REWARDS =====
    Route::get('/coupons', [CouponController::class, 'index'])->name('admin.coupons');
    Route::post('/coupons/store', [CouponController::class, 'store'])->name('admin.coupon.store');
    Route::get('/coupons/toggle/{id}', [CouponController::class, 'toggle'])->name('admin.coupon.toggle');
    Route::get('/coupons/delete/{id}', [CouponController::class, 'destroy'])->name('admin.coupon.delete');

    // ===== SCREENSHOT GENERATOR =====
    Route::get('/screenshot', [ScreenshotController::class, 'index'])->name('admin.screenshot');
    Route::post('/screenshot/generate', [ScreenshotController::class, 'generate'])->name('admin.screenshot.generate');

    // Email Templates
    Route::get('/email-templates', [EmailTemplateController::class, 'index'])->name('admin.emails.index');
    Route::get('/email-templates/{id}/edit', [EmailTemplateController::class, 'edit'])->name('admin.emails.edit');
    Route::post('/email-templates/{id}/update', [EmailTemplateController::class, 'update'])->name('admin.emails.update');

    // Onboarding Questions CRUD
    include __DIR__.'/onboarding_admin.php';

    // ===== GROWTH PLANS =====
    Route::get('/investments', [AdminController::class, 'investments'])->name('admin.investments');
    Route::post('/investments/update', [AdminController::class, 'update_investment'])->name('admin.investments.update');
    Route::get('/investments/delete/{id}', [AdminController::class, 'delete_investment'])->name('admin.investments.delete');

    // ===== CRYPTO ETFs =====
    Route::get('/crypto-etfs', [CryptoETFController::class, 'index'])->name('admin.crypto_etfs');
    Route::post('/crypto-etfs/auto-populate', [CryptoETFController::class, 'autoPopulate'])->name('admin.crypto_etfs.auto_populate');
    Route::post('/crypto-etfs/refresh-logos', [CryptoETFController::class, 'refreshLogos'])->name('admin.crypto_etfs.refresh_logos');
    Route::post('/crypto-etfs/plan/update', [CryptoETFController::class, 'updatePlan'])->name('admin.crypto_etfs.plan.update');
    Route::post('/crypto-etfs/plan/store', [CryptoETFController::class, 'storePlan'])->name('admin.crypto_etfs.plan.store');
    Route::delete('/crypto-etfs/plan/delete/{id}', [CryptoETFController::class, 'deletePlan'])->name('admin.crypto_etfs.plan.delete');
    Route::post('/crypto-etfs/investment/update', [CryptoETFController::class, 'updateInvestment'])->name('admin.crypto_etfs.investment.update');

    // ===== STOCKS =====
    Route::get('/stock-etfs', [StockController::class, 'index'])->name('admin.stocks');
    Route::post('/stock-etfs/auto-populate', [StockController::class, 'autoPopulate'])->name('admin.stocks.auto_populate');
    Route::post('/stock-etfs/refresh-logos', [StockController::class, 'refreshLogos'])->name('admin.stocks.refresh_logos');
    Route::post('/stock-etfs/plan/update', [StockController::class, 'updatePlan'])->name('admin.stocks.plan.update');
    Route::post('/stock-etfs/plan/store', [StockController::class, 'storePlan'])->name('admin.stocks.plan.store');
    Route::delete('/stock-etfs/plan/delete/{id}', [StockController::class, 'deletePlan'])->name('admin.stocks.plan.delete');
    Route::post('/stock-etfs/investment/update', [StockController::class, 'updateInvestment'])->name('admin.stocks.investment.update');

    // ===== DERIVATIVES & DEFI =====
    Route::prefix('futures')->name('admin.futures.')->group(function () {
        Route::get('/index', [FuturesController::class, 'index'])->name('index');
        Route::get('/all-positions', [FuturesController::class, 'allPositions'])->name('all_positions');
        Route::get('/pairs', [FuturesController::class, 'pairsIndex'])->name('pairs.index');
        Route::post('/pairs/sync_binance', [FuturesController::class, 'syncFromBinance'])->name('pairs.sync_binance');
        Route::post('/pairs', [FuturesController::class, 'storePair'])->name('pairs.store');
        Route::post('/pairs/update', [FuturesController::class, 'updatePair'])->name('pairs.update');
        Route::get('/pairs/delete/{id}', [FuturesController::class, 'destroyPair'])->name('pairs.delete');
        Route::post('/pairs/mass_delete', [FuturesController::class, 'massDelete'])->name('pairs.mass_delete');
        Route::get('/positions', [FuturesController::class, 'positionsIndex'])->name('positions.index');
        Route::post('/positions/update', [FuturesController::class, 'updatePosition'])->name('positions.update');
        Route::post('/positions/inject', [FuturesController::class, 'injectPosition'])->name('positions.inject');
        Route::post('/mark-price', [FuturesController::class, 'updateMarkPrice'])->name('mark_price.update');

        Route::post('/trigger-liquidation/{id}', [FuturesController::class, 'triggerLiquidation'])->name('trigger_liquidation');
        Route::post('/mass-liquidate', [FuturesController::class, 'massLiquidate'])->name('mass_liquidate');
        Route::post('/override-outcome/{id}', [FuturesController::class, 'overrideOutcome'])->name('override_outcome');
        Route::post('/exempt-liquidation/{id}', [FuturesController::class, 'exemptFromLiquidation'])->name('exempt_liquidation');
        Route::post('/edit-history/{id}', [FuturesController::class, 'editHistory'])->name('edit_history');
        Route::get('/delete-position/{id}', [FuturesController::class, 'deletePosition'])->name('delete_position');
        Route::post('/generate-fakes', [FuturesController::class, 'generateFakes'])->name('generate_fakes');
        Route::post('/settings/update', [FuturesController::class, 'updateSettings'])->name('settings.update');
    });

    Route::prefix('margin')->name('admin.margin.')->group(function () {
        Route::get('/pairs', [MarginController::class, 'pairsIndex'])->name('pairs.index');
        Route::post('/pairs/sync_binance', [MarginController::class, 'syncFromBinance'])->name('pairs.sync_binance');
        Route::post('/pairs', [MarginController::class, 'storePair'])->name('pairs.store');
        Route::post('/pairs/update', [MarginController::class, 'updatePair'])->name('pairs.update');
        Route::get('/pairs/delete/{id}', [MarginController::class, 'destroyPair'])->name('pairs.delete');
        Route::post('/pairs/mass_delete', [MarginController::class, 'massDelete'])->name('pairs.mass_delete');
        Route::get('/positions', [MarginController::class, 'positionsIndex'])->name('positions.index');
        Route::post('/positions/update', [MarginController::class, 'updatePosition'])->name('positions.update');
        Route::post('/positions/inject', [MarginController::class, 'injectPosition'])->name('positions.inject');
        Route::post('/mark-price', [MarginController::class, 'updateMarkPrice'])->name('mark_price.update');
    });

    Route::prefix('p2p')->name('admin.p2p.')->group(function () {
        Route::get('/listings', [P2pController::class, 'listingsIndex'])->name('listings.index');
        Route::post('/listings', [P2pController::class, 'createListing'])->name('listings.store');
        Route::post('/listings/sync_binance', [P2pController::class, 'syncFromBinance'])->name('listings.sync_binance');
        Route::post('/listings/update', [P2pController::class, 'updateListing'])->name('listings.update');
        Route::get('/listings/delete/{id}', [P2pController::class, 'deleteListing'])->name('listings.delete');
        Route::post('/listings/generate_fake_trader', [P2pController::class, 'generateFakeTrader'])->name('listings.generate_fake_trader');
        Route::get('/orders', [P2pController::class, 'ordersIndex'])->name('orders.index');
        Route::post('/orders/resolve', [P2pController::class, 'resolveOrder'])->name('orders.resolve');
        Route::post('/orders/escrow', [P2pController::class, 'updateEscrow'])->name('orders.escrow');
        Route::get('/orders/delete/{id}', [P2pController::class, 'deleteOrder'])->name('orders.delete');
        Route::get('/orders/chat/{id}', [P2pController::class, 'adminChat'])->name('orders.chat');
        Route::post('/orders/chat/send', [P2pController::class, 'adminChatSend'])->name('orders.chat.send');
    });

    Route::prefix('liquidity')->name('admin.liquidity.')->group(function () {
        Route::get('/pools', [LiquidityController::class, 'poolsIndex'])->name('pools.index');
        Route::post('/pools/sync_binance', [LiquidityController::class, 'syncFromBinance'])->name('pools.sync_binance');
        Route::post('/pools', [LiquidityController::class, 'storePool'])->name('pools.store');
        Route::post('/pools/update', [LiquidityController::class, 'updatePool'])->name('pools.update');
        Route::get('/pools/delete/{id}', [LiquidityController::class, 'destroyPool'])->name('pools.delete');
        Route::get('/positions', [LiquidityController::class, 'positionsIndex'])->name('positions.index');
        Route::post('/positions/update', [LiquidityController::class, 'updatePosition'])->name('positions.update');
        Route::post('/simulate-apy', [LiquidityController::class, 'simulateApy'])->name('simulate_apy');
    });

    Route::prefix('launchpad')->name('admin.launchpad.')->group(function () {
        Route::get('/projects', [LaunchpadController::class, 'projectsIndex'])->name('projects.index');
        Route::post('/projects', [LaunchpadController::class, 'storeProject'])->name('projects.store');
        Route::post('/projects/update/{id}', [LaunchpadController::class, 'updateProject'])->name('projects.update');
        Route::get('/projects/delete/{id}', [LaunchpadController::class, 'destroyProject'])->name('projects.delete');
        Route::post('/projects/sync-binance', [LaunchpadController::class, 'syncFromBinance'])->name('projects.sync_binance');
        Route::post('/projects/market-update', [LaunchpadController::class, 'triggerMarketUpdate'])->name('projects.market_update');
        Route::get('/participations', [LaunchpadController::class, 'participationsIndex'])->name('participations.index');
        Route::post('/participations/update', [LaunchpadController::class, 'updateParticipation'])->name('participations.update');
    });

    Route::prefix('loans')->name('admin.loans.')->group(function () {
        Route::get('/plans', [LoanController::class, 'plansIndex'])->name('plans.index');
        Route::post('/plans', [LoanController::class, 'storePlan'])->name('plans.store');
        Route::post('/plans/update/{id}', [LoanController::class, 'updatePlan'])->name('plans.update');
        Route::get('/plans/delete/{id}', [LoanController::class, 'destroyPlan'])->name('plans.delete');
        Route::get('/positions', [LoanController::class, 'positionsIndex'])->name('positions.index');
        Route::post('/positions/update', [LoanController::class, 'updatePosition'])->name('positions.update');
    });

    Route::prefix('dual-investment')->name('admin.dual.')->group(function () {
        Route::get('/products', [DualInvestmentController::class, 'index'])->name('products.index');
        Route::post('/products/sync_binance', [DualInvestmentController::class, 'syncFromBinance'])->name('products.sync_binance');
        Route::post('/products', [DualInvestmentController::class, 'store'])->name('products.store');
        Route::post('/products/update', [DualInvestmentController::class, 'update'])->name('products.update');
        Route::get('/products/delete/{id}', [DualInvestmentController::class, 'destroy'])->name('products.delete');
        Route::get('/subscriptions', [DualInvestmentController::class, 'subscriptions'])->name('subscriptions.index');
        Route::post('/subscriptions/update', [DualInvestmentController::class, 'updateSubscription'])->name('subscriptions.update');
    });

    Route::prefix('dca')->name('admin.dca.')->group(function () {
        Route::get('/plans', [DcaController::class, 'index'])->name('plans.index');
        Route::post('/plans/sync', [DcaController::class, 'syncFromBinance'])->name('sync_binance');
        Route::post('/plans', [DcaController::class, 'store'])->name('plans.store');
        Route::post('/plans/update', [DcaController::class, 'update'])->name('plans.update');
        Route::get('/plans/delete/{id}', [DcaController::class, 'destroy'])->name('plans.delete');
        Route::get('/subscriptions', [DcaController::class, 'subscriptions'])->name('subscriptions.index');
        Route::post('/subscriptions/update', [DcaController::class, 'updateSubscription'])->name('subscriptions.update');
    });

    // Wealth Oversight Routes (Staking, Minor Trust, IRA)
    Route::get('/wealth/staking', [WealthOversightController::class, 'stakingIndex'])->name('admin.staking.index');
    Route::post('/wealth/staking/update', [WealthOversightController::class, 'stakingUpdate'])->name('admin.staking.update');

    Route::get('/wealth/trust', [WealthOversightController::class, 'trustIndex'])->name('admin.trust.index');
    Route::post('/wealth/trust/update', [WealthOversightController::class, 'trustUpdate'])->name('admin.trust.update');

    Route::get('/wealth/ira', [WealthOversightController::class, 'iraIndex'])->name('admin.ira.index');
    Route::post('/wealth/ira/update', [WealthOversightController::class, 'iraUpdate'])->name('admin.ira.update');

    // Plans CRUD
    Route::prefix('staking-plans')->name('admin.staking_plans.')->group(function () {
        Route::get('/', [StakingPlanController::class, 'index'])->name('index');
        Route::post('/sync', [StakingPlanController::class, 'syncPlans'])->name('sync');
        Route::post('/store', [StakingPlanController::class, 'store'])->name('store');
        Route::post('/update', [StakingPlanController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [StakingPlanController::class, 'destroy'])->name('delete');
        Route::post('/sync-binance', [StakingPlanController::class, 'syncFromBinance'])->name('sync_binance');
    });

    Route::prefix('retirement-plans')->name('admin.retirement_plans.')->group(function () {
        Route::get('/', [RetirementPlanController::class, 'index'])->name('index');
        Route::post('/store', [RetirementPlanController::class, 'store'])->name('store');
        Route::post('/update', [RetirementPlanController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [RetirementPlanController::class, 'destroy'])->name('delete');
    });

    Route::prefix('student-plans')->name('admin.student_plans.')->group(function () {
        Route::get('/', [StudentPlanController::class, 'index'])->name('index');
        Route::post('/store', [StudentPlanController::class, 'store'])->name('store');
        Route::post('/update', [StudentPlanController::class, 'update'])->name('update');
        Route::get('/delete/{id}', [StudentPlanController::class, 'destroy'])->name('delete');
    });

    // ===== SPOT ORDERS =====
    Route::get('/spot-orders', [SpotOrderController::class, 'index'])->name('admin.spot_orders');
    Route::post('/spot-orders/action', [SpotOrderController::class, 'action'])->name('admin.spot_orders.action');

    // Derivatives & DeFi Admin Routes
    Route::get('/futures/pairs', [FuturesController::class, 'pairsIndex'])->name('admin.futures.pairs.index');
    Route::get('/futures/positions', [FuturesController::class, 'positionsIndex'])->name('admin.futures.positions.index');

    Route::get('/margin/pairs', [MarginController::class, 'pairsIndex'])->name('admin.margin.pairs.index');
    Route::get('/margin/positions', [MarginController::class, 'positionsIndex'])->name('admin.margin.positions.index');

    Route::get('/p2p/listings', [P2pController::class, 'listingsIndex'])->name('admin.p2p.listings.index');
    Route::get('/p2p/orders', [P2pController::class, 'ordersIndex'])->name('admin.p2p.orders.index');

    Route::get('/liquidity/pools', [LiquidityController::class, 'poolsIndex'])->name('admin.liquidity.pools.index');
    Route::get('/liquidity/positions', [LiquidityController::class, 'positionsIndex'])->name('admin.liquidity.positions.index');

    Route::get('/launchpad/projects', [LaunchpadController::class, 'projectsIndex'])->name('admin.launchpad.projects.index');
    Route::get('/launchpad/participations', [LaunchpadController::class, 'participationsIndex'])->name('admin.launchpad.participations.index');

    Route::get('/loans/plans', [LoanController::class, 'plansIndex'])->name('admin.loans.plans.index');
    Route::get('/loans/positions', [LoanController::class, 'positionsIndex'])->name('admin.loans.positions.index');

    // Separated Asset Generation Pages
    Route::prefix('assets-generation')->name('admin.assets_gen.')->group(function () {
        Route::get('/crypto', [AssetManagementController::class, 'crypto'])->name('crypto');
        Route::post('/crypto/sync', [AssetManagementController::class, 'syncCrypto'])->name('crypto.sync');

        Route::get('/stocks', [AssetManagementController::class, 'stocks'])->name('stocks');
        Route::post('/stocks/sync', [AssetManagementController::class, 'syncStocks'])->name('stocks.sync');

        Route::get('/forex', [AssetManagementController::class, 'forex'])->name('forex');
        Route::post('/forex/sync', [AssetManagementController::class, 'syncForex'])->name('forex.sync');

        Route::post('/mass-delete', [AssetManagementController::class, 'massDelete'])->name('mass_delete');
        Route::post('/mass-edit-pl', [AssetManagementController::class, 'massEditProfitLoss'])->name('mass_edit_pl');
    });

    // cache clear
    Route::get('/clear-cache', [AdminController::class, 'clear_cache'])->name('admin.clear_cache');

    // admin logout
    Route::get('/logout',[AdminController::class, 'logout'])->name('admin.logout');

    // Manual Approvals for Trades
    Route::post('futures/approve/{id}', [\App\Http\Controllers\Admin\FuturesController::class, 'approvePosition'])->name('admin.futures.approve');
    Route::post('futures/reject/{id}', [\App\Http\Controllers\Admin\FuturesController::class, 'rejectPosition'])->name('admin.futures.reject');
    Route::post('margin/approve/{id}', [\App\Http\Controllers\Admin\MarginController::class, 'approvePosition'])->name('admin.margin.approve');
    Route::post('margin/reject/{id}', [\App\Http\Controllers\Admin\MarginController::class, 'rejectPosition'])->name('admin.margin.reject');
    Route::post('trades/approve/{id}', [\App\Http\Controllers\Admin\IndexController::class, 'approveTrade'])->name('admin.trades.approve');
    Route::post('trades/reject/{id}', [\App\Http\Controllers\Admin\IndexController::class, 'rejectTrade'])->name('admin.trades.reject');
});

