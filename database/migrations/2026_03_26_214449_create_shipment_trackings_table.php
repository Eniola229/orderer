<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_trackings', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // UUID foreign key matching delivery_bookings.id
            $table->uuid('delivery_booking_id');
            $table->foreign('delivery_booking_id')
                  ->references('id')
                  ->on('delivery_bookings')
                  ->onDelete('cascade');

            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();
            $table->string('status')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('event_at')->nullable();
            $table->timestamps();

            // Prevent duplicate events
            $table->unique(['delivery_booking_id', 'event_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_trackings');
    }
};