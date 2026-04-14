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
        DB::statement("ALTER TABLE newsletters MODIFY COLUMN audience ENUM('buyers','sellers','both','guests') NOT NULL DEFAULT 'both'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE newsletters MODIFY COLUMN audience ENUM('buyers','sellers','both') NOT NULL DEFAULT 'both'");
    }
};
