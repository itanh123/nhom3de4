<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionsTableSeeder extends Seeder
{
    public function run(): void
    {
        // source_document IDs: 1-20 (documents seeded above, nullable)
        DB::table('questions')->insert([
            // Topic 1 - PHP cơ bản
            ['topic_id' => 1, 'created_by' => 3, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Trong PHP, biến được khai báo bắt đầu bằng ký tự nào?',                              'explanation' => 'Tất cả biến trong PHP đều bắt đầu bằng dấu $ (dollar sign).', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 1, 'created_by' => 3, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Hàm nào dùng để in ra màn hình trong PHP?',                                          'explanation' => 'echo và print đều in ra màn hình, nhưng echo phổ biến hơn.', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 5 - Tiếng Anh
            ['topic_id' => 5, 'created_by' => 5, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Which is the past tense of the verb "go"?',                                         'explanation' => '"Went" is the irregular past tense of "go".', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 5, 'created_by' => 5, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => '____ are you from? - I am from Vietnam.',                                           'explanation' => '"Where" is used to ask about a place.', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 6 - Toán học
            ['topic_id' => 6, 'created_by' => 5, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Đạo hàm của hàm số y = x^2 là gì?',                                                  'explanation' => 'Theo công thức (x^n)\' = n*x^(n-1), đạo hàm của x^2 là 2x.', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 6, 'created_by' => 5, 'type' => 'single_choice',   'difficulty' => 'medium', 'content' => 'Giá trị của sin(90°) là bao nhiêu?',                                                  'explanation' => 'Trong vòng tròn lượng giác, sin(90°) = 1.', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 7 - Lịch sử
            ['topic_id' => 7, 'created_by' => 7, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Ai là người đọc bản Tuyên ngôn Độc lập khai sinh ra nước Việt Nam Dân chủ Cộng hòa?', 'explanation' => 'Chủ tịch Hồ Chí Minh đọc Tuyên ngôn Độc lập ngày 2/9/1945.', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 7, 'created_by' => 7, 'type' => 'single_choice',   'difficulty' => 'medium', 'content' => 'Chiến dịch Điện Biên Phủ kết thúc vào năm nào?',                                      'explanation' => 'Chiến thắng Điện Biên Phủ lừng lẫy năm châu chấn động địa cầu diễn ra năm 1954.', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 8 - Văn học
            ['topic_id' => 8, 'created_by' => 7, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Ai là tác giả của Truyện Kiều?',                                                      'explanation' => 'Đại thi hào Nguyễn Du là tác giả của kiệt tác Truyện Kiều.', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 8, 'created_by' => 7, 'type' => 'single_choice',   'difficulty' => 'medium', 'content' => 'Nhân vật chính trong tác phẩm "Lão Hạc" của Nam Cao là ai?',                          'explanation' => 'Nhân vật chính là Lão Hạc, một người nông dân nghèo lương thiện.', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 9 - Vật lý
            ['topic_id' => 9, 'created_by' => 3, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Nhiệt độ sôi của nước tinh khiết ở áp suất tiêu chuẩn là bao nhiêu?',               'explanation' => 'Ở áp suất tiêu chuẩn, nước sôi ở 100 độ C.', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 9, 'created_by' => 3, 'type' => 'single_choice',   'difficulty' => 'medium', 'content' => 'Công thức tính vận tốc là gì?',                                                     'explanation' => 'v = s / t (vận tốc bằng quãng đường chia thời gian).', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 10 - Hóa học
            ['topic_id' => 10, 'created_by' => 4, 'type' => 'single_choice',  'difficulty' => 'easy',   'content' => 'Công thức hóa học của nước là gì?',                                                   'explanation' => 'Nước gồm 2 nguyên tử Hydro và 1 nguyên tử Oxy (H2O).', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 10, 'created_by' => 4, 'type' => 'single_choice',  'difficulty' => 'medium', 'content' => 'Nguyên tố phổ biến nhất trong vũ trụ là gì?',                                         'explanation' => 'Hydro (H) là nguyên tố nhẹ nhất và phổ biến nhất trong vũ trụ.', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 13 - Giải thuật
            ['topic_id' => 13, 'created_by' => 1, 'type' => 'single_choice',  'difficulty' => 'medium', 'content' => 'Thuật toán sắp xếp nào có độ phức tạp trung bình O(n log n)?',                    'explanation' => 'Merge Sort và Quick Sort đều có độ phức tạp trung bình O(n log n).', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 13, 'created_by' => 1, 'type' => 'single_choice',  'difficulty' => 'hard',   'content' => 'Độ phức tạp của thuật toán tìm kiếm nhị phân là gì?',                                'explanation' => 'Tìm kiếm nhị phân có độ phức tạp là O(log n).', 'ai_generated' => false, 'source_document' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
