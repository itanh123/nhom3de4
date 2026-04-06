<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('exams')->insert([
            // Topic 1 - PHP
            ['topic_id' => 1, 'created_by' => 3, 'title' => 'Kiểm tra PHP cơ bản - Chương 1',          'description' => 'Kiểm tra kiến thức biến, kiểu dữ liệu và cú pháp PHP.',          'duration_mins' => 20, 'pass_score' => 60, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => false, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 1, 'created_by' => 3, 'title' => 'Ôn tập PHP - Hàm và mảng',                'description' => 'Bài ôn tập các hàm built-in và thao tác với mảng trong PHP.',    'duration_mins' => 30, 'pass_score' => 70, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 2 - Laravel
            ['topic_id' => 2, 'created_by' => 3, 'title' => 'Laravel Routing & Controller',             'description' => 'Kiểm tra kiến thức routing, middleware và controller.',           'duration_mins' => 25, 'pass_score' => 65, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => false, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 2, 'created_by' => 3, 'title' => 'Eloquent ORM - Nâng cao',                 'description' => 'Kiểm tra Eloquent relationships, scopes và query builder.',       'duration_mins' => 40, 'pass_score' => 70, 'shuffle_q' => true,  'shuffle_a' => false, 'show_explain' => true,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 3 - MySQL
            ['topic_id' => 3, 'created_by' => 4, 'title' => 'SQL Cơ bản',                              'description' => 'Kiểm tra SELECT, INSERT, UPDATE, DELETE.',                       'duration_mins' => 20, 'pass_score' => 60, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => false, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 3, 'created_by' => 4, 'title' => 'Thiết kế cơ sở dữ liệu',                 'description' => 'Bài kiểm tra về chuẩn hóa, khóa ngoại và quan hệ bảng.',         'duration_mins' => 35, 'pass_score' => 65, 'shuffle_q' => false, 'shuffle_a' => true,  'show_explain' => true,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 5 - JavaScript
            ['topic_id' => 5, 'created_by' => 5, 'title' => 'JavaScript ES6+ Cơ bản',                  'description' => 'let/const, arrow function, destructuring, spread operator.',      'duration_mins' => 25, 'pass_score' => 60, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => false, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 5, 'created_by' => 5, 'title' => 'Async JavaScript',                        'description' => 'Kiểm tra Promise, async/await và xử lý bất đồng bộ.',           'duration_mins' => 30, 'pass_score' => 70, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 7 - Git
            ['topic_id' => 7, 'created_by' => 7, 'title' => 'Git Fundamentals',                        'description' => 'Các lệnh Git cơ bản và workflow phổ biến.',                      'duration_mins' => 15, 'pass_score' => 60, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => false, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 7, 'created_by' => 7, 'title' => 'Git Branching & Merging',                 'description' => 'Chiến lược branching, merge và resolve conflict.',                'duration_mins' => 20, 'pass_score' => 65, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => false, 'created_at' => now(), 'updated_at' => now()],

            // Topic 8 - Scrum
            ['topic_id' => 8, 'created_by' => 7, 'title' => 'Scrum Framework - Kiểm tra cuối kỳ',     'description' => 'Kiểm tra toàn bộ kiến thức về Scrum Guide 2020.',                 'duration_mins' => 45, 'pass_score' => 70, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 8, 'created_by' => 1, 'title' => 'Agile Values & Principles',               'description' => 'Bài kiểm tra 12 nguyên tắc Agile Manifesto.',                    'duration_mins' => 20, 'pass_score' => 60, 'shuffle_q' => true,  'shuffle_a' => false, 'show_explain' => false, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 9 - Bảo mật
            ['topic_id' => 9, 'created_by' => 3, 'title' => 'Web Security OWASP Top 10',              'description' => 'Kiểm tra nhận biết và phòng chống các lỗ hổng OWASP Top 10.',     'duration_mins' => 30, 'pass_score' => 75, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 13 - CTDL
            ['topic_id' => 13, 'created_by' => 1, 'title' => 'Thuật toán sắp xếp',                    'description' => 'Kiểm tra độ phức tạp và cơ chế các thuật toán sắp xếp cơ bản.',  'duration_mins' => 35, 'pass_score' => 65, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 13, 'created_by' => 1, 'title' => 'Cấu trúc dữ liệu tuyến tính',           'description' => 'Array, Stack, Queue, Linked List.',                               'duration_mins' => 40, 'pass_score' => 70, 'shuffle_q' => false, 'shuffle_a' => true,  'show_explain' => false, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Misc topics
            ['topic_id' => 6,  'created_by' => 5, 'title' => 'REST API Design Principles',             'description' => 'HTTP methods, status codes và thiết kế endpoint chuẩn.',          'duration_mins' => 25, 'pass_score' => 65, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => false, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 10, 'created_by' => 4, 'title' => 'Docker Cơ bản',                         'description' => 'Image, container, volume và Docker Compose.',                     'duration_mins' => 30, 'pass_score' => 60, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 11, 'created_by' => 5, 'title' => 'Vue 3 Composition API',                  'description' => 'ref, reactive, computed, watch và lifecycle hooks.',              'duration_mins' => 35, 'pass_score' => 65, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 12, 'created_by' => 7, 'title' => 'PHPUnit Testing',                       'description' => 'Viết unit test, mock và test coverage.',                          'duration_mins' => 40, 'pass_score' => 70, 'shuffle_q' => true,  'shuffle_a' => false, 'show_explain' => true,  'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 15, 'created_by' => 3, 'title' => 'OOP - SOLID Principles',                 'description' => 'Kiểm tra 5 nguyên tắc SOLID trong lập trình hướng đối tượng.',   'duration_mins' => 30, 'pass_score' => 70, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 20, 'created_by' => 2, 'title' => 'Machine Learning Nhập môn',              'description' => 'Các khái niệm cơ bản: supervised, unsupervised, overfitting.',    'duration_mins' => 45, 'pass_score' => 60, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
