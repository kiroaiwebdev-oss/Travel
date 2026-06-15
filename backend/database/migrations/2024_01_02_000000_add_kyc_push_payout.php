<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // KYC on the user (required before payout in India).
        Schema::table('users', function (Blueprint $table) {
            $table->string('kyc_status', 20)->default('none')->index();   // none|pending|approved|rejected
            $table->string('kyc_full_name')->nullable();
            $table->string('kyc_pan', 20)->nullable();
            $table->string('kyc_payout_method', 20)->nullable();          // upi|bank|paypal
            $table->text('kyc_payout_details')->nullable();               // encrypted JSON
            $table->timestamp('kyc_submitted_at')->nullable();
            $table->timestamp('kyc_reviewed_at')->nullable();
            $table->foreignId('kyc_reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('kyc_note')->nullable();
        });

        // Web Push subscriptions (browser push the admin can trigger).
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('endpoint', 1024);
            $table->string('public_key')->nullable();   // p256dh
            $table->string('auth_token')->nullable();
            $table->timestamps();
            $table->index('user_id');
        });

        // Payout gateway tracking on withdrawals.
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->string('gateway', 30)->nullable()->after('method');         // manual|razorpay|paypal
            $table->string('gateway_payout_id')->nullable()->after('reference');
            $table->json('gateway_response')->nullable()->after('gateway_payout_id');
        });
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn(['gateway', 'gateway_payout_id', 'gateway_response']);
        });
        Schema::dropIfExists('push_subscriptions');
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('kyc_reviewed_by');
            $table->dropColumn([
                'kyc_status', 'kyc_full_name', 'kyc_pan', 'kyc_payout_method',
                'kyc_payout_details', 'kyc_submitted_at', 'kyc_reviewed_at', 'kyc_note',
            ]);
        });
    }
};
