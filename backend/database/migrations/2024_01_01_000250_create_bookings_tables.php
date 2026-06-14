<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// NOTE: runs BEFORE the wallet/cashback migration (000300) because `cashbacks`
// has a foreign key to `bookings`. MySQL requires the referenced table to exist
// first, so booking tables must be created earlier.
return new class extends Migration
{
    public function up(): void
    {
        // Affiliate click-outs. The signed redirect creates one of these.
        Schema::create('booking_clicks', function (Blueprint $table) {
            $table->id();
            $table->uuid('click_id')->unique();          // passed to provider as sub-id
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->string('category')->nullable();
            $table->string('offer_ref')->nullable();     // provider offer id
            $table->decimal('expected_amount', 15, 2)->nullable();
            $table->string('currency', 8)->default('INR');
            $table->string('session_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('landing_url', 1024)->nullable();
            $table->string('status', 20)->default('clicked'); // clicked|converted|expired
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            $table->index(['provider_id', 'created_at']);
            $table->index(['user_id', 'status']);
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booking_click_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category')->index();         // hotels, flights ...
            $table->string('external_ref')->nullable();  // provider's booking id
            $table->string('title')->nullable();         // "Taj Goa, 2 nights"
            $table->json('details')->nullable();          // normalized snapshot
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('commission_amount', 15, 2)->default(0);
            $table->string('currency', 8)->default('INR');
            // pending -> confirmed -> completed ; or cancelled/refunded
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('booked_at')->nullable();
            $table->timestamp('travel_date')->nullable();
            $table->timestamps();

            $table->unique(['provider_id', 'external_ref']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('booking_clicks');
    }
};
