<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('guard_type', ['buyer', 'seller', 'admin'])
                  ->nullable();
            $table->uuid('guard_id')->nullable();
            $table->ipAddress('ip_address');
            $table->string('user_agent', 500)->nullable();
            $table->string('method', 10);
            $table->string('url', 2000);
            $table->unsignedSmallInteger('status_code');
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['guard_type', 'guard_id']);
            $table->index('ip_address');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};