<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ad_clicks', function (Blueprint $table) {
            if (!Schema::hasColumn('ad_clicks', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('ad_clicks', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};