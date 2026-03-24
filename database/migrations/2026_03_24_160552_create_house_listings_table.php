<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('house_listings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('seller_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->enum('property_type', [
                'apartment', 'house', 'land',
                'commercial', 'shortlet', 'other'
            ]);
            $table->enum('listing_type', ['sale', 'rent', 'shortlet']);
            $table->decimal('price', 15, 2);
            $table->string('location');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->unsignedTinyInteger('bedrooms')->nullable();
            $table->unsignedTinyInteger('bathrooms')->nullable();
            $table->unsignedTinyInteger('toilets')->nullable();
            $table->decimal('size_sqm', 10, 2)->nullable();
            $table->json('features')->nullable();
            $table->string('video_tour_url')->nullable();
            $table->enum('status', [
                'pending', 'approved', 'rejected', 'suspended'
            ])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('seller_id')
                  ->references('id')->on('sellers')
                  ->cascadeOnDelete();

            $table->index('seller_id');
            $table->index('status');
            $table->index('listing_type');
        });

        Schema::create('house_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('house_listing_id');
            $table->string('image_url');
            $table->string('cloudinary_public_id')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('house_listing_id')
                  ->references('id')->on('house_listings')
                  ->cascadeOnDelete();

            $table->index('house_listing_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('house_images');
        Schema::dropIfExists('house_listings');
    }
};