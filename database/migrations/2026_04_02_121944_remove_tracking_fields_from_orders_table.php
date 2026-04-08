<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipbubble_shipment_id', 'courier_id', 'tracking_number',
                'tracking_url', 'shipping_status', 'estimated_delivery_date',
                'all_shipments',   // if you added this column
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipbubble_shipment_id')->nullable();
            $table->string('courier_id')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();
            $table->string('shipping_status')->nullable();
            $table->string('estimated_delivery_date')->nullable();
            $table->json('all_shipments')->nullable();
        });
    }
};
