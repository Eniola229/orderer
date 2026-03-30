<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_listings', function (Blueprint $table) {
            if (!Schema::hasColumn('service_listings', 'views')) {
                $table->text('views')->nullable()->after('total_reviews');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_listings', function (Blueprint $table) {
            if (Schema::hasColumn('service_listings', 'views')) {
                $table->dropColumn('views');
            }
        });
    }
};