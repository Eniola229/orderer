<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // FLASH SALES
        Schema::create('flash_sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');

            $table->foreignUuid('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('sale_price', 12, 2);
            $table->decimal('original_price', 12, 2);

            $table->unsignedInteger('quantity_limit')->nullable();
            $table->unsignedInteger('quantity_sold')->default(0);

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->boolean('is_active')->default(true);

            $table->foreignUuid('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
        });

        // PRODUCT BUNDLES
        Schema::create('product_bundles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('seller_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->decimal('bundle_price', 12, 2);
            $table->decimal('original_total', 12, 2);

            $table->string('image')->nullable();
            $table->string('cloudinary_public_id')->nullable();

            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });

        // BUNDLE ITEMS
        Schema::create('bundle_items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('product_bundle_id')
                  ->constrained('product_bundles')
                  ->cascadeOnDelete();

            $table->foreignUuid('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->unsignedInteger('quantity')->default(1);

            $table->timestamps();

            // Prevent duplicate products in same bundle
            $table->unique(['product_bundle_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bundle_items');
        Schema::dropIfExists('product_bundles');
        Schema::dropIfExists('flash_sales');
    }
};