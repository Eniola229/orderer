<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->string('image_url');
            $table->string('cloudinary_public_id')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->cascadeOnDelete();

            $table->index('product_id');
        });

        Schema::create('product_videos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->string('video_url');
            $table->string('cloudinary_public_id')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->timestamps();

            $table->foreign('product_id')
                  ->references('id')->on('products')
                  ->cascadeOnDelete();

            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_videos');
        Schema::dropIfExists('product_images');
    }
};