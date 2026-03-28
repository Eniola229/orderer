<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_listings', function (Blueprint $table) {
            $table->uuid('approved_by')->nullable()->after('rejection_reason');
            $table->foreign('approved_by')
                  ->references('id')
                  ->on('admins')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('service_listings', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn('approved_by');
        });
    }
};