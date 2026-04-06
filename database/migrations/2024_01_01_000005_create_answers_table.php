<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->text('option_text')->comment('Nội dung đáp án');
            $table->boolean('is_correct')->default(false);
            $table->unsignedTinyInteger('display_order')->default(0);

            $table->index('question_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
