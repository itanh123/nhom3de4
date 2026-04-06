<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamResultsTableSeeder extends Seeder
{
    public function run(): void
    {
        // student IDs: 8-20
        // exam IDs: 1-20 (dùng exam 1,3,5,7,9,11,13,15 đã có questions)
        DB::table('exam_results')->insert([
            // Exam 1 (PHP cơ bản) - nhiều student làm
            ['exam_id' => 1, 'student_id' => 8,  'started_at' => now()->subDays(10), 'submitted_at' => now()->subDays(10)->addMinutes(18), 'total_questions' => 4, 'correct_count' => 4, 'score_pct' => 100.00, 'passed' => true,  'ai_summary' => 'Học viên nắm vững toàn bộ kiến thức PHP cơ bản.', 'ai_suggestions' => 'Tiếp tục chuyển sang chủ đề hàm và mảng PHP.'],
            ['exam_id' => 1, 'student_id' => 9,  'started_at' => now()->subDays(9),  'submitted_at' => now()->subDays(9)->addMinutes(20),  'total_questions' => 4, 'correct_count' => 3, 'score_pct' => 75.00,  'passed' => true,  'ai_summary' => 'Học viên hiểu tốt cú pháp cơ bản, còn nhầm lẫn về kiểu dữ liệu.', 'ai_suggestions' => 'Ôn lại phần kiểu dữ liệu nguyên thủy và kiểu phức hợp.'],
            ['exam_id' => 1, 'student_id' => 10, 'started_at' => now()->subDays(8),  'submitted_at' => now()->subDays(8)->addMinutes(15),  'total_questions' => 4, 'correct_count' => 2, 'score_pct' => 50.00,  'passed' => false, 'ai_summary' => 'Học viên chưa nắm vững nền tảng PHP.', 'ai_suggestions' => 'Cần đọc lại tài liệu từ đầu và thực hành thêm bài tập.'],
            ['exam_id' => 1, 'student_id' => 11, 'started_at' => now()->subDays(7),  'submitted_at' => now()->subDays(7)->addMinutes(19),  'total_questions' => 4, 'correct_count' => 3, 'score_pct' => 75.00,  'passed' => true,  'ai_summary' => 'Kết quả khá tốt. Cần củng cố phần toán tử.', 'ai_suggestions' => null],
            ['exam_id' => 1, 'student_id' => 13, 'started_at' => now()->subDays(6),  'submitted_at' => now()->subDays(6)->addMinutes(17),  'total_questions' => 4, 'correct_count' => 4, 'score_pct' => 100.00, 'passed' => true,  'ai_summary' => 'Xuất sắc! Học viên trả lời đúng tất cả câu hỏi.', 'ai_suggestions' => 'Sẵn sàng tiến lên level nâng cao.'],

            // Exam 3 (Laravel) - student làm
            ['exam_id' => 3, 'student_id' => 8,  'started_at' => now()->subDays(5),  'submitted_at' => now()->subDays(5)->addMinutes(22),  'total_questions' => 3, 'correct_count' => 3, 'score_pct' => 100.00, 'passed' => true,  'ai_summary' => 'Nắm tốt routing và middleware Laravel.', 'ai_suggestions' => 'Học tiếp Eloquent ORM và relationships.'],
            ['exam_id' => 3, 'student_id' => 14, 'started_at' => now()->subDays(5),  'submitted_at' => now()->subDays(5)->addMinutes(24),  'total_questions' => 3, 'correct_count' => 2, 'score_pct' => 66.67,  'passed' => true,  'ai_summary' => 'Hiểu routing cơ bản, chưa rõ về middleware.', 'ai_suggestions' => 'Ôn lại Kernel.php và cách đăng ký middleware.'],
            ['exam_id' => 3, 'student_id' => 15, 'started_at' => now()->subDays(4),  'submitted_at' => now()->subDays(4)->addMinutes(25),  'total_questions' => 3, 'correct_count' => 1, 'score_pct' => 33.33,  'passed' => false, 'ai_summary' => 'Học viên cần ôn lại kiến thức Laravel cơ bản.', 'ai_suggestions' => 'Xem lại tài liệu Laravel routing và làm thêm bài tập thực hành.'],

            // Exam 5 (MySQL)
            ['exam_id' => 5, 'student_id' => 9,  'started_at' => now()->subDays(4),  'submitted_at' => now()->subDays(4)->addMinutes(18),  'total_questions' => 3, 'correct_count' => 3, 'score_pct' => 100.00, 'passed' => true,  'ai_summary' => 'Thành thạo các câu lệnh SQL cơ bản.', 'ai_suggestions' => 'Học tiếp về JOIN và subquery.'],
            ['exam_id' => 5, 'student_id' => 16, 'started_at' => now()->subDays(3),  'submitted_at' => now()->subDays(3)->addMinutes(20),  'total_questions' => 3, 'correct_count' => 2, 'score_pct' => 66.67,  'passed' => true,  'ai_summary' => 'Nắm được SELECT cơ bản, còn yếu phần JOIN.', 'ai_suggestions' => 'Luyện tập thêm các câu JOIN phức tạp.'],

            // Exam 7 (JS ES6+)
            ['exam_id' => 7, 'student_id' => 10, 'started_at' => now()->subDays(3),  'submitted_at' => now()->subDays(3)->addMinutes(23),  'total_questions' => 2, 'correct_count' => 2, 'score_pct' => 100.00, 'passed' => true,  'ai_summary' => 'Nắm rõ ES6 const/let và async programming.', 'ai_suggestions' => 'Học tiếp về modules và class ES6.'],
            ['exam_id' => 7, 'student_id' => 17, 'started_at' => now()->subDays(2),  'submitted_at' => now()->subDays(2)->addMinutes(22),  'total_questions' => 2, 'correct_count' => 1, 'score_pct' => 50.00,  'passed' => false, 'ai_summary' => 'Chưa nắm rõ Promise và async/await.', 'ai_suggestions' => 'Xem lại tài liệu Async JS và thực hành ví dụ.'],

            // Exam 9 (Git)
            ['exam_id' => 9, 'student_id' => 11, 'started_at' => now()->subDays(2),  'submitted_at' => now()->subDays(2)->addMinutes(14),  'total_questions' => 2, 'correct_count' => 2, 'score_pct' => 100.00, 'passed' => true,  'ai_summary' => 'Sử dụng Git thành thạo.', 'ai_suggestions' => 'Học tiếp Git Flow và GitHub Actions.'],
            ['exam_id' => 9, 'student_id' => 19, 'started_at' => now()->subDays(1),  'submitted_at' => now()->subDays(1)->addMinutes(15),  'total_questions' => 2, 'correct_count' => 1, 'score_pct' => 50.00,  'passed' => false, 'ai_summary' => 'Biết lệnh cơ bản, chưa hiểu merge workflow.', 'ai_suggestions' => 'Luyện tập git merge và rebase trên repo thực tế.'],

            // Exam 11 (Scrum)
            ['exam_id' => 11, 'student_id' => 13, 'started_at' => now()->subDays(1), 'submitted_at' => now()->subDays(1)->addMinutes(40),  'total_questions' => 2, 'correct_count' => 2, 'score_pct' => 100.00, 'passed' => true,  'ai_summary' => 'Nắm chắc vai trò và sự kiện trong Scrum.', 'ai_suggestions' => null],
            ['exam_id' => 11, 'student_id' => 20, 'started_at' => now()->subHours(5),'submitted_at' => now()->subHours(5)->addMinutes(45),  'total_questions' => 2, 'correct_count' => 1, 'score_pct' => 50.00,  'passed' => false, 'ai_summary' => 'Nhầm lẫn giữa vai trò Scrum Master và Product Owner.', 'ai_suggestions' => 'Đọc lại Scrum Guide 2020, tập trung phần accountabilities.'],

            // Exam 13 (OWASP)
            ['exam_id' => 13, 'student_id' => 8,  'started_at' => now()->subHours(3),'submitted_at' => now()->subHours(3)->addMinutes(28),  'total_questions' => 2, 'correct_count' => 2, 'score_pct' => 100.00, 'passed' => true,  'ai_summary' => 'Nhận biết tốt các lỗ hổng bảo mật phổ biến.', 'ai_suggestions' => 'Học tiếp CORS, HTTPS và Security Headers.'],

            // Exam 15 (Thuật toán)
            ['exam_id' => 15, 'student_id' => 9,  'started_at' => now()->subHours(2),'submitted_at' => now()->subHours(2)->addMinutes(32),  'total_questions' => 2, 'correct_count' => 1, 'score_pct' => 50.00,  'passed' => false, 'ai_summary' => 'Nắm được khái niệm nhưng chưa rõ độ phức tạp BST.', 'ai_suggestions' => 'Ôn lại cây nhị phân và thực hành cài đặt BST.'],
            ['exam_id' => 15, 'student_id' => 16, 'started_at' => now()->subHours(1),'submitted_at' => now()->subHours(1)->addMinutes(33),  'total_questions' => 2, 'correct_count' => 2, 'score_pct' => 100.00, 'passed' => true,  'ai_summary' => 'Hiểu rõ Big-O và đặc điểm các thuật toán sắp xếp.', 'ai_suggestions' => 'Chuyển sang học cấu trúc dữ liệu cây và đồ thị.'],
        ]);
    }
}
