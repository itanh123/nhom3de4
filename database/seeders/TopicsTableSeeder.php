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
            ['created_by' => 5, 'name' => 'Tiếng Anh Giao tiếp',               'description' => 'Ngữ pháp, từ vựng và các tình huống giao tiếp cơ bản.',           'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 5, 'name' => 'Toán học Giải tích',                 'description' => 'Hàm số, đạo hàm, tích phân và ứng dụng.',                        'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 7, 'name' => 'Lịch sử Việt Nam',                   'description' => 'Tiến trình lịch sử dân tộc từ thời dựng nước đến nay.',          'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 7, 'name' => 'Văn học Hiện đại',                   'description' => 'Các tác phẩm văn học tiêu biểu từ đầu thế kỷ XX.',                'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 3, 'name' => 'Vật lý Nhiệt học',                   'description' => 'Các nguyên lý nhiệt động lực học và ứng dụng.',                  'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 4, 'name' => 'Hóa học Hữu cơ',                     'description' => 'Cấu trúc và tính chất của các hợp chất hữu cơ.',                 'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 5, 'name' => 'Địa lý Tự nhiên',                    'description' => 'Địa hình, khí hậu và tài nguyên thiên nhiên thế giới.',          'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 7, 'name' => 'Sinh học Tế bào',                    'description' => 'Cấu trúc, chức năng và sự phân chia của tế bào.',                 'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 1, 'name' => 'Cấu trúc dữ liệu & Giải thuật',    'description' => 'Array, linked list, stack, queue, tree và thuật toán.',           'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 2, 'name' => 'Mạng máy tính',                     'description' => 'Mô hình OSI, TCP/IP, HTTP và các giao thức mạng.',                'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 3, 'name' => 'Lập trình hướng đối tượng',         'description' => 'OOP với PHP: class, interface, trait, abstract.',                 'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 4, 'name' => 'Linux cơ bản',                       'description' => 'Lệnh shell, quản lý file và process trên Linux.',                 'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 5, 'name' => 'Kinh tế Vĩ mô',                      'description' => 'Cung cầu, lạm phát, thất nghiệp và các chính sách kinh tế.',      'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 7, 'name' => 'Tâm lý học Hành vi',                 'description' => 'Nghiên cứu về các phản ứng và hành vi của con người.',            'is_public' => false, 'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 1, 'name' => 'Triết học đại cương',                'description' => 'Lịch sử tư tưởng và các nguyên lý triết học cơ bản.',             'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
            ['created_by' => 2, 'name' => 'Xác suất Thống kê',                  'description' => 'Các định lý xác suất và phương pháp thống kê dữ liệu.',           'is_public' => true,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
