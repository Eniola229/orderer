<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_bookings', function (Blueprint $table) {
            $table->string('carrier')->nullable()->after('tracking_number');
            $table->string('service_code')->nullable()->after('carrier');
            $table->string('service_name')->nullable()->after('service_code');
            $table->string('tracking_url')->nullable()->after('service_name');
            $table->string('shipbubble_shipment_id')->nullable()->after('tracking_url');
            $table->decimal('declared_value', 12, 2)->nullable()->after('shipbubble_shipment_id');
            $table->json('rate_data')->nullable()->after('declared_value');
            $table->string('estimated_delivery_date')->nullable()->after('rate_data');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'carrier', 'service_code', 'service_name', 'tracking_url',
                'shipbubble_shipment_id', 'declared_value', 'rate_data',
                'estimated_delivery_date',
            ]);
        });
    }
};