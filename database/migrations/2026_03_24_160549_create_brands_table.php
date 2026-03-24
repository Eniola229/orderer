<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('seller_id');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->timestamps();

            $table->foreign('seller_id')
                  ->references('id')->on('sellers')
                  ->cascadeOnDelete();

            $table->index('slug');
            $table->index('seller_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};