<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ticket_number')->unique();
            $table->string('subject');
            $table->uuidMorphs('requester');
            $table->uuid('assigned_to')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                  ->default('medium');
            $table->enum('status', [
                'open', 'in_progress', 'waiting',
                'resolved', 'closed'
            ])->default('open');
            $table->enum('category', [
                'order_issue', 'payment', 'account',
                'product', 'shipping', 'other'
            ])->default('other');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('ticket_number');
            $table->index('status');
            //$table->index(['requester_type', 'requester_id']);
        });

        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('support_ticket_id');
            $table->uuidMorphs('sender');
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('support_ticket_id')
                  ->references('id')->on('support_tickets')
                  ->cascadeOnDelete();

            $table->index('support_ticket_id');
        });

        Schema::create('live_chat_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('session_key')->unique();
            $table->uuidMorphs('chatter');
            $table->uuid('admin_id')->nullable();
            $table->enum('status', ['waiting', 'active', 'closed'])
                  ->default('waiting');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index('session_key');
            $table->index('status');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('live_chat_session_id');
            $table->uuidMorphs('sender');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('live_chat_session_id')
                  ->references('id')->on('live_chat_sessions')
                  ->cascadeOnDelete();

            $table->index('live_chat_session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('live_chat_sessions');
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('support_tickets');
    }
};