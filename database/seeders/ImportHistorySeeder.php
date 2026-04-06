<?php

namespace Database\Seeders;

use App\Models\ImportHistory;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class ImportHistorySeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $teacher = User::where('role', User::ROLE_TEACHER)->first();
        $phpTopic = Topic::where('name', 'PHP')->first();
        $laravelTopic = Topic::where('name', 'Laravel')->first();

        $imports = [
            [
                'user_id' => $admin->id,
                'topic_id' => $phpTopic?->id,
                'file_name' => 'php_questions_set_a.xlsx',
                'file_path' => '/uploads/imports/php_questions_set_a.xlsx',
                'total_rows' => 50,
                'success_rows' => 48,
                'failed_rows' => 2,
                'status' => ImportHistory::STATUS_COMPLETED,
                'error_message' => null,
            ],
            [
                'user_id' => $teacher->id,
                'topic_id' => $laravelTopic?->id,
                'file_name' => 'laravel_basics.xlsx',
                'file_path' => '/uploads/imports/laravel_basics.xlsx',
                'total_rows' => 30,
                'success_rows' => 30,
                'failed_rows' => 0,
                'status' => ImportHistory::STATUS_COMPLETED,
                'error_message' => null,
            ],
            [
                'user_id' => $admin->id,
                'topic_id' => $phpTopic?->id,
                'file_name' => 'advanced_php_test.xlsx',
                'file_path' => '/uploads/imports/advanced_php_test.xlsx',
                'total_rows' => 100,
                'success_rows' => 0,
                'failed_rows' => 100,
                'status' => ImportHistory::STATUS_FAILED,
                'error_message' => 'Invalid file format. Expected columns: content, option_a, option_b, option_c, option_d, correct_answer',
            ],
        ];

        foreach ($imports as $import) {
            if (!$import['topic_id']) {
                continue;
            }

            ImportHistory::create($import);
        }
    }
}
