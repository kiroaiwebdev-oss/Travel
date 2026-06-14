<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('currency', 8)->default('INR');
            // Cached projections, reconciled from wallet_transactions ledger.
            $table->decimal('balance', 15, 2)->default(0);          // withdrawable now
            $table->decimal('pending_balance', 15, 2)->default(0);  // not yet confirmed
            $table->decimal('lifetime_earned', 15, 2)->default(0);
            $table->decimal('lifetime_withdrawn', 15, 2)->default(0);
            $table->timestamps();
        });

        // Append-only ledger. Every balance change is a row here (double-entry style).
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);          // cashback_credit, withdrawal_debit, referral_credit, adjustment, reversal
            $table->string('direction', 6);      // credit | debit
            $table->decimal('amount', 15, 2);
            $table->string('currency', 8)->default('INR');
            $table->decimal('balance_after', 15, 2);
            $table->nullableMorphs('source');    // cashback / withdrawal / referral
            $table->string('idempotency_key')->nullable()->unique(); // prevent double posting
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index('created_at');
        });

        // Configurable cashback rules. Most specific match wins (see CashbackRuleResolver).
        Schema::create('cashback_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('provider_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('category')->nullable();   // null = any category
            $table->string('type', 20);               // percentage | fixed
            $table->decimal('value', 10, 2);          // % share of commission OR fixed amount
            $table->decimal('max_cap', 12, 2)->nullable();
            $table->decimal('min_booking_amount', 12, 2)->default(0);
            $table->unsignedInteger('priority')->default(100); // lower wins on tie
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['provider_id', 'category', 'is_active']);
        });

        Schema::create('cashbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cashback_rule_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category')->nullable();
            $table->decimal('booking_amount', 15, 2)->default(0);
            $table->decimal('commission_amount', 15, 2)->default(0); // what we earned
            $table->decimal('amount', 15, 2);                        // cashback to the user
            $table->string('currency', 8)->default('INR');
            // pending -> confirmed -> withdrawable -> paid ; or -> rejected
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('matures_at')->nullable(); // becomes withdrawable after hold
            $table->timestamp('rejected_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 8)->default('INR');
            $table->string('method', 30);        // upi | bank | paypal | voucher
            $table->json('payout_details');      // encrypted
            $table->string('status', 20)->default('requested')->index(); // requested|approved|processing|paid|rejected
            $table->string('reference')->nullable();
            $table->string('admin_note')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('cashbacks');
        Schema::dropIfExists('cashback_rules');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
