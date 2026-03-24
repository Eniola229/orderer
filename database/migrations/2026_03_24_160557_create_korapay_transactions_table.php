<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('korapay_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference')->unique();
            $table->string('korapay_reference')->nullable();
            $table->uuidMorphs('payable');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('type', [
                'wallet_topup',
                'order_payment',
                'ads_topup',
            ]);
            $table->enum('status', [
                'pending', 'success', 'failed'
            ])->default('pending');
            $table->json('gateway_response')->nullable();
            $table->timestamps();

            $table->index('reference');
            $table->index('status');
            //$table->index(['payable_type', 'payable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('korapay_transactions');
    }
};