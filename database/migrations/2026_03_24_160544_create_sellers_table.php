<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->string('password');
            $table->string('business_name');
            $table->string('business_slug')->unique();
            $table->text('business_description')->nullable();
            $table->text('business_address')->nullable();
            $table->string('avatar')->nullable();
            $table->string('banner_image')->nullable();
            $table->boolean('is_verified_business')->default(false);
            $table->enum('verification_status', [
                'pending', 'approved', 'rejected'
            ])->default('pending');
            $table->text('approval_note')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->decimal('wallet_balance', 12, 2)->default(0.00);
            $table->decimal('ads_balance', 12, 2)->default(0.00);
            $table->string('referral_code', 20)->unique()->nullable();
            $table->uuid('referred_by')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->index('email');
            $table->index('business_slug');
            $table->index('verification_status');
            $table->index('is_approved');
            $table->foreign('referred_by')
                  ->references('id')->on('sellers')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};