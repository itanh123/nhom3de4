<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // View: Question Statistics
        DB::statement("
            CREATE OR REPLACE VIEW vw_question_stats AS
            SELECT
                q.id AS question_id,
                q.topic_id,
                q.content,
                q.difficulty,
                COUNT(ea.id) AS attempt_count,
                SUM(ea.is_correct) AS correct_count,
                ROUND(SUM(ea.is_correct) / COUNT(ea.id) * 100, 2) AS correct_rate_pct
            FROM questions q
            LEFT JOIN exam_answers ea ON ea.question_id = q.id
            GROUP BY q.id, q.topic_id, q.content, q.difficulty
        ");

        // View: Exam Statistics
        DB::statement("
            CREATE OR REPLACE VIEW vw_exam_stats AS
            SELECT
                e.id AS exam_id,
                e.title,
                e.topic_id,
                COUNT(er.id) AS total_attempts,
                ROUND(AVG(er.score_pct), 2) AS avg_score_pct,
                SUM(er.passed) AS pass_count,
                ROUND(SUM(er.passed) / COUNT(er.id) * 100, 2) AS pass_rate_pct
            FROM exams e
            LEFT JOIN exam_results er ON er.exam_id = e.id
            GROUP BY e.id, e.title, e.topic_id
        ");
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_question_stats');
        DB::statement('DROP VIEW IF EXISTS vw_exam_stats');
    }
};
