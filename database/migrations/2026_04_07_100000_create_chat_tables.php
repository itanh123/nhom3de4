<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title', 255)->nullable();
            $table->enum('type', ['user', 'admin'])->default('user');
            $table->text('context')->nullable();
            $table->boolean('is_starred')->default(false);
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index('last_message_at');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('sender_type', ['user', 'assistant', 'admin']);
            $table->text('content');
            $table->enum('status', ['sent', 'delivered', 'read'])->default('sent');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('chat_session_id');
            $table->index('sender_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
    }
};
