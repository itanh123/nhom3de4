<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('result_id')->constrained('exam_results')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('answer_id')->nullable()->constrained('answers')->onDelete('set null')->comment('Đáp án chọn (choice questions)');
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
