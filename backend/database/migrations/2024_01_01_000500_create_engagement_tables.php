<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('code', 12)->index();
            $table->string('status', 20)->default('pending'); // pending|qualified|rewarded|rejected
            $table->decimal('reward_amount', 12, 2)->default(0);
            $table->string('ip_address', 45)->nullable();   // fraud signal
            $table->string('signup_fingerprint')->nullable();
            $table->timestamp('qualified_at')->nullable();
            $table->timestamp('rewarded_at')->nullable();
            $table->timestamps();

            $table->index(['referrer_id', 'status']);
            $table->unique(['referee_id']); // a user can only be referred once
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->string('category', 30)->default('general'); // cashback|booking|system|promo
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_number', 20)->unique();
            $table->string('subject');
            $table->string('category', 40)->default('general');
            $table->string('priority', 20)->default('normal'); // low|normal|high|urgent
            $table->string('status', 20)->default('open');     // open|pending|resolved|closed
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_reply_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority']);
        });

        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_staff')->default(false);
            $table->text('body');
            $table->json('attachments')->nullable();
            $table->timestamps();
        });

        // Saved hotels/flights + saved searches + watchlist all share one polymorphic table.
        Schema::create('saved_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 30);          // saved_hotel|saved_flight|saved_search|watchlist
            $table->string('category')->nullable();
            $table->string('reference')->nullable(); // offer ref / search hash
            $table->json('payload');             // snapshot of the item/search
            $table->decimal('target_price', 15, 2)->nullable(); // watchlist price alert
            $table->timestamps();

            $table->index(['user_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_items');
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('referrals');
    }
};
