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
            $table->decimal('free_shipping_discount', 12, 2)->default(0)->after('shipping_fee');
            $table->uuid('free_shipping_rule_id')->nullable()->after('free_shipping_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['free_shipping_discount']);
            $table->dropForeign(['free_shipping_rule_id']);
        });
    }
};
