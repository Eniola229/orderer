<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escrow_holds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->uuid('seller_id');
            $table->uuid('buyer_id');
            $table->decimal('amount', 12, 2);
            $table->decimal('commission_amount', 12, 2)->default(0.00);
            $table->decimal('seller_amount', 12, 2);
            $table->enum('status', [
                'held', 'released', 'refunded', 'disputed'
            ])->default('held');
            $table->timestamp('release_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->foreign('order_id')
                  ->references('id')->on('orders')
                  ->cascadeOnDelete();
            $table->foreign('seller_id')
                  ->references('id')->on('sellers');
            $table->foreign('buyer_id')
                  ->references('id')->on('users');

            $table->index('order_id');
            $table->index('seller_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escrow_holds');
    }
};