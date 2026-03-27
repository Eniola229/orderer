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
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->string('bank_code', 5)->nullable()->after('status');      // NG, KE, GH, ZA
            $table->string('country_code', 5)->nullable()->after('bank_code');      // NG, KE, GH, ZA
            $table->string('currency', 10)->nullable()->after('country_code');      // NGN, KES, GHS, ZAR
            $table->string('payout_type')->default('bank_account')->after('currency'); // bank_account | mobile_money
            $table->string('mobile_money_operator')->nullable()->after('payout_type'); // e.g. safaricom-ke
            $table->string('mobile_number')->nullable()->after('mobile_money_operator');
            $table->string('korapay_reference')->nullable()->after('processed_by');
            $table->string('korapay_status')->nullable()->after('korapay_reference'); // processing|success|failed
            $table->decimal('payout_fee', 10, 2)->nullable()->after('korapay_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('withdrawal_requests', function (Blueprint $table) {
            $table->string('bank_code', 5)->nullable()->after('status');      // NG, KE, GH, ZA
            $table->string('country_code', 5)->nullable()->after('bank_code');      // NG, KE, GH, ZA
            $table->string('currency', 10)->nullable()->after('country_code');      // NGN, KES, GHS, ZAR
            $table->string('payout_type')->default('bank_account')->after('currency'); // bank_account | mobile_money
            $table->string('mobile_money_operator')->nullable()->after('payout_type'); // e.g. safaricom-ke
            $table->string('mobile_number')->nullable()->after('mobile_money_operator');
            $table->string('korapay_reference')->nullable()->after('processed_by');
            $table->string('korapay_status')->nullable()->after('korapay_reference'); // processing|success|failed
            $table->decimal('payout_fee', 10, 2)->nullable()->after('korapay_status');
        });
    }
};
