<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_configs', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50);
            $table->string('model_name', 100);
            $table->enum('purpose', [
                'question_generation',
                'answer_explanation',
                'result_evaluation',
                'learning_path',
                'general'
            ])->default('general');
            $table->string('api_key', 500)->nullable();
            $table->string('base_url', 500)->nullable();
            $table->decimal('temperature', 3, 2)->default(0.70);
            $table->unsignedInteger('max_tokens')->default(2000);
            $table->text('default_prompt')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->index('purpose');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_configs');
    }
};
