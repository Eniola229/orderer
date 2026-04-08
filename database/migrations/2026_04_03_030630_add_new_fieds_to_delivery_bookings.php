<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_bookings', function (Blueprint $table) {
            // Add service_fee column after 'fee'
            $table->decimal('service_fee', 10, 2)->default(200.00)->after('fee');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_bookings', function (Blueprint $table) {
            $table->dropColumn('service_fee');
        });
    }
};