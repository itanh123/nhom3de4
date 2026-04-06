<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExamQuestionsTableSeeder extends Seeder
{
    public function run(): void
    {
        // exam_id 1-20, question_id 1-20
        // Mỗi exam sẽ có một số câu hỏi, tổng 20 bản ghi (unique exam_id + question_id)
        DB::table('exam_questions')->insert([
            // Exam 1 (PHP cơ bản chương 1) → Q1, Q2, Q3, Q4
            ['exam_id' => 1, 'question_id' => 1,  'display_order' => 1, 'point' => 2.50],
            ['exam_id' => 1, 'question_id' => 2,  'display_order' => 2, 'point' => 2.50],
            ['exam_id' => 1, 'question_id' => 3,  'display_order' => 3, 'point' => 2.50],
            ['exam_id' => 1, 'question_id' => 4,  'display_order' => 4, 'point' => 2.50],

            // Exam 3 (Laravel Routing) → Q5, Q6, Q7
            ['exam_id' => 3, 'question_id' => 5,  'display_order' => 1, 'point' => 3.00],
            ['exam_id' => 3, 'question_id' => 6,  'display_order' => 2, 'point' => 3.00],
            ['exam_id' => 3, 'question_id' => 7,  'display_order' => 3, 'point' => 4.00],

            // Exam 5 (SQL cơ bản) → Q8, Q9, Q10
            ['exam_id' => 5, 'question_id' => 8,  'display_order' => 1, 'point' => 3.00],
            ['exam_id' => 5, 'question_id' => 9,  'display_order' => 2, 'point' => 3.50],
            ['exam_id' => 5, 'question_id' => 10, 'display_order' => 3, 'point' => 3.50],

            // Exam 7 (JS ES6+) → Q11, Q12
            ['exam_id' => 7, 'question_id' => 11, 'display_order' => 1, 'point' => 5.00],
            ['exam_id' => 7, 'question_id' => 12, 'display_order' => 2, 'point' => 5.00],

            // Exam 9 (Git Fundamentals) → Q13, Q14
            ['exam_id' => 9, 'question_id' => 13, 'display_order' => 1, 'point' => 5.00],
            ['exam_id' => 9, 'question_id' => 14, 'display_order' => 2, 'point' => 5.00],

            // Exam 11 (Scrum Framework) → Q15, Q16
            ['exam_id' => 11, 'question_id' => 15, 'display_order' => 1, 'point' => 5.00],
            ['exam_id' => 11, 'question_id' => 16, 'display_order' => 2, 'point' => 5.00],

            // Exam 13 (OWASP) → Q17, Q18
            ['exam_id' => 13, 'question_id' => 17, 'display_order' => 1, 'point' => 5.00],
            ['exam_id' => 13, 'question_id' => 18, 'display_order' => 2, 'point' => 5.00],

            // Exam 15 (Thuật toán) → Q19, Q20
            ['exam_id' => 15, 'question_id' => 19, 'display_order' => 1, 'point' => 5.00],
            ['exam_id' => 15, 'question_id' => 20, 'display_order' => 2, 'point' => 5.00],
        ]);
    }
}
