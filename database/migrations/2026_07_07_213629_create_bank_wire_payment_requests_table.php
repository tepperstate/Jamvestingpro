<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_wire_payment_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // User & Order
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            
            // Payment Method
            $table->string('payment_method', 30)->default('bank_wire');
            $table->string('currency', 3)->default('USD'); // USD, EUR, GBP, etc.
            
            // Amounts
            $table->decimal('amount', 24, 2);
            $table->string('fiat_currency', 3)->default('USD');
            $table->decimal('exchange_rate', 24, 10)->nullable();
            $table->decimal('fiat_amount', 24, 2)->nullable();
            
            // Bank Details (user provided)
            $table->string('bank_name', 255);
            $table->string('account_holder_name', 255);
            $table->string('account_number', 50);
            $table->string('routing_number')->nullable();      // ABA/routing for US
            $table->string('swift_bic', 11)->nullable();        // SWIFT/BIC
            $table->string('iban', 34)->nullable();             // IBAN
            $table->string('bank_address', 500)->nullable();
            $table->string('bank_country', 2)->default('US');   // ISO code
            $table->string('bank_city', 100)->nullable();
            $table->string('bank_state', 100)->nullable();
            $table->string('bank_zip', 20)->nullable();
            
            // Reference
            $table->string('wire_reference', 100)->nullable(); // User-provided reference
            $table->string('payment_reference')->nullable();   // System-generated
            
            // Status
            $table->string('status', 30)->default('pending');
            $table->string('finance_status', 30)->default('pending'); // pending, reviewed, approved, rejected
            $table->string('payment_status', 30)->default('pending'); // pending, sent, confirmed
            
            // Timing
            $table->timestamp('initiated_at');
            $table->timestamp('submitted_to_finance_at')->nullable();
            $table->timestamp('finance_reviewed_at')->nullable();
            $table->timestamp('payment_sent_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('expires_at')->index();
            
            // Notes & Audit
            $table->text('finance_notes')->nullable();
            $table->text('user_notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['user_id', 'status']);
            $table->index(['order_id', 'status']);
            $table->index(['uuid']);
            $table->index(['payment_reference']);
            $table->index(['status', 'expires_at']);
            $table->index(['finance_status']);
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_wire_payment_requests');
    }
};
