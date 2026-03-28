<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('seller_id');
            $table->decimal('amount', 12, 2);
            $table->string('bank_name');
            $table->string('account_number', 30);
            $table->string('account_name');
            $table->string('bank_country')->default('NG');
            $table->boolean('dollar_capable')->default(true);
            $table->string('swift_code')->nullable();
            $table->enum('status', [
                'pending', 'processing', 'completed', 'rejected', 'approved'
            ])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->uuid('processed_by')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->foreign('seller_id')
                  ->references('id')->on('sellers')
                  ->cascadeOnDelete();

            $table->index('seller_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};