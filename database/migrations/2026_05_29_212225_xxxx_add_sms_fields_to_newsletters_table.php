<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->boolean('send_sms')->default(false)->after('body');
            $table->string('sms_message', 160)->nullable()->after('send_sms');
            // 'users', 'sellers', 'both'
            $table->string('sms_audience')->nullable()->after('sms_message');
            // JSON array of extra phone numbers
            $table->json('sms_extra_numbers')->nullable()->after('sms_audience');
        });
    }

    public function down(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropColumn(['send_sms', 'sms_message', 'sms_audience', 'sms_extra_numbers']);
        });
    }
};