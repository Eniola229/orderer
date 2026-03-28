<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('house_listings', function (Blueprint $table) {
            // Add rejection_reason column if not exists
            if (!Schema::hasColumn('house_listings', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('status');
            }
            
            // Add approved_by column if not exists
            if (!Schema::hasColumn('house_listings', 'approved_by')) {
                $table->uuid('approved_by')->nullable()->after('rejection_reason');
                $table->foreign('approved_by')
                      ->references('id')
                      ->on('admins')
                      ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('house_listings', function (Blueprint $table) {
            if (Schema::hasColumn('house_listings', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }
            
            if (Schema::hasColumn('house_listings', 'rejection_reason')) {
                $table->dropColumn('rejection_reason');
            }
        });
    }
};