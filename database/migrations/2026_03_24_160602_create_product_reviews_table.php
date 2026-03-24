<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('user_id');
            $table->uuid('order_id');
            $table->unsignedTinyInteger('rating');
            $table->text('review')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_verified_purchase')->default(true);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->cascadeOnDelete();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();
            $table->foreign('order_id')
                  ->references('id')->on('orders');

            $table->unique(['product_id', 'user_id', 'order_id']);
            $table->index('product_id');
        });

        Schema::create('brand_reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('brand_id');
            $table->uuid('user_id');
            $table->unsignedTinyInteger('rating');
            $table->text('review')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->foreign('brand_id')
                  ->references('id')->on('brands')
                  ->cascadeOnDelete();
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();

            $table->unique(['brand_id', 'user_id']);
            $table->index('brand_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_reviews');
        Schema::dropIfExists('product_reviews');
    }
};