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
        Schema::table('sellers', function (Blueprint $table) {
            // Add the columns
            $table->timestamp('approved_at')->nullable();
            $table->uuid('approved_by')->nullable(); // Changed to UUID
            
            // Add foreign key constraint
            $table->foreign('approved_by')
                  ->references('id')
                  ->on('admins')
                  ->onDelete('set null'); // Optional: what happens when admin is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['approved_by']);
            
            // Then drop the columns
            $table->dropColumn(['approved_by', 'approved_at']);
        });
    }
};