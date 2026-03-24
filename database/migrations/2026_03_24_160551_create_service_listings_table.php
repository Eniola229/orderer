<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_listings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('seller_id');
            $table->uuid('category_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('pricing_type', ['fixed', 'hourly', 'negotiable']);
            $table->decimal('price', 12, 2)->nullable();
            $table->string('delivery_time')->nullable();
            $table->string('location')->nullable();
            $table->json('portfolio_images')->nullable();
            $table->enum('status', [
                'pending', 'approved', 'rejected', 'suspended'
            ])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('seller_id')
                  ->references('id')->on('sellers')
                  ->cascadeOnDelete();
            $table->foreign('category_id')
                  ->references('id')->on('categories');

            $table->index('seller_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_listings');
    }
};