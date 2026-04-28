<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            // marketer_id — set when seller registers with an OR-MRT- code
            $table->uuid('marketer_id')->nullable()->after('referred_by');
            $table->foreign('marketer_id')->references('id')->on('marketers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropForeign(['marketer_id']);
            $table->dropColumn('marketer_id');
        });
    }
};