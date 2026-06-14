<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // High-volume: every search is logged for analytics + abuse detection.
        Schema::create('search_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category', 20)->index();
            $table->string('origin')->nullable();
            $table->string('destination')->nullable();
            $table->date('depart_date')->nullable();
            $table->date('return_date')->nullable();
            $table->unsignedSmallInteger('travellers')->default(1);
            $table->json('filters')->nullable();
            $table->unsignedInteger('result_count')->default(0);
            $table->unsignedInteger('response_ms')->default(0);
            $table->boolean('cache_hit')->default(false);
            $table->string('session_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['category', 'created_at']);
        });

        // Audit trail for every privileged mutation (OWASP A09).
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');                 // providers.update, withdrawal.approve ...
            $table->nullableMorphs('auditable');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['action', 'created_at']);
        });

        // Device tracking for login security.
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('device_hash', 64)->index();
            $table->string('device_name')->nullable();
            $table->string('platform')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_trusted')->default(false);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'device_hash']);
        });

        // Key/value settings with typed casting (config that admin can edit live).
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group')->index();      // general|cashback|seo|payments
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // string|bool|int|float|json
            $table->boolean('is_public')->default(false);  // exposed to frontend?
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('search_logs');
    }
};
