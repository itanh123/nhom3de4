<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnswersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Mỗi câu hỏi single/multiple_choice có 4 đáp án.
        // fill_in_blank (question_id 4, 14) không cần answer records.
        // Chú ý: question IDs 1-20 theo thứ tự seed ở QuestionsTableSeeder.
        // Seed đúng 20 answer records (5 câu × 4 đáp án = 20, lấy câu 1-5).

        DB::table('answers')->insert([
            // Q1: Biến PHP bắt đầu bằng ký tự nào? (single_choice, easy)
            ['question_id' => 1, 'option_text' => '$',  'is_correct' => true,  'display_order' => 1],
            ['question_id' => 1, 'option_text' => '@',  'is_correct' => false, 'display_order' => 2],
            ['question_id' => 1, 'option_text' => '#',  'is_correct' => false, 'display_order' => 3],
            ['question_id' => 1, 'option_text' => '&',  'is_correct' => false, 'display_order' => 4],

            // Q2: Hàm in ra màn hình trong PHP? (single_choice, easy)
            ['question_id' => 2, 'option_text' => 'echo',    'is_correct' => true,  'display_order' => 1],
            ['question_id' => 2, 'option_text' => 'console.log()', 'is_correct' => false, 'display_order' => 2],
            ['question_id' => 2, 'option_text' => 'printf()', 'is_correct' => false, 'display_order' => 3],
            ['question_id' => 2, 'option_text' => 'write()',  'is_correct' => false, 'display_order' => 4],

            // Q3: Kiểu nguyên thủy trong PHP? (multiple_choice, medium) — 2 đáp án đúng
            ['question_id' => 3, 'option_text' => 'int',    'is_correct' => true,  'display_order' => 1],
            ['question_id' => 3, 'option_text' => 'string', 'is_correct' => true,  'display_order' => 2],
            ['question_id' => 3, 'option_text' => 'array',  'is_correct' => false, 'display_order' => 3],
            ['question_id' => 3, 'option_text' => 'object', 'is_correct' => false, 'display_order' => 4],

            // Q5: Lệnh Artisan tạo Controller? (single_choice, easy)
            ['question_id' => 5, 'option_text' => 'php artisan make:controller', 'is_correct' => true,  'display_order' => 1],
            ['question_id' => 5, 'option_text' => 'php artisan create:controller', 'is_correct' => false, 'display_order' => 2],
            ['question_id' => 5, 'option_text' => 'php artisan generate:controller', 'is_correct' => false, 'display_order' => 3],
            ['question_id' => 5, 'option_text' => 'php artisan new:controller', 'is_correct' => false, 'display_order' => 4],

            // Q6: Middleware đăng ký ở file nào? (single_choice, medium)
            ['question_id' => 6, 'option_text' => 'app/Http/Kernel.php',          'is_correct' => true,  'display_order' => 1],
            ['question_id' => 6, 'option_text' => 'config/middleware.php',         'is_correct' => false, 'display_order' => 2],
            ['question_id' => 6, 'option_text' => 'app/Providers/AppServiceProvider.php', 'is_correct' => false, 'display_order' => 3],
            ['question_id' => 6, 'option_text' => 'routes/web.php',                'is_correct' => false, 'display_order' => 4],

            // Q7: Eloquent firstOrCreate? (single_choice, hard)
            ['question_id' => 7, 'option_text' => 'firstOrCreate()',  'is_correct' => true,  'display_order' => 1],
            ['question_id' => 7, 'option_text' => 'findOrCreate()',   'is_correct' => false, 'display_order' => 2],
            ['question_id' => 7, 'option_text' => 'updateOrCreate()', 'is_correct' => false, 'display_order' => 3],
            ['question_id' => 7, 'option_text' => 'getOrNew()',       'is_correct' => false, 'display_order' => 4],

            // Q8: SELECT * FROM users? (single_choice, easy)
            ['question_id' => 8, 'option_text' => 'SELECT * FROM users',       'is_correct' => true,  'display_order' => 1],
            ['question_id' => 8, 'option_text' => 'GET ALL FROM users',        'is_correct' => false, 'display_order' => 2],
            ['question_id' => 8, 'option_text' => 'FETCH * FROM users',        'is_correct' => false, 'display_order' => 3],
            ['question_id' => 8, 'option_text' => 'SELECT ALL IN users',       'is_correct' => false, 'display_order' => 4],

            // Q9: Dạng chuẩn hóa NF? (multiple_choice, medium) — 3 đúng
            ['question_id' => 9, 'option_text' => '1NF', 'is_correct' => true,  'display_order' => 1],
            ['question_id' => 9, 'option_text' => '2NF', 'is_correct' => true,  'display_order' => 2],
            ['question_id' => 9, 'option_text' => '3NF', 'is_correct' => true,  'display_order' => 3],
            ['question_id' => 9, 'option_text' => '5NF', 'is_correct' => false, 'display_order' => 4],

            // Q10: INNER JOIN vs LEFT JOIN? (single_choice, hard)
            ['question_id' => 10, 'option_text' => 'INNER JOIN chỉ trả về hàng khớp cả 2 bảng; LEFT JOIN trả về tất cả hàng bảng trái kể cả không khớp bên phải.', 'is_correct' => true,  'display_order' => 1],
            ['question_id' => 10, 'option_text' => 'Cả hai đều giống nhau, chỉ khác tên gọi.',  'is_correct' => false, 'display_order' => 2],
            ['question_id' => 10, 'option_text' => 'LEFT JOIN chỉ lấy hàng bảng bên phải.',     'is_correct' => false, 'display_order' => 3],
            ['question_id' => 10, 'option_text' => 'INNER JOIN trả về tất cả hàng từ cả 2 bảng.', 'is_correct' => false, 'display_order' => 4],

            // Q11: const trong ES6? (single_choice, easy)
            ['question_id' => 11, 'option_text' => 'const',  'is_correct' => true,  'display_order' => 1],
            ['question_id' => 11, 'option_text' => 'let',    'is_correct' => false, 'display_order' => 2],
            ['question_id' => 11, 'option_text' => 'var',    'is_correct' => false, 'display_order' => 3],
            ['question_id' => 11, 'option_text' => 'final',  'is_correct' => false, 'display_order' => 4],

            // Q12: Promise.all vs Promise.race? (single_choice, medium)
            ['question_id' => 12, 'option_text' => 'Promise.all() chờ tất cả; Promise.race() resolve ngay khi 1 cái xong đầu tiên.', 'is_correct' => true,  'display_order' => 1],
            ['question_id' => 12, 'option_text' => 'Cả hai đều chờ tất cả promise hoàn thành.',  'is_correct' => false, 'display_order' => 2],
            ['question_id' => 12, 'option_text' => 'Promise.race() chạy song song, Promise.all() chạy tuần tự.', 'is_correct' => false, 'display_order' => 3],
            ['question_id' => 12, 'option_text' => 'Promise.all() reject khi 1 cái fail; Promise.race() bỏ qua lỗi.', 'is_correct' => false, 'display_order' => 4],

            // Q13: git log? (single_choice, easy)
            ['question_id' => 13, 'option_text' => 'git log',      'is_correct' => true,  'display_order' => 1],
            ['question_id' => 13, 'option_text' => 'git history',  'is_correct' => false, 'display_order' => 2],
            ['question_id' => 13, 'option_text' => 'git show',     'is_correct' => false, 'display_order' => 3],
            ['question_id' => 13, 'option_text' => 'git commits',  'is_correct' => false, 'display_order' => 4],

            // Q15: Product Owner trong Scrum? (single_choice, easy)
            ['question_id' => 15, 'option_text' => 'Product Owner',   'is_correct' => true,  'display_order' => 1],
            ['question_id' => 15, 'option_text' => 'Scrum Master',    'is_correct' => false, 'display_order' => 2],
            ['question_id' => 15, 'option_text' => 'Development Team','is_correct' => false, 'display_order' => 3],
            ['question_id' => 15, 'option_text' => 'Stakeholder',     'is_correct' => false, 'display_order' => 4],

            // Q16: Scrum events? (multiple_choice, medium)
            ['question_id' => 16, 'option_text' => 'Sprint Planning',      'is_correct' => true,  'display_order' => 1],
            ['question_id' => 16, 'option_text' => 'Daily Scrum',          'is_correct' => true,  'display_order' => 2],
            ['question_id' => 16, 'option_text' => 'Sprint Review',        'is_correct' => true,  'display_order' => 3],
            ['question_id' => 16, 'option_text' => 'Backlog Refinement',   'is_correct' => false, 'display_order' => 4],

            // Q17: SQL Injection prevention? (single_choice, medium)
            ['question_id' => 17, 'option_text' => 'Sử dụng Prepared Statements',      'is_correct' => true,  'display_order' => 1],
            ['question_id' => 17, 'option_text' => 'Chỉ cho phép GET request',         'is_correct' => false, 'display_order' => 2],
            ['question_id' => 17, 'option_text' => 'Mã hóa password người dùng',       'is_correct' => false, 'display_order' => 3],
            ['question_id' => 17, 'option_text' => 'Sử dụng HTTPS',                    'is_correct' => false, 'display_order' => 4],

            // Q18: CSRF? (single_choice, hard)
            ['question_id' => 18, 'option_text' => 'Lợi dụng session/cookie tự động gửi theo request để thực hiện hành động trái phép.', 'is_correct' => true,  'display_order' => 1],
            ['question_id' => 18, 'option_text' => 'Chèn script độc hại vào trang web để đánh cắp dữ liệu người dùng khác.',             'is_correct' => false, 'display_order' => 2],
            ['question_id' => 18, 'option_text' => 'Tấn công bằng cách dò mật khẩu brute force.',                                        'is_correct' => false, 'display_order' => 3],
            ['question_id' => 18, 'option_text' => 'Giả mạo địa chỉ IP nguồn của packet.',                                               'is_correct' => false, 'display_order' => 4],

            // Q19: O(n log n) sort? (single_choice, medium)
            ['question_id' => 19, 'option_text' => 'Merge Sort',    'is_correct' => true,  'display_order' => 1],
            ['question_id' => 19, 'option_text' => 'Bubble Sort',   'is_correct' => false, 'display_order' => 2],
            ['question_id' => 19, 'option_text' => 'Selection Sort','is_correct' => false, 'display_order' => 3],
            ['question_id' => 19, 'option_text' => 'Insertion Sort','is_correct' => false, 'display_order' => 4],

            // Q20: BST worst case? (single_choice, hard)
            ['question_id' => 20, 'option_text' => 'O(n)',       'is_correct' => true,  'display_order' => 1],
            ['question_id' => 20, 'option_text' => 'O(log n)',   'is_correct' => false, 'display_order' => 2],
            ['question_id' => 20, 'option_text' => 'O(n log n)', 'is_correct' => false, 'display_order' => 3],
            ['question_id' => 20, 'option_text' => 'O(1)',       'is_correct' => false, 'display_order' => 4],
        ]);
    }
}
