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
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('shipbubble_shipment_id')->nullable()->after('status');
            $table->string('courier_id')->nullable()->after('shipbubble_shipment_id');
            $table->string('tracking_number')->nullable()->after('courier_id');
            $table->string('tracking_url')->nullable()->after('tracking_number');
            $table->string('shipping_status')->nullable()->after('tracking_url');  
            $table->string('estimated_delivery_date')->nullable()->after('shipping_status');
            $table->timestamp('delivered_at')->nullable()->after('estimated_delivery_date');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'shipbubble_shipment_id', 'courier_id', 'tracking_number',
                'tracking_url', 'shipping_status', 'estimated_delivery_date', 'delivered_at',
            ]);
        });
    }
};
