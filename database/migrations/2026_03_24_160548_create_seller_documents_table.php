<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('seller_id');
            $table->enum('document_type', [
                'cac_certificate',
                'school_certificate',
                'government_id',
                'business_license',
                'other',
            ]);
            $table->string('document_url');
            $table->string('cloudinary_public_id')->nullable();
            $table->string('original_filename')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('seller_id')
                  ->references('id')->on('sellers')
                  ->cascadeOnDelete();

            $table->index(['seller_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_documents');
    }
};