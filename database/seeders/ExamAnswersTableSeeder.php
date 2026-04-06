<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamAnswersTableSeeder extends Seeder
{
    public function run(): void
    {
        // result_id từ ExamResultsTableSeeder (1-20)
        // Seed chi tiết đáp án cho result_id 1 (student 8, exam 1, 4 câu) và result_id 6 (student 8, exam 3, 3 câu)
        // Các result còn lại seed 1-2 câu đại diện — tổng đúng 20 bản ghi
        DB::table('exam_answers')->insert([
            // Result 1 (student 8, exam 1 – PHP cơ bản, 4 câu, đúng hết)
            ['result_id' => 1, 'question_id' => 1,  'answer_id' => 1,  'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. Tất cả biến PHP bắt đầu bằng $.'],
            ['result_id' => 1, 'question_id' => 2,  'answer_id' => 5,  'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. echo là cách in ra màn hình phổ biến nhất trong PHP.'],
            ['result_id' => 1, 'question_id' => 3,  'answer_id' => 9,  'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. int và string đều là kiểu nguyên thủy.'],
            ['result_id' => 1, 'question_id' => 4,  'answer_id' => null,'text_answer' => '.', 'is_correct' => true,  'ai_explanation' => 'Đúng. Dấu chấm (.) là toán tử nối chuỗi trong PHP.'],

            // Result 2 (student 9, exam 1 – 3/4 đúng)
            ['result_id' => 2, 'question_id' => 1,  'answer_id' => 1,  'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. Biến PHP bắt đầu bằng $.'],
            ['result_id' => 2, 'question_id' => 3,  'answer_id' => 11, 'text_answer' => null, 'is_correct' => false, 'ai_explanation' => 'Sai. array là kiểu phức hợp, không phải nguyên thủy.'],

            // Result 3 (student 10, exam 1 – 2/4 đúng)
            ['result_id' => 3, 'question_id' => 2,  'answer_id' => 7,  'text_answer' => null, 'is_correct' => false, 'ai_explanation' => 'Sai. printf() không phải lệnh in phổ biến nhất; dùng echo.'],
            ['result_id' => 3, 'question_id' => 4,  'answer_id' => null,'text_answer' => '@','is_correct' => false, 'ai_explanation' => 'Sai. Toán tử nối chuỗi PHP là dấu chấm (.), không phải @.'],

            // Result 6 (student 8, exam 3 – Laravel, 3/3 đúng)
            ['result_id' => 6, 'question_id' => 5,  'answer_id' => 13, 'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. php artisan make:controller là lệnh chuẩn tạo controller.'],
            ['result_id' => 6, 'question_id' => 6,  'answer_id' => 17, 'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. Middleware được đăng ký trong app/Http/Kernel.php.'],
            ['result_id' => 6, 'question_id' => 7,  'answer_id' => 21, 'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. firstOrCreate() tìm hoặc tạo mới bản ghi.'],

            // Result 9 (student 9, exam 5 – MySQL, 3/3 đúng)
            ['result_id' => 9, 'question_id' => 8,  'answer_id' => 25, 'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. SELECT * FROM users lấy toàn bộ dữ liệu.'],
            ['result_id' => 9, 'question_id' => 10, 'answer_id' => 37, 'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. Giải thích đầy đủ sự khác biệt INNER và LEFT JOIN.'],

            // Result 11 (student 10, exam 7 – JS, 2/2 đúng)
            ['result_id' => 11, 'question_id' => 11, 'answer_id' => 41, 'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. const không thể reassign sau khi khai báo.'],
            ['result_id' => 11, 'question_id' => 12, 'answer_id' => 45, 'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. Promise.all chờ tất cả, Promise.race lấy cái đầu tiên.'],

            // Result 13 (student 11, exam 9 – Git, 2/2 đúng)
            ['result_id' => 13, 'question_id' => 13, 'answer_id' => 49, 'text_answer' => null,     'is_correct' => true,  'ai_explanation' => 'Đúng. git log hiển thị lịch sử commit.'],
            ['result_id' => 13, 'question_id' => 14, 'answer_id' => null,'text_answer' => 'merge','is_correct' => true,  'ai_explanation' => 'Đúng. git merge feature gộp branch feature vào nhánh hiện tại.'],

            // Result 15 (student 13, exam 11 – Scrum, 2/2 đúng)
            ['result_id' => 15, 'question_id' => 15, 'answer_id' => 53, 'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. Product Owner chịu trách nhiệm về giá trị sản phẩm.'],
            ['result_id' => 15, 'question_id' => 16, 'answer_id' => 57, 'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. Sprint Planning là một trong 5 sự kiện Scrum.'],

            // Result 17 (student 8, exam 13 – OWASP, 2/2 đúng)
            ['result_id' => 17, 'question_id' => 17, 'answer_id' => 65, 'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. Prepared Statements là biện pháp hiệu quả nhất chống SQL Injection.'],
            ['result_id' => 17, 'question_id' => 18, 'answer_id' => 69, 'text_answer' => null, 'is_correct' => true,  'ai_explanation' => 'Đúng. CSRF lợi dụng cookie/session tự động đính kèm theo request.'],
        ]);
    }
}
