<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('free_shipping_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');                        // Admin label e.g. "Black Friday Free Shipping"
            $table->text('description')->nullable();

            // Who qualifies
            $table->enum('applies_to', [
                'all_buyers',
                'new_buyers',          // registered within X days
                'buyers_no_orders',    // never ordered before
                'specific_buyers',     // specific user IDs
            ])->default('all_buyers');

            $table->unsignedInteger('new_buyer_days')->nullable(); // e.g. 30 = registered in last 30 days

            // What it applies to (product/seller scope)
            $table->enum('product_scope', [
                'all',
                'specific_products',
                'specific_sellers',
            ])->default('all');

            // Min order amount to qualify (nullable = no minimum)
            $table->decimal('minimum_order_amount', 12, 2)->nullable();

            // Max discount cap (nullable = no cap)
            $table->decimal('max_discount_amount', 12, 2)->nullable();

            // Scheduling
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->boolean('is_active')->default(true);
            $table->uuid('created_by');
            $table->timestamps();
        });

        // Pivot: specific buyers
        Schema::create('free_shipping_rule_buyers', function (Blueprint $table) {
            $table->uuid('rule_id');
            $table->uuid('user_id');
            $table->primary(['rule_id', 'user_id']);
        });

        // Pivot: specific products
        Schema::create('free_shipping_rule_products', function (Blueprint $table) {
            $table->uuid('rule_id');
            $table->uuid('product_id');
            $table->primary(['rule_id', 'product_id']);
        });

        // Pivot: specific sellers
        Schema::create('free_shipping_rule_sellers', function (Blueprint $table) {
            $table->uuid('rule_id');
            $table->uuid('seller_id');
            $table->primary(['rule_id', 'seller_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('free_shipping_rule_sellers');
        Schema::dropIfExists('free_shipping_rule_products');
        Schema::dropIfExists('free_shipping_rule_buyers');
        Schema::dropIfExists('free_shipping_rules');
    }
};