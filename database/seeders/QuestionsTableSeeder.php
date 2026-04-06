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
            ['topic_id' => 1, 'created_by' => 3, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Trong PHP, biến được khai báo bắt đầu bằng ký tự nào?',                              'explanation' => 'Tất cả biến trong PHP đều bắt đầu bằng dấu $ (dollar sign).', 'ai_generated' => false, 'source_document' => 1, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 1, 'created_by' => 3, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Hàm nào dùng để in ra màn hình trong PHP?',                                          'explanation' => 'echo và print đều in ra màn hình, nhưng echo phổ biến hơn và không trả về giá trị.', 'ai_generated' => false, 'source_document' => 1, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 1, 'created_by' => 3, 'type' => 'multiple_choice', 'difficulty' => 'medium', 'content' => 'Kiểu dữ liệu nào là kiểu nguyên thủy (primitive) trong PHP?',                      'explanation' => 'PHP có 4 kiểu nguyên thủy: int, float, string, bool.', 'ai_generated' => false, 'source_document' => 2, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 1, 'created_by' => 3, 'type' => 'fill_in_blank',   'difficulty' => 'medium', 'content' => 'Trong PHP, toán tử nối chuỗi là ký tự ____.',                                       'explanation' => 'Dấu chấm (.) là toán tử nối chuỗi trong PHP.', 'ai_generated' => true,  'source_document' => 2, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 2 - Laravel
            ['topic_id' => 2, 'created_by' => 3, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Lệnh Artisan nào dùng để tạo một Controller trong Laravel?',                       'explanation' => 'php artisan make:controller là lệnh tạo controller mới.', 'ai_generated' => false, 'source_document' => 3, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 2, 'created_by' => 3, 'type' => 'single_choice',   'difficulty' => 'medium', 'content' => 'Trong Laravel, Middleware được đăng ký ở file nào?',                                'explanation' => 'Middleware được đăng ký tại app/Http/Kernel.php.', 'ai_generated' => false, 'source_document' => 3, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 2, 'created_by' => 3, 'type' => 'single_choice',   'difficulty' => 'hard',   'content' => 'Phương thức Eloquent nào dùng để lấy bản ghi đầu tiên hoặc tạo mới nếu chưa tồn tại?', 'explanation' => 'firstOrCreate() tìm bản ghi đầu tiên khớp điều kiện, nếu không có thì tạo mới.', 'ai_generated' => true, 'source_document' => 4, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 3 - MySQL
            ['topic_id' => 3, 'created_by' => 4, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Câu lệnh SQL nào dùng để lấy tất cả dữ liệu từ bảng users?',                      'explanation' => 'SELECT * FROM users lấy tất cả cột và hàng trong bảng users.', 'ai_generated' => false, 'source_document' => 5, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 3, 'created_by' => 4, 'type' => 'multiple_choice', 'difficulty' => 'medium', 'content' => 'Các dạng chuẩn hóa cơ sở dữ liệu nào thuộc chuẩn hóa thông thường (NF)?',        'explanation' => '1NF, 2NF, 3NF là ba dạng chuẩn hóa cơ bản nhất.', 'ai_generated' => false, 'source_document' => 6, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 3, 'created_by' => 4, 'type' => 'single_choice',   'difficulty' => 'hard',   'content' => 'Sự khác biệt giữa INNER JOIN và LEFT JOIN là gì?',                                  'explanation' => 'INNER JOIN chỉ trả về hàng khớp cả 2 bảng. LEFT JOIN trả về tất cả hàng bảng trái kể cả không khớp bên phải.', 'ai_generated' => false, 'source_document' => 5, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 5 - JavaScript
            ['topic_id' => 5, 'created_by' => 5, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Từ khóa nào trong ES6 dùng để khai báo biến không thể gán lại giá trị?',           'explanation' => 'const khai báo hằng số, không thể reassign nhưng object/array vẫn mutable.', 'ai_generated' => false, 'source_document' => 8, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 5, 'created_by' => 5, 'type' => 'single_choice',   'difficulty' => 'medium', 'content' => 'Promise.all() khác Promise.race() ở điểm nào?',                                    'explanation' => 'Promise.all() chờ tất cả resolve, Promise.race() resolve/reject ngay khi có 1 cái xong đầu tiên.', 'ai_generated' => true,  'source_document' => 9, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 7 - Git
            ['topic_id' => 7, 'created_by' => 7, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Lệnh Git nào dùng để xem lịch sử commit?',                                         'explanation' => 'git log hiển thị danh sách commit theo thứ tự từ mới nhất đến cũ nhất.', 'ai_generated' => false, 'source_document' => 11, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 7, 'created_by' => 7, 'type' => 'fill_in_blank',   'difficulty' => 'medium', 'content' => 'Lệnh để gộp branch feature vào branch main là: git ____ feature.',                 'explanation' => 'git merge feature (khi đang ở branch main) sẽ gộp branch feature vào.', 'ai_generated' => false, 'source_document' => 11, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 8 - Scrum
            ['topic_id' => 8, 'created_by' => 7, 'type' => 'single_choice',   'difficulty' => 'easy',   'content' => 'Trong Scrum, ai là người chịu trách nhiệm tối đa hóa giá trị sản phẩm?',          'explanation' => 'Product Owner là người quản lý Product Backlog và chịu trách nhiệm về giá trị sản phẩm.', 'ai_generated' => false, 'source_document' => 12, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 8, 'created_by' => 7, 'type' => 'multiple_choice', 'difficulty' => 'medium', 'content' => 'Các sự kiện (events) nào thuộc Scrum framework?',                                  'explanation' => 'Scrum có 5 sự kiện: Sprint, Sprint Planning, Daily Scrum, Sprint Review, Sprint Retrospective.', 'ai_generated' => false, 'source_document' => 12, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 9 - Bảo mật
            ['topic_id' => 9, 'created_by' => 3, 'type' => 'single_choice',   'difficulty' => 'medium', 'content' => 'Tấn công SQL Injection có thể được ngăn chặn bằng cách nào hiệu quả nhất?',       'explanation' => 'Sử dụng Prepared Statements / Parameterized Queries là cách hiệu quả nhất để ngăn SQL Injection.', 'ai_generated' => false, 'source_document' => 13, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 9, 'created_by' => 3, 'type' => 'single_choice',   'difficulty' => 'hard',   'content' => 'Cross-Site Request Forgery (CSRF) hoạt động dựa trên cơ chế nào?',                 'explanation' => 'CSRF lợi dụng session/cookie xác thực được gửi tự động theo mỗi request để thực hiện hành động trái phép.', 'ai_generated' => true,  'source_document' => 13, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Topic 13 - CTDL
            ['topic_id' => 13, 'created_by' => 1, 'type' => 'single_choice',  'difficulty' => 'medium', 'content' => 'Thuật toán sắp xếp nào có độ phức tạp trung bình O(n log n)?',                    'explanation' => 'Merge Sort và Quick Sort đều có độ phức tạp trung bình O(n log n).', 'ai_generated' => false, 'source_document' => 17, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['topic_id' => 13, 'created_by' => 1, 'type' => 'single_choice',  'difficulty' => 'hard',   'content' => 'Trong cấu trúc dữ liệu cây nhị phân tìm kiếm (BST), thao tác tìm kiếm có độ phức tạp tệ nhất là bao nhiêu?', 'explanation' => 'Trường hợp xấu nhất (cây lệch) là O(n) vì phải duyệt qua tất cả nút.', 'ai_generated' => false, 'source_document' => 17, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
