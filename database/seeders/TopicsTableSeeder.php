<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TopicsTableSeeder extends Seeder
{
    public function run(): void
    {
        // created_by: teacher IDs = 3,4,5,7 / admin IDs = 1,2
        DB::table('topics')->insert([
            ['created_by' => 3, 'name' => 'Lập trình PHP cơ bản',              'description' => 'Các kiến thức nền tảng về ngôn ngữ PHP.',                       'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 3, 'name' => 'Laravel Framework',                  'description' => 'Học Laravel từ routing, controller đến Eloquent ORM.',           'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 4, 'name' => 'Cơ sở dữ liệu MySQL',               'description' => 'SQL cơ bản, thiết kế schema và tối ưu truy vấn.',                'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 4, 'name' => 'HTML & CSS',                         'description' => 'Nền tảng thiết kế giao diện web.',                               'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 5, 'name' => 'JavaScript ES6+',                    'description' => 'Cú pháp hiện đại và các tính năng mới của JS.',                  'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 5, 'name' => 'RESTful API Design',                 'description' => 'Nguyên tắc thiết kế và triển khai REST API.',                    'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 7, 'name' => 'Git & GitHub',                       'description' => 'Quản lý phiên bản mã nguồn với Git.',                            'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 7, 'name' => 'Agile & Scrum',                      'description' => 'Phương pháp phát triển phần mềm Agile và quy trình Scrum.',      'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 3, 'name' => 'Bảo mật ứng dụng Web',              'description' => 'OWASP Top 10, XSS, CSRF và cách phòng chống.',                   'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 4, 'name' => 'Docker & DevOps cơ bản',            'description' => 'Container hóa ứng dụng với Docker và CI/CD.',                    'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 5, 'name' => 'Vue.js 3',                           'description' => 'Xây dựng SPA với Vue 3 Composition API.',                        'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 7, 'name' => 'Kiểm thử phần mềm',                 'description' => 'Unit test, integration test và TDD với PHPUnit.',                 'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 1, 'name' => 'Cấu trúc dữ liệu & Giải thuật',    'description' => 'Array, linked list, stack, queue, tree và các thuật toán sắp xếp.', 'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 2, 'name' => 'Mạng máy tính',                     'description' => 'Mô hình OSI, TCP/IP, HTTP và các giao thức mạng.',                'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 3, 'name' => 'Lập trình hướng đối tượng',         'description' => 'OOP với PHP: class, interface, trait, abstract.',                 'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 4, 'name' => 'Linux cơ bản',                       'description' => 'Lệnh shell, quản lý file và process trên Linux.',                 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 5, 'name' => 'Thiết kế UI/UX',                    'description' => 'Nguyên tắc thiết kế giao diện thân thiện người dùng.',            'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 7, 'name' => 'Quản lý dự án CNTT',                'description' => 'Kỹ năng lập kế hoạch, theo dõi và báo cáo dự án phần mềm.',      'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 1, 'name' => 'Điện toán đám mây AWS',             'description' => 'Các dịch vụ EC2, S3, RDS và Lambda trên AWS.',                   'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 2, 'name' => 'Machine Learning cơ bản',           'description' => 'Giới thiệu ML, supervised/unsupervised learning và Python sklearn.', 'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
