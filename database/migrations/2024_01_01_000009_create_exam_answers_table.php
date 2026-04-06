<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('result_id')->constrained('exam_results')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            // choice questions dùng answer_id; fill_in_blank dùng text_answer
            $table->foreignId('answer_id')
                  ->nullable()
                  ->constrained('answers')
                  ->nullOnDelete()
                  ->comment('Đáp án chọn (choice questions)');
            $table->text('text_answer')->nullable()->comment('Câu trả lời điền (fill_in_blank)');
            $table->boolean('is_correct')->default(false);
            $table->text('ai_explanation')->nullable()->comment('AI giải thích tại sao đúng/sai');

            $table->index('result_id');
            $table->index('question_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
