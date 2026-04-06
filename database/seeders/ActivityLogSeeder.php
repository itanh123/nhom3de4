<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $teacher = User::where('role', User::ROLE_TEACHER)->first();
        $student = User::where('role', User::ROLE_STUDENT)->first();

        $logs = [
            [
                'user_id' => $admin->id,
                'action' => ActivityLog::ACTION_LOGIN,
                'entity_type' => 'User',
                'entity_id' => $admin->id,
                'description' => 'Admin đăng nhập hệ thống',
            ],
            [
                'user_id' => $admin->id,
                'action' => ActivityLog::ACTION_CREATE_TOPIC,
                'entity_type' => 'Topic',
                'entity_id' => 1,
                'description' => 'Tạo chủ đề mới: Công nghệ thông tin',
            ],
            [
                'user_id' => $teacher->id,
                'action' => ActivityLog::ACTION_LOGIN,
                'entity_type' => 'User',
                'entity_id' => $teacher->id,
                'description' => 'Giáo viên đăng nhập hệ thống',
            ],
            [
                'user_id' => $teacher->id,
                'action' => ActivityLog::ACTION_CREATE_EXAM,
                'entity_type' => 'Exam',
                'entity_id' => 1,
                'description' => 'Tạo bài thi mới: PHP Basic Test',
            ],
            [
                'user_id' => $student->id,
                'action' => ActivityLog::ACTION_LOGIN,
                'entity_type' => 'User',
                'entity_id' => $student->id,
                'description' => 'Học sinh đăng nhập hệ thống',
            ],
            [
                'user_id' => $student->id,
                'action' => ActivityLog::ACTION_TAKE_EXAM,
                'entity_type' => 'Exam',
                'entity_id' => 1,
                'description' => 'Làm bài thi: PHP Basic Test',
            ],
            [
                'user_id' => $admin->id,
                'action' => ActivityLog::ACTION_IMPORT_QUESTIONS,
                'entity_type' => 'ImportHistory',
                'entity_id' => 1,
                'description' => 'Nhập 50 câu hỏi từ file Excel',
            ],
        ];

        foreach ($logs as $log) {
            ActivityLog::create(array_merge($log, [
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                'created_at' => now()->subDays(rand(0, 7)),
            ]));
        }
    }
}
