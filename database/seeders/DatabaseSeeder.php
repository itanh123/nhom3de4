<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            TopicSeeder::class,
            AiConfigSeeder::class,
            ExamSeeder::class,
            ActivityLogSeeder::class,
            ImportHistorySeeder::class,
        ]);
    }
}
