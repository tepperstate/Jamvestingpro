<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ═══════════════════════════════════════════════
        // Table 1: System wallet addresses we monitor
        // ═══════════════════════════════════════════════
        Schema::create('monitored_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('blockchain', 20)->index();      // btc, eth, trx, bsc, sol...
            $table->string('network', 20)->default('mainnet'); // mainnet, testnet
            $table->string('address', 128)->index();         // wallet address
            $table->string('address_type', 20)->default('master');
            $table->string('label')->nullable();             // human-readable label
            $table->string('currency', 20)->index();         // BTC, ETH, USDT, etc.
            $table->string('token_contract', 128)->nullable(); // for tokens: contract address
            $table->string('token_standard', 10)->nullable();  // ERC20, TRC20, BEP20, SPL
            $table->boolean('is_active')->default(true);
            $table->string('derivation_path')->nullable();   // for HD wallets
            $table->unsignedBigInteger('user_id')->nullable(); // if assigned to specific user
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['blockchain', 'address', 'currency'], 'wallet_chain_address_currency');
            $table->index('is_active');
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        // ═══════════════════════════════════════════════
        // Table 2: Pending payment requests awaiting matching
        // ═══════════════════════════════════════════════
        Schema::create('pending_payment_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->string('gateway', 30)->nullable();       // oxapay, nowpayments, direct
            $table->string('gateway_payment_id')->nullable(); // external gateway ref
            
            // What we expect to receive
            $table->string('blockchain', 20)->index();       // btc, eth, trx...
            $table->string('currency', 20)->index();         // BTC, USDT, ETH...
            $table->decimal('expected_amount', 24, 10);      // crypto amount expected
            $table->decimal('fiat_amount', 16, 2)->nullable();// original fiat amount
            $table->string('fiat_currency', 5)->nullable();  // USD, EUR
            $table->decimal('exchange_rate', 24, 10)->nullable(); // rate at time of creation
            
            // Where to receive
            $table->string('deposit_address', 128)->index(); // our address user sends to
            $table->unsignedBigInteger('monitored_wallet_id')->nullable();
            
            // Amount matching tolerance
            $table->decimal('amount_tolerance_pct', 5, 2)->default(2.00);
            $table->decimal('amount_min', 24, 10);           // expected - tolerance
            $table->decimal('amount_max', 24, 10);           // expected + tolerance
            
            // Timing
            $table->timestamp('initiated_at');
            $table->timestamp('expires_at')->index();
            $table->unsignedInteger('ttl_minutes')->default(60);
            
            // Status
            $table->string('status', 30)->default('pending')->index();
            
            // If matched
            $table->unsignedBigInteger('matched_transaction_id')->nullable();
            $table->timestamp('matched_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('monitored_wallet_id')
                  ->references('id')->on('monitored_wallets')
                  ->nullOnDelete();
            $table->index(['status', 'expires_at']);
            $table->index(['deposit_address', 'status']);
            $table->index(['blockchain', 'currency', 'status']);
        });

        // ═══════════════════════════════════════════════
        // Table 3: Discovered blockchain transactions
        // ═══════════════════════════════════════════════
        Schema::create('blockchain_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('blockchain', 20)->index();
            $table->string('network', 20)->default('mainnet');
            $table->string('tx_hash', 128)->index();
            $table->unsignedBigInteger('block_number')->nullable()->index();
            $table->string('block_hash')->nullable();
            $table->timestamp('block_timestamp')->nullable();
            
            // Transaction details
            $table->string('from_address', 128)->nullable()->index();
            $table->string('to_address', 128)->index();
            $table->string('currency', 20)->index();
            $table->string('token_contract', 128)->nullable();
            $table->decimal('amount', 36, 18);               // raw amount in token units
            $table->decimal('amount_decimal', 24, 10);        // human-readable amount
            $table->unsignedInteger('token_decimals')->nullable();
            
            // Gas/fees
            $table->decimal('fee', 24, 10)->nullable();
            $table->string('fee_currency', 10)->nullable();
            
            // Confirmation tracking
            $table->unsignedInteger('confirmations')->default(0);
            $table->unsignedInteger('required_confirmations');
            $table->boolean('is_confirmed')->default(false)->index();
            $table->timestamp('first_seen_at');
            $table->timestamp('confirmed_at')->nullable();
            
            // Matching
            $table->unsignedBigInteger('matched_payment_id')->nullable()->index();
            $table->string('match_status', 20)->default('unmatched');
            $table->decimal('match_confidence', 5, 2)->nullable(); // 0-100 score
            
            // Processing
            $table->string('scan_source', 30);               // mempool, block_scan, webhook, manual
            $table->boolean('is_processed')->default(false);
            $table->json('raw_data')->nullable();             // full API response
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            $table->unique(['blockchain', 'tx_hash', 'to_address'], 'unique_chain_tx_to');
            $table->foreign('matched_payment_id')
                  ->references('id')->on('pending_payment_requests')
                  ->nullOnDelete();
            $table->index(['to_address', 'currency', 'is_confirmed']);
            $table->index(['match_status', 'is_processed']);
        });

        // ═══════════════════════════════════════════════
        // Table 4: Scan checkpoints (where we left off)
        // ═══════════════════════════════════════════════
        Schema::create('blockchain_scan_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->string('blockchain', 20);
            $table->string('network', 20)->default('mainnet');
            $table->string('scanner_type', 30);              // block, address, mempool
            $table->string('address', 128)->nullable();      // specific address if per-address scan
            $table->unsignedBigInteger('last_block_number')->nullable();
            $table->string('last_block_hash')->nullable();
            $table->string('last_tx_hash')->nullable();
            $table->timestamp('last_scan_at')->nullable();
            $table->unsignedInteger('scan_count')->default(0);
            $table->unsignedInteger('consecutive_errors')->default(0);
            $table->text('last_error')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['blockchain', 'network', 'scanner_type', 'address'], 
                           'checkpoint_unique');
        });

        // ═══════════════════════════════════════════════
        // Table 5: Scan activity log (audit trail)
        // ═══════════════════════════════════════════════
        Schema::create('blockchain_scan_logs', function (Blueprint $table) {
            $table->id();
            $table->string('blockchain', 20);
            $table->string('event_type', 30);
            $table->string('severity', 10)->default('info'); // info, warning, error, critical
            $table->text('message');
            $table->string('tx_hash')->nullable();
            $table->unsignedBigInteger('payment_request_id')->nullable();
            $table->unsignedBigInteger('blockchain_transaction_id')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();
            
            $table->index(['blockchain', 'event_type', 'created_at']);
            $table->index('severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blockchain_scan_logs');
        Schema::dropIfExists('blockchain_scan_checkpoints');
        Schema::dropIfExists('blockchain_transactions');
        Schema::dropIfExists('pending_payment_requests');
        Schema::dropIfExists('monitored_wallets');
    }
};
