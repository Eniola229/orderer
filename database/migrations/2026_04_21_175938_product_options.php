<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->string('name');           // "Color", "Size", custom label
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_option_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_option_id');
            $table->foreign('product_option_id')
                  ->references('id')->on('product_options')->cascadeOnDelete();
            $table->string('value');                        // "Red", "XL"
            $table->string('image_url')->nullable();
            $table->string('cloudinary_public_id')->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Snapshot what the buyer selected when adding to cart
        // e.g. [{"option_id":"uuid","option_name":"Color","value":"Red","image_url":"..."}]
        Schema::table('cart_items', function (Blueprint $table) {
            $table->json('selected_options')->nullable()->after('price');
        });

        // Persist the selection permanently on the order line item
        Schema::table('order_items', function (Blueprint $table) {
            $table->json('selected_options')->nullable()->after('unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('selected_options');
        });
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropColumn('selected_options');
        });
        Schema::dropIfExists('product_option_values');
        Schema::dropIfExists('product_options');
    }
};