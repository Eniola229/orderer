<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the existing foreign key constraint
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });
        
        // Re-add the foreign key referencing admins table
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->foreign('created_by')
                  ->references('id')
                  ->on('admins')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Drop the new foreign key
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });
        
        // Restore the old foreign key referencing users
        Schema::table('flash_sales', function (Blueprint $table) {
            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }
};