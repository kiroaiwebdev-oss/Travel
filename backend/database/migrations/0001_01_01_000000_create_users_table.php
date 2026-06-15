<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable(); // nullable for OAuth-only accounts
            $table->string('phone', 32)->nullable()->index();
            $table->string('avatar_url')->nullable();
            $table->char('referral_code', 12)->nullable()->unique();
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete();

            // OAuth
            $table->string('provider_name')->nullable();      // e.g. google
            $table->string('provider_id')->nullable();

            // MFA-ready
            $table->boolean('mfa_enabled')->default(false);
            $table->text('mfa_secret')->nullable();           // encrypted
            $table->text('mfa_recovery_codes')->nullable();   // encrypted (ciphertext string, not raw JSON)

            $table->string('status', 20)->default('active');  // active|suspended|banned
            $table->string('locale', 8)->default('en');
            $table->string('currency', 8)->default('INR');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['provider_name', 'provider_id']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
