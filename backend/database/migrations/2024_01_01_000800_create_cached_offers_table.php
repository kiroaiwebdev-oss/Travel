<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Normalized provider offers. Source of truth for the Meilisearch "offers"
        // index (Scout syncs from here). Also serves as a DB fallback if the search
        // engine is unavailable. Refreshed on a TTL by the search workers.
        Schema::create('cached_offers', function (Blueprint $table) {
            $table->id();
            $table->string('offer_hash', 64)->unique(); // dedupe identical offers
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->string('provider_slug')->index();
            $table->string('category', 20)->index();
            $table->string('title');
            $table->string('origin')->nullable()->index();
            $table->string('destination')->nullable()->index();
            $table->string('city')->nullable();
            $table->decimal('price', 15, 2)->index();
            $table->decimal('cashback', 15, 2)->default(0);
            $table->decimal('rating', 3, 1)->nullable();
            $table->unsignedInteger('review_count')->default(0);
            $table->unsignedSmallInteger('stops')->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->string('currency', 8)->default('INR');
            $table->json('raw');           // full normalized offer payload for the UI
            $table->string('deep_link', 1024)->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();

            $table->index(['category', 'price']);
            $table->index(['category', 'destination', 'price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cached_offers');
    }
};
