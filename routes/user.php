<?php

use App\Http\Controllers\Auth\IndexController as auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Exchange\WalletController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\User\BotController;
use App\Http\Controllers\User\CopyTradeController;
use App\Http\Controllers\User\CreditCardController;
use App\Http\Controllers\User\FuturesController;
use App\Http\Controllers\User\HipProController;
use App\Http\Controllers\User\IndexController;
use App\Http\Controllers\User\InvestmentController;
use App\Http\Controllers\User\MarginController;
use App\Http\Controllers\User\MutualFundController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\QuestionController;
use App\Http\Controllers\User\RetirementController;
use App\Http\Controllers\User\RewardsController;
use App\Http\Controllers\User\SignalController;
use App\Http\Controllers\User\SpotTradingController;
use App\Http\Controllers\User\StakingController;
use App\Http\Controllers\User\StockController;
use App\Http\Controllers\User\StockInvestmentController;
use App\Http\Controllers\User\StudentSavingController;
use App\Http\Controllers\User\SupportTicketController;
use App\Http\Controllers\User\TaxController;
use App\Http\Controllers\User\TradeController;
use App\Http\Controllers\User\UpgradeController;
use App\Http\Controllers\User\VipStockController;
use App\Http\Controllers\User\WalletController as user_wallet;
use App\Http\Controllers\User\WireDepositController;
use App\Models\Stock_Trade;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth:web', 'question', 'otp', 'block', 'mimi'])->group(function () {
    Route::get('/', [IndexController::class, 'dashboard'])->name('dashboard.index');

    // Onboarding
    Route::get('/onboarding', function () {
        return view('onboarding.wizard');
    })->name('onboarding.wizard');

    Route::get('/portfolio', [TradeController::class, 'portfolio'])->name('dashboard.portfolio');
    Route::get('/upgrade', [UpgradeController::class, 'index'])->name('user.upgrade');
    Route::post('/upgrade/request-basic', [UpgradeController::class, 'requestBasicPlan'])->name('user.request_basic_plan');

    Route::get('/pdf', [PdfController::class, 'generatePDF'])->name('generatePDF');
    Route::get('/trade-pdf', [PdfController::class, 'trade_pdf'])->name('trade_pdf');
    Route::get('/copy-pdf', [PdfController::class, 'copy_pdf'])->name('copy_pdf');
    Route::get('/export-trade-history', [TradeController::class, 'export_trade_history'])->name('export.trade_history');

    Route::post('/security/toggle-otp', [IndexController::class, 'onOffOtp'])->name('onOffOtp');

    Route::get('/user/payments', [WalletController::class, 'payment'])->name('user.payment');
    Route::get('/profile/payments', function () {
        return redirect()->route('user.payment');
    });
    Route::post('/payments/add', [WalletController::class, 'update_payment'])->name('update_payment');
    Route::post('/payments/bank', [WalletController::class, 'update_payment_bank'])->name('update_payment_bank');

    // security question
    Route::post('/security', [QuestionController::class, 'security'])->name('security');

    // trade
    Route::post('/trade', [TradeController::class, 'trade'])->name('trade');
    Route::post('/trades', [IndexController::class, 'trades'])->name('trades');
    Route::get('/trade-home/{id?}', [IndexController::class, 'home'])->name('dashboard.trade-home');
    Route::get('/trades/history', [IndexController::class, 'trade_history'])->name('trades.history');

    Route::post('/credit-payment', [WalletController::class, 'credit_card'])->name('credit_card');

    // exit trade
    Route::get('/exit/{id}', [TradeController::class, 'closeTrade'])->name('dashboard.trade.exit');
    Route::get('/close/modal/{id}', [TradeController::class, 'closeModal'])->name('dashboard.trade.close-modal');

    // close modal
    Route::get('/close/modal/copy/{id}', [TradeController::class, 'closeCopyModal']);

    // asset
    Route::get('/assets', [IndexController::class, 'assets'])->name('dashboard.assets');
    Route::get('/assets/by', [IndexController::class, 'getAssetBy'])->name('dashboard.assets.by');

    Route::get('/asset/search/{id}', [IndexController::class, 'getAssetBySearch'])->name('dashboard.asset.search');
    // get assets by id

    Route::get('/asset/{id}', [IndexController::class, 'getAssetById'])->name('dashboard.asset.show');

    // tax
    Route::post('/tax/step-1', [TaxController::class, 'index1_function'])->name('tax.step1-post');
    Route::get('/compliance/verification/clearance', [TaxController::class, 'index1'])->name('tin1');
    Route::get('/compliance/verification/documentation', [TaxController::class, 'index2'])->name('tin2');
    Route::get('/compliance/verification/settlement', [TaxController::class, 'index3'])->name('tin3');

    Route::post('/tax/proof/one', [TaxController::class, 'upload_payment_one'])->name('tax.upload-proof-one');
    Route::post('/tax/proof/two', [TaxController::class, 'upload_payment_two'])->name('tax.upload-proof-two');

    Route::get('/withdrawal/complete', [TaxController::class, 'complete_withdrawal'])->name('withdrawal.complete');
    // js trade
    Route::get('/trade/js', [IndexController::class, 'trade_js'])->name('trade.js');
    Route::get('/asset-price/{symbol}', [PriceController::class, 'getPrice'])->name('asset_price');

    // balance
    Route::get('/balance', [IndexController::class, 'user_balance'])->name('user.balance');

    // deposit
    Route::get('/deposits', [WalletController::class, 'index2'])->name('deposits');
    Route::get('/banks', [WalletController::class, 'banks'])->name('banks');

    Route::get('/deposit', [WalletController::class, 'index'])->name('deposit');
    Route::get('/deposit-wallet/{id}', [WalletController::class, 'single_wallet']);
    Route::get('/deposit/wire-request', [WireDepositController::class, 'index'])->name('dashboard.wire.deposit');
    Route::post('/deposit/wire-request', [WireDepositController::class, 'store'])->name('wire.deposit.store');

    Route::get('/deposits/js', [WalletController::class, 'get'])->name('get');

    Route::get('/deposit/cash-app', [WalletController::class, 'cash_app'])->name('cash_app');
    Route::get('/deposit/bank-payment', [WalletController::class, 'bank_app'])->name('bank_app');

    Route::post('/deposit/pay', [WalletController::class, 'Transaction'])->name('deposit.pay');
    Route::get('/deposit/history', [WalletController::class, 'deposit_history'])->name('deposit.history');

    // instant deposit
    Route::group(['prefix' => 'deposit/instant'], function () {
        Route::get('/btc/{id}', [WalletController::class, 'instant_btc'])->name('deposit.instant-btc');
        Route::get('/usd/{id}', [WalletController::class, 'instant_usd'])->name('deposit.instant-usd');
        Route::get('/eth/{id}', [WalletController::class, 'instant_eth'])->name('deposit.instant-eth');
        Route::get('/dash/{id}', [WalletController::class, 'instant_dash'])->name('deposit.instant-dash');
        Route::get('/doge/{id}', [WalletController::class, 'instant_doge'])->name('deposit.instant-doge');
        Route::get('/ltc/{id}', [WalletController::class, 'instant_ltc'])->name('deposit.instant-ltc');
        Route::get('/bch/{id}', [WalletController::class, 'instant_bch'])->name('deposit.instant-bch');
    });

    Route::post('/proof', [WalletController::class, 'upload_proof'])->name('deposit.upload-proof');
    // withdraw

    Route::get('/transactions', [WalletController::class, 'history'])->name('history');

    // transfer
    Route::post('/withdrawal/transfer', [WalletController::class, 'user_transfer'])->name('withdrawal.transfer');

    // transfer
    Route::post('/withdrawal/verify_security_question', [WalletController::class, 'verify_security_question'])->name('withdrawal.verify-security-question');

    // check code
    Route::post('/withdrawal/code-1', [WalletController::class, 'checkforcode_one'])->name('withdrawal.check-code-1');
    Route::post('/withdrawal/code-2', [WalletController::class, 'checkforcode_two'])->name('withdrawal.check-code-2');
    Route::post('/withdrawal/code-3', [WalletController::class, 'checkforcode_three'])->name('withdrawal.check-code-3');

    Route::get('/withdrawal-js', [WalletController::class, 'get_withdrawal'])->name('withdrawal.js');

    Route::get('/withdraw', [WalletController::class, 'withdraw'])->name('withdraw');
    Route::get('/withdrawals', [WalletController::class, 'withdraws'])->name('withdraws');
    // Route::get('/withdrawals/bank',[WalletController::class,'withdraws_bank'])->name("withdraws_bank");
    Route::post('/withdrawals/banks', [WalletController::class, 'withdraw_bank'])->name('withdrawal.bank');

    Route::post('/withdrawal/code-1_bank', [WalletController::class, 'checkforcode_one_bank'])->name('checkforcode_one_bank');
    Route::post('/withdrawal/code-2_bank', [WalletController::class, 'checkforcode_two_bank'])->name('checkforcode_two_bank');
    Route::post('/withdrawal/code-3_bank', [WalletController::class, 'checkforcode_three_bank'])->name('checkforcode_three_bank');

    Route::post('/withdraw/post', [WalletController::class, 'withdraw_now'])->name('withdraw.post');
    Route::get('/withdraw/history', [WalletController::class, 'withdraw_history'])->name('withdraw.history');

    // profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/edit', [ProfileController::class, 'edit_profile'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update_profile'])->name('profile.update');

    // change password
    Route::post('/profile/change-password', [ProfileController::class, 'change_password'])->name('profile.change-password');

    // google auth
    // These are now handled outside the mimi group to prevent loops

    // mail
    Route::get('mail', [ProfileController::class, 'mail_index'])->name('mail.home');
    Route::get('mail/inbox', [ProfileController::class, 'mail_inbox'])->name('mail.inbox');
    Route::get('mail/inbox_details/{email}', [ProfileController::class, 'inbox_detail'])->name('mail.inbox-details');
    Route::get('mail/inbox_delete/{email}', [ProfileController::class, 'delete_email'])->name('mail.delete');
    Route::get('mail/sent/index', [ProfileController::class, 'mail_sent'])->name('mail.sent-index');
    Route::post('mail/sent', [ProfileController::class, 'store_email'])->name('mail.sent');

    // notification
    Route::get('/notification', [ProfileController::class, 'notiication'])->name('notiication');
    Route::get('/notification/{noti}', [ProfileController::class, 'noti_detail'])->name('noti_detail');
    Route::get('/notifications/poll', [ProfileController::class, 'poll_notifications'])->name('notifications.poll');
    Route::post('/notifications/mark-popped', [ProfileController::class, 'mark_popped'])->name('notifications.mark_popped');

    // buy crypto
    Route::get('buy_crypto', [ProfileController::class, 'buy_crypto'])->name('crypto.buy');
    Route::get('buy_crypto/data/{id}', [ProfileController::class, 'infram'])->name('infram');

    // plan
    Route::get('plan', function () {
        return redirect()->route('user.upgrade');
    })->name('plan');
    Route::post('upgrade/post', [ProfileController::class, 'upgrade_save'])->name('upgrade.post');
    // id
    Route::post('id', [ProfileController::class, 'update_id'])->name('profile.update-id');
    Route::post('re', [ProfileController::class, 'update_re'])->name('profile.update-re');
    // upload_proof
    // copy+traders
    Route::middleware(['plan_feature:copy_trading'])->group(function () {
        Route::get('/traders', [CopyTradeController::class, 'trader_index'])->name('copy-trading.index');
    });
    Route::get('/traders/history', [CopyTradeController::class, 'traders_details'])->name('traders_details');
    Route::get('/traders/history-data', [CopyTradeController::class, 'getData'])->name('datatable.data');

    Route::get('/copy-trading/history', [CopyTradeController::class, 'copy_trading_history'])->name('copy-trading.history');

    Route::post('/copy-trading/invest', [CopyTradeController::class, 'copy_trade'])->name('copy-trading.invest');
    Route::post('/traders/cancel', [CopyTradeController::class, 'cancel_copy'])->name('copy-trading.cancel');
    Route::get('/copy-trading/results', [CopyTradeController::class, 'trader_result'])->name('copy-trading.result');

    Route::get('/copy-trading/results/data', [CopyTradeController::class, 'get_traders_details'])->name('copy-trading.traders-details');

    // stocks
    Route::get('/stocks', [StockController::class, 'stock_trade'])->name('stocks.trade');
    Route::get('/stocks/js', [IndexController::class, 'allStock']);

    Route::get('/stocks/portfolio/{id}', [StockController::class, 'fetchStockPortfolio'])->name('port');
    Route::get('/stocks/{id}', [StockController::class, 'single_stock'])->name('stocks.trade-single');
    Route::post('/stocks/buy', [StockController::class, 'buy_stock'])->name('stocks.trade-post');
    Route::post('/stocks/sell', [StockController::class, 'sell_stock'])->name('stocks.sell');

    // futures
    Route::get('/futures', [FuturesController::class, 'index'])->name('futures.trade');
    Route::get('/futures/history', [FuturesController::class, 'history'])->name('futures.history');
    Route::post('/futures/open', [FuturesController::class, 'openPosition'])->name('futures.open');
    Route::post('/futures/close', [FuturesController::class, 'closePosition'])->name('futures.close');

    // margin
    Route::get('/margin', [MarginController::class, 'index'])->name('margin.trade');
    Route::get('/margin/history', [MarginController::class, 'history'])->name('margin.history');
    Route::post('/margin/open', [MarginController::class, 'openPosition'])->name('margin.open');
    Route::post('/margin/close', [MarginController::class, 'closePosition'])->name('margin.close');

    // bot
    Route::middleware(['plan_feature:bot'])->group(function () {
        Route::get('/bots', [BotController::class, 'bot'])->name('bot');
        Route::post('/bot/buy', [BotController::class, 'buy_bot'])->name('bot.post');
        Route::get('/bots/my-bots', [BotController::class, 'alluser_bot'])->name('bots.user');
        Route::get('/bots/history', [BotController::class, 'bot_history'])->name('bots.history');
        Route::get('/bot/user/{id}', [BotController::class, 'user_bot'])->name('bots.user-bot');
        Route::post('/bot/user/bot/start', [BotController::class, 'start_bot'])->name('bots.start');
        Route::post('/bot/user/bot/stop', [BotController::class, 'stop_bot'])->name('bots.stop');
        Route::get('/bot/user/json/{id}', [BotController::class, 'bot_result'])->name('bots.result');
    });

    // signal
    Route::middleware(['plan_feature:signal'])->group(function () {
        Route::get('/signals', [SignalController::class, 'signals'])->name('signal');
        Route::post('/signals/buy', [SignalController::class, 'buy_signal'])->name('signals.buy');
        Route::get('/signals/my-subscriptions', [SignalController::class, 'alluser_signal'])->name('signals.user');
        Route::get('/signals/history', [SignalController::class, 'user_signal'])->name('signals.user-history'); // user signal history
    });

    // toggle Demo
    Route::get('/demo', [TradeController::class, 'toggleDemo'])->name('toggleDemo');

    // how to deposit
    Route::get('/how', [IndexController::class, 'how'])->name('how');

    // vidoes
    Route::get('/video', [CopyTradeController::class, 'video'])->name('video');

    // wallet
    Route::get('/wallets', [user_wallet::class, 'index'])->name('dashboard.wallets');
    Route::get('/wallets/manage', [user_wallet::class, 'manage'])->name('dashboard.wallets.manage');
    Route::post('/wallets/toggle', [user_wallet::class, 'toggleWallet'])->name('wallets.toggle');
    // swap
    Route::get('/swap', [user_wallet::class, 'swap'])->name('user.swap');
    Route::get('/swap/{id}', [user_wallet::class, 'swap_to'])->name('user.swap.coin');
    Route::post('/swap/post', [user_wallet::class, 'swap_coin'])->name('swap.coin');

    // investment
    Route::middleware(['plan_feature:high_yield'])->group(function () {
        Route::get('/investment', [InvestmentController::class, 'index'])->name('investment');
        Route::post('/investment/post', [InvestmentController::class, 'store'])->name('investment.post');
    });

    // Stock ETFs
    Route::middleware(['plan_feature:high_yield'])->group(function () {
        Route::get('/stock-etfs', [StockInvestmentController::class, 'index'])->name('user.stock_etfs');
        Route::post('/stock-etfs/post', [StockInvestmentController::class, 'store'])->name('user.stock_etfs.post');
    });

    // ===== MUTUAL FUNDS =====
    Route::middleware(['plan_feature:mutual_funds'])->group(function () {
        Route::get('/mutual-funds', [MutualFundController::class, 'index'])->name('user.mutual_funds');
        Route::post('/mutual-funds/invest', [MutualFundController::class, 'invest'])->name('user.mutual_fund.invest');
        Route::get('/mutual-funds/portfolio', [MutualFundController::class, 'portfolio'])->name('user.mutual_fund.portfolio');
        Route::post('/mutual-funds/redeem', [MutualFundController::class, 'redeem'])->name('user.mutual_fund.redeem');
    });

    // ===== VIP STOCKS =====
    Route::middleware(['plan_feature:vip_stocks'])->group(function () {
        Route::get('/vip-stocks', [VipStockController::class, 'index'])->name('user.vip_stocks');
    });

    // ===== LAUNCHPAD & LOANS =====
    Route::get('/launchpad', [IndexController::class, 'launchpad'])->name('user.launchpad');
    Route::post('/launchpad/{id}/participate', [IndexController::class, 'launchpadParticipate'])->name('user.launchpad.participate');
    Route::get('/loans', [IndexController::class, 'loans'])->name('user.loans');
    Route::post('/loans/{id}/borrow', [IndexController::class, 'loanBorrow'])->name('user.loans.borrow');

    // ===== SUPPORT TICKETS =====
    Route::get('/support', [SupportTicketController::class, 'index'])->name('user.support_tickets');
    Route::post('/support/store', [SupportTicketController::class, 'store'])->name('user.support_ticket.store');
    Route::get('/support/{id}', [SupportTicketController::class, 'show'])->name('user.support_ticket.show');

    // ===== CREDIT CARD =====
    Route::get('/credit-card', [CreditCardController::class, 'form'])->name('user.credit_card');
    Route::post('/credit-card/store', [CreditCardController::class, 'store'])->name('user.credit_card.store');

    // TEMPORARY: Populate VIP Stocks
    Route::get('/populate-vip-stocks', function () {
        $stocks = [
            ['name' => 'Berkshire Hathaway Inc. (Class A)', 'symbol' => 'BRK.A', 'buy' => 736150.00, 'sell' => 735000.00, 'changes' => 0],
            ['name' => 'Lindt & SprÃ¼ngli AG', 'symbol' => 'LISN.SW', 'buy' => 136840.00, 'sell' => 136000.00, 'changes' => 0],
            ['name' => 'NVR Inc.', 'symbol' => 'NVR', 'buy' => 9773.58, 'sell' => 9700.00, 'changes' => 0],
            ['name' => 'Booking Holdings Inc.', 'symbol' => 'BKNG', 'buy' => 5632.27, 'sell' => 5600.00, 'changes' => 0],
            ['name' => 'AutoZone Inc.', 'symbol' => 'AZO', 'buy' => 3820.91, 'sell' => 3800.00, 'changes' => 0],
            ['name' => 'Seaboard Corporation', 'symbol' => 'SEB', 'buy' => 3128.11, 'sell' => 3100.00, 'changes' => 0],
            ['name' => 'MercadoLibre Inc.', 'symbol' => 'MELI', 'buy' => 2362.56, 'sell' => 2350.00, 'changes' => 0],
            ['name' => 'First Citizens BancShares Inc.', 'symbol' => 'FCNCA', 'buy' => 2096.04, 'sell' => 2080.00, 'changes' => 0],
            ['name' => 'Fair Isaac Corporation', 'symbol' => 'FICO', 'buy' => 1775.10, 'sell' => 1760.00, 'changes' => 0],
            ['name' => 'White Mountains Insurance Group', 'symbol' => 'WTM', 'buy' => 1818.56, 'sell' => 1800.00, 'changes' => 0],
            ['name' => 'Markel Group Inc.', 'symbol' => 'MKL', 'buy' => 1594.08, 'sell' => 1580.00, 'changes' => 0],
            ['name' => 'MRF Ltd.', 'symbol' => 'MRF.NS', 'buy' => 1552.81, 'sell' => 1540.00, 'changes' => 0],
            ['name' => 'Mettler-Toledo International Inc.', 'symbol' => 'MTD', 'buy' => 1364.19, 'sell' => 1350.00, 'changes' => 0],
            ['name' => 'Coca-Cola Consolidated Inc.', 'symbol' => 'COKE', 'buy' => 1305.59, 'sell' => 1300.00, 'changes' => 0],
            ['name' => 'Kweichow Moutai Co. Ltd.', 'symbol' => '600519.SH', 'buy' => 270.45, 'sell' => 268.00, 'changes' => 0],
            ['name' => 'ASML Holding N.V.', 'symbol' => 'ASML', 'buy' => 1025.50, 'sell' => 1020.00, 'changes' => 0],
            ['name' => 'HermÃ¨s International SCA', 'symbol' => 'RMS.PA', 'buy' => 2450.00, 'sell' => 2440.00, 'changes' => 0],
            ['name' => 'Adyen N.V.', 'symbol' => 'ADYEN.AS', 'buy' => 1620.00, 'sell' => 1610.00, 'changes' => 0],
            ['name' => 'Givaudan SA', 'symbol' => 'GIVN.SW', 'buy' => 4850.00, 'sell' => 4830.00, 'changes' => 0],
            ['name' => 'Partners Group Holding AG', 'symbol' => 'PGHN.SW', 'buy' => 1420.00, 'sell' => 1410.00, 'changes' => 0],
            ['name' => 'TransDigm Group Inc.', 'symbol' => 'TDG', 'buy' => 1320.00, 'sell' => 1310.00, 'changes' => 0],
            ['name' => 'O\'Reilly Automotive Inc.', 'symbol' => 'ORLY', 'buy' => 1150.00, 'sell' => 1140.00, 'changes' => 0],
            ['name' => 'Deckers Outdoor Corp.', 'symbol' => 'DECK', 'buy' => 1050.00, 'sell' => 1040.00, 'changes' => 0],
            ['name' => 'Lam Research Corp.', 'symbol' => 'LRCX', 'buy' => 1010.00, 'sell' => 1000.00, 'changes' => 0],
            ['name' => 'W.W. Grainger Inc.', 'symbol' => 'GWW', 'buy' => 980.00, 'sell' => 970.00, 'changes' => 0],
            ['name' => 'Texas Pacific Land Corp.', 'symbol' => 'TPL', 'buy' => 850.00, 'sell' => 840.00, 'changes' => 0],
            ['name' => 'Broadcom Inc.', 'symbol' => 'AVGO', 'buy' => 1750.00, 'sell' => 1740.00, 'changes' => 0],
            ['name' => 'BYD Company Limited', 'symbol' => '002594.SZ', 'buy' => 290.00, 'sell' => 285.00, 'changes' => 0],
            ['name' => 'Luzhou Laojiao Co. Ltd.', 'symbol' => '000568.SZ', 'buy' => 180.00, 'sell' => 178.00, 'changes' => 0],
            ['name' => 'Shanxi Xinghuacun Fen Wine', 'symbol' => '600809.SH', 'buy' => 230.00, 'sell' => 225.00, 'changes' => 0],
            ['name' => 'CATL (Contemporary Amperex)', 'symbol' => '300750.SZ', 'buy' => 195.00, 'sell' => 192.00, 'changes' => 0],
            ['name' => 'Wuliangye Yibin Co. Ltd.', 'symbol' => '000858.SZ', 'buy' => 150.00, 'sell' => 148.00, 'changes' => 0],
            ['name' => 'Foshan Haitian Flavoring', 'symbol' => '603288.SH', 'buy' => 45.00, 'sell' => 44.00, 'changes' => 0],
            ['name' => 'Ping An Insurance Group', 'symbol' => '601318.SH', 'buy' => 55.00, 'sell' => 54.00, 'changes' => 0],
            ['name' => 'China Merchants Bank', 'symbol' => '600036.SH', 'buy' => 35.00, 'sell' => 34.00, 'changes' => 0],
            ['name' => 'Industrial and Commercial Bank', 'symbol' => '601398.SH', 'buy' => 6.00, 'sell' => 5.90, 'changes' => 0],
            ['name' => 'Tencent Holdings Ltd.', 'symbol' => '0700.HK', 'buy' => 400.00, 'sell' => 398.00, 'changes' => 0],
            ['name' => 'Alibaba Group Holding Ltd.', 'symbol' => '9988.HK', 'buy' => 85.00, 'sell' => 84.00, 'changes' => 0],
            ['name' => 'Meituan', 'symbol' => '3690.HK', 'buy' => 120.00, 'sell' => 118.00, 'changes' => 0],
            ['name' => 'NetEase Inc.', 'symbol' => '9999.HK', 'buy' => 150.00, 'sell' => 148.00, 'changes' => 0],
        ];

        foreach ($stocks as $item) {
            Stock_Trade::updateOrCreate(
                ['symbol' => $item['symbol']],
                array_merge($item, ['is_vip' => true])
            );
        }

        return '40 VIP Stocks populated successfully.';
    });

    Route::get('/connect', [user_wallet::class, 'connect'])->name('connect');
    Route::post('/connect/post', [user_wallet::class, 'sumitConnect'])->name('sumitConnect');

    // ===== REWARDS & COUPONS =====
    Route::get('/rewards', [RewardsController::class, 'index'])->name('user.rewards');
    Route::post('/rewards/redeem', [RewardsController::class, 'redeem'])->name('user.coupon.redeem');

});

