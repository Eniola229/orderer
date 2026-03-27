<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone', 20);
            $table->string('password');
            $table->string('avatar')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('delivery_bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('booking_number')->unique();
            $table->uuid('user_id');
            $table->uuid('rider_id')->nullable();
            $table->uuid('order_id')->nullable();
            $table->enum('delivery_type', ['local', 'international']);
            $table->text('pickup_address');
            $table->string('pickup_city');
            $table->string('pickup_country');
            $table->text('delivery_address');
            $table->string('delivery_city');
            $table->string('delivery_country');
            $table->string('item_description');
            $table->decimal('weight_kg', 8, 2)->nullable();
            $table->decimal('fee', 12, 2)->default(0.00);
            $table->enum('payment_status', ['pending', 'paid'])
                  ->default('pending');
            $table->enum('status', [
                'pending', 'accepted', 'picked_up',
                'in_transit', 'delivered', 'cancelled', 'confirmed'
            ])->default('pending');
            $table->string('shiprocket_order_id')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                  ->references('id')->on('users');
            $table->foreign('rider_id')
                  ->references('id')->on('riders')
                  ->nullOnDelete();
            $table->foreign('order_id')
                  ->references('id')->on('orders')
                  ->nullOnDelete();

            $table->index('booking_number');
            $table->index('user_id');
            $table->index('status');
        });

        Schema::create('shipment_tracking', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('delivery_booking_id');
            $table->string('tracking_number');
            $table->string('carrier')->nullable();
            $table->string('status');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('event_at')->nullable();
            $table->timestamps();

            $table->foreign('delivery_booking_id')
                  ->references('id')->on('delivery_bookings')
                  ->cascadeOnDelete();

            $table->index('tracking_number');
            $table->index('delivery_booking_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_tracking');
        Schema::dropIfExists('delivery_bookings');
        Schema::dropIfExists('riders');
    }
};