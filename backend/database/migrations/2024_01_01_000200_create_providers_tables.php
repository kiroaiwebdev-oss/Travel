<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Affiliate networks group providers (e.g. CJ, Impact, Admitad)
        Schema::create('affiliate_networks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('postback_secret')->nullable(); // HMAC verify postbacks
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('affiliate_network_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_url')->nullable();
            $table->string('adapter')->default('generic_rest'); // maps to config/providers.php
            $table->json('categories');                         // ["hotels","flights"]
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('priority')->default(100);  // lower = shown first
            $table->decimal('commission_percent', 6, 2)->default(0); // commission we earn
            $table->string('tracking_template')->nullable();    // deep-link template
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('priority');
        });

        // Encrypted, versioned config for each provider (API keys, endpoints, params)
        Schema::create('provider_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained()->cascadeOnDelete();
            $table->string('environment', 20)->default('production'); // sandbox|production
            $table->json('config');     // encrypted at the model layer (api_key, secret, base_url...)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['provider_id', 'environment']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_configurations');
        Schema::dropIfExists('providers');
        Schema::dropIfExists('affiliate_networks');
    }
};
