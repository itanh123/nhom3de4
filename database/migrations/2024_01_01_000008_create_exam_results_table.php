<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedSmallInteger('total_questions')->default(0);
            $table->unsignedSmallInteger('correct_count')->default(0);
            $table->decimal('score_pct', 5, 2)->default(0.00)->comment('Điểm phần trăm');
            $table->boolean('passed')->default(false);
            $table->text('ai_summary')->nullable()->comment('AI tóm tắt kết quả học tập');
            $table->text('ai_suggestions')->nullable()->comment('AI đề xuất lộ trình cải thiện');

            $table->index('exam_id');
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
