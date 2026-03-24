<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('referrer');
            $table->uuidMorphs('referred');
            $table->string('referral_code', 20);
            $table->timestamps();

            $table->index('referral_code');
        });

        Schema::create('referral_earnings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('referral_id');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('triggered_by');
            $table->enum('status', ['pending', 'credited', 'cancelled'])
                  ->default('pending');
            $table->timestamp('credited_at')->nullable();
            $table->timestamps();

            $table->foreign('referral_id')
                  ->references('id')->on('referrals')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_earnings');
        Schema::dropIfExists('referrals');
    }
};