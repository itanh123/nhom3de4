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
            ['topic_id' => 1, 'created_by' => 3, 'title' => 'Kiểm tra PHP cơ bản - Chương 1',          'description' => 'Kiểm tra kiến thức biến, kiểu dữ liệu và cú pháp PHP.',          'duration_mins' => 20, 'pass_score' => 60, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => false, 'is_active' => true, 'status' => 'open', 'is_published' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 5 - Tiếng Anh
            ['topic_id' => 5, 'created_by' => 5, 'title' => 'English Grammar Quiz - Level A1',        'description' => 'Basic grammar check: tenses, pronouns, and simple sentences.',    'duration_mins' => 25, 'pass_score' => 60, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => true, 'status' => 'open', 'is_published' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 6 - Toán học
            ['topic_id' => 6, 'created_by' => 5, 'title' => 'Toán học Giải tích - Đạo hàm',            'description' => 'Kiểm tra kiến thức về tính đạo hàm và ứng dụng của đạo hàm.',     'duration_mins' => 40, 'pass_score' => 50, 'shuffle_q' => true,  'shuffle_a' => false, 'show_explain' => true,  'is_active' => true, 'status' => 'open', 'is_published' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 7 - Lịch sử
            ['topic_id' => 7, 'created_by' => 7, 'title' => 'Lịch sử Việt Nam - Giai đoạn 1945-1975',  'description' => 'Về các chiến dịch lớn và mốc thời gian quan trọng.',             'duration_mins' => 30, 'pass_score' => 70, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => false, 'is_active' => true, 'status' => 'open', 'is_published' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 8 - Văn học
            ['topic_id' => 8, 'created_by' => 7, 'title' => 'Văn học Hiện đại - Tác giả & Tác phẩm',   'description' => 'Kiểm tra kiến thức về các nhà văn, nhà thơ tiêu biểu.',          'duration_mins' => 30, 'pass_score' => 65, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => true, 'status' => 'open', 'is_published' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 9 - Vật lý
            ['topic_id' => 9, 'created_by' => 3, 'title' => 'Vật lý Nhiệt học - Cơ bản',               'description' => 'Cơ cấu nhiệt, nguyên lý 1 và 2 nhiệt động lực học.',            'duration_mins' => 25, 'pass_score' => 60, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => false, 'is_active' => true, 'status' => 'open', 'is_published' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 10 - Hóa học
            ['topic_id' => 10, 'created_by' => 4, 'title' => 'Hóa học Hữu cơ - Đại cương',             'description' => 'Danh pháp, cấu tạo và tính chất các nhóm hidrocacbon.',          'duration_mins' => 35, 'pass_score' => 65, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => true, 'status' => 'open', 'is_published' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 13 - Giải thuật
            ['topic_id' => 13, 'created_by' => 1, 'title' => 'Cấu trúc dữ liệu & Thuật toán',         'description' => 'Kiểm tra kiến thức về Array, Tree và Sorting algorithms.',      'duration_mins' => 45, 'pass_score' => 70, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => true,  'is_active' => true, 'status' => 'open', 'is_published' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 17 - Kinh tế
            ['topic_id' => 17, 'created_by' => 5, 'title' => 'Kinh tế Vĩ mô - Nhập môn',                'description' => 'GDP, CPI, lạm phát và các chỉ số kinh tế cơ bản.',                'duration_mins' => 30, 'pass_score' => 60, 'shuffle_q' => true,  'shuffle_a' => true,  'show_explain' => false, 'is_active' => true, 'status' => 'open', 'is_published' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 20 - Thống kê
            ['topic_id' => 20, 'created_by' => 2, 'title' => 'Xác suất Thống kê - Bài tập lớn',         'description' => 'Phân phối chuẩn, kiểm định giả thuyết và hồi quy.',             'duration_mins' => 60, 'pass_score' => 50, 'shuffle_q' => false, 'shuffle_a' => true,  'show_explain' => true,  'is_active' => true, 'status' => 'open', 'is_published' => true,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
