<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('topic_id')->constrained('topics')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade')->comment('Người tạo bài thi (teacher/admin)');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('duration_mins')->nullable()->comment('Thời gian làm bài (phút), NULL = không giới hạn');
            $table->unsignedTinyInteger('pass_score')->default(50)->comment('Điểm pass (%)');
            $table->boolean('shuffle_q')->default(true)->comment('Trộn thứ tự câu hỏi');
            $table->boolean('shuffle_a')->default(true)->comment('Trộn thứ tự đáp án');
            $table->boolean('show_explain')->default(false)->comment('Hiển thị giải thích sau khi thi');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('topic_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
