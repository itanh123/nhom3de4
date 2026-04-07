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
            // Q1: Biến PHP bắt đầu bằng ký tự nào?
            ['question_id' => 1, 'option_text' => '$',  'is_correct' => true,  'display_order' => 1],
            ['question_id' => 1, 'option_text' => '@',  'is_correct' => false, 'display_order' => 2],
            ['question_id' => 1, 'option_text' => '#',  'is_correct' => false, 'display_order' => 3],
            ['question_id' => 1, 'option_text' => '&',  'is_correct' => false, 'display_order' => 4],

            // Q2: Hàm in ra màn hình trong PHP?
            ['question_id' => 2, 'option_text' => 'echo',    'is_correct' => true,  'display_order' => 1],
            ['question_id' => 2, 'option_text' => 'print_r()', 'is_correct' => false, 'display_order' => 2],
            ['question_id' => 2, 'option_text' => 'scanf()',  'is_correct' => false, 'display_order' => 3],
            ['question_id' => 2, 'option_text' => 'write()',  'is_correct' => false, 'display_order' => 4],

            // Q3: Which is the past tense of "go"?
            ['question_id' => 3, 'option_text' => 'went',   'is_correct' => true,  'display_order' => 1],
            ['question_id' => 3, 'option_text' => 'gone',   'is_correct' => false, 'display_order' => 2],
            ['question_id' => 3, 'option_text' => 'goed',   'is_correct' => false, 'display_order' => 3],
            ['question_id' => 3, 'option_text' => 'goes',   'is_correct' => false, 'display_order' => 4],

            // Q4: Where are you from?
            ['question_id' => 4, 'option_text' => 'Where',  'is_correct' => true,  'display_order' => 1],
            ['question_id' => 4, 'option_text' => 'What',   'is_correct' => false, 'display_order' => 2],
            ['question_id' => 4, 'option_text' => 'When',   'is_correct' => false, 'display_order' => 3],
            ['question_id' => 4, 'option_text' => 'Who',    'is_correct' => false, 'display_order' => 4],

            // Q5: Đạo hàm x^2
            ['question_id' => 5, 'option_text' => '2x',     'is_correct' => true,  'display_order' => 1],
            ['question_id' => 5, 'option_text' => 'x',      'is_correct' => false, 'display_order' => 2],
            ['question_id' => 5, 'option_text' => 'x^2',    'is_correct' => false, 'display_order' => 3],
            ['question_id' => 5, 'option_text' => '2',      'is_correct' => false, 'display_order' => 4],

            // Q6: sin(90)
            ['question_id' => 6, 'option_text' => '1',      'is_correct' => true,  'display_order' => 1],
            ['question_id' => 6, 'option_text' => '0',      'is_correct' => false, 'display_order' => 2],
            ['question_id' => 6, 'option_text' => '0.5',    'is_correct' => false, 'display_order' => 3],
            ['question_id' => 6, 'option_text' => '-1',     'is_correct' => false, 'display_order' => 4],

            // Q7: Tuyên ngôn độc lập
            ['question_id' => 7, 'option_text' => 'Hồ Chí Minh', 'is_correct' => true,  'display_order' => 1],
            ['question_id' => 7, 'option_text' => 'Võ Nguyên Giáp', 'is_correct' => false, 'display_order' => 2],
            ['question_id' => 7, 'option_text' => 'Phan Bội Châu', 'is_correct' => false, 'display_order' => 3],
            ['question_id' => 7, 'option_text' => 'Trần Phú',   'is_correct' => false, 'display_order' => 4],

            // Q8: Điện Biên Phủ
            ['question_id' => 8, 'option_text' => '1954',   'is_correct' => true,  'display_order' => 1],
            ['question_id' => 8, 'option_text' => '1945',   'is_correct' => false, 'display_order' => 2],
            ['question_id' => 8, 'option_text' => '1975',   'is_correct' => false, 'display_order' => 3],
            ['question_id' => 8, 'option_text' => '1930',   'is_correct' => false, 'display_order' => 4],

            // Q9: Tác giả Truyện Kiều
            ['question_id' => 9, 'option_text' => 'Nguyễn Du',  'is_correct' => true,  'display_order' => 1],
            ['question_id' => 9, 'option_text' => 'Nguyễn Trãi', 'is_correct' => false, 'display_order' => 2],
            ['question_id' => 9, 'option_text' => 'Tố Hữu',     'is_correct' => false, 'display_order' => 3],
            ['question_id' => 9, 'option_text' => 'Xuân Diệu',  'is_correct' => false, 'display_order' => 4],

            // Q10: Lão Hạc
            ['question_id' => 10, 'option_text' => 'Lão Hạc',   'is_correct' => true,  'display_order' => 1],
            ['question_id' => 10, 'option_text' => 'Ông Giáo',  'is_correct' => false, 'display_order' => 2],
            ['question_id' => 10, 'option_text' => 'Chị Dậu',   'is_correct' => false, 'display_order' => 3],
            ['question_id' => 10, 'option_text' => 'Anh Pha',   'is_correct' => false, 'display_order' => 4],

            // Q11: Nhiệt độ sôi của nước
            ['question_id' => 11, 'option_text' => '100°C',    'is_correct' => true,  'display_order' => 1],
            ['question_id' => 11, 'option_text' => '0°C',      'is_correct' => false, 'display_order' => 2],
            ['question_id' => 11, 'option_text' => '50°C',     'is_correct' => false, 'display_order' => 3],
            ['question_id' => 11, 'option_text' => '200°C',    'is_correct' => false, 'display_order' => 4],

            // Q12: v = s / t
            ['question_id' => 12, 'option_text' => 'v = s / t', 'is_correct' => true,  'display_order' => 1],
            ['question_id' => 12, 'option_text' => 'v = s * t', 'is_correct' => false, 'display_order' => 2],
            ['question_id' => 12, 'option_text' => 'v = t / s', 'is_correct' => false, 'display_order' => 3],
            ['question_id' => 12, 'option_text' => 'v = s + t', 'is_correct' => false, 'display_order' => 4],

            // Q13: H2O
            ['question_id' => 13, 'option_text' => 'H2O',       'is_correct' => true,  'display_order' => 1],
            ['question_id' => 13, 'option_text' => 'CO2',       'is_correct' => false, 'display_order' => 2],
            ['question_id' => 13, 'option_text' => 'NaCl',      'is_correct' => false, 'display_order' => 3],
            ['question_id' => 13, 'option_text' => 'O2',        'is_correct' => false, 'display_order' => 4],

            // Q14: Hydro
            ['question_id' => 14, 'option_text' => 'Hydro',     'is_correct' => true,  'display_order' => 1],
            ['question_id' => 14, 'option_text' => 'Oxy',       'is_correct' => false, 'display_order' => 2],
            ['question_id' => 14, 'option_text' => 'Sắt',       'is_correct' => false, 'display_order' => 3],
            ['question_id' => 14, 'option_text' => 'Vàng',      'is_correct' => false, 'display_order' => 4],

            // Q15: Merge Sort
            ['question_id' => 15, 'option_text' => 'Merge Sort',    'is_correct' => true,  'display_order' => 1],
            ['question_id' => 15, 'option_text' => 'Bubble Sort',   'is_correct' => false, 'display_order' => 2],
            ['question_id' => 15, 'option_text' => 'Selection Sort','is_correct' => false, 'display_order' => 3],
            ['question_id' => 15, 'option_text' => 'Insertion Sort','is_correct' => false, 'display_order' => 4],

            // Q16: O(log n)
            ['question_id' => 16, 'option_text' => 'O(log n)',   'is_correct' => true,  'display_order' => 1],
            ['question_id' => 16, 'option_text' => 'O(n)',       'is_correct' => false, 'display_order' => 2],
            ['question_id' => 16, 'option_text' => 'O(n^2)',     'is_correct' => false, 'display_order' => 3],
            ['question_id' => 16, 'option_text' => 'O(1)',       'is_correct' => false, 'display_order' => 4],
        ]);
    }
}
