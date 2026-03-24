<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique();
            $table->uuid('user_id');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_fee', 12, 2)->default(0.00);
            $table->decimal('commission_total', 12, 2)->default(0.00);
            $table->decimal('total', 12, 2);
            $table->enum('payment_method', ['wallet', 'korapay', 'mixed']);
            $table->enum('payment_status', [
                'pending', 'paid', 'failed', 'refunded'
            ])->default('pending');
            $table->enum('status', [
                'pending', 'confirmed', 'processing',
                'shipped', 'delivered', 'completed',
                'cancelled', 'disputed',
            ])->default('pending');
            $table->string('shipping_name');
            $table->string('shipping_phone', 20);
            $table->text('shipping_address');
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_country');
            $table->string('shipping_zip')->nullable();
            $table->string('korapay_reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users');

            $table->index('order_number');
            $table->index('user_id');
            $table->index('status');
            $table->index('payment_status');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->uuid('seller_id');
            $table->uuidMorphs('orderable');
            $table->string('item_name');
            $table->string('item_image')->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('total_price', 12, 2);
            $table->decimal('commission_rate', 5, 2)->default(0.00);
            $table->decimal('commission_amount', 12, 2)->default(0.00);
            $table->decimal('seller_earnings', 12, 2)->default(0.00);
            $table->enum('status', [
                'pending', 'confirmed',
                'shipped', 'delivered', 'cancelled'
            ])->default('pending');
            $table->timestamps();

            $table->foreign('order_id')
                  ->references('id')->on('orders')
                  ->cascadeOnDelete();
            $table->foreign('seller_id')
                  ->references('id')->on('sellers');

            $table->index('order_id');
            $table->index('seller_id');
        });

        Schema::create('order_status_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->string('changed_by_type');
            $table->uuid('changed_by_id')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('order_id')
                  ->references('id')->on('orders')
                  ->cascadeOnDelete();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_logs');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};