<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Shipbubble / shipping carrier fields
            $table->string('shipping_carrier')->nullable()->after('shipping_zip')
                  ->comment('e.g. DHL, GIG, FEDEX, UPS');
            $table->string('shipping_service_code')->nullable()->after('shipping_carrier')
                  ->comment('Shipbubble service code selected at checkout');
            $table->string('shipping_service_name')->nullable()->after('shipping_service_code')
                  ->comment('Human readable e.g. DHL Express');
            $table->string('shipbubble_order_id')->nullable()->after('shipping_service_name')
                  ->comment('Order ID returned by Shipbubble after booking');
            $table->string('shipbubble_shipment_id')->nullable()->after('shipbubble_order_id');
            $table->string('tracking_number')->nullable()->after('shipbubble_shipment_id');
            $table->string('tracking_url')->nullable()->after('tracking_number');
            $table->string('estimated_delivery_date')->nullable()->after('tracking_url');
            $table->decimal('declared_value', 12, 2)->nullable()->after('estimated_delivery_date')
                  ->comment('Value declared for customs/insurance');
            $table->string('package_weight')->nullable()->after('declared_value')
                  ->comment('Total package weight kg');
            $table->json('shipping_rate_data')->nullable()->after('package_weight')
                  ->comment('Full rate object returned by Shipbubble stored for reference');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_carrier', 'shipping_service_code', 'shipping_service_name',
                'shipbubble_order_id', 'shipbubble_shipment_id', 'tracking_number',
                'tracking_url', 'estimated_delivery_date', 'declared_value',
                'package_weight', 'shipping_rate_data',
            ]);
        });
    }
};