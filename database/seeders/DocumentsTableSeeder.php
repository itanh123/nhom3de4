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
            ['topic_id' => 1,  'uploaded_by' => 3, 'file_name' => 'php-bien-va-kieu-du-lieu.pptx',   'file_path' => 'documents/topics/1/php-bien-va-kieu-du-lieu.pptx',   'file_size' => 2048000,  'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'created_at' => now()],
            ['topic_id' => 2,  'uploaded_by' => 3, 'file_name' => 'laravel-routing.pdf',             'file_path' => 'documents/topics/2/laravel-routing.pdf',             'file_size' => 876000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 2,  'uploaded_by' => 3, 'file_name' => 'eloquent-orm-guide.pdf',          'file_path' => 'documents/topics/2/eloquent-orm-guide.pdf',          'file_size' => 1540000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 3,  'uploaded_by' => 4, 'file_name' => 'mysql-select-query.pdf',          'file_path' => 'documents/topics/3/mysql-select-query.pdf',          'file_size' => 640000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 3,  'uploaded_by' => 4, 'file_name' => 'database-normalization.pdf',      'file_path' => 'documents/topics/3/database-normalization.pdf',      'file_size' => 930000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 4,  'uploaded_by' => 4, 'file_name' => 'html5-semantic-tags.pdf',         'file_path' => 'documents/topics/4/html5-semantic-tags.pdf',         'file_size' => 710000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 5,  'uploaded_by' => 5, 'file_name' => 'es6-arrow-functions.pdf',         'file_path' => 'documents/topics/5/es6-arrow-functions.pdf',         'file_size' => 520000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 5,  'uploaded_by' => 5, 'file_name' => 'promise-async-await.pptx',        'file_path' => 'documents/topics/5/promise-async-await.pptx',        'file_size' => 1800000,  'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'created_at' => now()],
            ['topic_id' => 6,  'uploaded_by' => 5, 'file_name' => 'rest-api-principles.pdf',         'file_path' => 'documents/topics/6/rest-api-principles.pdf',         'file_size' => 1200000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 7,  'uploaded_by' => 7, 'file_name' => 'git-branching-strategy.pdf',      'file_path' => 'documents/topics/7/git-branching-strategy.pdf',      'file_size' => 680000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 8,  'uploaded_by' => 7, 'file_name' => 'scrum-guide-2020.pdf',            'file_path' => 'documents/topics/8/scrum-guide-2020.pdf',            'file_size' => 460000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 9,  'uploaded_by' => 3, 'file_name' => 'owasp-top-10.pdf',               'file_path' => 'documents/topics/9/owasp-top-10.pdf',               'file_size' => 1380000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 10, 'uploaded_by' => 4, 'file_name' => 'docker-getting-started.pdf',     'file_path' => 'documents/topics/10/docker-getting-started.pdf',     'file_size' => 2200000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 11, 'uploaded_by' => 5, 'file_name' => 'vuejs3-composition-api.pdf',     'file_path' => 'documents/topics/11/vuejs3-composition-api.pdf',     'file_size' => 1750000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 12, 'uploaded_by' => 7, 'file_name' => 'phpunit-testing-guide.pdf',      'file_path' => 'documents/topics/12/phpunit-testing-guide.pdf',      'file_size' => 990000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 13, 'uploaded_by' => 1, 'file_name' => 'sorting-algorithms.pdf',         'file_path' => 'documents/topics/13/sorting-algorithms.pdf',         'file_size' => 1100000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 15, 'uploaded_by' => 3, 'file_name' => 'oop-principles-solid.pdf',       'file_path' => 'documents/topics/15/oop-principles-solid.pdf',       'file_size' => 830000,   'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 19, 'uploaded_by' => 1, 'file_name' => 'aws-ec2-s3-overview.pdf',        'file_path' => 'documents/topics/19/aws-ec2-s3-overview.pdf',        'file_size' => 1640000,  'mime_type' => 'application/pdf',  'created_at' => now()],
            ['topic_id' => 20, 'uploaded_by' => 2, 'file_name' => 'ml-supervised-learning.pdf',     'file_path' => 'documents/topics/20/ml-supervised-learning.pdf',     'file_size' => 2500000,  'mime_type' => 'application/pdf',  'created_at' => now()],
        ]);
    }
}
