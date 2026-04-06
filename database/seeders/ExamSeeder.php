<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::where('role', User::ROLE_TEACHER)->first();
        $phpTopic = Topic::where('name', 'PHP')->first();
        $laravelTopic = Topic::where('name', 'Laravel')->first();

        $exams = [
            [
                'topic_id' => $phpTopic?->id,
                'title' => 'PHP Basic Test',
                'description' => 'Bài kiểm tra cơ bản về PHP',
                'duration_mins' => 30,
                'pass_score' => 50,
                'shuffle_q' => true,
                'shuffle_a' => true,
                'show_explain' => true,
                'is_active' => true,
                'start_time' => now(),
                'end_time' => now()->addMonth(),
                'status' => Exam::STATUS_OPEN,
                'is_published' => true,
            ],
            [
                'topic_id' => $laravelTopic?->id,
                'title' => 'Laravel Beginner Quiz',
                'description' => 'Bài kiểm tra dành cho người mới bắt đầu với Laravel',
                'duration_mins' => 45,
                'pass_score' => 60,
                'shuffle_q' => true,
                'shuffle_a' => false,
                'show_explain' => false,
                'is_active' => true,
                'start_time' => now()->addDays(7),
                'end_time' => now()->addMonth(),
                'status' => Exam::STATUS_SCHEDULED,
                'is_published' => true,
            ],
        ];

        foreach ($exams as $examData) {
            if (!$examData['topic_id']) {
                continue;
            }

            Exam::firstOrCreate(
                [
                    'title' => $examData['title'],
                    'created_by' => $teacher->id,
                ],
                array_merge($examData, ['created_by' => $teacher->id])
            );
        }
    }
}
