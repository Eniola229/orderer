<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', [
                'banner_image',
                'banner_video',
                'top_listing',
                'cpc',
            ]);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ad_banner_slots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('location', [
                'homepage_hero',
                'category_page',
                'product_page_sidebar',
                'search_results',
            ]);
            $table->decimal('price_per_day', 10, 2);
            $table->unsignedInteger('max_ads')->default(5);
            $table->string('dimensions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('seller_id');
            $table->uuid('ad_category_id');
            $table->uuid('ad_banner_slot_id')->nullable();
            $table->uuidMorphs('promotable');
            $table->string('title');
            $table->string('media_url')->nullable();
            $table->string('cloudinary_public_id')->nullable();
            $table->string('media_type')->default('image');
            $table->string('click_url')->nullable();
            $table->decimal('budget', 12, 2);
            $table->decimal('amount_spent', 12, 2)->default(0.00);
            $table->decimal('cost_per_day', 10, 2)->default(0.00);
            $table->decimal('cost_per_click', 10, 2)->default(0.00);
            $table->enum('status', [
                'pending', 'approved', 'active',
                'paused', 'rejected', 'exhausted', 'expired'
            ])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('total_impressions')->default(0);
            $table->unsignedBigInteger('total_clicks')->default(0);
            $table->unsignedBigInteger('total_conversions')->default(0);
            $table->uuid('approved_by')->nullable();
            $table->timestamps();

            $table->foreign('seller_id')
                  ->references('id')->on('sellers')
                  ->cascadeOnDelete();
            $table->foreign('ad_category_id')
                  ->references('id')->on('ad_categories');
            $table->foreign('ad_banner_slot_id')
                  ->references('id')->on('ad_banner_slots')
                  ->nullOnDelete();

            $table->index('seller_id');
            $table->index('status');
            //$table->index(['promotable_type', 'promotable_id']);
        });

        Schema::create('ad_impressions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ad_id');
            $table->ipAddress('ip_address');
            $table->uuid('user_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('ad_id')
                  ->references('id')->on('ads')
                  ->cascadeOnDelete();

            $table->index('ad_id');
            $table->index('created_at');
        });

        Schema::create('ad_clicks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ad_id');
            $table->ipAddress('ip_address');
            $table->uuid('user_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('ad_id')
                  ->references('id')->on('ads')
                  ->cascadeOnDelete();

            $table->index('ad_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_clicks');
        Schema::dropIfExists('ad_impressions');
        Schema::dropIfExists('ads');
        Schema::dropIfExists('ad_banner_slots');
        Schema::dropIfExists('ad_categories');
    }
};