<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admin-curated offers/deals (the marketing catalog, separate from live search).
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category', 30)->index();
            $table->string('cashback_label')->nullable();          // "Up to 40% cashback"
            $table->string('cashback_type', 12)->default('percentage'); // percentage|flat
            $table->decimal('cashback_value', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->text('terms')->nullable();
            $table->string('image_url')->nullable();
            $table->string('deep_link', 1024)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'is_featured']);
            $table->index(['category', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
