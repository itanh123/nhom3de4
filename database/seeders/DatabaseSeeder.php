<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,        // 1. users (no FK)
            TopicsTableSeeder::class,        // 2. topics → users
            DocumentsTableSeeder::class,     // 3. documents → topics, users
            QuestionsTableSeeder::class,     // 4. questions → topics, users, documents
            AnswersTableSeeder::class,       // 5. answers → questions
            ExamsTableSeeder::class,         // 6. exams → topics, users
            ExamQuestionsTableSeeder::class, // 7. exam_questions → exams, questions
            ExamResultsTableSeeder::class,   // 8. exam_results → exams, users
            ExamAnswersTableSeeder::class,   // 9. exam_answers → exam_results, questions, answers
            AiConfigSeeder::class,
            ActivityLogSeeder::class,
            ImportHistorySeeder::class,
        ]);
    }
}
