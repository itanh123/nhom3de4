<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentsTableSeeder extends Seeder
{
    public function run(): void
    {
        // topic_id: 1-20, uploaded_by: teacher/admin IDs
        DB::table('documents')->insert([
            ['topic_id' => 1,  'uploaded_by' => 3, 'file_name' => 'php-co-ban-chuong-1.pdf',         'file_path' => 'documents/topics/1/php-co-ban-chuong-1.pdf',         'file_size' => 1024000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 2,  'uploaded_by' => 3, 'file_name' => 'laravel-routing.pdf',             'file_path' => 'documents/topics/2/laravel-routing.pdf',             'file_size' => 876000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 3,  'uploaded_by' => 4, 'file_name' => 'mysql-select-query.pdf',          'file_path' => 'documents/topics/3/mysql-select-query.pdf',          'file_size' => 640000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 4,  'uploaded_by' => 4, 'file_name' => 'html5-semantic-tags.pdf',         'file_path' => 'documents/topics/4/html5-semantic-tags.pdf',         'file_size' => 710000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 5,  'uploaded_by' => 5, 'file_name' => 'english-grammar-basics.pdf',      'file_path' => 'documents/topics/5/english-grammar-basics.pdf',      'file_size' => 520000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 6,  'uploaded_by' => 5, 'file_name' => 'toán-hoc-giải-tích-đạo-hàm.pdf',   'file_path' => 'documents/topics/6/toan-hoc-giai-tich.pdf',          'file_size' => 1200000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 7,  'uploaded_by' => 7, 'file_name' => 'lich-su-viet-nam-1945-1975.pdf',   'file_path' => 'documents/topics/7/lich-su-vn.pdf',                 'file_size' => 680000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 8,  'uploaded_by' => 7, 'file_name' => 'van-hoc-hien-dai-viet-nam.pdf',   'file_path' => 'documents/topics/8/van-hoc-vn.pdf',                 'file_size' => 460000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 9,  'uploaded_by' => 3, 'file_name' => 'vat-ly-nhiet-hoc-dai-cuong.pdf',  'file_path' => 'documents/topics/9/vat-ly-nhiet.pdf',                'file_size' => 1380000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 10, 'uploaded_by' => 4, 'file_name' => 'hoa-hoc-huu-co-co-ban.pdf',       'file_path' => 'documents/topics/10/hoa-hoc-huu-co.pdf',             'file_size' => 2200000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 11, 'uploaded_by' => 5, 'file_name' => 'dia-ly-tu-nhien-the-gioi.pdf',    'file_path' => 'documents/topics/11/dia-ly-tn.pdf',                  'file_size' => 1750000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 12, 'uploaded_by' => 7, 'file_name' => 'sinh-hoc-te-bao-nang-cao.pdf',    'file_path' => 'documents/topics/12/sinh-hoc-te-bao.pdf',            'file_size' => 990000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 13, 'uploaded_by' => 1, 'file_name' => 'sorting-algorithms-summary.pdf',  'file_path' => 'documents/topics/13/sorting-algorithms.pdf',         'file_size' => 1100000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 14, 'uploaded_by' => 2, 'file_name' => 'mang-may-tinh-osi-model.pdf',     'file_path' => 'documents/topics/14/mang-may-tinh.pdf',             'file_size' => 1450000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 17, 'uploaded_by' => 5, 'file_name' => 'kinh-te-vi-mo-nhap-mon.pdf',      'file_path' => 'documents/topics/17/kinh-te-vi-mo.pdf',              'file_size' => 1250000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 20, 'uploaded_by' => 2, 'file_name' => 'xac-suat-thong-ke-toan-hoc.pdf',  'file_path' => 'documents/topics/20/xac-suat-thong-ke.pdf',          'file_size' => 2500000,  'mime_type' => 'application/pdf',  'created_at' => now()],
        ]);
    }
}
