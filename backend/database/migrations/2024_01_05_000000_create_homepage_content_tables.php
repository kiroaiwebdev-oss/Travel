<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admin-managed "Trending now" destinations shown on the landing page.
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tag')->nullable();              // Beaches, Luxury, Mountains...
            $table->string('image_url');
            $table->string('category', 30)->default('hotels'); // which search category to open
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // User reviews & suggestions (moderated before showing on the site).
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('location')->nullable();         // "Trip to Goa" / city
            $table->unsignedTinyInteger('rating')->nullable(); // 1-5 (reviews only)
            $table->string('type', 20)->default('review');  // review | suggestion
            $table->text('message');
            $table->string('status', 20)->default('pending'); // pending | approved | rejected
            $table->boolean('is_featured')->default(false);  // show on landing page
            $table->timestamps();

            $table->index(['status', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('destinations');
    }
};
