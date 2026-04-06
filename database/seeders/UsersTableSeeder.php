<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            // Admins (ID 1-2)
            ['name' => 'Nguyễn Văn Hùng',   'email' => 'admin@edu.vn',           'password' => Hash::make('password'), 'role' => 'admin',   'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Trần Thị Mai',       'email' => 'mai.tran@edu.vn',        'password' => Hash::make('password'), 'role' => 'admin',   'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Teachers (ID 3-7)
            ['name' => 'Lê Minh Tuấn',       'email' => 'tuan.le@edu.vn',         'password' => Hash::make('password'), 'role' => 'teacher', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Phạm Quốc Bảo',      'email' => 'bao.pham@edu.vn',        'password' => Hash::make('password'), 'role' => 'teacher', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hoàng Thị Linh',     'email' => 'linh.hoang@edu.vn',      'password' => Hash::make('password'), 'role' => 'teacher', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vũ Đức Thắng',       'email' => 'thang.vu@edu.vn',        'password' => Hash::make('password'), 'role' => 'teacher', 'avatar' => null, 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Đinh Thị Hoa',       'email' => 'hoa.dinh@edu.vn',        'password' => Hash::make('password'), 'role' => 'teacher', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],

            // Students (ID 8-20)
            ['name' => 'Ngô Thanh Tùng',     'email' => 'tung.ngo@gmail.com',     'password' => Hash::make('password'), 'role' => 'student', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bùi Thị Hương',      'email' => 'huong.bui@gmail.com',    'password' => Hash::make('password'), 'role' => 'student', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Đỗ Văn Khoa',        'email' => 'khoa.do@gmail.com',      'password' => Hash::make('password'), 'role' => 'student', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lý Thị Kim Ngân',    'email' => 'ngan.ly@gmail.com',      'password' => Hash::make('password'), 'role' => 'student', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Phan Anh Dũng',      'email' => 'dung.phan@gmail.com',    'password' => Hash::make('password'), 'role' => 'student', 'avatar' => null, 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Trương Văn Phúc',    'email' => 'phuc.truong@gmail.com',  'password' => Hash::make('password'), 'role' => 'student', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hà Thị Ngọc',        'email' => 'ngoc.ha@gmail.com',      'password' => Hash::make('password'), 'role' => 'student', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Võ Minh Châu',       'email' => 'chau.vo@gmail.com',      'password' => Hash::make('password'), 'role' => 'student', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dương Văn Long',     'email' => 'long.duong@gmail.com',   'password' => Hash::make('password'), 'role' => 'student', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mai Thị Thu',        'email' => 'thu.mai@gmail.com',      'password' => Hash::make('password'), 'role' => 'student', 'avatar' => null, 'is_active' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lưu Quang Hải',     'email' => 'hai.luu@gmail.com',      'password' => Hash::make('password'), 'role' => 'student', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Châu Thị Bích',      'email' => 'bich.chau@gmail.com',    'password' => Hash::make('password'), 'role' => 'student', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Trịnh Văn Nam',      'email' => 'nam.trinh@gmail.com',    'password' => Hash::make('password'), 'role' => 'student', 'avatar' => null, 'is_active' => true,  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
