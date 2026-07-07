<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->boolean('email_verified')->default(false);
                $table->string('question')->nullable();
                $table->integer('otp')->default(1);
                $table->boolean('is_exempt_from_2fa')->default(false);
                $table->string('password')->nullable();
                $table->rememberToken();
                $table->string('type')->nullable();
                $table->string('status')->default('active');
                $table->boolean('hip_pro_override')->nullable();
                $table->unsignedBigInteger('package_id')->nullable();
                $table->boolean('is_demo')->default(false);
                $table->string('phone')->nullable();
                $table->string('country')->nullable();
                $table->string('package_plan')->nullable();
                $table->string('withdrawal')->nullable();
                $table->string('image')->nullable();
                $table->decimal('traded', 15, 2)->default(0);
                $table->integer('trades')->default(0);
                $table->decimal('highest_investment', 15, 2)->default(0);
                $table->string('user_id')->nullable();
                $table->string('currency')->default('USD');
                $table->string('custodia')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('packages')) {
            Schema::create('packages', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->decimal('amount', 15, 2)->default(0);
                $table->decimal('min_deposit', 15, 2)->default(0);
                $table->integer('trade')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('balances')) {
            Schema::create('balances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('symbol')->default('USD');
                $table->decimal('amount', 15, 2)->default(0);
                $table->decimal('demo', 15, 2)->default(0);
                $table->decimal('bitcoin', 15, 2)->default(0);
                $table->decimal('referral', 15, 2)->default(0);
                $table->string('name')->nullable();
                $table->string('image')->nullable();
                $table->decimal('bonus', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('stock_trades')) {
            Schema::create('stock_trades', function (Blueprint $table) {
                $table->id();
                $table->string('symbol')->nullable();
                $table->decimal('buy', 15, 2)->default(0);
                $table->decimal('sell', 15, 2)->default(0);
                $table->integer('changes')->default(0);
                $table->integer('volume')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('deposits')) {
            Schema::create('deposits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('status')->default('pending');
                $table->decimal('amount', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('notis')) {
            Schema::create('notis', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('title')->nullable();
                $table->string('message')->nullable();
                $table->string('status')->default('unread');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('email')->unique();
                $table->string('password')->nullable();
                $table->string('status')->default('active');
                $table->boolean('is_super_admin')->default(false);
                $table->boolean('is_2fa_exempt')->default(false);
                $table->text('data')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('activities')) {
            Schema::create('activities', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('activities')->nullable();
                $table->timestamps();
            });
        }

        $dummyTables = [
            'emails', 'orders', 'site_settings', 'botresults', 'copy_trade_orders',
            'corders', 'copy_trade_order', 'investment_history',
            'stock_balance', 'assets', 'blogs', 'bot_contracts',
            'bot_contracts_results', 'crypto_plans', 'forex_plans',
            'stock_plans', 'forex_investments', 'stock_investments',
            'crypto_investments', 'staking_investments', 'generate_signal', 'signalresults', 'default_package',
            'bots', 'signals', 'stocks', 'traders',
        ];

        foreach ($dummyTables as $tableName) {
            if (! Schema::hasTable($tableName)) {
                Schema::create($tableName, function (Blueprint $table) use ($tableName) {
                    $table->id();
                    $table->string('name')->nullable();
                    $table->string('plan')->nullable();
                    $table->decimal('amount', 15, 2)->default(0);
                    $table->decimal('min_deposit', 15, 2)->default(0);
                    $table->integer('trade')->default(0);
                    $table->string('slug')->nullable();

                    if ($tableName === 'bots') {
                        $table->decimal('min_profit', 8, 2)->default(0);
                        $table->decimal('max_profit', 8, 2)->default(0);
                        $table->decimal('min_loss', 8, 2)->default(0);
                        $table->decimal('max_loss', 8, 2)->default(0);
                    }
                    if ($tableName === 'assets' || $tableName === 'pairs') {
                        $table->string('symbols')->nullable();
                    }
                    if ($tableName === 'investment_history') {
                        $table->unsignedBigInteger('user_id')->nullable();
                        $table->string('status')->nullable();
                    }

                    $table->timestamps();
                });

                if ($tableName === 'default_package') {
                    DB::table('default_package')->insert([
                        'id' => 1,
                        'name' => 'Default',
                        'plan' => 'Basic Plan',
                    ]);
                }
            }
        }
    }

    public function down()
    {
        $dummyTables = [
            'emails', 'orders', 'site_settings', 'botresults', 'copy_trade_orders',
            'corders', 'copy_trade_order', 'investment_history',
            'stock_balance', 'assets', 'blogs', 'bot_contracts',
            'bot_contracts_results', 'crypto_plans', 'forex_plans',
            'stock_plans', 'forex_investments', 'stock_investments',
            'crypto_investments', 'staking_investments', 'generate_signal', 'signalresults', 'default_package',
            'bots', 'signals', 'stocks', 'traders',
        ];

        foreach ($dummyTables as $tableName) {
            Schema::dropIfExists($tableName);
        }

        Schema::dropIfExists('deposits');
        Schema::dropIfExists('stock_trades');
        Schema::dropIfExists('balances');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('users');
    }
};
