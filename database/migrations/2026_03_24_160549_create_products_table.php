<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('seller_id');
            $table->uuid('category_id');
            $table->uuid('subcategory_id')->nullable();
            $table->uuid('brand_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->decimal('sale_price', 12, 2)->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->string('sku')->nullable()->unique();
            $table->string('condition')->default('new');
            $table->string('location')->nullable();
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->enum('status', [
                'pending', 'approved',
                'rejected', 'suspended', 'draft',
            ])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->unsignedInteger('total_sold')->default(0);
            $table->unsignedInteger('views')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('seller_id')
                  ->references('id')->on('sellers')
                  ->cascadeOnDelete();
            $table->foreign('category_id')
                  ->references('id')->on('categories');
            $table->foreign('subcategory_id')
                  ->references('id')->on('subcategories')
                  ->nullOnDelete();
            $table->foreign('brand_id')
                  ->references('id')->on('brands')
                  ->nullOnDelete();

            $table->index('slug');
            $table->index('seller_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};