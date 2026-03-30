<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ad_clicks', function (Blueprint $table) {
            if (!Schema::hasColumn('ad_clicks', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip_address');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ad_clicks', function (Blueprint $table) {
            if (Schema::hasColumn('ad_clicks', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
        });
    }
};