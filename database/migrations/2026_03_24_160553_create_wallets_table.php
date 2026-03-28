<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('walletable');
            $table->decimal('balance', 12, 2)->default(0.00);
            $table->decimal('escrow_balance', 12, 2)->default(0.00);
            $table->decimal('ads_balance', 12, 2)->default(0.00);
            $table->string('currency', 3)->default('USD');
            $table->timestamps();

            //$table->index(['walletable_type', 'walletable_id']);
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('wallet_id');
            $table->enum('type', [
                'credit',
                'debit',
                'escrow_hold',
                'escrow_release',
                'escrow_refund',
                'ads_debit',
                'commission_debit',
                'withdrawal',
                'referral_credit',
                'refund',
            ]);
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->string('reference')->unique();
            $table->string('description')->nullable();
            $table->string('related_type')->nullable();
            $table->uuid('related_id')->nullable();
            $table->enum('status', [
                'pending', 'completed', 'failed', 'reversed'
            ])->default('completed');
            $table->timestamps();

            $table->foreign('wallet_id')
                  ->references('id')->on('wallets')
                  ->cascadeOnDelete();

            $table->index('wallet_id');
            $table->index('reference');
            $table->index('type');
            $table->index(['related_type', 'related_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};