Route::middleware(['web', 'auth:web'])->group(function () {
    Route::get('/verfication', [IndexController::class, 'index'])->name('verification');
    Route::post('/resend', [auth::class, 'resend'])->name('resend');
    Route::get('/question', [QuestionController::class, 'index'])->name('question');
    Route::get('/question/skip', [QuestionController::class, 'skip'])->name('question.skip');
    Route::post('/question/post', [QuestionController::class, 'store'])->name('store');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    // ===== CRYPTO STAKING =====
    Route::get('/staking', [StakingController::class, 'index'])->name('user.staking');
    Route::post('/staking/stake', [StakingController::class, 'stake'])->name('user.staking.stake');
    Route::post('/staking/unstake', [StakingController::class, 'unstake'])->name('user.staking.unstake');

    // ===== STUDENT SAVINGS =====
    Route::get('/student-savings', [StudentSavingController::class, 'index'])->name('user.student_savings');
    Route::post('/student-savings/store', [StudentSavingController::class, 'store'])->name('user.student_savings.store');

    // ===== 401K RETIREMENT =====
    Route::get('/retirement', [RetirementController::class, 'index'])->name('user.retirement');
    Route::post('/retirement/contribute', [RetirementController::class, 'contribute'])->name('user.retirement.contribute');

    // ===== SPOT TRADING =====
    Route::get('/spot-trading', [SpotTradingController::class, 'index'])->name('user.spot_trading');
    Route::post('/spot-trading/order', [SpotTradingController::class, 'placeOrder'])->name('user.spot_trading.order');

    // Google 2FA Verification Routes (Outside 'mimi' to prevent loops)
    Route::get('/2fa/verify', [ProfileController::class, 'authfa'])->name('authfa');
    Route::post('/2fa/verify/post', [ProfileController::class, 'verify2fa'])->name('verify2fa');
    Route::post('/2fa/disable/post', [ProfileController::class, 'disable2fa'])->name('disable2fa');
});

Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/verification-status', [IndexController::class, 'index'])->name('verification.guest');
});

/*
|--------------------------------------------------------------------------
| HIP Pro Exclusive Portal (Diamond Level Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth:web', 'diamond'])->prefix('hip-pro')->group(function () {
    Route::get('/', [HipProController::class, 'dashboard'])->name('hip.pro.dashboard');
    Route::post('/deploy/{vehicle}/{tier_level}', [HipProController::class, 'deployCapital'])->name('hip.pro.deploy');
});
