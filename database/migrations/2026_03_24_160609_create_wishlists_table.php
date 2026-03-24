<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuidMorphs('wishlistable');
            $table->decimal('price_at_save', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->cascadeOnDelete();

            $table->unique([
                'user_id', 'wishlistable_type', 'wishlistable_id'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};