<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['single_choice', 'multiple_choice', 'fill_in_blank'])
                  ->default('single_choice');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->text('content')->comment('Nội dung câu hỏi');
            $table->text('explanation')->nullable()->comment('Giải thích đáp án');
            $table->boolean('ai_generated')->default(false);
            $table->foreignId('source_document')
                  ->nullable()
                  ->constrained('documents')
                  ->nullOnDelete()
                  ->comment('Sinh từ document nào');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('topic_id');
            $table->index('difficulty');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
